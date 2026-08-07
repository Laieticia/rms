<?php $__env->startSection('title', $restaurant->name); ?>

<?php $__env->startSection('content'); ?>

<div style="height:260px;overflow:hidden;position:relative;">
    <img src="<?php echo e($restaurant->cover_url ?? url('website/assets/img/about1.jpg')); ?>" alt="<?php echo e($restaurant->name); ?>" class="w-100 h-100" style="object-fit:cover;">
    <div class="position-absolute bottom-0 start-0 w-100" style="background:linear-gradient(transparent, rgba(0,0,0,.6));height:120px;"></div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-4 mt-n5 position-relative mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="<?php echo e($restaurant->logo_url ?? url('website/assets/img/about2.jpg')); ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:14px;">
                    <div>
                        <h2 class="mb-1"><?php echo e($restaurant->name); ?></h2>
                        <div class="text-muted">
                            <i class="bi bi-star-fill text-warning"></i> <?php echo e($restaurant->average_rating); ?>

                            <small>(<?php echo e($restaurant->total_reviews); ?> avis)</small>
                            <span class="mx-2">·</span>
                            <i class="bi bi-geo-alt"></i> <?php echo e($restaurant->city); ?>

                            <?php if($restaurant->isOpenNow()): ?>
                                <span class="badge bg-success ms-2">Ouvert</span>
                            <?php else: ?>
                                <span class="badge bg-secondary ms-2">Fermé</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <p class="mb-3"><?php echo e($restaurant->description); ?></p>
                <div class="row text-center g-3">
                    <div class="col-4">
                        <div class="border rounded-3 p-2">
                            <i class="bi bi-clock text-primary"></i>
                            <div class="fw-bold"><?php echo e($restaurant->estimated_delivery_time); ?> min</div>
                            <small class="text-muted">Livraison</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2">
                            <i class="bi bi-truck text-primary"></i>
                            <div class="fw-bold"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($restaurant->delivery_fee)); ?></div>
                            <small class="text-muted">Frais livraison</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2">
                            <i class="bi bi-cash-stack text-primary"></i>
                            <div class="fw-bold"><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($restaurant->minimum_order)); ?></div>
                            <small class="text-muted">Commande min.</small>
                        </div>
                    </div>
                </div>
                <a href="<?php echo e(route('restaurant.menu', $restaurant)); ?>" class="btn w-100 mt-4 py-3" style="background:var(--primary);color:#fff;border-radius:14px;">
                    <i class="bi bi-book"></i> Voir le menu complet
                </a>
                <a href="<?php echo e(route('reservations.create', $restaurant)); ?>" class="btn btn-outline-secondary w-100 mt-2 py-3" style="border-radius:14px;">
                    <i class="bi bi-calendar-check"></i> Réserver une table
                </a>
            </div>

            <?php if($featuredProducts->count() > 0): ?>
                <h5 class="mb-3">Plats populaires</h5>
                <div class="row g-3 mb-4">
                    <?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-6 col-md-4">
                            <a href="<?php echo e(route('products.show', $product)); ?>" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
                                    <div style="height:110px;overflow:hidden;">
                                        <img src="<?php echo e($product->primary_image_url ?? url('website/assets/img/category/2.jpg')); ?>" alt="<?php echo e($product->name); ?>" class="w-100 h-100" style="object-fit:cover;">
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="small text-truncate" style="color:#1a1a1a;"><?php echo e($product->name); ?></div>
                                        <strong style="color:var(--primary);"><?php echo e($product->formatted_price); ?></strong>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <h5 class="mb-3">Avis clients</h5>
            <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-bottom py-3">
                    <div class="d-flex justify-content-between">
                        <strong><?php echo e($review->user->first_name ?? 'Client'); ?></strong>
                        <div class="text-warning">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?php echo e($i <= $review->rating ? '-fill' : ''); ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-1"><?php echo e($review->comment); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted">Aucun avis pour le moment.</p>
            <?php endif; ?>
            <?php echo e($reviews->links()); ?>

        </div>

        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h6 class="mb-3">Catégories du menu</h6>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('restaurant.menu', [$restaurant, 'category' => $category->slug])); ?>" class="list-group-item list-group-item-action d-flex justify-content-between">
                            <?php echo e($category->name); ?>

                            <span class="badge bg-secondary rounded-pill"><?php echo e($category->available_products_count); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h6 class="mb-3">Adresse</h6>
                <p class="text-muted mb-1"><i class="bi bi-geo-alt me-2"></i><?php echo e($restaurant->address); ?>, <?php echo e($restaurant->city); ?></p>
                <?php if($restaurant->phone): ?>
                    <p class="text-muted mb-0"><i class="bi bi-telephone me-2"></i><?php echo e($restaurant->phone); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/restaurants/show.blade.php ENDPATH**/ ?>