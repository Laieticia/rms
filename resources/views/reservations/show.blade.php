@extends('layouts.storefront')

@section('title', 'Réservation ' . $reservation->reservation_number)

@section('content')
<div class="container py-5 text-center" style="max-width:600px;">
    <div style="width:80px;height:80px;border-radius:50%;background:rgba(40,167,69,0.1);color:#28a745;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 20px;">
        <i class="bi bi-calendar-check"></i>
    </div>
    <h2 class="mb-2">Réservation envoyée !</h2>
    <p class="text-muted mb-4">Référence : <strong>{{ $reservation->reservation_number }}</strong></p>

    <div class="bg-white rounded-4 shadow-sm p-4 text-start">
        <div class="d-flex justify-content-between border-bottom py-2">
            <span class="text-muted">Restaurant</span><strong>{{ $reservation->restaurant->name }}</strong>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
            <span class="text-muted">Date</span><strong>{{ $reservation->date->format('d/m/Y') }} à {{ $reservation->time->format('H:i') }}</strong>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
            <span class="text-muted">Personnes</span><strong>{{ $reservation->guests_count }}</strong>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span class="text-muted">Statut</span>
            <span class="badge {{ $reservation->status === 'confirmed' ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ $reservation->status === 'pending' ? 'En attente de confirmation' : ucfirst($reservation->status) }}
            </span>
        </div>
    </div>

    <a href="{{ route('restaurants.show', $reservation->restaurant) }}" class="btn mt-4" style="background:var(--primary);color:#fff;">
        Retour au restaurant
    </a>
</div>
@endsection
