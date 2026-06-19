@extends('layouts.app')

@section('title', 'Gestion des Coupons')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-ticket-perforated me-2"></i>Coupons de réduction</h3>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nouveau coupon
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Valeur</th>
                                <th>Min. commande</th>
                                <th>Utilisations</th>
                                <th>Validité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td><span class="badge bg-dark fs-6">{{ $coupon->code }}</span></td>
                                    <td>{{ Str::limit($coupon->description, 40) }}</td>
                                    <td>
                                        @if($coupon->type == 'percentage')
                                            <span class="badge bg-info">Pourcentage</span>
                                        @elseif($coupon->type == 'fixed_amount')
                                            <span class="badge bg-primary">Montant fixe</span>
                                        @else
                                            <span class="badge bg-success">Livraison offerte</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $coupon->formatted_value }}</strong></td>
                                    <td>{{ number_format($coupon->min_order_amount, 2) }} €</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $coupon->used_count }}/{{ $coupon->max_uses ?? '∞' }}</span>
                                            <div class="progress flex-grow-1" style="height:6px;">
                                                <div class="progress-bar bg-{{ $coupon->usage_percentage > 80 ? 'danger' : 'success' }}" 
                                                    style="width: {{ $coupon->usage_percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($coupon->expires_at)
                                            @if($coupon->is_expired)
                                                <span class="badge bg-danger">Expiré</span>
                                            @else
                                                <small>{{ $coupon->expires_at->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <small class="text-muted">Illimitée</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->is_valid)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-{{ $coupon->is_expired ? 'danger' : 'secondary' }}">
                                                {{ $coupon->is_expired ? 'Expiré' : 'Inactif' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" 
                                                onsubmit="return confirm('Supprimer ce coupon ?')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line mr-0"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center py-4">Aucun coupon créé</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection