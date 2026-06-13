@extends('layouts.app')

@section('title', 'Gestion des Produits')

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
                                    <h4 class="mb-3">Produits</h4>
                                </div>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Nouveau produit</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <select name="category_id" class="form-control">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Stock faible</option>
                                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Rupture</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher un produit..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive rounded mb-3">
                                <table class="table mb-0 tbl-server-info">
                                    <thead class="bg-white text-uppercase">
                                        <tr class="ligth ligth-data">
                                            <th>Nom</th>
                                            <th>Catégorie</th>
                                            <th>Prix</th>
                                            <th>Stock</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ligth-body">
                                        @forelse($products as $product)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center flex">
                                                        <img src="{{ $product->primary_image_url ?? url('admin/assets/images/logo.png') }}" class="img-fluid rounded avatar-50 mr-3" style="object-fit: cover;" alt="{{ $product->name }}">
                                                        <div>
                                                            <strong>{{ $product->name }}</strong>
                                                            @if($product->is_on_sale || $product->is_featured)
                                                            <ul class="list-group">
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    @if($product->is_on_sale)
                                                                        <span class="badge bg-danger ms-1">-{{ $product->discount_percentage }}%</span>
                                                                    @endif
                                                                    @if($product->is_featured)
                                                                        <span class="badge bg-warning badge-pill">
                                                                            <i class="ri-star-fill"></i>
                                                                        </span>
                                                                    @endif
                                                                </li>
                                                            </ul>
                                                            @endif
                                                            {{-- <p class="mb-0"><small>d</small></p> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($product->is_on_sale)
                                                        <small class="text-decoration-line-through text-muted">{{ number_format($product->compare_price, 2) }} €</small>
                                                    @endif
                                                    <span class="fw-bold">{{ $product->formatted_price }}</span>
                                                </td>
                                                <td>
                                                    @if($product->track_inventory)
                                                        @if($product->stock_quantity <= 0)
                                                            <span class="badge bg-danger">Rupture</span>
                                                        @elseif($product->isLowStock())
                                                            <span class="badge bg-warning">{{ $product->stock_quantity }}</span>
                                                        @else
                                                            <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->is_available)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        <button type="button" class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Stock" data-bs-target="#stockModal{{ $product->id }}">
                                                            <i class="ri-star-fill mr-2"></i>
                                                        </button>
                                                        <a class="badge bg-success mr-2" href="{{ route('admin.products.edit', $product) }}" aria-label="Modifier">
                                                            <i class="ri-pencil-line mr-0"></i>
                                                        </a>
                                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Supprimer" onclick="return confirm('Supprimer ce produit ?')">
                                                                <i class="ri-delete-bin-line mr-0"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="bi bi-inbox display-4 text-muted"></i>
                                                    <p class="mt-2">Aucun produit trouvé</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

@endpush