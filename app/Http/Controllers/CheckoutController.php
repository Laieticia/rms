<?php

namespace App\Http\Controllers;

use App\Events\NewOrderPlaced;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CameroonianPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
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

        $defaultAddress = auth()->user()->getDefaultAddress();
        $addresses = auth()->user()->addresses;

        $restaurant = $cartItems[0]['product']->restaurant;

        $deliveryFee = $defaultAddress
            ? $restaurant->getDeliveryFee($defaultAddress->latitude, $defaultAddress->longitude)
            : $restaurant->delivery_fee;

        $taxRate = $restaurant->tax_rate ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount + $deliveryFee - $discount;

        $paymentMethods = array_intersect_key(
            CameroonianPaymentService::getPaymentMethods(),
            array_flip(['cash', 'mtn_money', 'orange_money', 'nexttel'])
        );

        return view('checkout.index', compact(
            'cartItems',
            'restaurant',
            'subtotal',
            'discount',
            'taxAmount',
            'deliveryFee',
            'total',
            'couponCode',
            'defaultAddress',
            'addresses',
            'paymentMethods'
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
            'payment_method' => 'required|in:mtn_money,orange_money,nexttel,cash',
            'payment_phone' => 'required_unless:payment_method,cash|nullable|string',
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

            $deliveryFee = $restaurant->getDeliveryFee($address->latitude, $address->longitude);
            $taxAmount = $subtotal * (($restaurant->tax_rate ?? 0) / 100);
            $total = $subtotal + $taxAmount + $deliveryFee - $discount;

            $coupon = $couponCode ? Coupon::where('code', $couponCode)->first() : null;

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
                'payment_method' => $validated['payment_method'] === 'cash' ? 'cash' : 'wallet',
                'payment_status' => $validated['payment_method'] === 'cash' ? 'pending' : 'paid',
                'payment_gateway' => $validated['payment_method'],
                'delivery_address' => $address->street_address,
                'delivery_city' => $address->city,
                'delivery_postal_code' => $address->postal_code,
                'delivery_latitude' => $address->latitude,
                'delivery_longitude' => $address->longitude,
                'delivery_instructions' => $validated['delivery_instructions'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'estimated_delivery_time' => $restaurant->estimated_delivery_time,
                'source' => 'web',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
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

                if ($item['product']->track_inventory) {
                    $item['product']->decrementStock($item['quantity']);
                }

                $item['product']->incrementOrders($item['quantity']);
            }

            // Paiement simulé pour tests
            if ($validated['payment_method'] === 'cash') {
                $order->update(['payment_status' => 'pending']);
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'amount' => $total,
                    'currency' => 'XAF',
                    'status' => 'completed',
                    'type' => 'payment',
                    'gateway' => $validated['payment_method'],
                    'transaction_id' => 'SIM-'.time(),
                    'metadata' => ['payment_method' => $validated['payment_method']],
                ]);
            }

            if ($coupon) {
                $coupon->incrementUsage();
            }

            session()->forget('cart');
            session()->forget('coupon_code');
            session()->forget('discount');

            event(new NewOrderPlaced($order));

            DB::commit();

            $message = $validated['payment_method'] === 'cash'
                ? 'Votre commande a été passée avec succès ! Vous paierez à la livraison.'
                : 'Votre commande a été passée. Validez la demande de paiement reçue sur votre téléphone.';

            return redirect()->route('orders.track', $order)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
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
