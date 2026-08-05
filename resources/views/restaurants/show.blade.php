@extends('layouts.storefront')

@section('title', $restaurant->name)

@section('content')

<div style="height:260px;overflow:hidden;position:relative;">
    <img src="{{ $restaurant->cover_url ?? url('website/assets/img/about1.jpg') }}" alt="{{ $restaurant->name }}" class="w-100 h-100" style="object-fit:cover;">
    <div class="position-absolute bottom-0 start-0 w-100" style="background:linear-gradient(transparent, rgba(0,0,0,.6));height:120px;"></div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-4 mt-n5 position-relative mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $restaurant->logo_url ?? url('website/assets/img/about2.jpg') }}" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:14px;">
                    <div>
                        <h2 class="mb-1">{{ $restaurant->name }}</h2>
                        <div class="text-muted">
                            <i class="bi bi-star-fill text-warning"></i> {{ $restaurant->average_rating }}
                            <small>({{ $restaurant->total_reviews }} avis)</small>
                            <span class="mx-2">·</span>
                            <i class="bi bi-geo-alt"></i> {{ $restaurant->city }}
                            @if($restaurant->isOpenNow())
                                <span class="badge bg-success ms-2">Ouvert</span>
                            @else
                                <span class="badge bg-secondary ms-2">Fermé</span>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="mb-3">{{ $restaurant->description }}</p>
                <div class="row text-center g-3">
                    <div class="col-4">
                        <div class="border rounded-3 p-2">
                            <i class="bi bi-clock text-primary"></i>
                            <div class="fw-bold">{{ $restaurant->estimated_delivery_time }} min</div>
                            <small class="text-muted">Livraison</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2">
                            <i class="bi bi-truck text-primary"></i>
                            <div class="fw-bold">{{ \App\Helpers\CameroonHelper::formatCurrency($restaurant->delivery_fee) }}</div>
                            <small class="text-muted">Frais livraison</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2">
                            <i class="bi bi-cash-stack text-primary"></i>
                            <div class="fw-bold">{{ \App\Helpers\CameroonHelper::formatCurrency($restaurant->minimum_order) }}</div>
                            <small class="text-muted">Commande min.</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('restaurant.menu', $restaurant) }}" class="btn w-100 mt-4 py-3" style="background:var(--primary);color:#fff;border-radius:14px;">
                    <i class="bi bi-book"></i> Voir le menu complet
                </a>
                <a href="{{ route('reservations.create', $restaurant) }}" class="btn btn-outline-secondary w-100 mt-2 py-3" style="border-radius:14px;">
                    <i class="bi bi-calendar-check"></i> Réserver une table
                </a>
            </div>

            @if($featuredProducts->count() > 0)
                <h5 class="mb-3">Plats populaires</h5>
                <div class="row g-3 mb-4">
                    @foreach($featuredProducts as $product)
                        <div class="col-6 col-md-4">
                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
                                    <div style="height:110px;overflow:hidden;">
                                        <img src="{{ $product->primary_image_url ?? url('website/assets/img/category/2.jpg') }}" alt="{{ $product->name }}" class="w-100 h-100" style="object-fit:cover;">
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="small text-truncate" style="color:#1a1a1a;">{{ $product->name }}</div>
                                        <strong style="color:var(--primary);">{{ $product->formatted_price }}</strong>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <h5 class="mb-3">Avis clients</h5>
            @forelse($reviews as $review)
                <div class="border-bottom py-3">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->user->first_name ?? 'Client' }}</strong>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-1">{{ $review->comment }}</p>
                </div>
            @empty
                <p class="text-muted">Aucun avis pour le moment.</p>
            @endforelse
            {{ $reviews->links() }}
        </div>

        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h6 class="mb-3">Catégories du menu</h6>
                <div class="list-group list-group-flush">
                    @foreach($categories as $category)
                        <a href="{{ route('restaurant.menu', [$restaurant, 'category' => $category->slug]) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                            {{ $category->name }}
                            <span class="badge bg-secondary rounded-pill">{{ $category->available_products_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h6 class="mb-3">Adresse</h6>
                <p class="text-muted mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $restaurant->address }}, {{ $restaurant->city }}</p>
                @if($restaurant->phone)
                    <p class="text-muted mb-0"><i class="bi bi-telephone me-2"></i>{{ $restaurant->phone }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
