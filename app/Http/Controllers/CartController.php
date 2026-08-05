<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->is_available) {
                $itemTotal = $product->price * $item['quantity'];
                
                // Ajouter le prix des options
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $option) {
                        $itemTotal += $option['price'] * $item['quantity'];
                    }
                }

                $subtotal += $itemTotal;
                
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'options' => $item['options'] ?? [],
                    'notes' => $item['notes'] ?? '',
                    'total' => $itemTotal,
                ];
            }
        }

        // Appliquer le coupon si présent
        $coupon = null;
        $discount = 0;
        $couponCode = session()->get('coupon_code');

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('coupon_code');
                session()->forget('discount');
            }
        }

        $total = $subtotal - $discount;

        return view('cart.index', compact('cartItems', 'subtotal', 'discount', 'total', 'coupon'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'options.*.option_id' => 'required|integer',
            'options.*.item_id' => 'required|integer',
            'options.*.price' => 'required|numeric',
            'options.*.name' => 'required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->isAvailable() || !$product->canBeOrdered($validated['quantity'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit n\'est pas disponible en quantité suffisante.',
            ], 400);
        }

        $cart = session()->get('cart', []);

        // Vérifier si le produit est déjà dans le panier
        $cartKey = $this->findCartItem($cart, $validated);

        if ($cartKey !== false) {
            $cart[$cartKey]['quantity'] += $validated['quantity'];
        } else {
            $cart[] = [
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'options' => $validated['options'] ?? [],
                'notes' => $validated['notes'] ?? '',
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier.',
            'cart_count' => count($cart),
            'cart_total' => $this->calculateCartTotal($cart),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.index' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);

        foreach ($validated['items'] as $item) {
            $index = $item['index'];
            
            if (isset($cart[$index])) {
                if ($item['quantity'] > 0) {
                    $cart[$index]['quantity'] = $item['quantity'];
                } else {
                    unset($cart[$index]);
                }
            }
        }

        session()->put('cart', array_values($cart));

        return redirect()->route('cart.index')->with('success', 'Panier mis à jour.');
    }

    public function remove(Request $request, $index)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            session()->put('cart', array_values($cart));
        }

        return redirect()->route('cart.index')->with('success', 'Produit retiré du panier.');
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('coupon_code');
        session()->forget('discount');

        return redirect()->route('cart.index')->with('success', 'Panier vidé.');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:coupons,code',
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (!$coupon->isValid()) {
            return back()->with('error', 'Ce code promo n\'est pas valide ou a expiré.');
        }

        $cart = session()->get('cart', []);
        $subtotal = $this->calculateCartTotal($cart);

        if ($subtotal < $coupon->min_order_amount) {
            $formattedMin = \App\Helpers\CameroonHelper::formatCurrency($coupon->min_order_amount);
            return back()->with('error', "Le montant minimum de commande est de {$formattedMin} pour utiliser ce code.");
        }

        if (!$coupon->isValidForUser(auth()->user())) {
            return back()->with('error', 'Vous avez déjà utilisé ce code promo.');
        }

        session()->put('coupon_code', $coupon->code);
        session()->put('discount', $coupon->calculateDiscount($subtotal));

        return back()->with('success', 'Code promo appliqué avec succès !');
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');
        session()->forget('discount');

        return back()->with('success', 'Code promo retiré.');
    }

    protected function findCartItem(array $cart, array $newItem): int|false
    {
        foreach ($cart as $index => $item) {
            if ($item['product_id'] == $newItem['product_id'] &&
                ($item['options'] ?? []) == ($newItem['options'] ?? []) &&
                ($item['notes'] ?? '') == ($newItem['notes'] ?? '')) {
                return $index;
            }
        }

        return false;
    }

    protected function calculateCartTotal(array $cart): float
    {
        $total = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $itemTotal = $product->price * $item['quantity'];
                
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $option) {
                        $itemTotal += $option['price'] * $item['quantity'];
                    }
                }
                
                $total += $itemTotal;
            }
        }

        return $total;
    }
}