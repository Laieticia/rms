<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductOption;
use App\Models\ProductOptionItem;
use App\HasRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasRestaurant;

    public function index(Request $request)
    {
        $restaurantId = $this->getRestaurantId();

        if (!$restaurantId) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Aucun restaurant configuré. Veuillez contacter l\'administrateur.');
        }
        
        $query = Product::with(['category', 'primaryImage'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId));

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            match($request->status) {
                'active' => $query->available(),
                'inactive' => $query->where('is_available', false),
                'out_of_stock' => $query->outOfStock(),
                'low_stock' => $query->lowStock(),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(5)->withQueryString();
        $categories = Category::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->active()
            ->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('restaurant_id', $this->getRestaurantId())
            ->active()
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'preparation_time' => 'required|integer|min:1',
            'calories' => 'nullable|integer|min:0',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_gluten_free' => 'boolean',
            'is_spicy' => 'boolean',
            'allergens' => 'nullable|array',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
            'images.*' => 'nullable|image|max:2048',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string',
            'variants.*.price_adjustment' => 'required|numeric',
            'options' => 'nullable|array',
            'options.*.name' => 'required|string',
            'options.*.type' => 'required|in:single,multiple',
            'options.*.items' => 'nullable|array',
            'options.*.items.*.name' => 'required|string',
            'options.*.items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create([
                'restaurant_id' => $this->getRestaurantId(),
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'compare_price' => $validated['compare_price'],
                'cost_price' => $validated['cost_price'],
                'preparation_time' => $validated['preparation_time'],
                'calories' => $validated['calories'],
                'is_vegetarian' => $request->boolean('is_vegetarian'),
                'is_vegan' => $request->boolean('is_vegan'),
                'is_gluten_free' => $request->boolean('is_gluten_free'),
                'is_spicy' => $request->boolean('is_spicy'),
                'allergens' => $validated['allergens'] ?? [],
                'track_inventory' => $request->boolean('track_inventory'),
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'],
                'is_featured' => $request->boolean('is_featured'),
                'is_available' => $request->boolean('is_available'),
            ]);

            // Upload des images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'alt_text' => $product->name,
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            // Créer les variantes
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $index => $variant) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variant['name'],
                        'price_adjustment' => $variant['price_adjustment'],
                        'sort_order' => $index,
                        'is_available' => true,
                    ]);
                }
            }

            // Créer les options
            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $optionData) {
                    $option = ProductOption::create([
                        'product_id' => $product->id,
                        'name' => $optionData['name'],
                        'type' => $optionData['type'],
                        'is_required' => false,
                        'sort_order' => 0,
                    ]);

                    if (!empty($optionData['items'])) {
                        foreach ($optionData['items'] as $index => $item) {
                            ProductOptionItem::create([
                                'product_option_id' => $option->id,
                                'name' => $item['name'],
                                'price' => $item['price'],
                                'sort_order' => $index,
                                'is_available' => true,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du produit: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        // $this->authorize('update', $product);
        if (!auth()->user()->isAdmin() && 
            !auth()->user()->restaurants()->where('restaurant_id', $product->restaurant_id)->exists()) {
            abort(403, 'Action non autorisée.');
        }

        $product->load(['images', 'variants', 'options.items']);
        $categories = Category::where('restaurant_id', $product->restaurant_id)->active()->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // $this->authorize('update', $product);
        if (!auth()->user()->isAdmin() && 
            !auth()->user()->restaurants()->where('restaurant_id', $product->restaurant_id)->exists()) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'preparation_time' => 'required|integer|min:1',
            'calories' => 'nullable|integer|min:0',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_gluten_free' => 'boolean',
            'is_spicy' => 'boolean',
            'allergens' => 'nullable|array',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $product->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        // Supprimer les images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,remove,set',
            'reason' => 'nullable|string',
        ]);

        match($validated['type']) {
            'add' => $product->incrementStock(abs($validated['quantity'])),
            'remove' => $product->decrementStock(abs($validated['quantity'])),
            'set' => $product->setStock($validated['quantity']),
        };

        return response()->json([
            'success' => true,
            'new_quantity' => $product->stock_quantity,
            'message' => 'Stock mis à jour avec succès.',
        ]);
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => 'required|image|max:2048',
        ]);

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');
            
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'sort_order' => $product->images()->count() + $index,
                'is_primary' => !$product->images()->exists() && $index === 0,
            ]);
        }

        return back()->with('success', 'Images ajoutées avec succès.');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image supprimée avec succès.');
    }

    // protected function getRestaurantId(): int
    // {
    //     $user = auth()->user();
        
    //     if ($user->isAdmin() && request()->filled('restaurant_id')) {
    //         return request()->restaurant_id;
    //     }

    //     return $user->restaurants()->first()->id;
    // }
}