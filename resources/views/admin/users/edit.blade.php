@extends('layouts.app')

@section('title', 'Modifier : ' . $user->full_name)



@section('content')
    <div class="content-page">
        <div class="container-fluid">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary mb-3"><i
                    class="bi bi-arrow-left"></i> Retour</a>
            {{-- <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-4">Modifier: {{ $user->full_name }}</h3>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i>
                    Retour</a>
            </div> --}}
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informations du client</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Prénom *</label>
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ old('first_name', $user->first_name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nom *</label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', $user->last_name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Téléphone</label>
                                        <input type="tel" name="phone" class="form-control"
                                            value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                {{ $user->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label">Compte actif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="is_blocked" value="1"
                                                {{ $user->is_blocked ? 'checked' : '' }}>
                                            <label class="form-check-label">Compte bloqué</label>
                                        </div>
                                    </div>
                                    @if ($user->is_blocked)
                                        <div class="col-12">
                                            <label class="form-label">Raison du blocage</label>
                                            <textarea name="blocked_reason" class="form-control" rows="2">{{ $user->blocked_reason }}</textarea>
                                        </div>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-check-lg"></i> Mettre à
                                    jour</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Rôles -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Rôles</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf @method('PUT')

                                @foreach ($roles as $role)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                            value="{{ $role->name }}" id="role{{ $role->id }}"
                                            {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="role{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</label>
                                    </div>
                                @endforeach

                                <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-shield-check"></i>
                                    Mettre à jour les rôles</button>
                            </form>
                        </div>
                    </div>

                    <!-- Info rapide -->
                    <div class="card mt-3">
                        <div class="card-header">Informations</div>
                        <div class="card-body">
                            <p><strong>Inscrit :</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Dernière connexion :</strong>
                                {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</p>
                            <p><strong>Fidélité :</strong> {{ $user->getLoyaltyBalance() }} pts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
