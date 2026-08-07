<?php $__env->startSection('title', 'Commande #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<div class="content-page">
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-receipt me-2"></i>Commande #<?php echo e($order->order_number); ?>

                </h3>
                <small class="text-muted"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></small>
            </div>
            <div>
                <a href="<?php echo e(route('admin.orders.print', $order)); ?>" class="btn btn-outline-secondary me-2" target="_blank">
                    <i class="bi bi-printer"></i> Imprimer
                </a>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-8">
                
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📊 Statut de la commande</h5>
                        <span class="badge bg-<?php echo e($order->status_color); ?> fs-6 px-3 py-2"><?php echo e($order->status_label); ?></span>
                    </div>
                    <div class="card-body">
                        
                        <?php
                            $statuses = [
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'preparing' => 'En préparation',
                                'ready' => 'Prête',
                                'in_delivery' => 'En livraison',
                                'delivered' => 'Livrée',
                                'completed' => 'Terminée'
                            ];
                            $statusKeys = array_keys($statuses);
                            $currentIndex = array_search($order->status, $statusKeys);
                            if ($currentIndex === false) $currentIndex = 0;
                        ?>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $index = array_search($status, $statusKeys); ?>
                                <div class="text-center flex-fill">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center 
                                        <?php echo e($index < $currentIndex ? 'bg-success text-white' : ($index == $currentIndex ? 'bg-primary text-white' : 'bg-light text-muted')); ?>"
                                        style="width:35px;height:35px;font-size:14px;">
                                        <?php if($index < $currentIndex): ?>
                                            <i class="bi bi-check-lg"></i>
                                        <?php elseif($index == $currentIndex && !in_array($order->status, ['delivered','completed'])): ?>
                                            <i class="bi bi-arrow-right"></i>
                                        <?php else: ?>
                                            <?php echo e($index + 1); ?>

                                        <?php endif; ?>
                                    </div>
                                    <br>
                                    <small class="text-muted" style="font-size:11px;"><?php echo e($label); ?></small>
                                </div>
                                <?php if(!$loop->last): ?>
                                    <div class="flex-fill d-flex align-items-center px-1">
                                        <div class="progress flex-grow-1" style="height:3px;">
                                            <div class="progress-bar <?php echo e($index < $currentIndex ? 'bg-success' : 'bg-light'); ?>" 
                                                style="width: <?php echo e($index < $currentIndex ? '100' : '0'); ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        
                        <div class="d-flex flex-wrap gap-2">
                            <?php if($order->status == 'pending'): ?>
                                <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn btn-info">
                                        <i class="bi bi-check-circle"></i> Confirmer la commande
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if($order->status == 'confirmed'): ?>
                                <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-fire"></i> Commencer la préparation
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if($order->status == 'preparing'): ?>
                                <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-all"></i> Marquer comme prête
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if($order->status == 'ready' && $order->type == 'delivery'): ?>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignDeliveryModal">
                                    <i class="bi bi-truck"></i> Assigner un livreur
                                </button>
                            <?php endif; ?>

                            <?php if($order->status == 'ready' && $order->type != 'delivery'): ?>
                                <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-all"></i> Marquer comme terminée
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if(in_array($order->status, ['in_delivery'])): ?>
                                <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-box-seam"></i> Marquer comme livrée
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if(in_array($order->status, ['delivered'])): ?>
                                <form action="<?php echo e(route('admin.orders.status', $order)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-star"></i> Terminer la commande
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if(in_array($order->status, ['pending', 'confirmed'])): ?>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal">
                                    <i class="bi bi-x-circle"></i> Annuler la commande
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🛒 Articles commandés (<?php echo e($order->items->sum('quantity')); ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-center">Prix unitaire</th>
                                        <th class="text-center">Quantité</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo e($item->product->primary_image_url ?? 'https://via.placeholder.com/40'); ?>" 
                                                        class="rounded me-3" width="40" height="40" style="object-fit:cover;">
                                                    <div>
                                                        <strong><?php echo e($item->product_name); ?></strong>
                                                        <?php if($item->special_instructions): ?>
                                                            <br><small class="text-muted fst-italic">"<?php echo e($item->special_instructions); ?>"</small>
                                                        <?php endif; ?>
                                                        <?php if($item->options->count() > 0): ?>
                                                            <br><small class="text-muted">
                                                                <?php $__currentLoopData = $item->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    + <?php echo e($option->item_name); ?> (<?php echo e(\App\Helpers\CameroonHelper::formatCurrency($option->price)); ?>)
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($item->unit_price)); ?></td>
                                            <td class="text-center"><?php echo e($item->quantity); ?></td>
                                            <td class="text-end fw-bold"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($item->total_price)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end">Sous-total</td>
                                        <td class="text-end"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->subtotal)); ?></td>
                                    </tr>
                                    <?php if($order->discount_amount > 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-end text-success">Réduction</td>
                                            <td class="text-end text-success">-<?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->discount_amount)); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="3" class="text-end">TVA</td>
                                        <td class="text-end"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->tax_amount)); ?></td>
                                    </tr>
                                    <?php if($order->delivery_fee > 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-end">Frais de livraison</td>
                                            <td class="text-end"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->delivery_fee)); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="fw-bold fs-5">
                                        <td colspan="3" class="text-end">Total</td>
                                        <td class="text-end text-primary"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($order->total)); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Historique</h5>
                    </div>
                    <div class="card-body">
                        <?php $__empty_1 = true; $__currentLoopData = $order->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="bg-<?php echo e($loop->first ? 'primary' : 'success'); ?> text-white rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width:30px;height:30px;">
                                        <i class="bi bi-<?php echo e($loop->first ? 'clock' : 'check'); ?> small"></i>
                                    </div>
                                </div>
                                <div>
                                    <strong>
                                        <?php
                                            $statusLabels = [
                                                'pending' => 'Commande créée',
                                                'confirmed' => 'Commande confirmée',
                                                'preparing' => 'En préparation',
                                                'ready' => 'Commande prête',
                                                'in_delivery' => 'En cours de livraison',
                                                'delivered' => 'Commande livrée',
                                                'completed' => 'Commande terminée',
                                                'cancelled' => 'Commande annulée',
                                            ];
                                        ?>
                                        <?php echo e($statusLabels[$history->status] ?? $history->status); ?>

                                    </strong>
                                    <?php if($history->comment): ?>
                                        <p class="mb-0 text-muted"><?php echo e($history->comment); ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <?php echo e($history->created_at->format('d/m/Y H:i')); ?> 
                                        par <?php echo e($history->user->full_name ?? 'Système'); ?>

                                    </small>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-muted text-center">Aucun historique</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">👤 Client</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo e($order->user->avatar_url); ?>" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <strong><?php echo e($order->user->full_name); ?></strong>
                                <br><small class="text-muted"><?php echo e($order->user->email); ?></small>
                            </div>
                        </div>
                        <p class="mb-1"><i class="bi bi-telephone me-2"></i><?php echo e($order->user->phone ?? 'Non renseigné'); ?></p>
                        <?php if($order->user->created_at): ?>
                            <p class="mb-0"><i class="bi bi-calendar me-2"></i>Client depuis <?php echo e($order->user->created_at->format('m/Y')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if($order->type == 'delivery'): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">📍 Livraison</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Adresse :</strong></p>
                            <p class="mb-2"><?php echo e($order->delivery_address); ?></p>
                            <p class="mb-1"><strong>Code postal :</strong> <?php echo e($order->delivery_postal_code); ?></p>
                            <p class="mb-1"><strong>Ville :</strong> <?php echo e($order->delivery_city); ?></p>
                            
                            <?php if($order->delivery_instructions): ?>
                                <hr>
                                <p class="mb-1"><strong>Instructions :</strong></p>
                                <p class="text-muted"><?php echo e($order->delivery_instructions); ?></p>
                            <?php endif; ?>

                            <?php if($order->deliveryPerson): ?>
                                <hr>
                                <p class="mb-1"><strong>Livreur assigné :</strong></p>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo e($order->deliveryPerson->avatar_url); ?>" class="rounded-circle me-2" width="35" height="35">
                                    <div>
                                        <strong><?php echo e($order->deliveryPerson->full_name); ?></strong>
                                        <br><small class="text-muted"><?php echo e($order->deliveryPerson->phone); ?></small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($order->estimated_delivery_time): ?>
                                <hr>
                                <p class="mb-0">
                                    <i class="bi bi-clock me-2"></i>
                                    Temps estimé : <strong><?php echo e($order->estimated_delivery_time); ?> minutes</strong>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Informations</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Type :</strong>
                            <span class="badge bg-<?php echo e($order->type=='dine_in'?'info':($order->type=='takeaway'?'warning':'primary')); ?>">
                                <?php if($order->type == 'dine_in'): ?>
                                    🏠 Sur place
                                <?php elseif($order->type == 'takeaway'): ?>
                                    🥡 À emporter
                                <?php else: ?>
                                    🛵 Livraison
                                <?php endif; ?>
                            </span>
                        </p>
                        <?php if($order->table_number): ?>
                            <p class="mb-2"><strong>Table :</strong> <?php echo e($order->table_number); ?></p>
                        <?php endif; ?>
                        <p class="mb-2">
                            <strong>Paiement :</strong> 
                            <span class="badge bg-<?php echo e($order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger')); ?>">
                                <?php echo e($order->payment_status == 'paid' ? 'Payé' : ($order->payment_status == 'pending' ? 'En attente' : $order->payment_status)); ?>

                            </span>
                        </p>
                        <p class="mb-2">
                            <strong>Méthode :</strong> <?php echo e(ucfirst($order->payment_method ?? 'N/A')); ?>

                        </p>
                        <p class="mb-0">
                            <strong>Source :</strong> <?php echo e($order->source ?? 'Web'); ?>

                        </p>
                    </div>
                </div>

                
                <?php if($order->notes || $order->kitchen_notes): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">📝 Notes</h5>
                        </div>
                        <div class="card-body">
                            <?php if($order->notes): ?>
                                <p class="mb-2"><strong>Client :</strong></p>
                                <p class="text-muted"><?php echo e($order->notes); ?></p>
                            <?php endif; ?>
                            <?php if($order->kitchen_notes): ?>
                                <hr>
                                <p class="mb-2"><strong>Cuisine :</strong></p>
                                <p class="text-muted"><?php echo e($order->kitchen_notes); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assignDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo e(route('admin.orders.delivery', $order)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assigner un livreur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if($deliveryPersons->count() > 0): ?>
                        <select name="delivery_person_id" class="form-select" required>
                            <option value="">Choisir un livreur...</option>
                            <?php $__currentLoopData = $deliveryPersons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($person->id); ?>"><?php echo e($person->full_name); ?> (<?php echo e($person->phone); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php else: ?>
                        <div class="alert alert-warning">Aucun livreur disponible</div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <?php if($deliveryPersons->count() > 0): ?>
                        <button type="submit" class="btn btn-primary">Assigner</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo e(route('admin.orders.cancel', $order)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Annuler la commande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Vous êtes sur le point d'annuler la commande <strong>#<?php echo e($order->order_number); ?></strong>.</p>
                    <label class="form-label">Raison de l'annulation *</label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Expliquez la raison..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .progress-steps .step.active { background-color: #0d6efd; color: white; }
    .progress-steps .step.completed { background-color: #198754; color: white; }
    .progress-steps .step.pending { background-color: #e9ecef; color: #6c757d; }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>