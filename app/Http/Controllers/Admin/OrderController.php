<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewOrderReceived;
use App\Events\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\OrderStatusChanged;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $this->getRestaurantId();

        $query = Order::with(['user', 'items.product', 'deliveryPerson'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId));

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('first_name', 'like', "%{$search}%")
                                                     ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(20)->withQueryString();

        // Statistiques rapides
        $stats = [
            'pending' => Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->where('status', 'pending')->count(),
            'preparing' => Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->where('status', 'preparing')->count(),
            'ready' => Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->where('status', 'ready')->count(),
            'in_delivery' => Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->where('status', 'in_delivery')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        // $this->authorize('view', $order);

        $order->load([
            'user',
            'items.product',
            'items.options',
            'statusHistory.user',
            'deliveryPerson',
            'deliveryTracking',
            'review',
            'payment',
        ]);

        $availableStatuses = $this->getAvailableStatuses($order->status);
        $deliveryPersons = User::role('delivery_person')->active()->get();

        return view('admin.orders.show', compact('order', 'availableStatuses', 'deliveryPersons'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', [
                'pending', 'confirmed', 'preparing', 'ready',
                'in_delivery', 'delivered', 'completed', 'cancelled',
            ]),
            'comment' => 'nullable|string|max:500',
        ]);

        // Vérifier si le statut est valide
        if (!$this->isValidStatusTransition($order->status, $validated['status'])) {
            return back()->with('error', 'Transition de statut non valide.');
        }

        $order->updateStatus(
            $validated['status'],
            $validated['comment'] ?? null,
            auth()->user()
        );

        return back()->with('success', 'Statut de la commande mis à jour.');
    }

    public function assignDelivery(Request $request, Order $order)
    {
        // $this->authorize('update', $order);

        $validated = $request->validate([
            'delivery_person_id' => 'required|exists:users,id',
        ]);

        $deliveryPerson = User::findOrFail($validated['delivery_person_id']);
        
        if (!$deliveryPerson->hasRole('delivery_person')) {
            return back()->with('error', 'L\'utilisateur sélectionné n\'est pas un livreur.');
        }

        $order->assignDeliveryPerson($deliveryPerson);

        return back()->with('success', 'Livreur assigné avec succès.');
    }

    public function cancel(Request $request, Order $order)
    {
        // $this->authorize('update', $order);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        $order->cancel($validated['reason']);

        return back()->with('success', 'Commande annulée avec succès.');
    }

    public function print(Order $order)
    {
        $order->load(['items.product', 'user', 'restaurant']);

        return view('admin.orders.print', compact('order'));
    }

    public function export(Request $request)
    {
        $restaurantId = $this->getRestaurantId();

        $orders = Order::with(['user', 'items.product'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->get();

        $filename = 'commandes_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'N° Commande',
                'Date',
                'Client',
                'Type',
                'Statut',
                'Articles',
                'Total',
                'Paiement',
            ]);

            // Données
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->full_name,
                    $order->type,
                    $order->status_label,
                    $order->items->sum('quantity'),
                    number_format($order->total, 2),
                    $order->payment_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getAvailableStatuses(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['in_delivery', 'completed'],
            'in_delivery' => ['delivered'],
            'delivered' => ['completed'],
            default => [],
        };
    }

    protected function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $allowedTransitions = $this->getAvailableStatuses($currentStatus);
        return in_array($newStatus, $allowedTransitions);
    }

    protected function getRestaurantId(): ?int
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return request()->get('restaurant_id');
        }

        return $user->restaurants()->first()?->id;
    }

    // Après avoir créé/mis à jour une commande
    // $notification = \App\Models\Notification::create([
    //     'user_id' => auth()->id(),
    //     'type' => 'new_order',
    //     'title' => 'Nouvelle commande',
    //     'message' => "Commande #{$order->order_number} reçue - {$order->user->full_name}",
    //     'data' => json_encode(['order_id' => $order->id]),
    // ]);

    // // Diffuser l'événement
    // broadcast(new NewOrderReceived($order))->toOthers();
    // broadcast(new NewNotification($notification))->toOthers();

}