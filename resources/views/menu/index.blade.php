@extends('layouts.app')

@section('title', 'Notre Menu')

@section('content')
<div class="container">
    <div class="row">
        {{-- Sidebar Catégories --}}
        <div class="col-md-3">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Catégories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('menu.index') }}" 
                       class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Tout le menu
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('menu.index', ['category' => $category->slug]) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == $category->slug ? 'active' : '' }}">
                            @if($category->image)
                                <img src="{{ asset('storage/'.$category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="me-2" 
                                     style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%;">
                            @endif
                            {{ $category->name }}
                            <span class="badge bg-secondary float-end">
                                {{ $category->products_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
                
                {{-- Filtres --}}
                <div class="card-body border-top">
                    <h6 class="fw-bold">Filtres</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" 
                                   type="checkbox" 
                                   id="vegetarian" 
                                   data-filter="vegetarian"
                                   {{ request('vegetarian') ? 'checked' : '' }}>
                            <label class="form-check-label" for="vegetarian">
                                <i class="bi bi-leaf text-success"></i> Végétarien
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" 
                                   type="checkbox" 
                                   id="vegan"
                                   data-filter="vegan"
                                   {{ request('vegan') ? 'checked' : '' }}>
                            <label class="form-check-label" for="vegan">
                                <i class="bi bi-flower1 text-success"></i> Vegan
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" 
                                   type="checkbox" 
                                   id="gluten_free"
                                   data-filter="gluten_free"
                                   {{ request('gluten_free') ? 'checked' : '' }}>
                            <label class="form-check-label" for="gluten_free">
                                <i class="bi bi-shield-check text-warning"></i> Sans gluten
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prix</label>
                        <input type="range" class="form-range" min="0" max="50" step="1" id="priceRange">
                        <div class="d-flex justify-content-between">
                            <small>0€</small>
                            <small>50€</small>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-secondary btn-sm w-100" id="resetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Liste des Produits --}}
        <div class="col-md-9">
            {{-- Barre de recherche --}}
            <div class="mb-4">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Rechercher un plat..." 
                           id="searchProducts"
                           value="{{ request('search') }}">
                </div>
            </div>
            
            {{-- Grille de produits --}}
            <div class="row g-4" id="productsGrid">
                @forelse($products as $product)
                    <div class="col-md-6 col-lg-4" data-category="{{ $product->category_id }}">
                        <div class="card h-100 shadow-sm hover-shadow product-card">
                            {{-- Image du produit --}}
                            <div class="position-relative">
                                @if($product->primary_image)
                                    <img src="{{ asset('storage/'.$product->primary_image->path) }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                
                                {{-- Badges --}}
                                <div class="position-absolute top-0 start-0 p-2">
                                    @if($product->is_vegetarian)
                                        <span class="badge bg-success me-1" title="Végétarien">
                                            <i class="bi bi-leaf"></i>
                                        </span>
                                    @endif
                                    @if($product->is_vegan)
                                        <span class="badge bg-success me-1" title="Vegan">
                                            <i class="bi bi-flower1"></i>
                                        </span>
                                    @endif
                                    @if($product->is_spicy)
                                        <span class="badge bg-danger" title="Épicé">
                                            <i class="bi bi-fire"></i>
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Promo --}}
                                @if($product->is_on_sale)
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-danger">
                                            -{{ $product->discount_percentage }}%
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $product->name }}</h6>
                                    <div class="text-end">
                                        @if($product->is_on_sale)
                                            <span class="text-decoration-line-through text-muted small">
                                                {{ number_format($product->compare_price, 2) }} €
                                            </span>
                                        @endif
                                        <span class="text-primary fw-bold ms-2">
                                            {{ $product->formatted_price }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($product->description, 80) }}
                                </p>
                                
                                @if($product->rating_count > 0)
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= round($product->rating_avg) ? '-fill' : '' }} small"></i>
                                                @endfor
                                            </div>
                                            <small class="text-muted">
                                                ({{ $product->rating_count }})
                                            </small>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($product->options->count() > 0)
                                    <small class="text-info">
                                        <i class="bi bi-plus-circle"></i> Options disponibles
                                    </small>
                                @endif
                            </div>
                            
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#productModal{{ $product->id }}">
                                        <i class="bi bi-eye"></i> Détails
                                    </button>
                                    
                                    @if($product->isInStock())
                                        <button class="btn btn-primary btn-sm add-to-cart" 
                                                data-product-id="{{ $product->id }}">
                                            <i class="bi bi-cart-plus"></i> Ajouter
                                        </button>
                                    @else
                                        <span class="badge bg-danger">Rupture de stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Modal Détails Produit --}}
                    <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $product->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if($product->primary_image)
                                                <img src="{{ asset('storage/'.$product->primary_image->path) }}" 
                                                     class="img-fluid rounded" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            
                                            {{-- Images supplémentaires --}}
                                            @if($product->images->count() > 1)
                                                <div class="row mt-2 g-2">
                                                    @foreach($product->images as $image)
                                                        <div class="col-3">
                                                            <img src="{{ asset('storage/'.$image->path) }}" 
                                                                 class="img-fluid rounded cursor-pointer" 
                                                                 alt="{{ $image->alt_text }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{ $product->description }}</p>
                                            
                                            <div class="mb-3">
                                                <strong>Prix :</strong>
                                                @if($product->is_on_sale)
                                                    <span class="text-decoration-line-through me-2">
                                                        {{ number_format($product->compare_price, 2) }} €
                                                    </span>
                                                @endif
                                                <span class="h4 text-primary">
                                                    {{ $product->formatted_price }}
                                                </span>
                                            </div>
                                            
                                            {{-- Informations nutritionnelles --}}
                                            @if($product->calories)
                                                <div class="mb-3">
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-fire"></i> {{ $product->calories }} cal
                                                    </span>
                                                    @if($product->preparation_time)
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-clock"></i> {{ $product->preparation_time }} min
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            {{-- Allergènes --}}
                                            @if($product->allergens)
                                                <div class="mb-3">
                                                    <strong>Allergènes :</strong>
                                                    <div>
                                                        @foreach($product->allergens as $allergen)
                                                            <span class="badge bg-danger me-1">{{ $allergen }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            {{-- Options du produit --}}
                                            @foreach($product->options as $option)
                                                <div class="mb-3">
                                                    <strong>{{ $option->name }} :</strong>
                                                    @if($option->is_required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                    <div>
                                                        @foreach($option->items as $item)
                                                            <div class="form-check">
                                                                <input class="form-check-input product-option" 
                                                                       type="{{ $option->type == 'single' ? 'radio' : 'checkbox' }}"
                                                                       name="options[{{ $option->id }}][]"
                                                                       value="{{ $item->id }}"
                                                                       data-price="{{ $item->price }}">
                                                                <label class="form-check-label">
                                                                    {{ $item->name }}
                                                                    @if($item->price > 0)
                                                                        (+{{ number_format($item->price, 2) }} €)
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Fermer
                                    </button>
                                    <button type="button" class="btn btn-primary btn-lg add-to-cart-with-options"
                                            data-product-id="{{ $product->id }}">
                                        <i class="bi bi-cart-plus"></i> Ajouter au panier - {{ $product->formatted_price }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-frown text-muted" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">Aucun produit trouvé</h3>
                            <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            {{-- Pagination --}}
            <div class="mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
.product-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}
.product-card .card-img-top {
    transition: transform 0.3s ease;
}
.product-card:hover .card-img-top {
    transform: scale(1.05);
}
.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Ajouter au panier
    $('.add-to-cart').click(function() {
        const productId = $(this).data('product-id');
        addToCart(productId);
    });
    
    // Ajouter au panier avec options (depuis la modal)
    $('.add-to-cart-with-options').click(function() {
        const productId = $(this).data('product-id');
        const options = collectOptions(productId);
        addToCart(productId, 1, options);
        $('#productModal' + productId).modal('hide');
    });
    
    function addToCart(productId, quantity = 1, options = []) {
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                options: options,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                updateCartCount(response.cartCount);
                showToast('Produit ajouté au panier !', 'success');
            },
            error: function(xhr) {
                showToast('Erreur lors de l\'ajout au panier', 'error');
            }
        });
    }
    
    function collectOptions(productId) {
        const options = [];
        $(`#productModal${productId} .product-option:checked`).each(function() {
            options.push({
                option_id: $(this).attr('name').match(/\d+/)[0],
                item_id: $(this).val(),
                price: $(this).data('price')
            });
        });
        return options;
    }
    
    // Recherche en temps réel
    let searchTimeout;
    $('#searchProducts').on('keyup', function() {
        clearTimeout(searchTimeout);
        const search = $(this).val();
        searchTimeout = setTimeout(function() {
            window.location.href = updateQueryString('search', search);
        }, 500);
    });
    
    // Filtres
    $('.filter-checkbox').change(function() {
        const filter = $(this).data('filter');
        const value = $(this).prop('checked') ? '1' : '';
        window.location.href = updateQueryString(filter, value);
    });
    
    function updateQueryString(key, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        return url.toString();
    }
    
    function updateCartCount(count) {
        $('#cartCount').text(count);
        if (count > 0) {
            $('#cartCount').removeClass('d-none');
        } else {
            $('#cartCount').addClass('d-none');
        }
    }
    
    function showToast(message, type) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
});
</script>
@endpush
@endsection