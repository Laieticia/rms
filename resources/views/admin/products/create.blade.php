@extends('layouts.app')

@section('title', 'Ajouter un Produit')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Ajouter un Produit</h3>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-8">
                    <!-- Infos de base -->
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">📋 Informations générales</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nom du produit *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Catégorie *</label>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">Choisir...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Décrivez le produit...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">💰 Prix et Stock</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Prix (FCFA) *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Prix barré (FCFA)</label>
                                    <input type="number" step="0.01" name="compare_price" class="form-control" value="{{ old('compare_price') }}" placeholder="Pour promo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Prix de revient (FCFA)</label>
                                    <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Temps de préparation (min) *</label>
                                    <input type="number" name="preparation_time" class="form-control" value="{{ old('preparation_time', 15) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Calories</label>
                                    <input type="number" name="calories" class="form-control" value="{{ old('calories') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quantité en stock</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Options -->
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="mb-0">⚙️ Options</h5></div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian') ? 'checked' : '' }}>
                                <label class="form-check-label">🥬 Végétarien</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_vegan" value="1" {{ old('is_vegan') ? 'checked' : '' }}>
                                <label class="form-check-label">🌱 Vegan</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_gluten_free" value="1" {{ old('is_gluten_free') ? 'checked' : '' }}>
                                <label class="form-check-label">🌾 Sans gluten</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_spicy" value="1" {{ old('is_spicy') ? 'checked' : '' }}>
                                <label class="form-check-label">🌶️ Épicé</label>
                            </div>
                            <hr>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label">⭐ En vedette</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_available" value="1" checked>
                                <label class="form-check-label">✅ Disponible</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="track_inventory" value="1" {{ old('track_inventory') ? 'checked' : '' }}>
                                <label class="form-check-label">📦 Suivi de stock</label>
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">🖼️ Images</h5></div>
                        <div class="card-body">
                            <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" multiple accept="image/*">
                            @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">JPG, PNG, WebP - Max 2 Mo par image</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Créer le produit</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection