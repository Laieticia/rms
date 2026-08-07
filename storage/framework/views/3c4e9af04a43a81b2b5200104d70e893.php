<?php $__env->startSection('title', 'Réservation ' . $reservation->reservation_number); ?>

<?php $__env->startSection('content'); ?>
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Réservation <?php echo e($reservation->reservation_number); ?></h3>
            <a href="<?php echo e(route('admin.reservations.index')); ?>" class="btn btn-outline-secondary btn-sm">Retour</a>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Détails</h5>
                        <table class="table">
                            <tr><th>Client</th><td><?php echo e($reservation->customer_name); ?></td></tr>
                            <tr><th>Téléphone</th><td><?php echo e($reservation->customer_phone); ?></td></tr>
                            <tr><th>Email</th><td><?php echo e($reservation->customer_email); ?></td></tr>
                            <tr><th>Date</th><td><?php echo e($reservation->date->format('d/m/Y')); ?></td></tr>
                            <tr><th>Heure</th><td><?php echo e($reservation->time->format('H:i')); ?></td></tr>
                            <tr><th>Nombre de personnes</th><td><?php echo e($reservation->guests_count); ?></td></tr>
                            <tr><th>Table</th><td><?php echo e($reservation->table_number ?? 'Non attribuée'); ?></td></tr>
                            <tr><th>Demandes particulières</th><td><?php echo e($reservation->special_requests ?? '—'); ?></td></tr>
                            <tr>
                                <th>Statut</th>
                                <td>
                                    <span class="badge bg-<?php echo e(match($reservation->status) {
                                        'confirmed' => 'success',
                                        'cancelled' => 'danger',
                                        'completed' => 'secondary',
                                        default => 'warning',
                                    }); ?>">
                                        <?php echo e(ucfirst($reservation->status)); ?>

                                    </span>
                                </td>
                            </tr>
                            <?php if($reservation->status === 'cancelled' && $reservation->cancellation_reason): ?>
                                <tr><th>Motif d'annulation</th><td><?php echo e($reservation->cancellation_reason); ?></td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <?php if($reservation->status === 'pending'): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="mb-3">Confirmer la réservation</h6>
                            <form action="<?php echo e(route('admin.reservations.confirm', $reservation)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label">Numéro de table (optionnel)</label>
                                    <input type="text" name="table_number" class="form-control" placeholder="Ex: T12">
                                </div>
                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg"></i> Confirmer</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Refuser la réservation</h6>
                            <form action="<?php echo e(route('admin.reservations.cancel', $reservation)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label">Motif</label>
                                    <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Ex: Complet à cette heure-ci"></textarea>
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-x-lg"></i> Refuser</button>
                            </form>
                        </div>
                    </div>
                <?php elseif($reservation->status === 'confirmed'): ?>
                    <div class="card">
                        <div class="card-body">
                            <form action="<?php echo e(route('admin.reservations.complete', $reservation)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-check2-all"></i> Marquer comme terminée</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/admin/reservations/show.blade.php ENDPATH**/ ?>