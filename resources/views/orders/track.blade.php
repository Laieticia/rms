@extends('layouts.storefront')

@section('title', 'Suivi de la commande #' . $order->order_number)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0">Suivi de la commande #{{ $order->order_number }}</h2>
        <div class="d-flex gap-2">
            @if($order->canBeReviewedBy(auth()->user()))
                <a href="{{ route('orders.review.create', $order) }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-star"></i> Laisser un avis
                </a>
            @endif
            <a href="{{ route('profile.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">Détails de la commande</a>
        </div>
    </div>

    {{-- Étapes --}}
    @php
        $steps = [
            'pending' => 'Reçue',
            'confirmed' => 'Confirmée',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'in_delivery' => 'En livraison',
            'delivered' => 'Livrée',
        ];
        $stepKeys = array_keys($steps);
        $currentIndex = array_search($order->status, $stepKeys);
    @endphp
    @if($order->status === 'cancelled')
        <div class="alert alert-danger">Cette commande a été annulée.</div>
    @else
        <div class="d-flex justify-content-between mb-5 flex-wrap gap-2">
            @foreach($steps as $key => $label)
                <div class="text-center flex-fill">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                         style="width:36px;height:36px;background:{{ $currentIndex !== false && array_search($key, $stepKeys) <= $currentIndex ? 'var(--primary)' : '#e0e0e0' }};color:#fff;">
                        <i class="bi bi-check"></i>
                    </div>
                    <small>{{ $label }}</small>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div id="trackingMap" class="rounded-4 shadow-sm" style="height:420px;background:#eee;"></div>
            <p class="text-muted small mt-2" id="trackingStatus">
                @if($order->deliveryPerson)
                    Livreur : {{ $order->deliveryPerson->first_name }} — en route.
                @else
                    En attente d'assignation d'un livreur.
                @endif
            </p>
        </div>

        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h6 class="mb-3">{{ $order->restaurant->name }}</h6>
                @foreach($order->items as $item)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $item->quantity }} x {{ $item->product_name }}</span>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span><span>{{ $order->formatted_total }}</span>
                </div>
                <hr>
                <p class="small text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $order->delivery_address }}, {{ $order->delivery_city }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @php
        $lastPoint = $order->deliveryTracking->first();
        $restaurantLat = $order->restaurant->latitude ?? 5.4667;
        $restaurantLng = $order->restaurant->longitude ?? 10.4167;
    @endphp

    const startLat = {{ $lastPoint->latitude ?? $restaurantLat }};
    const startLng = {{ $lastPoint->longitude ?? $restaurantLng }};

    const map = L.map('trackingMap').setView([startLat, startLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const deliveryIcon = L.divIcon({
        html: '<i class="bi bi-motorcycle" style="font-size:22px;color:var(--primary,#e8281a);"></i>',
        className: '', iconSize: [24, 24],
    });

    let marker = L.marker([startLat, startLng], { icon: deliveryIcon }).addTo(map);

    L.marker([{{ $order->delivery_latitude ?? $restaurantLat }}, {{ $order->delivery_longitude ?? $restaurantLng }}])
        .addTo(map)
        .bindPopup('Adresse de livraison');

    // Abonnement temps réel (Reverb / protocole Pusher)
    @if(auth()->check())
    try {
        const pusher = new Pusher('{{ config('broadcasting.connections.reverb.key') }}', {
            wsHost: '{{ config('broadcasting.connections.reverb.options.host') }}',
            wsPort: {{ config('broadcasting.connections.reverb.options.port', 8080) }},
            forceTLS: {{ config('broadcasting.connections.reverb.options.useTLS') ? 'true' : 'false' }},
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } },
        });

        const channel = pusher.subscribe('private-orders.{{ $order->id }}');
        channel.bind('delivery.location.updated', function (data) {
            const newLatLng = [data.latitude, data.longitude];
            marker.setLatLng(newLatLng);
            map.panTo(newLatLng);
            document.getElementById('trackingStatus').innerText = 'Livreur en mouvement — mise à jour en direct.';
        });
    } catch (e) {
        console.warn('Suivi temps réel indisponible :', e);
    }
    @endif
});
</script>
@endpush
@endsection
