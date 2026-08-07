<?php $__env->startSection('title', 'Espace Livreur'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-page">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0"><i class="bi bi-bicycle me-2"></i>Mes courses</h3>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-<?php echo e($order->status === 'in_delivery' ? 'primary' : 'warning'); ?>">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">#<?php echo e($order->order_number); ?></span>
                        <span class="badge bg-<?php echo e($order->status_color); ?>">
                            <i class="bi bi-<?php echo e($order->status_icon); ?> me-1"></i><?php echo e($order->status_label); ?>

                        </span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-shop me-1"></i><?php echo e($order->restaurant->name); ?>

                        </h6>
                        <p class="card-text mb-1">
                            <i class="bi bi-geo-alt text-danger me-1"></i>
                            <strong>Livrer à:</strong> <?php echo e($order->delivery_address); ?>

                        </p>
                        <?php if($order->delivery_city): ?>
                            <p class="card-text mb-2 text-muted ms-4">
                                <?php echo e($order->delivery_city); ?> <?php echo e($order->delivery_postal_code); ?>

                            </p>
                        <?php endif; ?>
                        <p class="card-text mb-0">
                            <i class="bi bi-person me-1"></i><?php echo e($order->user->full_name); ?>

                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 text-end">
                        <a href="<?php echo e(route('delivery.show', $order)); ?>" class="btn btn-<?php echo e($order->status === 'in_delivery' ? 'primary' : 'warning'); ?> w-100">
                            Voir les détails <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <div class="display-1 text-muted mb-3"><i class="bi bi-emoji-smile"></i></div>
                <h4 class="text-muted">Aucune course assignée pour le moment</h4>
                <p>Les nouvelles courses apparaîtront ici.</p>
                <button onclick="window.location.reload()" class="btn btn-outline-primary mt-2">
                    <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/delivery/dashboard.blade.php ENDPATH**/ ?>