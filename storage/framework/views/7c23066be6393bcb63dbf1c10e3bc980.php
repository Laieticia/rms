<?php $__env->startSection('title', 'Mon panier'); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="padding-top:120px;">
    <div class="text-center mb-5">
        <span class="slbl">Votre panier</span>
        <h2 class="stitle">Mon <span>panier</span></h2>
        <div class="sline"></div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if(count($cartItems) === 0): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h4>Votre panier est vide</h4>
            <p class="text-muted">Ajoutez de délicieux plats depuis nos restaurants !</p>
            <a href="<?php echo e(route('restaurants.index')); ?>" class="btn-red">Parcourir les restaurants</a>
        </div>
    <?php else: ?>
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
                            <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?php echo e($item['product']->primary_image_url ?? url('website/assets/img/menu1.jpg')); ?>" class="cart-item-img" alt="<?php echo e($item['product']->name); ?>">
                                            <div>
                                                <div class="cart-item-title"><?php echo e($item['product']->name); ?></div>
                                                <?php if(!empty($item['options'])): ?>
                                                    <div class="cart-item-opt">
                                                        <?php echo e(collect($item['options'])->pluck('name')->join(', ')); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <?php if(!empty($item['notes'])): ?>
                                                    <div class="cart-item-opt"><em><?php echo e($item['notes']); ?></em></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($item['product']->price)); ?></td>
                                    <td>
                                        <form action="<?php echo e(route('cart.update')); ?>" method="POST" class="qty-form update-qty-form">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <input type="hidden" name="items[0][index]" value="<?php echo e($index); ?>">
                                            <button type="button" class="qty-btn qty-minus">-</button>
                                            <input type="number" name="items[0][quantity]" class="qty-input" value="<?php echo e($item['quantity']); ?>" min="1" onchange="this.form.submit()">
                                            <button type="button" class="qty-btn qty-plus">+</button>
                                        </form>
                                    </td>
                                    <td><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($item['total'])); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('cart.remove', $index)); ?>" class="remove-item" onclick="return confirm('Retirer ce produit du panier ?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="<?php echo e(route('restaurants.index')); ?>" class="btn btn-outline-danger">
                        <i class="fas fa-arrow-left"></i> Continuer mes achats
                    </a>
                    <form action="<?php echo e(route('cart.clear')); ?>" method="POST" onsubmit="return confirm('Vider tout le panier ?')">
                        <?php echo csrf_field(); ?>
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
                        <span><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($subtotal)); ?></span>
                    </div>
                    <?php if($discount > 0): ?>
                        <div class="summary-row">
                            <span>Remise <?php if($coupon): ?>(<?php echo e($coupon->code); ?>)<?php endif; ?></span>
                            <span>-<?php echo e(\App\Helpers\CameroonHelper::formatCurrency($discount)); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-row summary-total">
                        <strong>Total</strong>
                        <strong><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($total)); ?></strong>
                    </div>
                    <small class="text-muted d-block mb-3">Frais de livraison et taxes calculés à l'étape suivante.</small>

                    <?php if($coupon): ?>
                        <form action="<?php echo e(route('cart.coupon.remove')); ?>" method="POST" class="mb-3">
                            <?php echo csrf_field(); ?>
                            <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-0">
                                <span><i class="fas fa-tag me-1"></i><?php echo e($coupon->code); ?></span>
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">Retirer</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <form action="<?php echo e(route('cart.coupon')); ?>" method="POST" class="mb-3">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="code" class="coupon-input" placeholder="Code promo">
                            <button type="submit" class="apply-btn">Appliquer le code</button>
                        </form>
                    <?php endif; ?>

                    <a href="<?php echo e(route('checkout.index')); ?>" class="btn-red w-100 mt-2 justify-content-center">
                        <i class="fas fa-credit-card"></i> Passer la commande
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/cart/index.blade.php ENDPATH**/ ?>