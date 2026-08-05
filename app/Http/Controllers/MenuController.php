<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menu d'un restaurant : liste de produits filtrable (catégorie, régime, prix, recherche).
     */
    public function index(Restaurant $restaurant, Request $request)
    {
        if (!$restaurant->is_active) {
            abort(404);
        }

        $categories = $restaurant->categories()
            ->active()
            ->withCount('availableProducts')
            ->orderBy('sort_order')
            ->get();

        $query = $restaurant->products()->available()->with('primaryImage');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->get('category'));
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->boolean('vegetarian')) {
            $query->where('is_vegetarian', true);
        }

        if ($request->boolean('vegan')) {
            $query->where('is_vegan', true);
        }

        if ($request->boolean('gluten_free')) {
            $query->where('is_gluten_free', true);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        $products = $query->orderBy('sort_order')->paginate(12)->withQueryString();

        return view('menu.index', compact('restaurant', 'categories', 'products'));
    }

    /**
     * Fiche détaillée d'un produit : options, avis, produits similaires.
     */
    public function show(Product $product)
    {
        if (!$product->is_available) {
            abort(404);
        }

        $product->load(['restaurant', 'category', 'images', 'variants', 'options.items']);
        $product->loadCount('reviews')->loadAvg('reviews', 'rating');

        $relatedProducts = Product::available()
            ->where('restaurant_id', $product->restaurant_id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('primaryImage')
            ->take(4)
            ->get();

        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('menu.show', compact('product', 'relatedProducts', 'reviews'));
    }
}
