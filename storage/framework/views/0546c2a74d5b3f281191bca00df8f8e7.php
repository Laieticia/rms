<?php $__env->startSection('title', 'Nos restaurants'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="slbl">Trouvez votre bonheur</span>
        <h2 class="stitle">Tous les <span>restaurants</span></h2>
        <div class="sline"></div>
    </div>

    
    <form method="GET" class="row g-2 justify-content-center mb-5">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-lg" placeholder="Rechercher un restaurant..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-3">
            <select name="city" class="form-select form-select-lg">
                <option value="">Toutes les villes</option>
                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($city); ?>" <?php echo e(request('city') == $city ? 'selected' : ''); ?>><?php echo e($city); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select form-select-lg">
                <option value="popular" <?php echo e(request('sort', 'popular') == 'popular' ? 'selected' : ''); ?>>Les plus populaires</option>
                <option value="rating" <?php echo e(request('sort') == 'rating' ? 'selected' : ''); ?>>Les mieux notés</option>
                <option value="delivery_time" <?php echo e(request('sort') == 'delivery_time' ? 'selected' : ''); ?>>Livraison la plus rapide</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-lg w-100" style="background:var(--primary);color:#fff;"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo e(route('restaurants.show', $restaurant)); ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                        <div style="height:180px;overflow:hidden;position:relative;">
                            <img src="<?php echo e($restaurant->cover_url ?? url('website/assets/img/about1.jpg')); ?>" alt="<?php echo e($restaurant->name); ?>" class="w-100 h-100" style="object-fit:cover;">
                            <?php if(!$restaurant->isOpenNow()): ?>
                                <span class="badge bg-dark position-absolute top-0 end-0 m-2">Fermé actuellement</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="mb-0" style="color:#1a1a1a;"><?php echo e($restaurant->name); ?></h5>
                                <span class="badge" style="background:rgba(255,193,7,0.15);color:#c99400;">
                                    <i class="bi bi-star-fill"></i> <?php echo e($restaurant->average_rating); ?>

                                    <small>(<?php echo e($restaurant->total_reviews); ?>)</small>
                                </span>
                            </div>
                            <p class="text-muted small mb-2"><?php echo e(\Illuminate\Support\Str::limit($restaurant->description, 90)); ?></p>
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="bi bi-geo-alt me-1"></i><?php echo e($restaurant->city); ?></span>
                                <span><i class="bi bi-clock me-1"></i><?php echo e($restaurant->estimated_delivery_time); ?> min</span>
                                <span><i class="bi bi-truck me-1"></i><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($restaurant->delivery_fee)); ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-emoji-frown" style="font-size:3rem;"></i>
                <p class="mt-3">Aucun restaurant ne correspond à votre recherche.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-5">
        <?php echo e($restaurants->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/restaurants/index.blade.php ENDPATH**/ ?>