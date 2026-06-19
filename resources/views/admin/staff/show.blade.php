@extends('layouts.app')

@section('title', $user->full_name)

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Retour</a>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100">
                        <h4>{{ $user->full_name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <p>{{ $user->phone }}</p>
                        @foreach($user->getRoleNames() as $role)
                            <span class="badge bg-info fs-6">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                        @endforeach
                        <hr>
                        <p><strong>Statut :</strong> 
                            @if($user->is_blocked)<span class="badge bg-danger">Bloqué</span>
                            @elseif($user->is_active)<span class="badge bg-success">Actif</span>
                            @else<span class="badge bg-warning">Inactif</span>@endif
                        </p>
                        <p><strong>Ajouté le :</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                        <p><strong>Dernière connexion :</strong> {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Activité récente</h5></div>
                    <div class="card-body">
                        @if($user->orders->count() > 0)
                            @foreach($user->orders as $order)
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span>Commande #{{ $order->order_number }}</span>
                                    <span class="badge bg-{{$order->status_color}}">{{$order->status_label}}</span>
                                    <small>{{ $order->created_at->format('d/m/Y') }}</small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Aucune activité récente</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection