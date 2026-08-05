<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function track(Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== auth()->id() && !auth()->user()->isStaff()) {
            abort(403);
        }

        $order->load([
            'items.product',
            'statusHistory.user',
            'deliveryTracking' => function($q) {
                $q->latest()->take(20);
            },
            'deliveryPerson',
            'restaurant',
        ]);

        return view('orders.track', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order->cancel($request->reason);

        return redirect()
            ->route('profile.orders')
            ->with('success', 'Commande annulée.');
    }

    public function reviewForm(Order $order)
    {
        if (!$order->canBeReviewedBy(auth()->user())) {
            abort(403, 'Cette commande ne peut pas être notée.');
        }

        return view('reviews.create', compact('order'));
    }

    public function addReview(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeReviewedBy(auth()->user())) {
            return back()->with('error', 'Vous ne pouvez pas laisser d\'avis sur cette commande.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'food_rating' => 'nullable|integer|min:1|max:5',
            'delivery_rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order->review()->create([
            'user_id' => auth()->id(),
            'restaurant_id' => $order->restaurant_id,
            'rating' => $request->rating,
            'food_rating' => $request->food_rating,
            'delivery_rating' => $request->delivery_rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Merci pour votre avis !');
    }
}