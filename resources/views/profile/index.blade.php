@extends('layouts.storefront')

@section('title', 'Mon profil')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="bg-white rounded-4 shadow-sm p-4 text-center">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 12px;">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                </div>
                <h5 class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</h5>
                <p class="text-muted small">{{ $user->email }}</p>
                <div class="badge mb-3" style="background:rgba(232,40,26,0.1);color:var(--primary);">
                    <i class="bi bi-award"></i> Niveau {{ ucfirst($user->loyalty_level ?? 'bronze') }} — {{ $loyaltyPoints }} pts
                </div>
                <div class="list-group list-group-flush text-start">
                    <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action active"><i class="bi bi-person me-2"></i>Aperçu</a>
                    <a href="{{ route('profile.orders') }}" class="list-group-item list-group-item-action"><i class="bi bi-receipt me-2"></i>Mes commandes</a>
                    <a href="{{ route('reservations.index') }}" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i>Mes réservations</a>
                    <a href="{{ route('profile.favorites') }}" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i>Mes favoris</a>
                    <a href="{{ route('profile.loyalty') }}" class="list-group-item list-group-item-action"><i class="bi bi-award me-2"></i>Fidélité</a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action"><i class="bi bi-gear me-2"></i>Paramètres</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            {{-- Adresses --}}
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Mes adresses</h5>
                    <button type="button" class="btn btn-sm" style="background:var(--primary);color:#fff;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="bi bi-plus"></i> Ajouter
                    </button>
                </div>
                @forelse($addresses as $address)
                    <div class="d-flex justify-content-between align-items-start border rounded-3 p-3 mb-2">
                        <div>
                            <strong>{{ $address->label }}</strong>
                            @if($address->is_default)<span class="badge bg-secondary ms-2">Par défaut</span>@endif
                            <div class="text-muted small">{{ $address->street_address }}, {{ $address->city }}</div>
                        </div>
                        <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Supprimer cette adresse ?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aucune adresse enregistrée pour le moment.</p>
                @endforelse
            </div>

            {{-- Dernières commandes --}}
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Dernières commandes</h5>
                    <a href="{{ route('profile.orders') }}" class="small">Voir tout</a>
                </div>
                @forelse($orders as $order)
                    <a href="{{ route('profile.orders.show', $order) }}" class="text-decoration-none">
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                            <div>
                                <strong style="color:#1a1a1a;">#{{ $order->order_number }}</strong>
                                <div class="text-muted small">{{ $order->restaurant->name }} · {{ $order->created_at->format('d/m/Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ \App\Helpers\CameroonHelper::formatCurrency($order->total) }}</div>
                                <span class="badge bg-light text-dark">{{ $order->status_label }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-muted mb-0">Vous n'avez pas encore passé de commande.</p>
                @endforelse
            </div>

            {{-- Favoris --}}
            <div class="bg-white rounded-4 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-heart me-2"></i>Mes favoris</h5>
                    <a href="{{ route('profile.favorites') }}" class="small">Voir tout</a>
                </div>
                <div class="row g-3">
                    @forelse($favorites->take(4) as $favorite)
                        <div class="col-6 col-md-3">
                            <a href="{{ route('products.show', $favorite->product) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
                                    <div style="height:90px;overflow:hidden;">
                                        <img src="{{ $favorite->product->primary_image_url ?? url('website/assets/img/category/2.jpg') }}" class="w-100 h-100" style="object-fit:cover;">
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="small text-truncate">{{ $favorite->product->name }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucun favori pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajouter Adresse --}}
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.addresses.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle adresse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Libellé</label>
                        <input type="text" name="label" class="form-control" placeholder="Domicile, Bureau..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="street_address" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Ville</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Code postal</label>
                            <input type="text" name="postal_code" class="form-control" value="000" required>
                        </div>
                    </div>
                    <input type="hidden" name="country" value="CM">
                    <div class="mb-2">
                        <label class="form-label">Instructions (optionnel)</label>
                        <textarea name="instructions" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">
                        <label class="form-check-label" for="is_default">Définir comme adresse par défaut</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn" style="background:var(--primary);color:#fff;">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
