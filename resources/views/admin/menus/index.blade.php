@extends('layouts.app')

@section('title', 'Gestion des Menus')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-menu-button-wide me-2"></i>Menus</h3>
            <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nouveau menu
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Produits</th>
                                <th>Période</th>
                                <th>Disponibilité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                                <tr>
                                    <td><strong>{{ $menu->name }}</strong></td>
                                    <td>
                                        @php
                                            $typeColors = ['regular'=>'primary','lunch'=>'warning','dinner'=>'info','weekend'=>'success','special'=>'danger','seasonal'=>'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $typeColors[$menu->type] ?? 'primary' }}">
                                            {{ $menu->type_label ?? $menu->type }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-info">{{ $menu->products_count }}</span></td>
                                    <td>
                                        @if($menu->start_date || $menu->end_date)
                                            <small>{{ $menu->start_date?->format('d/m/Y') ?? 'N/A' }} - {{ $menu->end_date?->format('d/m/Y') ?? 'N/A' }}</small>
                                        @else
                                            <small class="text-muted">Permanent</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->available_from && $menu->available_until)
                                            <small>{{ $menu->available_from }} - {{ $menu->available_until }}</small>
                                        @else
                                            <small class="text-muted">Toute la journée</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" 
                                                onsubmit="return confirm('Supprimer ce menu ?')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line mr-0"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4">Aucun menu créé</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection