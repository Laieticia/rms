@extends('layouts.app')

@section('title', 'Gestion des Clients')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-people me-2"></i>Clients</h3>
            <span class="text-muted">{{ $stats['total'] }} clients inscrits</span>
        </div>

        <!-- Statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white"><div class="card-body text-center py-3"><h4>{{$stats['total']}}</h4><small>Total clients</small></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white"><div class="card-body text-center py-3"><h4>{{$stats['active']}}</h4><small>Actifs</small></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white"><div class="card-body text-center py-3"><h4>{{$stats['blocked']}}</h4><small>Bloqués</small></div></div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white"><div class="card-body text-center py-3"><h4>{{$stats['new_today']}}</h4><small>Nouveaux aujourd'hui</small></div></div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status')=='active'?'selected':'' }}>Actifs</option>
                            <option value="blocked" {{ request('status')=='blocked'?'selected':'' }}>Bloqués</option>
                            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactifs</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, email, téléphone..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Filtrer</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Client</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Commandes</th>
                                <th>Fidélité</th>
                                {{-- <th>Statut</th> --}}
                                <th>Inscrit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar_url }}" class="rounded-circle me-2" width="35" height="35">
                                            <div>
                                                <strong>{{ $user->full_name }}</strong>
                                                @if($user->loyalty_level && $user->loyalty_level !== 'bronze')
                                                    <span class="badge bg-{{ $user->loyalty_level=='gold'?'warning':'secondary' }} ms-1">
                                                        {{ ucfirst($user->loyalty_level) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><small>{{ $user->email }}</small></td>
                                    <td><small>{{ $user->phone ?? 'N/A' }}</small></td>
                                    <td><span class="badge bg-primary">{{ $user->orders_count ?? 0 }}</span></td>
                                    <td><span class="badge bg-warning">{{ $user->getLoyaltyBalance() }} pts</span></td>
                                    {{-- <td>
                                        @if($user->is_blocked)
                                            <span class="badge bg-danger">Bloqué</span>
                                        @elseif($user->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-warning">Inactif</span>
                                        @endif
                                    </td> --}}
                                    <td><small>{{ $user->created_at->format('d/m/Y') }}</small></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info"><i class="ri-eye-line mr-0"></i></a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="ri-pencil-line mr-0"></i></a>
                                            @if($user->is_blocked)
                                                <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" title="Débloquer"><i class="ri-unblock-line mr-0"></i></button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#blockModal{{$user->id}}" title="Bloquer"><i class="ri-lock-line mr-0"></i></button>
                                            @endif
                                            {{-- <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement ce client ?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line mr-0"></i></button>
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center py-4">Aucun client trouvé</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>

</div>
<!-- Modals de blocage -->
@foreach($users as $user)
    <div class="modal fade" id="blockModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.users.block', $user) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="bi bi-lock"></i> 
                            Bloquer {{ $user->full_name }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Raison du blocage *</label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="Raison du blocage..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durée (en jours)</label>
                            <input type="number" name="days" class="form-control" min="1" max="365" placeholder="Laisser vide pour permanent">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Confirmer le blocage</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection

@section('scripts')
<script>
    // Tooltips initialization
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection