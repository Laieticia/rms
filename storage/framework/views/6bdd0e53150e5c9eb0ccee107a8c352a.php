<?php $__env->startSection('title', 'Commande #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Commande #<?php echo e($order->order_number); ?></h2>
        <span class="badge fs-6
            <?php if($order->status === 'cancelled'): ?> bg-danger
            <?php elseif(in_array($order->status, ['delivered','completed'])): ?> bg-success
            <?php else: ?> bg-warning text-dark <?php endif; ?>">
            <?php echo e($order->status_label); ?>

        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h5 class="mb-3"><?php echo e($order->restaurant->name); ?></h5>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <div><?php echo e($item->quantity); ?> x <?php echo e($item->product_name); ?></div>
                            <?php if($item->special_instructions): ?>
                                <small class="text-muted"><?php echo e($item->special_instructions); ?></small>
                            <?php endif; ?>
                        </div>
                        <div><?php echo e($item->formatted_total_price); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex justify-content-between pt-3">
                    <span>Sous-total</span><span><?php echo e($order->formatted_subtotal); ?></span>
                </div>
                <?php if($order->discount_amount > 0): ?>
                    <div class="d-flex justify-content-between text-success"><span>Réduction</span><span>-<?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->discount_amount)); ?></span></div>
                <?php endif; ?>
                <div class="d-flex justify-content-between"><span>Livraison</span><span><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->delivery_fee)); ?></span></div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span style="color:var(--primary);"><?php echo e($order->formatted_total); ?></span></div>
            </div>

            <?php if(!in_array($order->status, ['delivered','completed','cancelled'])): ?>
                <a href="<?php echo e(route('orders.track', $order)); ?>" class="btn w-100 py-3 mb-4" style="background:var(--primary);color:#fff;border-radius:14px;">
                    <i class="bi bi-geo-alt"></i> Suivre ma livraison
                </a>
            <?php endif; ?>

            <?php if($order->canBeReviewedBy(auth()->user())): ?>
                <a href="<?php echo e(route('orders.review.create', $order)); ?>" class="btn btn-outline-warning w-100 py-3 mb-4">
                    <i class="bi bi-star"></i> Laisser un avis sur cette commande
                </a>
            <?php endif; ?>
        </div>

        <div class="col-lg-5">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h6 class="mb-3">Livraison</h6>
                <p class="text-muted mb-1"><i class="bi bi-geo-alt me-2"></i><?php echo e($order->delivery_address); ?>, <?php echo e($order->delivery_city); ?></p>
                <?php if($order->delivery_instructions): ?>
                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-2"></i><?php echo e($order->delivery_instructions); ?></p>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-4 shadow-sm p-4">
                <h6 class="mb-3">Historique</h6>
                <ul class="list-unstyled mb-0">
                    <?php $__currentLoopData = $order->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="mb-2">
                            <strong><?php echo e($history->status); ?></strong>
                            <div class="text-muted small"><?php echo e($history->created_at->format('d/m/Y à H:i')); ?></div>
                            <?php if($history->comment): ?>
                                <div class="small"><?php echo e($history->comment); ?></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/profile/order-detail.blade.php ENDPATH**/ ?>