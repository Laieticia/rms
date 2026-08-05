<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Liste des restaurants, avec filtre optionnel par catégorie/ville et recherche.
     */
    public function index(Request $request)
    {
        $query = Restaurant::active()
            ->withCount(['reviews', 'orders'])
            ->withAvg('reviews', 'rating');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->get('city'));
        }

        if ($request->filled('category')) {
            $categorySlug = $request->get('category');
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $sort = $request->get('sort', 'popular');
        match ($sort) {
            'rating' => $query->orderByDesc('reviews_avg_rating'),
            'delivery_time' => $query->orderBy('estimated_delivery_time'),
            default => $query->orderByDesc('orders_count'),
        };

        $restaurants = $query->paginate(12)->withQueryString();

        // Liste des villes disponibles pour le filtre
        $cities = Restaurant::active()->distinct()->pluck('city')->filter()->values();

        return view('restaurants.index', compact('restaurants', 'cities'));
    }

    /**
     * Fiche d'un restaurant : infos, catégories de son menu, avis récents.
     */
    public function show(Restaurant $restaurant)
    {
        if (!$restaurant->is_active) {
            abort(404);
        }

        $restaurant->loadCount('reviews')->loadAvg('reviews', 'rating');

        $categories = $restaurant->categories()
            ->active()
            ->withCount('availableProducts')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = $restaurant->products()
            ->available()
            ->featured()
            ->with('primaryImage')
            ->take(6)
            ->get();

        $reviews = $restaurant->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('restaurants.show', compact('restaurant', 'categories', 'featuredProducts', 'reviews'));
    }
}
