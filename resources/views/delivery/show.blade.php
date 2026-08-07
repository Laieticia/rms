@extends('layouts.app')

@section('title', 'Détails de la Course')

@section('content')
<div class="content-page">
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('delivery.dashboard') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Retour aux courses
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Informations Client & Adresse -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4 border-top border-4 border-{{ $order->status === 'in_delivery' ? 'primary' : 'warning' }}">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Commande #{{ $order->order_number }}</h5>
                    <span class="badge bg-{{ $order->status_color }} fs-6">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Retrait</h6>
                            <p class="mb-1 fw-bold"><i class="bi bi-shop me-2"></i>{{ $order->restaurant->name }}</p>
                            <p class="mb-0 text-muted ms-4">{{ $order->restaurant->address }}</p>
                            <p class="mb-0 text-muted ms-4">{{ $order->restaurant->city }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Livraison</h6>
                            <p class="mb-1 fw-bold"><i class="bi bi-person me-2"></i>{{ $order->user->full_name }}</p>
                            <p class="mb-0 text-muted ms-4">{{ $order->delivery_address }}</p>
                            @if($order->delivery_city)
                                <p class="mb-1 text-muted ms-4">{{ $order->delivery_city }} {{ $order->delivery_postal_code }}</p>
                            @endif
                            <p class="mb-0 mt-2 ms-4">
                                <a href="tel:{{ $order->user->phone }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-telephone me-1"></i>Appeler le client
                                </a>
                            </p>
                        </div>
                    </div>

                    @if($order->delivery_instructions)
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i><strong>Instructions :</strong><br>
                            {{ $order->delivery_instructions }}
                        </div>
                    @endif
                    @if($order->payment_status !== 'paid')
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-cash me-2"></i><strong>Paiement :</strong> Le client doit payer <strong>{{ $order->formatted_total }}</strong> en espèces à la livraison.
                        </div>
                    @else
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle-fill me-2"></i><strong>Paiement :</strong> Déjà payé. Ne rien encaisser.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contenu de la commande -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Détails des articles ({{ $order->items->sum('quantity') }})</h6>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <span class="fw-bold">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                @if($item->special_instructions)
                                    <div class="text-muted small mt-1"><i class="bi bi-chat-text me-1"></i>{{ $item->special_instructions }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Actions</h6>
                    
                    @if($order->delivery_latitude && $order->delivery_longitude)
                        <a href="https://maps.google.com/?q={{ $order->delivery_latitude }},{{ $order->delivery_longitude }}" target="_blank" class="btn btn-outline-secondary w-100 mb-3">
                            <i class="bi bi-map me-1"></i>Ouvrir dans Maps
                        </a>
                    @endif

                    <form action="{{ route('delivery.status', $order) }}" method="POST">
                        @csrf
                        @if($order->status === 'ready' || $order->status === 'preparing')
                            <input type="hidden" name="status" value="in_delivery">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5 shadow-sm">
                                <i class="bi bi-play-fill me-2"></i>Démarrer la course
                            </button>
                            <p class="text-muted small text-center mt-2">Cliquez lorsque vous avez récupéré la commande et êtes en route vers le client.</p>
                        @elseif($order->status === 'in_delivery')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm" onclick="return confirm('Confirmez-vous que la commande a été remise au client ?')">
                                <i class="bi bi-check2-all me-2"></i>Marquer comme Livrée
                            </button>
                            <p class="text-muted small text-center mt-2">Le suivi de votre position GPS est actuellement <strong>actif</strong>.</p>
                            
                            <div id="tracking-status" class="alert alert-info p-2 text-center small mt-3 mb-0" style="display:none;">
                                <i class="bi bi-geo-alt-fill me-1"></i><span id="tracking-text">Envoi de la position...</span>
                            </div>
                        @else
                            <div class="alert alert-secondary text-center mb-0">
                                Cette course est {{ $order->status_label }}.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
@if($order->status === 'in_delivery')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let trackingStatus = document.getElementById('tracking-status');
    let trackingText = document.getElementById('tracking-text');
    let watchId = null;

    if ("geolocation" in navigator) {
        trackingStatus.style.display = 'block';
        trackingText.innerText = 'Recherche du signal GPS...';

        // Options pour le watchPosition
        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        // Variable pour limiter les envois (ex: toutes les 15 secondes max)
        let lastSendTime = 0;

        watchId = navigator.geolocation.watchPosition(
            function(position) {
                const now = Date.now();
                // N'envoyer au serveur que si 10 secondes se sont écoulées
                if (now - lastSendTime > 10000) {
                    lastSendTime = now;
                    sendLocationToServer(position.coords.latitude, position.coords.longitude, position.coords.speed);
                }
            },
            function(error) {
                console.error("Erreur GPS:", error);
                trackingStatus.classList.replace('alert-info', 'alert-danger');
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        trackingText.innerText = "Autorisation GPS refusée.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        trackingText.innerText = "Position GPS indisponible.";
                        break;
                    case error.TIMEOUT:
                        trackingText.innerText = "Délai d'attente GPS dépassé.";
                        break;
                    default:
                        trackingText.innerText = "Erreur de suivi GPS.";
                        break;
                }
            },
            options
        );
    } else {
        trackingStatus.style.display = 'block';
        trackingStatus.classList.replace('alert-info', 'alert-warning');
        trackingText.innerText = "La géolocalisation n'est pas supportée par ce navigateur.";
    }

    function sendLocationToServer(lat, lng, speed) {
        trackingText.innerText = "Mise à jour de la position...";
        
        fetch('{{ route('delivery.location', $order) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                speed: speed
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                trackingText.innerText = "Position mise à jour en direct.";
                setTimeout(() => {
                    trackingText.innerText = "En attente du prochain signal...";
                }, 3000);
            }
        })
        .catch(err => {
            console.error("Erreur d'envoi", err);
            trackingText.innerText = "Erreur de connexion au serveur.";
        });
    }
});
</script>
@endif
@endsection
