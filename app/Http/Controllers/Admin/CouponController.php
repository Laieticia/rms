<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $restaurantId = $this->getRestaurantId();
        
        $coupons = Coupon::where('restaurant_id', $restaurantId)
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::where('restaurant_id', $this->getRestaurantId())->get();
        $categories = Category::where('restaurant_id', $this->getRestaurantId())->get();

        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,free_delivery',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'applies_to_all' => 'boolean',
            'products' => 'nullable|array',
            'categories' => 'nullable|array',
        ]);

        $validated['restaurant_id'] = $this->getRestaurantId();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['applies_to_all'] = $request->boolean('applies_to_all');

        $coupon = Coupon::create($validated);

        // Associer les produits
        if (!$validated['applies_to_all'] && !empty($validated['products'])) {
            $coupon->products()->sync($validated['products']);
        }

        // Associer les catégories
        if (!$validated['applies_to_all'] && !empty($validated['categories'])) {
            $coupon->categories()->sync($validated['categories']);
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon créé avec succès.');
    }

    public function edit(Coupon $coupon)
    {
        $coupon->load(['products', 'categories']);
        $products = Product::where('restaurant_id', $coupon->restaurant_id)->get();
        $categories = Category::where('restaurant_id', $coupon->restaurant_id)->get();

        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id . '|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,free_delivery',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'applies_to_all' => 'boolean',
            'products' => 'nullable|array',
            'categories' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['applies_to_all'] = $request->boolean('applies_to_all');

        $coupon->update($validated);

        // Mettre à jour les associations
        if (!$validated['applies_to_all']) {
            $coupon->products()->sync($validated['products'] ?? []);
            $coupon->categories()->sync($validated['categories'] ?? []);
        } else {
            $coupon->products()->detach();
            $coupon->categories()->detach();
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon mis à jour.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon supprimé.');
    }

    private function getRestaurantId(): int
    {
        $user = auth()->user();
        
        if ($user->isAdmin() && request()->filled('restaurant_id')) {
            return request()->restaurant_id;
        }

        return $user->restaurants()->first()?->id ?? 1;
    }
}
