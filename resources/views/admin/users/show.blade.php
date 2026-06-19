@extends('layouts.app')

@section('title', 'Client : ' . $user->full_name)

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Retour à la liste</a>

        <div class="row g-4">
            <!-- Profil -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100">
                        <h4>{{ $user->full_name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <p>{{ $user->phone ?? 'Non renseigné' }}</p>
                        
                        <div class="mb-2">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge bg-info">{{ ucfirst($role) }}</span>
                            @endforeach
                        </div>
                        
                        <hr>
                        
                        <div class="row text-start">
                            <div class="col-6 mb-2">
                                <small class="text-muted">Statut</small>
                                @if($user->is_blocked)
                                    <p class="text-danger fw-bold">Bloqué</p>
                                    @if($user->blocked_reason)<small class="text-muted">{{ $user->blocked_reason }}</small>@endif
                                @elseif($user->is_active)
                                    <p class="text-success fw-bold">Actif</p>
                                @else
                                    <p class="text-warning fw-bold">Inactif</p>
                                @endif
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted">Inscrit le</small>
                                <p>{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted">Dernière connexion</small>
                                <p>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</p>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted">Fidélité</small>
                                <p><span class="badge bg-warning">{{ $loyaltyBalance }} pts</span> ({{ $user->loyalty_level }})</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card mt-3">
                    <div class="card-header">Actions</div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary w-100 mb-2"><i class="bi bi-pencil"></i> Modifier</a>
                        @if($user->is_blocked)
                            <form action="{{ route('admin.users.unblock', $user) }}" method="POST">
                                @csrf
                                <button class="btn btn-success w-100"><i class="bi bi-unlock"></i> Débloquer</button>
                            </form>
                        @else
                            <button class="btn btn-warning w-100" data-toggle="modal" data-target="#blockModal"><i class="bi bi-lock"></i> Bloquer</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Détails -->
            <div class="col-md-8">
                <!-- Statistiques -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center py-3"><h4>{{$orderStats['total_orders']}}</h4><small>Commandes</small></div></div></div>
                    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center py-3"><h4>{{number_format($orderStats['total_spent'],0)}}€</h4><small>Dépensé</small></div></div></div>
                    <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center py-3"><h4>{{number_format($orderStats['average_order'],2)}}€</h4><small>Panier moyen</small></div></div></div>
                    <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center py-3"><h4>{{$loyaltyBalance}}</h4><small>Points fidélité</small></div></div></div>
                </div>

                <!-- Dernières commandes -->
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">📋 Dernières commandes</h5></div>
                    <div class="card-body">
                        @forelse($user->orders as $order)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <strong>#{{ $order->order_number }}</strong>
                                    <br><small>{{ $order->restaurant->name ?? 'N/A' }}</small>
                                </div>
                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                <strong>{{ number_format($order->total, 2) }}€</strong>
                                <small>{{ $order->created_at->format('d/m/Y') }}</small>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line mr-0"></i></a>
                            </div>
                        @empty
                            <p class="text-muted">Aucune commande</p>
                        @endforelse
                    </div>
                </div>

                <!-- Adresses -->
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">📍 Adresses</h5></div>
                    <div class="card-body">
                        @forelse($user->addresses as $address)
                            <div class="border rounded p-2 mb-2">
                                <strong>{{ $address->label }}</strong>
                                @if($address->is_default)<span class="badge bg-primary ms-1">Défaut</span>@endif
                                <p class="mb-0">{{ $address->street_address }}, {{ $address->postal_code }} {{ $address->city }}</p>
                            </div>
                        @empty
                            <p class="text-muted">Aucune adresse</p>
                        @endforelse
                    </div>
                </div>

                <!-- Avis -->
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">⭐ Avis</h5></div>
                    <div class="card-body">
                        @forelse($user->reviews as $review)
                            <div class="border-bottom pb-2 mb-2">
                                <span class="text-warning">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{$i<=$review->rating?'-fill':''}} small"></i>@endfor</span>
                                <p class="mb-1">{{ $review->comment }}</p>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y') }} - {{ $review->restaurant->name ?? '' }}</small>
                            </div>
                        @empty
                            <p class="text-muted">Aucun avis</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bloquer -->
<div class="modal fade" id="blockModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.users.block', $user) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-warning" id="exampleModalLabel">
                    <h5>Bloquer {{$user->full_name}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Raison *</label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Durée (jours)</label><input type="number" name="days" class="form-control" min="1" max="365" placeholder="Permanent si vide"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-danger">Confirmer le blocage</button></div>
            </div>
        </form>
    </div>
</div>

@endsection