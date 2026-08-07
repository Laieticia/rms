@extends('layouts.storefront')

@section('title', 'Connexion')

@section('content')
<div class="container" style="padding-top:140px; padding-bottom:80px; max-width:480px;">
    <div class="text-center mb-4">
        <div style="width:70px;height:70px;background:rgba(232,40,26,0.1);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:15px;">
            <i class="fas fa-sign-in-alt" style="font-size:30px;color:var(--primary);"></i>
        </div>
        <h4>Connexion à votre compte</h4>
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
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="form-control form-control-lg @error('email') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color:var(--primary);font-size:14px;">Mot de passe oublié ?</a>
                    @endif
                </div>

                <button type="submit" class="btn-red w-100 justify-content-center" style="padding:12px;">
                    <i class="fas fa-sign-in-alt me-2"></i> Connexion
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0">Pas encore de compte ?
                    <a href="{{ route('register') }}" style="color:var(--primary);font-weight:600;">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
