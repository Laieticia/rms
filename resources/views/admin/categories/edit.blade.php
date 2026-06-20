@extends('layouts.app')

@section('title', 'Modifier Catégorie')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="mb-3">Modifier: {{ $category->name }}</h3>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary add-list">
                        <i class="las la-arrow-left mr-3"></i>Retour à la liste
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Informations générales</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                            @csrf 
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                        value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Catégorie parente</label>
                                    <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value="">Aucune (Catégorie principale)</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" 
                                                {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}
                                                {{ $cat->id == $category->id ? 'disabled' : '' }}>
                                                {{ $cat->name }}
                                                @if($cat->id == $category->id) (Actuelle) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                            rows="4" placeholder="Description de la catégorie...">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Ordre d'affichage</label>
                                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                        value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                                    <small class="text-muted">Plus le nombre est petit, plus la catégorie apparaît en haut</small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                                            id="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Catégorie active</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Image de la catégorie</label>
                                    @if($category->image)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($category->image) }}" 
                                                alt="{{ $category->name }}" 
                                                style="max-width: 200px; max-height: 150px;" 
                                                class="img-thumbnail">
                                            <div class="mt-1">
                                                <small class="text-muted">Image actuelle</small>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                        accept="image/*">
                                    <small class="text-muted">Laissez vide pour conserver l'image actuelle. Format acceptés: JPG, PNG, GIF. Max 2MB</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="las la-save mr-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted">Nombre de produits</label>
                            <h3 class="mb-0">{{ $category->products_count ?? 0 }}</h3>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Date de création</label>
                            <p class="mb-0">{{ $category->created_at ? $category->created_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Dernière modification</label>
                            <p class="mb-0">{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        
                        @if($category->products_count > 0)
                            <div class="alert alert-warning mt-3">
                                <i class="ri-alert-line mr-2"></i>
                                <strong>Attention :</strong> Cette catégorie contient {{ $category->products_count }} produit(s). La suppression de la catégorie ne supprimera pas les produits, mais ils n'auront plus de catégorie associée.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection