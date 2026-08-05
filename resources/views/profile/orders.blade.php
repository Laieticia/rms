@extends('layouts.storefront')

@section('title', 'Mes commandes')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-receipt me-2"></i>Mes commandes</h2>

    @forelse($orders as $order)
        <div class="bg-white rounded-4 shadow-sm p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="mb-1">#{{ $order->order_number }} — {{ $order->restaurant->name }}</h6>
                    <div class="text-muted small">{{ $order->created_at->format('d/m/Y à H:i') }} · {{ $order->items->count() }} article(s)</div>
                </div>
                <div class="text-center">
                    <span class="badge
                        @if($order->status === 'cancelled') bg-danger
                        @elseif(in_array($order->status, ['delivered','completed'])) bg-success
                        @else bg-warning text-dark @endif">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="fw-bold" style="color:var(--primary);">{{ \App\Helpers\CameroonHelper::formatCurrency($order->total) }}</div>
                <div class="d-flex gap-2">
                    <a href="{{ route('profile.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Détails</a>
                    @if(!in_array($order->status, ['delivered','completed','cancelled']))
                        <a href="{{ route('orders.track', $order) }}" class="btn btn-sm" style="background:var(--primary);color:#fff;">Suivre</a>
                    @endif
                    @if(($order->canBeReviewedBy(auth()->user())) ?? false)
                        <a href="{{ route('orders.review.create', $order) }}" class="btn btn-sm btn-outline-warning">Noter</a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt" style="font-size:3rem;"></i>
            <p class="mt-3">Vous n'avez pas encore passé de commande. <a href="{{ route('restaurants.index') }}">Découvrez nos restaurants</a>.</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
