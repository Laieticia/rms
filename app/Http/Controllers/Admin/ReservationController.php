<?php

namespace App\Http\Controllers\Admin;

use App\HasRestaurant;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Notifications\ReservationConfirmed;
use App\Notifications\ReservationCancelled;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use HasRestaurant;

    /**
     * Liste des réservations du restaurant.
     */
    public function index(Request $request)
    {
        $restaurantId = $this->getRestaurantId();

        $query = Reservation::where('restaurant_id', $restaurantId)->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            // Par défaut : aujourd'hui et les réservations à venir
            $query->where('date', '>=', today())->orWhere(function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId)->where('status', 'pending');
            });
        }

        $reservations = $query->orderBy('date')->orderBy('time')->paginate(20)->withQueryString();

        $stats = [
            'pending' => Reservation::where('restaurant_id', $restaurantId)->where('status', 'pending')->count(),
            'confirmed_today' => Reservation::where('restaurant_id', $restaurantId)
                ->where('status', 'confirmed')->whereDate('date', today())->count(),
            'total_today' => Reservation::where('restaurant_id', $restaurantId)->whereDate('date', today())->count(),
        ];

        return view('admin.reservations.index', compact('reservations', 'stats'));
    }

    /**
     * Détail d'une réservation.
     */
    public function show(Reservation $reservation)
    {
        $this->authorizeRestaurant($reservation);

        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Confirmer une réservation (avec attribution optionnelle d'une table).
     */
    public function confirm(Request $request, Reservation $reservation)
    {
        $this->authorizeRestaurant($reservation);

        $validated = $request->validate([
            'table_number' => 'nullable|string|max:50',
        ]);

        $reservation->update([
            'status' => 'confirmed',
            'table_number' => $validated['table_number'] ?? $reservation->table_number,
        ]);

        if ($reservation->user) {
            $reservation->user->notify(new ReservationConfirmed($reservation));
        }

        return back()->with('success', 'Réservation confirmée.');
    }

    /**
     * Refuser / annuler une réservation.
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $this->authorizeRestaurant($reservation);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        if ($reservation->user) {
            $reservation->user->notify(new ReservationCancelled($reservation));
        }

        return back()->with('success', 'Réservation annulée.');
    }

    /**
     * Marquer une réservation comme terminée (client venu et reparti).
     */
    public function complete(Reservation $reservation)
    {
        $this->authorizeRestaurant($reservation);

        $reservation->update(['status' => 'completed']);

        return back()->with('success', 'Réservation marquée comme terminée.');
    }

    /**
     * Vérifie que la réservation appartient bien au restaurant de l'utilisateur connecté.
     */
    protected function authorizeRestaurant(Reservation $reservation): void
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->restaurants()->where('restaurant_id', $reservation->restaurant_id)->exists()) {
            abort(403, 'Action non autorisée.');
        }
    }
}
