@extends('layouts.app')

@section('title', 'Détail Utilisateur')

@section('content')
<div class="container-fluid">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Retour</a>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100">
                    <h4>{{ $user->full_name }}</h4>
                    <p>{{ $user->email }}</p>
                    <p>{{ $user->phone }}</p>
                    @foreach($user->getRoleNames() as $role)<span class="badge bg-info">{{$role}}</span>@endforeach
                    <hr>
                    <p><strong>Inscrit:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    <p><strong>Dernière connexion:</strong> {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</p>
                    <p><strong>Fidélité:</strong> {{ $loyaltyPoints }} pts ({{ $user->loyalty_level }})</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5>Commandes récentes</h5></div>
                <div class="card-body">
                    @forelse($user->orders as $order)
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <div><strong>#{{$order->order_number}}</strong><br><small>{{$order->restaurant->name}}</small></div>
                            <div><span class="badge bg-{{$order->status_color}}">{{$order->status_label}}</span></div>
                            <div><strong>{{number_format($order->total,2)}}€</strong><br><small>{{$order->created_at->format('d/m/Y')}}</small></div>
                        </div>
                    @empty
                        <p class="text-muted">Aucune commande</p>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5>Adresses</h5></div>
                <div class="card-body">
                    @forelse($user->addresses as $address)
                        <div class="border rounded p-2 mb-2">
                            <strong>{{$address->label}}</strong>@if($address->is_default)<span class="badge bg-primary">Défaut</span>@endif
                            <p class="mb-0">{{$address->street_address}}, {{$address->postal_code}} {{$address->city}}</p>
                        </div>
                    @empty
                        <p class="text-muted">Aucune adresse</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection