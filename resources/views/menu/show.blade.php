@extends('layouts.storefront')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('restaurants.show', $product->restaurant) }}">{{ $product->restaurant->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('restaurant.menu', $product->restaurant) }}">Menu</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="rounded-4 overflow-hidden shadow-sm" style="height:380px;">
                <img id="mainImage" src="{{ $product->primary_image_url ?? url('website/assets/img/category/2.jpg') }}" alt="{{ $product->name }}" class="w-100 h-100" style="object-fit:cover;">
            </div>
            @if($product->images->count() > 1)
                <div class="d-flex gap-2 mt-2">
                    @foreach($product->images as $image)
                        <img src="{{ asset('storage/'.$image->path) }}" class="rounded-3" style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                             onclick="document.getElementById('mainImage').src = this.src;">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-6">
            <div class="d-flex gap-2 mb-2">
                @if($product->is_vegetarian)<span class="badge bg-success"><i class="bi bi-leaf"></i> Végétarien</span>@endif
                @if($product->is_vegan)<span class="badge bg-success"><i class="bi bi-flower1"></i> Vegan</span>@endif
                @if($product->is_spicy)<span class="badge bg-danger"><i class="bi bi-fire"></i> Épicé</span>@endif
            </div>
            <h2>{{ $product->name }}</h2>
            @if($product->reviews_count > 0)
                <div class="mb-2 text-warning">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($product->reviews_avg_rating) ? '-fill' : '' }}"></i>
                    @endfor
                    <span class="text-muted small ms-1">({{ $product->reviews_count }} avis)</span>
                </div>
            @endif
            <p class="text-muted">{{ $product->description }}</p>

            <div class="d-flex align-items-center gap-3 mb-4">
                @if($product->is_on_sale)
                    <span class="text-muted text-decoration-line-through">{{ \App\Helpers\CameroonHelper::formatCurrency($product->compare_price) }}</span>
                @endif
                <h3 class="mb-0" style="color:var(--primary);">{{ $product->formatted_price }}</h3>
            </div>

            <form id="addToCartForm">
                @if($product->options->count() > 0)
                    @foreach($product->options as $option)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $option->name }} @if($option->is_required)<span class="text-danger">*</span>@endif</label>
                            <div>
                                @foreach($option->items as $item)
                                    <div class="form-check">
                                        <input class="form-check-input product-option" type="{{ $option->type === 'single' ? 'radio' : 'checkbox' }}"
                                               name="option_{{ $option->id }}{{ $option->type === 'single' ? '' : '[]' }}"
                                               id="opt{{ $item->id }}" value="{{ $item->id }}"
                                               data-option-id="{{ $option->id }}" data-price="{{ $item->price }}" data-name="{{ $item->name }}"
                                               {{ !$item->is_available ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="opt{{ $item->id }}">
                                            {{ $item->name }}
                                            @if($item->price > 0)(+{{ \App\Helpers\CameroonHelper::formatCurrency($item->price) }})@endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Quantité</label>
                    <div class="input-group" style="width:140px;">
                        <button type="button" class="btn btn-outline-secondary" id="qtyMinus">-</button>
                        <input type="number" class="form-control text-center" id="qtyInput" value="1" min="{{ $product->min_per_order ?? 1 }}" max="{{ $product->max_per_order ?? 10 }}">
                        <button type="button" class="btn btn-outline-secondary" id="qtyPlus">+</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Instructions particulières</label>
                    <textarea class="form-control" id="notesInput" rows="2" maxlength="255" placeholder="Ex : sans piment, bien cuit..."></textarea>
                </div>

                @if($product->is_in_stock)
                    <button type="button" id="addToCartBtn" class="btn btn-lg w-100" style="background:var(--primary);color:#fff;border-radius:14px;">
                        <i class="bi bi-cart-plus"></i> Ajouter au panier
                    </button>
                @else
                    <button type="button" class="btn btn-lg w-100 btn-secondary" disabled>Produit indisponible</button>
                @endif
            </form>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
        <h5 class="mt-5 mb-3">Vous pourriez aussi aimer</h5>
        <div class="row g-3">
            @foreach($relatedProducts as $related)
                <div class="col-6 col-md-3">
                    <a href="{{ route('products.show', $related) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
                            <div style="height:120px;overflow:hidden;">
                                <img src="{{ $related->primary_image_url ?? url('website/assets/img/category/3.jpg') }}" class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            <div class="card-body p-2">
                                <div class="small text-truncate" style="color:#1a1a1a;">{{ $related->name }}</div>
                                <strong style="color:var(--primary);">{{ $related->formatted_price }}</strong>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <h5 class="mt-5 mb-3">Avis clients</h5>
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
        <p class="text-muted">Aucun avis pour le moment sur ce produit.</p>
    @endforelse
    {{ $reviews->links() }}
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('qtyInput');
    document.getElementById('qtyMinus')?.addEventListener('click', () => {
        qtyInput.value = Math.max(parseInt(qtyInput.min || 1), parseInt(qtyInput.value) - 1);
    });
    document.getElementById('qtyPlus')?.addEventListener('click', () => {
        qtyInput.value = Math.min(parseInt(qtyInput.max || 10), parseInt(qtyInput.value) + 1);
    });

    document.getElementById('addToCartBtn')?.addEventListener('click', function () {
        const options = [];
        document.querySelectorAll('.product-option:checked').forEach(function (el) {
            options.push({
                option_id: el.dataset.optionId,
                item_id: el.value,
                price: el.dataset.price,
                name: el.dataset.name,
            });
        });

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                quantity: parseInt(qtyInput.value),
                options: options,
                notes: document.getElementById('notesInput').value,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route('cart.index') }}';
            } else {
                alert(data.message || "Impossible d'ajouter ce produit au panier.");
            }
        })
        .catch(() => alert('Une erreur est survenue.'));
    });
});
</script>
@endpush
@endsection
