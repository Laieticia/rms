@extends('layouts.app')

@section('title', 'Nouveau Coupon')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nouveau Coupon</h3>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Informations du coupon</h5></div>
                    <div class="card-body">
                        <form action="{{ route('admin.coupons.store') }}" method="POST">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Code promo *</label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                        value="{{ old('code') }}" placeholder="ex: ETE2024" required>
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Le code que le client devra entrer</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Type de réduction *</label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" id="couponType" required>
                                        <option value="">Choisir...</option>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                        <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>Montant fixe (FCFA)</option>
                                        <option value="free_delivery" {{ old('type') == 'free_delivery' ? 'selected' : '' }}>Livraison gratuite</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-4" id="valueField">
                                    <label class="form-label">Valeur *</label>
                                    <input type="number" step="0.01" name="value" class="form-control @error('value') is-invalid @enderror" 
                                        value="{{ old('value') }}" id="valueInput">
                                    @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted" id="valueHint">Ex: 10 pour 10% ou 500 pour 500 FCFA</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Montant minimum (FCFA)</label>
                                    <input type="number" step="0.01" name="min_order_amount" class="form-control" 
                                        value="{{ old('min_order_amount', 0) }}">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Réduction max (FCFA)</label>
                                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" 
                                        value="{{ old('max_discount_amount') }}">
                                    <small class="text-muted">Pour plafonner la réduction</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Utilisations max</label>
                                    <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses') }}">
                                    <small class="text-muted">Laisser vide pour illimité</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Utilisations par client</label>
                                    <input type="number" name="max_uses_per_user" class="form-control" 
                                        value="{{ old('max_uses_per_user', 1) }}">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date de début</label>
                                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date d'expiration</label>
                                    <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description') }}" 
                                        placeholder="Description visible par le client">
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                        <label class="form-check-label">Coupon actif</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="applies_to_all" value="1" checked>
                                        <label class="form-check-label">Appliquer à tous les produits</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-lg"></i> Créer le coupon
                                </button>
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('couponType').addEventListener('change', function() {
    const valueField = document.getElementById('valueField');
    const valueInput = document.getElementById('valueInput');
    const valueHint = document.getElementById('valueHint');
    
    if (this.value === 'free_delivery') {
        valueField.style.display = 'none';
        valueInput.removeAttribute('required');
    } else {
        valueField.style.display = 'block';
        valueInput.setAttribute('required', 'required');
        if (this.value === 'percentage') {
            valueHint.textContent = 'Ex: 10 pour 10% de réduction';
            valueInput.placeholder = '10';
        } else {
            valueHint.textContent = 'Ex: 500 pour 500 FCFA de réduction';
            valueInput.placeholder = '5';
        }
    }
});
</script>
@endpush