@extends('layouts.storefront')

@section('title', 'Créer un compte')

@section('content')
<div class="container" style="padding-top:140px; padding-bottom:80px; max-width:560px;">
    <div class="text-center mb-4">
        <div style="width:70px;height:70px;background:rgba(232,40,26,0.1);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:15px;">
            <i class="fas fa-user-plus" style="font-size:30px;color:var(--primary);"></i>
        </div>
        <h4>Créer votre compte RMS</h4>
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
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">Prénom</label>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                               class="form-control form-control-lg @error('first_name') is-invalid @enderror" style="border-radius:12px;">
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Nom</label>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                               class="form-control form-control-lg @error('last_name') is-invalid @enderror" style="border-radius:12px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="form-control form-control-lg @error('email') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="6XXXXXXXX"
                           class="form-control form-control-lg @error('phone') is-invalid @enderror" style="border-radius:12px;">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror" style="border-radius:12px;">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmer</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="form-control form-control-lg" style="border-radius:12px;">
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        J'accepte les conditions générales d'utilisation
                    </label>
                </div>

                <button type="submit" class="btn-red w-100 justify-content-center" style="padding:12px;">
                    <i class="fas fa-user-plus me-2"></i> Créer mon compte
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0">Déjà inscrit ?
                    <a href="{{ route('login') }}" style="color:var(--primary);font-weight:600;">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
