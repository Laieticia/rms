@extends('layouts.storefront')

@section('title', 'Noter votre commande')

@section('content')
<div class="container py-5" style="max-width:600px;">
    <h2 class="mb-4">Comment était votre commande ?</h2>
    <p class="text-muted mb-4">{{ $order->restaurant->name }} — Commande #{{ $order->order_number }}</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('orders.review', $order) }}" class="bg-white rounded-4 shadow-sm p-4">
        @csrf

        @php
            $ratingFields = [
                'rating' => 'Note globale',
                'food_rating' => 'Qualité des plats',
                'delivery_rating' => 'Livraison',
                'service_rating' => 'Service',
            ];
        @endphp

        @foreach($ratingFields as $field => $label)
            <div class="mb-4">
                <label class="form-label fw-bold">{{ $label }} @if($field === 'rating')<span class="text-danger">*</span>@endif</label>
                <div class="star-rating" data-field="{{ $field }}">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star star-icon" data-value="{{ $i }}" style="font-size:28px;cursor:pointer;color:#ddd;"></i>
                    @endfor
                </div>
                <input type="hidden" name="{{ $field }}" id="input_{{ $field }}" value="{{ old($field) }}" {{ $field === 'rating' ? 'required' : '' }}>
            </div>
        @endforeach

        <div class="mb-4">
            <label class="form-label fw-bold">Votre commentaire (optionnel)</label>
            <textarea name="comment" class="form-control" rows="4" maxlength="1000" placeholder="Partagez votre expérience...">{{ old('comment') }}</textarea>
        </div>

        <button type="submit" class="btn w-100 py-3" style="background:var(--primary);color:#fff;border-radius:14px;">
            <i class="bi bi-send"></i> Envoyer mon avis
        </button>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('.star-rating').forEach(function (group) {
    const field = group.dataset.field;
    const input = document.getElementById('input_' + field);
    const stars = group.querySelectorAll('.star-icon');

    function paint(value) {
        stars.forEach(function (star) {
            const active = parseInt(star.dataset.value) <= value;
            star.className = active ? 'bi bi-star-fill star-icon' : 'bi bi-star star-icon';
            star.style.color = active ? '#ffc107' : '#ddd';
        });
    }

    stars.forEach(function (star) {
        star.addEventListener('click', function () {
            input.value = this.dataset.value;
            paint(parseInt(this.dataset.value));
        });
    });

    if (input.value) { paint(parseInt(input.value)); }
});
</script>
@endpush
@endsection
