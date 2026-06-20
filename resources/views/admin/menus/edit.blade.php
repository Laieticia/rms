@extends('layouts.app')

@section('title', 'Modifier le Menu')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $menu->name }}</h3>
            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Informations</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-control" required>
                                @foreach(['regular'=>'Régulier','lunch'=>'Déjeuner','dinner'=>'Dîner','weekend'=>'Weekend','special'=>'Spécial','seasonal'=>'Saisonnier'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $menu->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $menu->description) }}</textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date début</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $menu->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $menu->end_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Disponible de</label>
                            <input type="time" name="available_from" class="form-control" value="{{ old('available_from', $menu->available_from) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jusqu'à</label>
                            <input type="time" name="available_until" class="form-control" value="{{ old('available_until', $menu->available_until) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }}>
                                <label class="form-check-label">Menu actif</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h5>Produits du menu</h5>
                    <div class="row g-3">
                        @foreach($products as $index => $product)
                            @php $menuItem = $menu->items->where('product_id', $product->id)->first(); @endphp
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="products[{{ $index }}][id]" 
                                            value="{{ $product->id }}" 
                                            id="p{{ $product->id }}"
                                            {{ $menuItem ? 'checked' : '' }}>
                                        <label for="p{{ $product->id }}"><strong>{{ $product->name }}</strong></label>
                                    </div>
                                    <div class="mt-2">
                                        <input type="number" step="0.01" 
                                            name="products[{{ $index }}][special_price]" 
                                            class="form-control form-control-sm"
                                            value="{{ $menuItem?->special_price }}"
                                            placeholder="Prix spécial (optionnel)">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg mt-4">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection