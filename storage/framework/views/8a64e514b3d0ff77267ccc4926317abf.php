<?php $__env->startSection('title', 'Gestion des Commandes'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-page">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4"><i class="bi bi-cart-check me-2"></i>Commandes</h3>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <?php $__currentLoopData = ['pending'=>'En attente|warning','preparing'=>'En préparation|info','ready'=>'Prêtes|success','in_delivery'=>'En livraison|primary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php [$label, $color] = explode('|', $info); ?>
                <div class="col-md-3">
                    <div class="card bg-<?php echo e($color); ?> text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-0"><?php echo e($stats[$status] ?? 0); ?></h4>
                            <small><?php echo e($label); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('admin.orders.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Tous statuts</option>
                            <?php $__currentLoopData = ['pending'=>'En attente','confirmed'=>'Confirmée','preparing'=>'En préparation','ready'=>'Prête','in_delivery'=>'En livraison','delivered'=>'Livrée','completed'=>'Terminée','cancelled'=>'Annulée']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(request('status')==$val?'selected':''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-control">
                            <option value="">Tous types</option>
                            <option value="dine_in" <?php echo e(request('type')=='dine_in'?'selected':''); ?>>Sur place</option>
                            <option value="takeaway" <?php echo e(request('type')=='takeaway'?'selected':''); ?>>À emporter</option>
                            <option value="delivery" <?php echo e(request('type')=='delivery'?'selected':''); ?>>Livraison</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" placeholder="Du" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" placeholder="Au" value="<?php echo e(request('date_to')); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="search" class="form-control" placeholder="N° commande ou client..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100">Filtrer</button>
                    </div>
                    <div class="col-md-1">
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Articles</th>
                                <th>Total</th>
                                <th>Paiement</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong>#<?php echo e($order->order_number); ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo e($order->user->avatar_url); ?>" class="rounded-circle me-2" width="30" height="30">
                                            <div>
                                                <strong><?php echo e($order->user->full_name); ?></strong>
                                                <br><small class="text-muted"><?php echo e($order->user->phone); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($order->type=='dine_in'?'info':($order->type=='takeaway'?'warning':'primary')); ?>">
                                            <?php echo e($order->type=='dine_in'?'🏠':($order->type=='takeaway'?'🥡':'🛵')); ?>

                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary"><?php echo e($order->items->sum('quantity')); ?></span></td>
                                    <td><strong><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->total)); ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($order->payment_status=='paid'?'success':'warning'); ?>">
                                            <?php echo e($order->payment_status=='paid'?'Payé':'En attente'); ?>

                                        </span>
                                    </td>
                                    <td><span class="badge bg-<?php echo e($order->status_color); ?>"><?php echo e($order->status_label); ?></span></td>
                                    <td><small><?php echo e($order->created_at->format('d/m/Y H:i')); ?></small></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn btn-sm btn-primary">
                                            <i class="ri-eye-line mr-0"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="9" class="text-center py-5">Aucune commande</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><?php echo e($orders->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>