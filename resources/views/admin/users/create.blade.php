@extends('layouts.app')

@section('title', 'Modifier Utilisateur')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-4">Modifier: {{ $user->full_name }}</h3>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Retour</a>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Prénom</label><input name="first_name" class="form-control" value="{{$user->first_name}}" required></div>
                                <div class="col-md-6"><label class="form-label">Nom</label><input name="last_name" class="form-control" value="{{$user->last_name}}" required></div>
                                <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{$user->email}}" required></div>
                                <div class="col-md-6"><label class="form-label">Téléphone</label><input name="phone" class="form-control" value="{{$user->phone}}"></div>
                                <div class="col-md-6"><div class="form-check form-switch"><input name="is_active" value="1" class="form-check-input" type="checkbox" {{$user->is_active?'checked':''}}><label class="form-check-label">Actif</label></div></div>
                                <div class="col-md-6"><div class="form-check form-switch"><input name="is_blocked" value="1" class="form-check-input" type="checkbox" {{$user->is_blocked?'checked':''}}><label class="form-check-label">Bloqué</label></div></div>
                            </div>
                            <button class="btn btn-primary mt-3">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Rôles</div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf @method('PUT')
                            @foreach($roles as $role)
                                <div class="form-check"><input name="roles[]" value="{{$role->name}}" class="form-check-input" type="checkbox" {{$user->hasRole($role->name)?'checked':''}}><label>{{ucfirst($role->name)}}</label></div>
                            @endforeach
                            <button class="btn btn-primary w-100 mt-3">Mettre à jour les rôles</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection