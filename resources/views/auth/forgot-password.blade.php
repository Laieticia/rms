@extends('layouts.storefront')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="container" style="padding-top:140px; padding-bottom:80px; max-width:480px;">
    <div class="text-center mb-4">
        <div style="width:70px;height:70px;background:rgba(232,40,26,0.1);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:15px;">
            <i class="fas fa-key" style="font-size:30px;color:var(--primary);"></i>
        </div>
        <h4>Mot de passe oublié</h4>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

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
            <p class="text-muted small mb-3">Indiquez votre adresse email, nous vous envoyons un lien pour réinitialiser votre mot de passe.</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="form-control form-control-lg @error('email') is-invalid @enderror" style="border-radius:12px;">
                </div>
                <button type="submit" class="btn-red w-100 justify-content-center" style="padding:12px;">
                    <i class="fas fa-paper-plane me-2"></i> Envoyer le lien
                </button>
            </form>
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" style="color:var(--primary);font-weight:600;">Retour à la connexion</a>
            </div>
        </div>
    </div>
</div>
@endsection
