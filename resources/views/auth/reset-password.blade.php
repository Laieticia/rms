@extends('layouts.storefront')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="container" style="padding-top:140px; padding-bottom:80px; max-width:480px;">
    <div class="text-center mb-4">
        <div style="width:70px;height:70px;background:rgba(232,40,26,0.1);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:15px;">
            <i class="fas fa-lock" style="font-size:30px;color:var(--primary);"></i>
        </div>
        <h4>Nouveau mot de passe</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius:20px;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                           class="form-control form-control-lg @error('email') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <button type="submit" class="btn-red w-100 justify-content-center" style="padding:12px;">
                    <i class="fas fa-check me-2"></i> Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
