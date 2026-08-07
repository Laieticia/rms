<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DeliveryTracking;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $orders = Order::with(['restaurant', 'user'])
            ->where('delivery_person_id', auth()->id())
            ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery.dashboard', compact('orders'));
    }

    public function show(Order $order)
    {
        // Vérifier que la commande appartient au livreur connecté
        if ($order->delivery_person_id !== auth()->id()) {
            abort(403, 'Accès non autorisé à cette commande.');
        }

        $order->load(['restaurant', 'user', 'items.product']);

        return view('delivery.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:in_delivery,delivered',
        ]);

        $order->updateStatus($validated['status'], 'Mis à jour par le livreur');

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    public function updateLocation(Request $request, Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed' => 'nullable|numeric',
        ]);

        DeliveryTracking::create([
            'order_id' => $order->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'speed' => $validated['speed'] ?? 0,
            'status' => $order->status,
            'recorded_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
