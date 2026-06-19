@extends('layouts.app')

@section('title', 'Modifier ' . $staff->full_name)

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $staff->full_name }}</h3>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informations</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.staff.update', $staff) }}" method="POST">
                            @csrf @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $staff->first_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $staff->last_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone *</label>
                                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nouveau mot de passe (optionnel)</label>
                                    <input type="password" name="password" class="form-control">
                                    <small class="text-muted">Laisser vide pour ne pas changer</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rôle *</label>
                                    <select name="role" class="form-control" required>
                                        <option value="manager" {{ $staff->hasRole('manager')?'selected':'' }}>👔 Manager</option>
                                        <option value="chef" {{ $staff->hasRole('chef')?'selected':'' }}>👨‍🍳 Chef cuisinier</option>
                                        <option value="waiter" {{ $staff->hasRole('waiter')?'selected':'' }}>🧑‍💼 Serveur</option>
                                        <option value="delivery_person" {{ $staff->hasRole('delivery_person')?'selected':'' }}>🛵 Livreur</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $staff->is_active?'checked':'' }}>
                                        <label class="form-check-label">Compte actif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-lg"></i> Mettre à jour
                                </button>
                                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection