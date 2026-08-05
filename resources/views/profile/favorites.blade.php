@extends('layouts.storefront')

@section('title', 'Mes favoris')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-heart-fill text-danger me-2"></i>Mes favoris</h2>

    <div class="row g-4">
        @forelse($favorites as $favorite)
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                    <a href="{{ route('products.show', $favorite->product) }}" class="text-decoration-none">
                        <div style="height:130px;overflow:hidden;">
                            <img src="{{ $favorite->product->primary_image_url ?? url('website/assets/img/category/2.jpg') }}" class="w-100 h-100" style="object-fit:cover;">
                        </div>
                    </a>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-1">{{ $favorite->product->restaurant->name }}</p>
                        <a href="{{ route('products.show', $favorite->product) }}" class="text-decoration-none">
                            <h6 style="color:#1a1a1a;">{{ $favorite->product->name }}</h6>
                        </a>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong style="color:var(--primary);">{{ $favorite->product->formatted_price }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-favorite" data-product-id="{{ $favorite->product->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-heart" style="font-size:3rem;"></i>
                <p class="mt-3">Vous n'avez pas encore de favoris. Explorez le <a href="{{ route('restaurants.index') }}">menu des restaurants</a> !</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $favorites->links() }}</div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.remove-favorite').forEach(function (btn) {
    btn.addEventListener('click', function () {
        fetch('{{ route('profile.favorites.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ product_id: this.dataset.productId }),
        })
        .then(r => r.json())
        .then(() => location.reload());
    });
});
</script>
@endpush
@endsection
