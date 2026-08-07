<?php $__env->startSection('title', 'Finaliser la commande'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="mb-4">Finaliser votre commande</h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('checkout.process')); ?>" id="checkoutForm">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-lg-7">
                
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h5 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Adresse de livraison</h5>
                    <?php $__empty_1 = true; $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="form-check border rounded-3 p-3 mb-2">
                            <input class="form-check-input" type="radio" name="address_id" id="addr<?php echo e($address->id); ?>"
                                   value="<?php echo e($address->id); ?>" <?php echo e((old('address_id', $defaultAddress?->id) == $address->id) ? 'checked' : ''); ?> required>
                            <label class="form-check-label w-100" for="addr<?php echo e($address->id); ?>">
                                <strong><?php echo e($address->label ?? 'Adresse'); ?></strong>
                                <?php if($address->is_default): ?><span class="badge bg-secondary ms-2">Par défaut</span><?php endif; ?>
                                <br>
                                <span class="text-muted"><?php echo e($address->street_address); ?>, <?php echo e($address->city); ?></span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted">Vous n'avez pas encore d'adresse enregistrée.</p>
                    <?php endif; ?>
                    <a href="<?php echo e(route('profile.index')); ?>" class="small">+ Ajouter une nouvelle adresse</a>
                </div>

                
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <h5 class="mb-3"><i class="bi bi-phone me-2"></i>Mode de paiement</h5>
                    <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check border rounded-3 p-3 mb-2">
                            <input class="form-check-input payment-method-input" type="radio" name="payment_method"
                                   id="pm_<?php echo e($key); ?>" value="<?php echo e($key); ?>" data-needs-phone="<?php echo e($key === 'cash' ? '0' : '1'); ?>"
                                   <?php echo e(old('payment_method') == $key ? 'checked' : ''); ?> required>
                            <label class="form-check-label" for="pm_<?php echo e($key); ?>">
                                <?php if($key === 'cash'): ?> <i class="bi bi-cash-stack me-1"></i>
                                <?php else: ?> <i class="bi bi-phone-vibrate me-1"></i> <?php endif; ?>
                                <?php echo e($method['label']); ?>

                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div id="phoneField" class="mt-3" style="display:none;">
                        <label class="form-label">Numéro Mobile Money</label>
                        <input type="text" name="payment_phone" class="form-control" placeholder="Ex: 6XX XXX XXX" value="<?php echo e(old('payment_phone')); ?>">
                        <small class="text-muted">Vous recevrez une demande de confirmation sur ce numéro.</small>
                    </div>
                </div>

                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <label class="form-label fw-bold">Instructions de livraison (optionnel)</label>
                    <textarea name="delivery_instructions" class="form-control" rows="2" maxlength="500"><?php echo e(old('delivery_instructions')); ?></textarea>
                    <label class="form-label fw-bold mt-3">Notes pour le restaurant (optionnel)</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="500"><?php echo e(old('notes')); ?></textarea>
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
                    <h5 class="mb-3">Récapitulatif — <?php echo e($restaurant->name); ?></h5>
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between small mb-2">
                            <span><?php echo e($item['quantity']); ?> x <?php echo e($item['product']->name); ?></span>
                            <span><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($item['total'])); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Sous-total</span><span><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($subtotal)); ?></span></div>
                    <?php if($discount > 0): ?>
                        <div class="d-flex justify-content-between text-success"><span>Réduction (<?php echo e($couponCode); ?>)</span><span>-<?php echo e(\App\Helpers\CameroonHelper::formatCurrency($discount)); ?></span></div>
                    <?php endif; ?>
                    <?php if($taxAmount > 0): ?>
                        <div class="d-flex justify-content-between"><span>Taxes</span><span><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($taxAmount)); ?></span></div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between"><span>Livraison</span><span><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($deliveryFee)); ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Total</span>
                        <span style="color:var(--primary);"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($total)); ?></span>
                    </div>
                    <button type="submit" class="btn w-100 py-3" style="background:var(--primary);color:#fff;border-radius:14px;">
                        <i class="bi bi-lock"></i> Confirmer et payer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/checkout/index.blade.php ENDPATH**/ ?>