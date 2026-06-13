@extends('layouts.app')

@section('title', 'Gestion des Catégories')

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
                                        <h4 class="mb-3">Catégories</h4>
                                    </div>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary add-list">
                                        <i class="las la-plus mr-3"></i>Nouvelle catégorie
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher une catégorie..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive rounded mb-3">
                                    <table class="table mb-0 tbl-server-info">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>Image</th>
                                                <th>Nom</th>
                                                <th>Catégorie parente</th>
                                                <th>Description</th>
                                                <th>Produits</th>
                                                <th>Ordre</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @forelse($categories as $category)
                                            <tr>
                                                <td>
                                                    <img src="{{ $category->image ? Storage::url($category->image) : url('admin/assets/images/logo.png') }}" 
                                                         class="img-fluid rounded avatar-50" 
                                                         style="object-fit: cover; width: 50px; height: 50px;" 
                                                         alt="{{ $category->name }}">
                                                </td>
                                                <td>
                                                    <strong>{{ $category->name }}</strong>
                                                </td>
                                                <td>
                                                    {{ $category->parent->name ?? 'Principale' }}
                                                </td>
                                                <td>
                                                    {{ Str::limit($category->description, 50) ?? '-' }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $category->products_count }}</span>
                                                </td>
                                                <td>
                                                    {{ $category->sort_order ?? '-' }}
                                                </td>
                                                <td>
                                                    @if($category->is_active)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        <a class="badge bg-success mr-2" href="{{ route('admin.categories.edit', $category) }}" 
                                                           data-toggle="tooltip" data-placement="top" title="Modifier">
                                                            <i class="ri-pencil-line mr-0"></i>
                                                        </a>
                                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                                            @csrf 
                                                            @method('DELETE')
                                                            <button type="submit" class="badge bg-warning mr-2" 
                                                                    data-toggle="tooltip" data-placement="top" title="Supprimer" 
                                                                    onclick="return confirm('Supprimer cette catégorie ? Les produits associés ne seront pas supprimés mais n\'auront plus de catégorie.')">
                                                                <i class="ri-delete-bin-line mr-0"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="bi bi-inbox display-4 text-muted"></i>
                                                    <p class="mt-2">Aucune catégorie trouvée</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
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
<script>
    // Tooltip activation
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush