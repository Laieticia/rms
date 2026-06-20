@extends('layouts.app')

@section('title', 'Modifier le Coupon')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $coupon->code }}</h3>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Informations</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                            @csrf @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Code promo *</label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code) }}" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Type *</label>
                                    <select name="type" class="form-control" id="couponType" required>
                                        <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                                        <option value="fixed_amount" {{ old('type', $coupon->type) == 'fixed_amount' ? 'selected' : '' }}>Montant fixe</option>
                                        <option value="free_delivery" {{ old('type', $coupon->type) == 'free_delivery' ? 'selected' : '' }}>Livraison gratuite</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4" id="valueField" style="{{ $coupon->type == 'free_delivery' ? 'display:none;' : '' }}">
                                    <label class="form-label">Valeur</label>
                                    <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $coupon->value) }}">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Montant minimum (€)</label>
                                    <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Réduction max (€)</label>
                                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Utilisations max</label>
                                    <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses', $coupon->max_uses) }}">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Utilisations par client</label>
                                    <input type="number" name="max_uses_per_user" class="form-control" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Date de début</label>
                                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ $coupon->starts_at?->format('Y-m-d\TH:i') }}">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Date d'expiration</label>
                                    <input type="datetime-local" name="expires_at" class="form-control" value="{{ $coupon->expires_at?->format('Y-m-d\TH:i') }}">
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description', $coupon->description) }}">
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">Actif</label>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg mt-4">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Statistiques</h5></div>
                    <div class="card-body">
                        <p><strong>Utilisé :</strong> {{ $coupon->used_count }} fois</p>
                        <p><strong>Restant :</strong> {{ $coupon->max_uses ? $coupon->max_uses - $coupon->used_count : 'Illimité' }}</p>
                        <div class="progress mb-3" style="height:10px;">
                            <div class="progress-bar bg-success" style="width:{{ $coupon->usage_percentage }}%"></div>
                        </div>
                        <p><strong>Créé le :</strong> {{ $coupon->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection