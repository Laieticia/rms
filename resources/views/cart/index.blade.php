@extends('layouts.storefront')

@section('title', 'Mon panier')

@push('styles')
<style>
    .cart-table { background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,0.05); }
    .cart-table th { background:var(--primary); color:#fff; padding:15px 20px; font-weight:600; border:none; }
    .cart-table td { padding:20px; vertical-align:middle; border-bottom:1px solid #eee; }
    .cart-item-img { width:70px; height:70px; border-radius:12px; object-fit:cover; }
    .cart-item-title { font-weight:600; color:#333; margin-bottom:3px; }
    .cart-item-opt { font-size:12px; color:#888; }
    .qty-form { display:flex; align-items:center; gap:6px; }
    .qty-btn { width:32px; height:32px; border-radius:8px; background:#f0f0f0; border:none; font-weight:bold; }
    .qty-btn:hover { background:var(--primary); color:#fff; }
    .qty-input { width:46px; text-align:center; border:1px solid #ddd; border-radius:8px; padding:5px; }
    .remove-item { color:#ff4757; background:none; border:none; font-size:18px; }
    .remove-item:hover { color:#e63946; }
    .cart-summary { background:#fff; border-radius:20px; padding:25px; box-shadow:0 5px 20px rgba(0,0,0,0.05); position:sticky; top:100px; }
    .summary-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px dashed #ddd; }
    .summary-total { font-size:20px; font-weight:700; color:var(--primary); border-bottom:none; padding-top:15px; }
    .empty-cart { text-align:center; padding:80px 20px; }
    .empty-cart i { font-size:70px; color:#ddd; margin-bottom:20px; }
    .coupon-input { border:1px solid #ddd; border-radius:30px; padding:10px 15px; width:100%; margin-bottom:10px; }
    .apply-btn { background:#333; color:#fff; border:none; border-radius:30px; padding:10px 20px; width:100%; }
    .apply-btn:hover { background:var(--primary); }
</style>
@endpush

@section('content')
<div class="container py-5" style="padding-top:120px;">
    <div class="text-center mb-5">
        <span class="slbl">Votre panier</span>
        <h2 class="stitle">Mon <span>panier</span></h2>
        <div class="sline"></div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (count($cartItems) === 0)
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h4>Votre panier est vide</h4>
            <p class="text-muted">Ajoutez de délicieux plats depuis nos restaurants !</p>
            <a href="{{ route('restaurants.index') }}" class="btn-red">Parcourir les restaurants</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-table">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Sous-total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $index => $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $item['product']->primary_image_url ?? url('website/assets/img/menu1.jpg') }}" class="cart-item-img" alt="{{ $item['product']->name }}">
                                            <div>
                                                <div class="cart-item-title">{{ $item['product']->name }}</div>
                                                @if(!empty($item['options']))
                                                    <div class="cart-item-opt">
                                                        {{ collect($item['options'])->pluck('name')->join(', ') }}
                                                    </div>
                                                @endif
                                                @if(!empty($item['notes']))
                                                    <div class="cart-item-opt"><em>{{ $item['notes'] }}</em></div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \App\Helpers\CameroonHelper::formatCurrency($item['product']->price) }}</td>
                                    <td>
                                        <form action="{{ route('cart.update') }}" method="POST" class="qty-form update-qty-form">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="items[0][index]" value="{{ $index }}">
                                            <button type="button" class="qty-btn qty-minus">-</button>
                                            <input type="number" name="items[0][quantity]" class="qty-input" value="{{ $item['quantity'] }}" min="1" onchange="this.form.submit()">
                                            <button type="button" class="qty-btn qty-plus">+</button>
                                        </form>
                                    </td>
                                    <td>{{ \App\Helpers\CameroonHelper::formatCurrency($item['total']) }}</td>
                                    <td>
                                        <a href="{{ route('cart.remove', $index) }}" class="remove-item" onclick="return confirm('Retirer ce produit du panier ?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="{{ route('restaurants.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-arrow-left"></i> Continuer mes achats
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Vider tout le panier ?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-trash"></i> Vider le panier
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 class="mb-3">Résumé de la commande</h5>
                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span>{{ \App\Helpers\CameroonHelper::formatCurrency($subtotal) }}</span>
                    </div>
                    @if($discount > 0)
                        <div class="summary-row">
                            <span>Remise @if($coupon)({{ $coupon->code }})@endif</span>
                            <span>-{{ \App\Helpers\CameroonHelper::formatCurrency($discount) }}</span>
                        </div>
                    @endif
                    <div class="summary-row summary-total">
                        <strong>Total</strong>
                        <strong>{{ \App\Helpers\CameroonHelper::formatCurrency($total) }}</strong>
                    </div>
                    <small class="text-muted d-block mb-3">Frais de livraison et taxes calculés à l'étape suivante.</small>

                    @if($coupon)
                        <form action="{{ route('cart.coupon.remove') }}" method="POST" class="mb-3">
                            @csrf
                            <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-0">
                                <span><i class="fas fa-tag me-1"></i>{{ $coupon->code }}</span>
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">Retirer</button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('cart.coupon') }}" method="POST" class="mb-3">
                            @csrf
                            <input type="text" name="code" class="coupon-input" placeholder="Code promo">
                            <button type="submit" class="apply-btn">Appliquer le code</button>
                        </form>
                    @endif

                    <a href="{{ route('checkout.index') }}" class="btn-red w-100 mt-2 justify-content-center">
                        <i class="fas fa-credit-card"></i> Passer la commande
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.update-qty-form').forEach(function (form) {
        var input = form.querySelector('.qty-input');
        form.querySelector('.qty-plus').addEventListener('click', function () {
            input.value = parseInt(input.value || 1) + 1;
            form.submit();
        });
        form.querySelector('.qty-minus').addEventListener('click', function () {
            var val = parseInt(input.value || 1) - 1;
            if (val >= 1) {
                input.value = val;
                form.submit();
            }
        });
    });
</script>
@endpush
