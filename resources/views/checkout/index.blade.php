@extends('layouts.storefront')

@section('title', 'Finaliser la commande')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Finaliser votre commande</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                {{-- Adresse de livraison --}}
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h5 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Adresse de livraison</h5>
                    @forelse($addresses as $address)
                        <div class="form-check border rounded-3 p-3 mb-2">
                            <input class="form-check-input" type="radio" name="address_id" id="addr{{ $address->id }}"
                                   value="{{ $address->id }}" {{ (old('address_id', $defaultAddress?->id) == $address->id) ? 'checked' : '' }} required>
                            <label class="form-check-label w-100" for="addr{{ $address->id }}">
                                <strong>{{ $address->label ?? 'Adresse' }}</strong>
                                @if($address->is_default)<span class="badge bg-secondary ms-2">Par défaut</span>@endif
                                <br>
                                <span class="text-muted">{{ $address->street_address }}, {{ $address->city }}</span>
                            </label>
                        </div>
                    @empty
                        <p class="text-muted">Vous n'avez pas encore d'adresse enregistrée.</p>
                    @endforelse
                    <a href="{{ route('profile.index') }}" class="small">+ Ajouter une nouvelle adresse</a>
                </div>

                {{-- Mode de paiement --}}
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h5 class="mb-3"><i class="bi bi-phone me-2"></i>Mode de paiement</h5>
                    @foreach($paymentMethods as $key => $method)
                        <div class="form-check border rounded-3 p-3 mb-2">
                            <input class="form-check-input payment-method-input" type="radio" name="payment_method"
                                   id="pm_{{ $key }}" value="{{ $key }}" data-needs-phone="{{ $key === 'cash' ? '0' : '1' }}"
                                   {{ old('payment_method') == $key ? 'checked' : '' }} required>
                            <label class="form-check-label" for="pm_{{ $key }}">
                                @if($key === 'cash') <i class="bi bi-cash-stack me-1"></i>
                                @else <i class="bi bi-phone-vibrate me-1"></i> @endif
                                {{ $method['label'] }}
                            </label>
                        </div>
                    @endforeach

                    <div id="phoneField" class="mt-3" style="display:none;">
                        <label class="form-label">Numéro Mobile Money</label>
                        <input type="text" name="payment_phone" class="form-control" placeholder="Ex: 6XX XXX XXX" value="{{ old('payment_phone') }}">
                        <small class="text-muted">Vous recevrez une demande de confirmation sur ce numéro.</small>
                    </div>
                </div>

                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <label class="form-label fw-bold">Instructions de livraison (optionnel)</label>
                    <textarea name="delivery_instructions" class="form-control" rows="2" maxlength="500">{{ old('delivery_instructions') }}</textarea>
                    <label class="form-label fw-bold mt-3">Notes pour le restaurant (optionnel)</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="500">{{ old('notes') }}</textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        J'accepte les conditions générales de vente
                    </label>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="bg-white rounded-4 shadow-sm p-4" style="position:sticky;top:20px;">
                    <h5 class="mb-3">Récapitulatif — {{ $restaurant->name }}</h5>
                    @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between small mb-2">
                            <span>{{ $item['quantity'] }} x {{ $item['product']->name }}</span>
                            <span>{{ \App\Helpers\CameroonHelper::formatCurrency($item['total']) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between"><span>Sous-total</span><span>{{ \App\Helpers\CameroonHelper::formatCurrency($subtotal) }}</span></div>
                    @if($discount > 0)
                        <div class="d-flex justify-content-between text-success"><span>Réduction ({{ $couponCode }})</span><span>-{{ \App\Helpers\CameroonHelper::formatCurrency($discount) }}</span></div>
                    @endif
                    @if($taxAmount > 0)
                        <div class="d-flex justify-content-between"><span>Taxes</span><span>{{ \App\Helpers\CameroonHelper::formatCurrency($taxAmount) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between"><span>Livraison</span><span>{{ \App\Helpers\CameroonHelper::formatCurrency($deliveryFee) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Total</span>
                        <span style="color:var(--primary);">{{ \App\Helpers\CameroonHelper::formatCurrency($total) }}</span>
                    </div>
                    <button type="submit" class="btn w-100 py-3" style="background:var(--primary);color:#fff;border-radius:14px;">
                        <i class="bi bi-lock"></i> Confirmer et payer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneField = document.getElementById('phoneField');
    document.querySelectorAll('.payment-method-input').forEach(function (input) {
        input.addEventListener('change', function () {
            phoneField.style.display = this.dataset.needsPhone === '1' ? 'block' : 'none';
        });
        if (input.checked) {
            phoneField.style.display = input.dataset.needsPhone === '1' ? 'block' : 'none';
        }
    });
});
</script>
@endpush
@endsection
