@extends('layouts.storefront')

@section('title', 'Mes réservations')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-calendar-check me-2"></i>Mes réservations</h2>

    @forelse($reservations as $reservation)
        <a href="{{ route('reservations.show', $reservation) }}" class="text-decoration-none">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="mb-1" style="color:#1a1a1a;">{{ $reservation->restaurant->name }}</h6>
                    <div class="text-muted small">{{ $reservation->date->format('d/m/Y') }} à {{ $reservation->time->format('H:i') }} · {{ $reservation->guests_count }} pers.</div>
                </div>
                <span class="badge {{ $reservation->status === 'confirmed' ? 'bg-success' : ($reservation->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                    {{ ucfirst($reservation->status) }}
                </span>
            </div>
        </a>
    @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-calendar-x" style="font-size:3rem;"></i>
            <p class="mt-3">Vous n'avez pas encore de réservation.</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $reservations->links() }}</div>
</div>
@endsection
