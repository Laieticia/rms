@extends('layouts.app')

@section('title', 'Gestion du Personnel')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Personnel</h3>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Ajouter un membre
            </a>
        </div>

        <!-- Statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-md">
                <div class="card bg-primary text-white"><div class="card-body text-center py-3"><h4>{{$stats['total']}}</h4><small>Total</small></div></div>
            </div>
            <div class="col-md">
                <div class="card bg-info text-white"><div class="card-body text-center py-3"><h4>{{$stats['managers']}}</h4><small>Managers</small></div></div>
            </div>
            <div class="col-md">
                <div class="card bg-warning text-white"><div class="card-body text-center py-3"><h4>{{$stats['chefs']}}</h4><small>Chefs</small></div></div>
            </div>
            <div class="col-md">
                <div class="card bg-success text-white"><div class="card-body text-center py-3"><h4>{{$stats['waiters']}}</h4><small>Serveurs</small></div></div>
            </div>
            <div class="col-md">
                <div class="card bg-secondary text-white"><div class="card-body text-center py-3"><h4>{{$stats['delivery_persons']}}</h4><small>Livreurs</small></div></div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.staff.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="role" class="form-control">
                            <option value="">Tous les rôles</option>
                            <option value="manager" {{ request('role')=='manager'?'selected':'' }}>Manager</option>
                            <option value="chef" {{ request('role')=='chef'?'selected':'' }}>Chef</option>
                            <option value="waiter" {{ request('role')=='waiter'?'selected':'' }}>Serveur</option>
                            <option value="delivery_person" {{ request('role')=='delivery_person'?'selected':'' }}>Livreur</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Statut</option>
                            <option value="active" {{ request('status')=='active'?'selected':'' }}>Actif</option>
                            <option value="blocked" {{ request('status')=='blocked'?'selected':'' }}>Bloqué</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filtrer</button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Membre</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Ajouté le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $member->avatar_url }}" class="rounded-circle me-2" width="35" height="35">
                                            <strong>{{ $member->full_name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->phone }}</td>
                                    <td>
                                        @foreach($member->getRoleNames() as $role)
                                            <span class="badge bg-{{ $role=='manager'?'info':($role=='chef'?'warning':($role=='waiter'?'success':'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($member->is_blocked)
                                            <span class="badge bg-danger">Bloqué</span>
                                        @elseif($member->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-warning">Inactif</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $member->created_at->format('d/m/Y') }}</small></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.staff.show', $member) }}" class="btn btn-sm btn-outline-info"><i class="ri-eye-line mr-0"></i></a>
                                            <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-sm btn-outline-primary"><i class="ri-pencil-line mr-0"></i></a>
                                            @if($member->is_blocked)
                                                <form action="{{ route('admin.staff.unblock', $member) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success"><i class="ri-unblock-line mr-0"></i></button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#blockModal{{$member->id}}"><i class="ri-lock-line mr-0"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4">Aucun membre du personnel</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $staff->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Bloquer -->
@foreach($staff as $member)
    <div class="modal fade" id="blockModal{{$member->id}}">
        <div class="modal-dialog">
            <form action="{{ route('admin.staff.block', $member) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header"><h5>Bloquer {{$member->full_name}}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><label class="form-label">Raison</label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
                    <div class="modal-footer"><button class="btn btn-danger">Bloquer</button></div>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection