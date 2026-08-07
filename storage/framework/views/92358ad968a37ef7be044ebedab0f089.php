<?php $__env->startSection('title', 'Mes commandes'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-receipt me-2"></i>Mes commandes</h2>

    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-4 shadow-sm p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="mb-1">#<?php echo e($order->order_number); ?> — <?php echo e($order->restaurant->name); ?></h6>
                    <div class="text-muted small"><?php echo e($order->created_at->format('d/m/Y à H:i')); ?> · <?php echo e($order->items->count()); ?> article(s)</div>
                </div>
                <div class="text-center">
                    <span class="badge
                        <?php if($order->status === 'cancelled'): ?> bg-danger
                        <?php elseif(in_array($order->status, ['delivered','completed'])): ?> bg-success
                        <?php else: ?> bg-warning text-dark <?php endif; ?>">
                        <?php echo e($order->status_label); ?>

                    </span>
                </div>
                <div class="fw-bold" style="color:var(--primary);"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->total)); ?></div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('profile.orders.show', $order)); ?>" class="btn btn-sm btn-outline-secondary">Détails</a>
                    <?php if(!in_array($order->status, ['delivered','completed','cancelled'])): ?>
                        <a href="<?php echo e(route('orders.track', $order)); ?>" class="btn btn-sm" style="background:var(--primary);color:#fff;">Suivre</a>
                    <?php endif; ?>
                    <?php if(($order->canBeReviewedBy(auth()->user())) ?? false): ?>
                        <a href="<?php echo e(route('orders.review.create', $order)); ?>" class="btn btn-sm btn-outline-warning">Noter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt" style="font-size:3rem;"></i>
            <p class="mt-3">Vous n'avez pas encore passé de commande. <a href="<?php echo e(route('restaurants.index')); ?>">Découvrez nos restaurants</a>.</p>
        </div>
    <?php endif; ?>

    <div class="mt-4"><?php echo e($orders->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/profile/orders.blade.php ENDPATH**/ ?>