@extends('layouts.app')

@section('title', 'Modifier le produit')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-icon">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-icon">
                        <i class="ri-information-line"></i>
                    </div>
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-3">Modifier : {{ $product->name }}</h4>
                                </div>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-primary"><i class="las la-list mr-3"></i>Retour</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="row g-4">
                            <!-- Informations de base -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Informations de base</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="">Nom du produit *</label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                                    value="{{ old('name', $product->name) }}" required>
                                                @error('name')<div class="help-block with-errors">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="">Catégorie *</label>
                                                <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Prix et stock -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Prix et Stock</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="">Prix (€) *</label>
                                                <input type="number" step="0.01" name="price" class="form-control" 
                                                    value="{{ old('price', $product->price) }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="">Prix barré (€)</label>
                                                <input type="number" step="0.01" name="compare_price" class="form-control" 
                                                    value="{{ old('compare_price', $product->compare_price) }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="">Temps prépa (min)</label>
                                                <input type="number" name="preparation_time" class="form-control" 
                                                    value="{{ old('preparation_time', $product->preparation_time) }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="">Calories</label>
                                                <input type="number" name="calories" class="form-control" 
                                                    value="{{ old('calories', $product->calories) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="">Quantité en stock</label>
                                                <input type="number" name="stock_quantity" class="form-control" 
                                                    value="{{ old('stock_quantity', $product->stock_quantity) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="col-md-4">
                                <!-- Options -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Options</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_vegetarian" value="1" 
                                                {{ old('is_vegetarian', $product->is_vegetarian) ? 'checked' : '' }}>
                                            <label class="form-check-label">🥬 Végétarien</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_vegan" value="1"
                                                {{ old('is_vegan', $product->is_vegan) ? 'checked' : '' }}>
                                            <label class="form-check-label">🌱 Vegan</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_gluten_free" value="1"
                                                {{ old('is_gluten_free', $product->is_gluten_free) ? 'checked' : '' }}>
                                            <label class="form-check-label">🌾 Sans gluten</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_spicy" value="1"
                                                {{ old('is_spicy', $product->is_spicy) ? 'checked' : '' }}>
                                            <label class="form-check-label">🌶️ Épicé</label>
                                        </div>
                                        <hr>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                                {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label">⭐ En vedette</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_available" value="1"
                                                {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                            <label class="form-check-label">✅ Disponible</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="track_inventory" value="1"
                                                {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }}>
                                            <label class="form-check-label">📦 Suivi de stock</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2 mb-3">
                                            @foreach($product->images as $image)
                                                <div class="col-6 position-relative">
                                                    <img src="{{ asset('storage/'.$image->path) }}" class="img-fluid rounded" alt="">
                                                    <form action="{{ route('admin.products.images.destroy', $image) }}" method="POST" 
                                                        class="position-absolute top-0 end-0 m-1">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle" 
                                                                onclick="return confirm('Supprimer cette image ?')">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" class="form-control image-file mb-2" name="images[]" accept="image/*" multiple>
                                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="bi bi-upload"></i> Ajouter des images
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Annuler</a>
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

@endpush