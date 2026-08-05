@extends('layouts.storefront')

@section('title', 'Résultats pour "' . $query . '"')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Résultats pour « {{ $query }} »</h2>

    <form method="GET" action="{{ route('search') }}" class="mb-5">
        <div class="input-group input-group-lg">
            <input type="text" name="q" class="form-control" value="{{ $query }}" placeholder="Rechercher un restaurant ou un plat...">
            <button class="btn" style="background:var(--primary);color:#fff;" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <h5 class="mb-3">Restaurants ({{ $restaurants->count() }})</h5>
    <div class="row g-4 mb-5">
        @forelse($restaurants as $restaurant)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                        <div style="height:150px;overflow:hidden;">
                            <img src="{{ $restaurant->cover_url ?? url('website/assets/img/about1.jpg') }}" class="w-100 h-100" style="object-fit:cover;">
                        </div>
                        <div class="card-body">
                            <h6 style="color:#1a1a1a;">{{ $restaurant->name }}</h6>
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $restaurant->city }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-muted">Aucun restaurant ne correspond à votre recherche.</div>
        @endforelse
    </div>

    <h5 class="mb-3">Plats ({{ $products->count() }})</h5>
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-6 col-md-3">
                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
                        <div style="height:110px;overflow:hidden;">
                            <img src="{{ $product->primary_image_url ?? url('website/assets/img/category/2.jpg') }}" class="w-100 h-100" style="object-fit:cover;">
                        </div>
                        <div class="card-body p-2">
                            <p class="text-muted small mb-1">{{ $product->restaurant->name }}</p>
                            <div class="small text-truncate" style="color:#1a1a1a;">{{ $product->name }}</div>
                            <strong style="color:var(--primary);">{{ $product->formatted_price }}</strong>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-muted">Aucun plat ne correspond à votre recherche.</div>
        @endforelse
    </div>
</div>
@endsection
