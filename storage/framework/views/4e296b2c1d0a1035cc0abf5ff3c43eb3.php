<?php $__env->startSection('title', 'Mes réservations'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-calendar-check me-2"></i>Mes réservations</h2>

    <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="text-decoration-none">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="mb-1" style="color:#1a1a1a;"><?php echo e($reservation->restaurant->name); ?></h6>
                    <div class="text-muted small"><?php echo e($reservation->date->format('d/m/Y')); ?> à <?php echo e($reservation->time->format('H:i')); ?> · <?php echo e($reservation->guests_count); ?> pers.</div>
                </div>
                <span class="badge <?php echo e($reservation->status === 'confirmed' ? 'bg-success' : ($reservation->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark')); ?>">
                    <?php echo e(ucfirst($reservation->status)); ?>

                </span>
            </div>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-calendar-x" style="font-size:3rem;"></i>
            <p class="mt-3">Vous n'avez pas encore de réservation.</p>
        </div>
    <?php endif; ?>

    <div class="mt-4"><?php echo e($reservations->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/reservations/index.blade.php ENDPATH**/ ?>