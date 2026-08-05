@extends('layouts.storefront')

@section('title', 'Nos restaurants')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="slbl">Trouvez votre bonheur</span>
        <h2 class="stitle">Tous les <span>restaurants</span></h2>
        <div class="sline"></div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="row g-2 justify-content-center mb-5">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-lg" placeholder="Rechercher un restaurant..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="city" class="form-select form-select-lg">
                <option value="">Toutes les villes</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select form-select-lg">
                <option value="popular" {{ request('sort', 'popular') == 'popular' ? 'selected' : '' }}>Les plus populaires</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Les mieux notés</option>
                <option value="delivery_time" {{ request('sort') == 'delivery_time' ? 'selected' : '' }}>Livraison la plus rapide</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-lg w-100" style="background:var(--primary);color:#fff;"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="row g-4">
        @forelse($restaurants as $restaurant)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                        <div style="height:180px;overflow:hidden;position:relative;">
                            <img src="{{ $restaurant->cover_url ?? url('website/assets/img/about1.jpg') }}" alt="{{ $restaurant->name }}" class="w-100 h-100" style="object-fit:cover;">
                            @if(!$restaurant->isOpenNow())
                                <span class="badge bg-dark position-absolute top-0 end-0 m-2">Fermé actuellement</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="mb-0" style="color:#1a1a1a;">{{ $restaurant->name }}</h5>
                                <span class="badge" style="background:rgba(255,193,7,0.15);color:#c99400;">
                                    <i class="bi bi-star-fill"></i> {{ $restaurant->average_rating }}
                                    <small>({{ $restaurant->total_reviews }})</small>
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($restaurant->description, 90) }}</p>
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="bi bi-geo-alt me-1"></i>{{ $restaurant->city }}</span>
                                <span><i class="bi bi-clock me-1"></i>{{ $restaurant->estimated_delivery_time }} min</span>
                                <span><i class="bi bi-truck me-1"></i>{{ \App\Helpers\CameroonHelper::formatCurrency($restaurant->delivery_fee) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-emoji-frown" style="font-size:3rem;"></i>
                <p class="mt-3">Aucun restaurant ne correspond à votre recherche.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $restaurants->links() }}
    </div>
</div>
@endsection
