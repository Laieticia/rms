@extends('layouts.app')

@section('title', 'Ajouter un membre')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-person-plus me-2"></i>Ajouter un membre du personnel</h3>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informations du membre</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.staff.store') }}" method="POST">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                    @error('first_name')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                    @error('last_name')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone *</label>
                                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                                    @error('phone')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe *</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmer le mot de passe *</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rôle *</label>
                                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                        <option value="">Choisir un rôle</option>
                                        <option value="manager" {{ old('role')=='manager'?'selected':'' }}>👔 Manager</option>
                                        <option value="chef" {{ old('role')=='chef'?'selected':'' }}>👨‍🍳 Chef cuisinier</option>
                                        <option value="waiter" {{ old('role')=='waiter'?'selected':'' }}>🧑‍💼 Serveur</option>
                                        <option value="delivery_person" {{ old('role')=='delivery_person'?'selected':'' }}>🛵 Livreur</option>
                                    </select>
                                    @error('role')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                        <label class="form-check-label">Compte actif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <i class="bi bi-info-circle me-2"></i>
                                Le membre recevra ses identifiants par email et pourra se connecter immédiatement.
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-check"></i> Créer le compte
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