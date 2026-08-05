<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\HasRestaurant;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use HasRestaurant;

    public function index()
    {
        $restaurantId = $this->getRestaurantId();
        
        $menus = Menu::where('restaurant_id', $restaurantId)
            ->withCount('products')
            ->orderBy('sort_order')
            ->paginate(10);

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $products = Product::where('restaurant_id', $this->getRestaurantId())
            ->available()
            ->orderBy('name')
            ->get();

        return view('admin.menus.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:regular,lunch,dinner,weekend,special,seasonal',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'available_from' => 'nullable|date_format:H:i',
            'available_until' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'products' => 'nullable|array',
            'products.*.id' => 'exists:products,id',
            'products.*.special_price' => 'nullable|numeric|min:0',
        ]);

        $validated['restaurant_id'] = $this->getRestaurantId();
        $validated['is_active'] = $request->boolean('is_active');

        $menu = Menu::create($validated);

        // Ajouter les produits au menu
        if (!empty($validated['products'])) {
            foreach ($validated['products'] as $index => $productData) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'product_id' => $productData['id'],
                    'special_price' => $productData['special_price'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu créé avec succès.');
    }

    public function edit(Menu $menu)
    {
        $menu->load('products');
        
        $products = Product::where('restaurant_id', $menu->restaurant_id)
            ->orderBy('name')
            ->get();

        return view('admin.menus.edit', compact('menu', 'products'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:regular,lunch,dinner,weekend,special,seasonal',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'available_from' => 'nullable|date_format:H:i',
            'available_until' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'products' => 'nullable|array',
            'products.*.id' => 'exists:products,id',
            'products.*.special_price' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $menu->update($validated);

        // Mettre à jour les produits du menu
        $menu->items()->delete();
        
        if (!empty($validated['products'])) {
            foreach ($validated['products'] as $index => $productData) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'product_id' => $productData['id'],
                    'special_price' => $productData['special_price'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu mis à jour.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu supprimé.');
    }

    // getRestaurantId() est fourni par le trait HasRestaurant
}