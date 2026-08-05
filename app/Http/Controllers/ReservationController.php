<?php

namespace App\Http\Controllers;

use App\Events\ReservationCreated;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Formulaire de réservation pour un restaurant.
     */
    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }

    /**
     * Enregistrer une nouvelle réservation.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'guests_count' => 'required|integer|min:1|max:30',
            'special_requests' => 'nullable|string|max:500',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
        ]);

        $reservation = Reservation::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => auth()->id(),
            'reservation_number' => $this->generateReservationNumber(),
            'date' => $validated['date'],
            'time' => $validated['time'],
            'guests_count' => $validated['guests_count'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
        ]);

        event(new ReservationCreated($reservation));

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Votre demande de réservation a bien été envoyée ! Le restaurant vous confirmera sous peu.');
    }

    /**
     * Détail d'une réservation (confirmation).
     */
    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Liste des réservations du client connecté.
     */
    public function index()
    {
        $reservations = auth()->user()->reservations()
            ->with('restaurant')
            ->orderByDesc('date')
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    protected function generateReservationNumber(): string
    {
        $prefix = 'RES' . date('Ymd');
        $lastReservation = Reservation::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastReservation && str_starts_with($lastReservation->reservation_number, $prefix)) {
            $sequence = intval(substr($lastReservation->reservation_number, -4)) + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
