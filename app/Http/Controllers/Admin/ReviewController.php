<?php

namespace App\Http\Controllers\Admin;

use App\HasRestaurant;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    use HasRestaurant;
    
    public function index(Request $request)
    {
        $restaurantId = $this->getRestaurantId();

        $query = Review::with(['user', 'order', 'product'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId));

        if ($request->filled('status')) {
            match($request->status) {
                'pending' => $query->where('is_approved', false),
                'approved' => $query->where('is_approved', true),
                'featured' => $query->where('is_featured', true),
                default => null,
            };
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'order.items.product', 'product', 'votes']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->approve();

        return back()->with('success', 'Avis approuvé.');
    }

    public function respond(Request $request, Review $review)
    {
        $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        $review->addAdminResponse($request->response);

        return back()->with('success', 'Réponse publiée.');
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $review->update($validated);

        if ($review->product) {
            $review->product->updateRating();
        }

        return back()->with('success', 'Avis mis à jour.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        if ($review->product) {
            $review->product->updateRating();
        }

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Avis supprimé.');
    }

}