<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Restaurants en vedette
        $featuredRestaurants = Restaurant::active()
            ->withCount(['reviews', 'orders'])
            ->withAvg('reviews', 'rating')
            ->take(6)
            ->get();

        // Produits populaires
        $popularProducts = Product::available()
            ->featured()
            ->with(['restaurant', 'primaryImage'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('orders_count')
            ->take(8)
            ->get();

        // Catégories populaires
        $categories = Category::active()
            ->whereHas('availableProducts')
            ->withCount('availableProducts')
            ->orderByDesc('available_products_count')
            ->take(8)
            ->get();

        // Avis récents
        $recentReviews = \App\Models\Review::approved()
            ->with(['user', 'restaurant'])
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact(
            'featuredRestaurants',
            'popularProducts',
            'categories',
            'recentReviews'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $restaurants = Restaurant::active()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        $products = Product::available()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with('restaurant')
            ->get();

        return view('search.results', compact('restaurants', 'products', 'query'));
    }
}