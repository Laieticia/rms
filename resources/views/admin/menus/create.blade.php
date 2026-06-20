@extends('layouts.app')

@section('title', 'Nouveau Menu')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nouveau Menu</h3>
            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Informations du menu</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.menus.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom du menu *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Choisir...</option>
                                <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>Régulier</option>
                                <option value="lunch" {{ old('type') == 'lunch' ? 'selected' : '' }}>Déjeuner</option>
                                <option value="dinner" {{ old('type') == 'dinner' ? 'selected' : '' }}>Dîner</option>
                                <option value="weekend" {{ old('type') == 'weekend' ? 'selected' : '' }}>Weekend</option>
                                <option value="special" {{ old('type') == 'special' ? 'selected' : '' }}>Spécial</option>
                                <option value="seasonal" {{ old('type') == 'seasonal' ? 'selected' : '' }}>Saisonnier</option>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Disponible de</label>
                            <input type="time" name="available_from" class="form-control" value="{{ old('available_from') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Disponible jusqu'à</label>
                            <input type="time" name="available_until" class="form-control" value="{{ old('available_until') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Menu actif</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h5>Sélectionner les produits</h5>
                    <div class="row g-3">
                        @foreach($products as $index => $product)
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="products[{{ $index }}][id]" 
                                            value="{{ $product->id }}" 
                                            id="product{{ $product->id }}">
                                        <label class="form-check-label" for="product{{ $product->id }}">
                                            <strong>{{ $product->name }}</strong>
                                            <br><small class="text-muted">{{ $product->formatted_price }}</small>
                                        </label>
                                    </div>
                                    <div class="mt-2">
                                        <label class="form-label small">Prix spécial (optionnel)</label>
                                        <input type="number" step="0.01" 
                                            name="products[{{ $index }}][special_price]" 
                                            class="form-control form-control-sm"
                                            placeholder="Laisser vide pour prix normal">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg mt-4">
                        <i class="bi bi-check-lg"></i> Créer le menu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection