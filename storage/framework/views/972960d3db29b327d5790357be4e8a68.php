<?php $__env->startSection('title', 'Réserver une table — ' . $restaurant->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width:600px;">
    <h2 class="mb-1">Réserver une table</h2>
    <p class="text-muted mb-4"><?php echo e($restaurant->name); ?> — <?php echo e($restaurant->city); ?></p>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('reservations.store', $restaurant)); ?>" class="bg-white rounded-4 shadow-sm p-4">
        <?php echo csrf_field(); ?>
        <div class="row g-3">
            <div class="col-6">
                <label class="form-label fw-bold">Date</label>
                <input type="date" name="date" class="form-control" min="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e(old('date')); ?>" required>
            </div>
            <div class="col-6">
                <label class="form-label fw-bold">Heure</label>
                <input type="time" name="time" class="form-control" value="<?php echo e(old('time')); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Nombre de personnes</label>
                <input type="number" name="guests_count" class="form-control" min="1" max="30" value="<?php echo e(old('guests_count', 2)); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Votre nom</label>
                <input type="text" name="customer_name" class="form-control" value="<?php echo e(old('customer_name', auth()->user()->first_name . ' ' . auth()->user()->last_name)); ?>" required>
            </div>
            <div class="col-6">
                <label class="form-label fw-bold">Téléphone</label>
                <input type="text" name="customer_phone" class="form-control" value="<?php echo e(old('customer_phone', auth()->user()->phone)); ?>" placeholder="6XX XXX XXX" required>
            </div>
            <div class="col-6">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="customer_email" class="form-control" value="<?php echo e(old('customer_email', auth()->user()->email)); ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Demandes particulières (optionnel)</label>
                <textarea name="special_requests" class="form-control" rows="3" maxlength="500" placeholder="Anniversaire, table près de la fenêtre..."><?php echo e(old('special_requests')); ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn w-100 py-3 mt-4" style="background:var(--primary);color:#fff;border-radius:14px;">
            <i class="bi bi-calendar-check"></i> Confirmer la demande de réservation
        </button>
        <p class="text-muted small mt-2 mb-0">Le restaurant confirmera votre réservation par téléphone ou email.</p>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/reservations/create.blade.php ENDPATH**/ ?>