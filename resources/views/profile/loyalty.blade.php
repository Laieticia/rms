@extends('layouts.storefront')

@section('title', 'Mes points de fidélité')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-award me-2"></i>Mes points de fidélité</h2>

    <div class="bg-white rounded-4 shadow-sm p-4 mb-4 text-center" style="background:linear-gradient(135deg, var(--primary), #ff8a65) !important;color:#fff;">
        <div style="font-size:3rem;font-weight:700;">{{ $points }}</div>
        <p class="mb-0">points disponibles</p>
        <div class="mt-2 small">1 point gagné tous les 100 FCFA dépensés · +5 points bonus par livraison</div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h5 class="mb-3">Historique des points</h5>
                @forelse($history as $entry)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <div>{{ $entry->description ?? 'Points gagnés' }}</div>
                            <small class="text-muted">{{ $entry->created_at->format('d/m/Y') }}</small>
                        </div>
                        <span class="fw-bold {{ $entry->points >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $entry->points >= 0 ? '+' : '' }}{{ $entry->points }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aucun historique pour le moment.</p>
                @endforelse
                {{ $history->links() }}
            </div>
        </div>

        <div class="col-lg-5">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h5 class="mb-3">Récompenses échangées</h5>
                @forelse($redemptions as $redemption)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $redemption->reward->name ?? 'Récompense' }}</span>
                        <small class="text-muted">{{ $redemption->created_at->format('d/m/Y') }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Vous n'avez pas encore échangé de récompense.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
