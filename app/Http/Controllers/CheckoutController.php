<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Events\NewOrderPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->middleware('auth');
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $cartItems = $this->getCartItems($cart);
        $subtotal = $this->calculateSubtotal($cartItems);
        $discount = session()->get('discount', 0);
        $couponCode = session()->get('coupon_code');

        // Récupérer l'adresse par défaut de l'utilisateur
        $defaultAddress = auth()->user()->getDefaultAddress();
        $addresses = auth()->user()->addresses;

        // Calculer les frais de livraison
        $deliveryFee = 0;
        if ($defaultAddress && isset($cartItems[0])) {
            $restaurant = $cartItems[0]['product']->restaurant;
            $deliveryFee = $restaurant->getDeliveryFee(
                $defaultAddress->latitude,
                $defaultAddress->longitude
            );
        }

        $taxRate = isset($cartItems[0]) ? $cartItems[0]['product']->restaurant->tax_rate : 20;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount + $deliveryFee - $discount;

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'discount',
            'taxAmount',
            'deliveryFee',
            'total',
            'couponCode',
            'defaultAddress',
            'addresses'
        ));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'delivery_instructions' => 'nullable|string|max:500',
            'payment_method' => 'required|in:card,cash,online',
            'stripe_token' => 'required_if:payment_method,card',
            'notes' => 'nullable|string|max:500',
            'terms' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            $cartItems = $this->getCartItems($cart);
            $subtotal = $this->calculateSubtotal($cartItems);
            $discount = session()->get('discount', 0);
            $couponCode = session()->get('coupon_code');

            $restaurant = $cartItems[0]['product']->restaurant;
            $address = auth()->user()->addresses()->findOrFail($validated['address_id']);

            // Calculer les frais de livraison
            $deliveryFee = $restaurant->getDeliveryFee($address->latitude, $address->longitude);
            $taxAmount = $subtotal * ($restaurant->tax_rate / 100);
            $total = $subtotal + $taxAmount + $deliveryFee - $discount;

            // Récupérer le coupon
            $coupon = null;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
            }

            // Créer la commande
            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => auth()->id(),
                'address_id' => $address->id,
                'coupon_id' => $coupon?->id,
                'order_number' => Order::generateOrderNumber(),
                'type' => 'delivery',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'delivery_address' => $address->street_address,
                'delivery_city' => $address->city,
                'delivery_postal_code' => $address->postal_code,
                'delivery_latitude' => $address->latitude,
                'delivery_longitude' => $address->longitude,
                'delivery_instructions' => $validated['delivery_instructions'],
                'notes' => $validated['notes'],
                'estimated_delivery_time' => $restaurant->estimated_delivery_time,
                'source' => 'web',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Créer les items de la commande
            foreach ($cartItems as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'unit_price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total'],
                    'special_instructions' => $item['notes'] ?? '',
                    'product_data' => [
                        'id' => $item['product']->id,
                        'name' => $item['product']->name,
                        'image' => $item['product']->primary_image_url,
                    ],
                ]);

                // Décrémenter le stock
                if ($item['product']->track_inventory) {
                    $item['product']->decrementStock($item['quantity']);
                }

                // Incrémenter le compteur de commandes
                $item['product']->incrementOrders($item['quantity']);
            }

            // Traiter le paiement
            if ($validated['payment_method'] === 'card') {
                try {
                    $paymentIntent = $this->stripe->paymentIntents->create([
                        'amount' => (int)($total * 100),
                        'currency' => 'eur',
                        'payment_method' => $validated['stripe_token'],
                        'confirmation_method' => 'manual',
                        'confirm' => true,
                        'metadata' => [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                        ],
                    ]);

                    $order->update([
                        'payment_status' => 'paid',
                        'payment_id' => $paymentIntent->id,
                        'payment_gateway' => 'stripe',
                        'paid_at' => now(),
                    ]);

                    // Créer l'enregistrement de paiement
                    \App\Models\Payment::create([
                        'order_id' => $order->id,
                        'user_id' => auth()->id(),
                        'amount' => $total,
                        'currency' => 'EUR',
                        'status' => 'completed',
                        'type' => 'payment',
                        'gateway' => 'stripe',
                        'transaction_id' => $paymentIntent->id,
                        'metadata' => [
                            'payment_intent_id' => $paymentIntent->id,
                        ],
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Erreur de paiement: ' . $e->getMessage());
                }
            }

            // Incrémenter l'utilisation du coupon
            if ($coupon) {
                $coupon->incrementUsage();
            }

            // Vider le panier
            session()->forget('cart');
            session()->forget('coupon_code');
            session()->forget('discount');

            // Déclencher l'événement
            event(new NewOrderPlaced($order));

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Votre commande a été passée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    protected function getCartItems(array $cart): array
    {
        $items = [];

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            
            if ($product && $product->isAvailable()) {
                $itemTotal = $product->price * $item['quantity'];
                
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $option) {
                        $itemTotal += $option['price'] * $item['quantity'];
                    }
                }

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'options' => $item['options'] ?? [],
                    'notes' => $item['notes'] ?? '',
                    'total' => $itemTotal,
                ];
            }
        }

        return $items;
    }

    protected function calculateSubtotal(array $cartItems): float
    {
        return array_sum(array_column($cartItems, 'total'));
    }
}