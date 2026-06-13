@extends('layouts.app')

@section('title', 'Nouvelle Catégorie')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="mb-3">Nouvelle Catégorie</h3>
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
                        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Catégorie parente</label>
                                    <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value="">Aucune (Catégorie principale)</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
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
                                            rows="4" placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Ordre d'affichage</label>
                                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                        value="{{ old('sort_order', 0) }}">
                                    <small class="text-muted">Plus le nombre est petit, plus la catégorie apparaît en haut</small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="checkbox d-inline-block mt-5">
                                        <input type="checkbox" name="is_active" value="1" class="checkbox-input" 
                                            id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label for="is_active">Catégorie active</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Image de la catégorie</label>
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                        accept="image/*">
                                    <small class="text-muted">Format acceptés: JPG, PNG, GIF. Max 2MB</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="las la-save mr-2"></i>Créer la catégorie
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
                        <h5 class="mb-0">Informations</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <div class="iq-alert-text">
                                <h5 class="alert-heading">
                                    <i class="ri-information-line mr-2"></i>Conseils :
                                </h5>
                                <ul class="mb-0 mt-2">
                                    <li>Les catégories principales apparaîtront en premier</li>
                                    <li>Les sous-catégories aideront vos clients à mieux naviguer</li>
                                    <li>Une image attrayante augmente le taux de conversion</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Catégories existantes :</h6>
                            <div class="list-group">
                                @foreach($categories->take(5) as $cat)
                                    <div class="list-group-item">
                                        <i class="ri-folder-line mr-2"></i>
                                        {{ $cat->name }}
                                        <span class="badge bg-info float-right">{{ $cat->products_count ?? 0 }} produits</span>
                                    </div>
                                @endforeach
                                @if($categories->count() > 5)
                                    <div class="list-group-item text-muted text-center">
                                        et {{ $categories->count() - 5 }} autres...
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection