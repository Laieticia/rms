<?php $__env->startSection('title', 'Gestion des Produits'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php if(session('success')): ?>
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-icon">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="iq-alert-text"><?php echo e(session('success')); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-icon">
                        <i class="ri-information-line"></i>
                    </div>
                    <div class="iq-alert-text"><?php echo e(session('error')); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-3">Produits</h4>
                                </div>
                                <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Nouveau produit</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.products.index')); ?>" method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <select name="category_id" class="form-control">
                                <option value="">Toutes les catégories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?>>Disponible</option>
                                <option value="unavailable" <?php echo e(request('status') == 'unavailable' ? 'selected' : ''); ?>>Indisponible</option>
                                <option value="low_stock" <?php echo e(request('status') == 'low_stock' ? 'selected' : ''); ?>>Stock faible</option>
                                <option value="out_of_stock" <?php echo e(request('status') == 'out_of_stock' ? 'selected' : ''); ?>>Rupture</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher un produit..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                        </div>
                        <div class="col-md-2">
                            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive rounded mb-3">
                                <table class="table mb-0 tbl-server-info">
                                    <thead class="bg-white text-uppercase">
                                        <tr class="ligth ligth-data">
                                            <th>Nom</th>
                                            <th>Catégorie</th>
                                            <th>Prix</th>
                                            <th>Stock</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ligth-body">
                                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center flex">
                                                        <img src="<?php echo e($product->primary_image_url ?? url('admin/assets/images/logo.png')); ?>" class="img-fluid rounded avatar-50 mr-3" style="object-fit: cover;" alt="<?php echo e($product->name); ?>">
                                                        <div>
                                                            <strong><?php echo e($product->name); ?></strong>
                                                            <?php if($product->is_on_sale || $product->is_featured): ?>
                                                            <ul class="list-group">
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <?php if($product->is_on_sale): ?>
                                                                        <span class="badge bg-danger ms-1">-<?php echo e($product->discount_percentage); ?>%</span>
                                                                    <?php endif; ?>
                                                                    <?php if($product->is_featured): ?>
                                                                        <span class="badge bg-warning badge-pill">
                                                                            <i class="ri-star-fill"></i>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </li>
                                                            </ul>
                                                            <?php endif; ?>
                                                            
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo e($product->category->name ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if($product->is_on_sale): ?>
                                                        <small class="text-decoration-line-through text-muted"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($product->compare_price)); ?></small>
                                                    <?php endif; ?>
                                                    <span class="fw-bold"><?php echo e($product->formatted_price); ?></span>
                                                </td>
                                                <td>
                                                    <?php if($product->track_inventory): ?>
                                                        <?php if($product->stock_quantity <= 0): ?>
                                                            <span class="badge bg-danger">Rupture</span>
                                                        <?php elseif($product->isLowStock()): ?>
                                                            <span class="badge bg-warning"><?php echo e($product->stock_quantity); ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success"><?php echo e($product->stock_quantity); ?></span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($product->is_available): ?>
                                                        <span class="badge bg-success">Actif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        <button type="button" class="badge badge-info mr-2" data-toggle="modal" data-target="#stockModal<?php echo e($product->id); ?>" title="Gérer le stock">
                                                            <i class="ri-archive-line mr-0"></i>
                                                        </button>
                                                        <a class="badge bg-success mr-2" href="<?php echo e(route('admin.products.edit', $product)); ?>" aria-label="Modifier">
                                                            <i class="ri-pencil-line mr-0"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('admin.products.destroy', $product)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Supprimer" onclick="return confirm('Supprimer ce produit ?')">
                                                                <i class="ri-delete-bin-line mr-0"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="bi bi-inbox display-4 text-muted"></i>
                                                    <p class="mt-2">Aucun produit trouvé</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php echo e($products->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="stockModal<?php echo e($product->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="<?php echo e(route('admin.products.stock', $product)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Gérer le stock — <?php echo e($product->name); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Stock actuel : <strong><?php echo e($product->stock_quantity); ?></strong></p>
                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <select name="type" class="form-control" required>
                                <option value="add">Ajouter au stock</option>
                                <option value="remove">Retirer du stock</option>
                                <option value="set">Définir la quantité exacte</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" name="quantity" class="form-control" min="0" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Raison (optionnel)</label>
                            <input type="text" name="reason" class="form-control" placeholder="Ex: réapprovisionnement, perte, inventaire...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/admin/products/index.blade.php ENDPATH**/ ?>