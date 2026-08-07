@extends('layouts.storefront')

@section('title', 'Commande #' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Commande #{{ $order->order_number }}</h2>
        <span class="badge fs-6
            @if($order->status === 'cancelled') bg-danger
            @elseif(in_array($order->status, ['delivered','completed'])) bg-success
            @else bg-warning text-dark @endif">
            {{ $order->status_label }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h5 class="mb-3">{{ $order->restaurant->name }}</h5>
                @foreach($order->items as $item)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <div>{{ $item->quantity }} x {{ $item->product_name }}</div>
                            @if($item->special_instructions)
                                <small class="text-muted">{{ $item->special_instructions }}</small>
                            @endif
                        </div>
                        <div>{{ $item->formatted_total_price }}</div>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between pt-3">
                    <span>Sous-total</span><span>{{ $order->formatted_subtotal }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between text-success"><span>Réduction</span><span>-{{ \App\Helpers\CameroonHelper::formatCurrency($order->discount_amount) }}</span></div>
                @endif
                <div class="d-flex justify-content-between"><span>Livraison</span><span>{{ \App\Helpers\CameroonHelper::formatCurrency($order->delivery_fee) }}</span></div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span style="color:var(--primary);">{{ $order->formatted_total }}</span></div>
            </div>

            @if(!in_array($order->status, ['delivered','completed','cancelled']))
                <a href="{{ route('orders.track', $order) }}" class="btn w-100 py-3 mb-4" style="background:var(--primary);color:#fff;border-radius:14px;">
                    <i class="bi bi-geo-alt"></i> Suivre ma livraison
                </a>
            @endif

            @if($order->canBeReviewedBy(auth()->user()))
                <a href="{{ route('orders.review.create', $order) }}" class="btn btn-outline-warning w-100 py-3 mb-4">
                    <i class="bi bi-star"></i> Laisser un avis sur cette commande
                </a>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h6 class="mb-3">Livraison</h6>
                <p class="text-muted mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $order->delivery_address }}, {{ $order->delivery_city }}</p>
                @if($order->delivery_instructions)
                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-2"></i>{{ $order->delivery_instructions }}</p>
                @endif
            </div>

            <div class="bg-white rounded-4 shadow-sm p-4">
                <h6 class="mb-3">Historique</h6>
                <ul class="list-unstyled mb-0">
                    @foreach($order->statusHistory as $history)
                        <li class="mb-2">
                            <strong>{{ $history->status }}</strong>
                            <div class="text-muted small">{{ $history->created_at->format('d/m/Y à H:i') }}</div>
                            @if($history->comment)
                                <div class="small">{{ $history->comment }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
