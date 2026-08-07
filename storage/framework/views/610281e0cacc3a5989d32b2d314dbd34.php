<?php $__env->startSection('title', 'Réservations'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-page">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4"><i class="bi bi-calendar-check me-2"></i>Réservations</h3>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0"><?php echo e($stats['pending']); ?></h4>
                        <small>En attente de confirmation</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0"><?php echo e($stats['confirmed_today']); ?></h4>
                        <small>Confirmées aujourd'hui</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0"><?php echo e($stats['completed_today']); ?></h4>
                        <small>Terminées aujourd'hui</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0"><?php echo e($stats['total_today']); ?></h4>
                        <small>Total aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('admin.reservations.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tous statuts</option>
                            <?php $__currentLoopData = ['pending'=>'En attente','confirmed'=>'Confirmée','cancelled'=>'Annulée','completed'=>'Terminée']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(request('status')==$val?'selected':''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date / Heure</th>
                            <th>Personnes</th>
                            <th>Table</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($reservation->reservation_number); ?></td>
                                <td>
                                    <?php echo e($reservation->customer_name); ?>

                                    <div class="small text-muted"><?php echo e($reservation->customer_phone); ?></div>
                                </td>
                                <td><?php echo e($reservation->date->format('d/m/Y')); ?> à <?php echo e($reservation->time->format('H:i')); ?></td>
                                <td><?php echo e($reservation->guests_count); ?></td>
                                <td><?php echo e($reservation->table_number ?? '—'); ?></td>
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
                                <td>
                                    <a href="<?php echo e(route('admin.reservations.show', $reservation)); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if($reservation->status === 'confirmed'): ?>
                                        <form action="<?php echo e(route('admin.reservations.complete', $reservation)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marquer comme terminée">
                                                <i class="bi bi-check2-all"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucune réservation.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php echo e($reservations->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/admin/reservations/index.blade.php ENDPATH**/ ?>