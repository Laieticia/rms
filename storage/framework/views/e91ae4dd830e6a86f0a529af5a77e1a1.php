<?php $__env->startSection('title', 'Menu — ' . $restaurant->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4 mb-3">
    <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 shadow-sm">
        <img src="<?php echo e($restaurant->logo_url ?? url('website/assets/img/about1.jpg')); ?>" alt="<?php echo e($restaurant->name); ?>"
             style="width:64px;height:64px;object-fit:cover;border-radius:14px;">
        <div>
            <h4 class="mb-1"><?php echo e($restaurant->name); ?></h4>
            <div class="text-muted small">
                <i class="bi bi-star-fill text-warning"></i> <?php echo e($restaurant->average_rating); ?>

                <span class="mx-2">·</span>
                <i class="bi bi-geo-alt"></i> <?php echo e($restaurant->city); ?>

                <span class="mx-2">·</span>
                <i class="bi bi-clock"></i> <?php echo e($restaurant->estimated_delivery_time); ?> min
            </div>
        </div>
        <a href="<?php echo e(route('restaurants.show', $restaurant)); ?>" class="btn btn-outline-secondary btn-sm ms-auto">
            <i class="bi bi-shop"></i> Voir le restaurant
        </a>
    </div>
</div>
<div class="container">
    <div class="row">
        
        <div class="col-md-3">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header text-white">
                    <h5 class="mb-0">Catégories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo e(route('restaurant.menu', $restaurant)); ?>" 
                       class="list-group-item list-group-item-action <?php echo e(!request('category') ? 'active' : ''); ?>">
                        <i class="bi bi-grid"></i> Tout le menu
                    </a>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('restaurant.menu', [$restaurant, 'category' => $category->slug])); ?>" 
                           class="list-group-item list-group-item-action <?php echo e(request('category') == $category->slug ? 'active' : ''); ?>">
                            <?php if($category->image): ?>
                                <img src="<?php echo e(asset('storage/'.$category->image)); ?>" 
                                     alt="<?php echo e($category->name); ?>" 
                                     class="me-2" 
                                     style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                            <?php echo e($category->name); ?>

                            <span class="badge bg-secondary float-end">
                                <?php echo e($category->available_products_count); ?>

                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                
                <div class="card-body border-top">
                    <h6 class="fw-bold">Filtres</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" 
                                   type="checkbox" 
                                   id="vegetarian" 
                                   data-filter="vegetarian"
                                   <?php echo e(request('vegetarian') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="vegetarian">
                                <i class="bi bi-leaf text-success"></i> Végétarien
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" 
                                   type="checkbox" 
                                   id="vegan"
                                   data-filter="vegan"
                                   <?php echo e(request('vegan') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="vegan">
                                <i class="bi bi-flower1 text-success"></i> Vegan
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" 
                                   type="checkbox" 
                                   id="gluten_free"
                                   data-filter="gluten_free"
                                   <?php echo e(request('gluten_free') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="gluten_free">
                                <i class="bi bi-shield-check text-warning"></i> Sans gluten
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prix</label>
                        <input type="range" class="form-range" min="0" max="15000" step="500" id="priceRange">
                        <div class="d-flex justify-content-between">
                            <small>0 FCFA</small>
                            <small>15 000 FCFA</small>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-secondary btn-sm w-100" id="resetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>
        
        
        <div class="col-md-9">
            
            <div class="mb-4">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Rechercher un plat..." 
                           id="searchProducts"
                           value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            
            
            <div class="row g-4" id="productsGrid">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-md-6 col-lg-4" data-category="<?php echo e($product->category_id); ?>">
                        <div class="card h-100 shadow-sm hover-shadow product-card">
                            
                            <div class="position-relative">
                                <?php if($product->primary_image): ?>
                                    <img src="<?php echo e(asset('storage/'.$product->primary_image->path)); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo e($product->name); ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                
                                <div class="position-absolute top-0 start-0 p-2">
                                    <?php if($product->is_vegetarian): ?>
                                        <span class="badge bg-success me-1" title="Végétarien">
                                            <i class="bi bi-leaf"></i>
                                        </span>
                                    <?php endif; ?>
                                    <?php if($product->is_vegan): ?>
                                        <span class="badge bg-success me-1" title="Vegan">
                                            <i class="bi bi-flower1"></i>
                                        </span>
                                    <?php endif; ?>
                                    <?php if($product->is_spicy): ?>
                                        <span class="badge bg-danger" title="Épicé">
                                            <i class="bi bi-fire"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                
                                <?php if($product->is_on_sale): ?>
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-danger">
                                            -<?php echo e($product->discount_percentage); ?>%
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0"><?php echo e($product->name); ?></h6>
                                    <div class="text-end">
                                        <?php if($product->is_on_sale): ?>
                                            <span class="text-decoration-line-through text-muted small">
                                                <?php echo e(\App\Helpers\CameroonHelper::formatCurrency($product->compare_price)); ?>

                                            </span>
                                        <?php endif; ?>
                                        <span class="text-primary fw-bold ms-2">
                                            <?php echo e($product->formatted_price); ?>

                                        </span>
                                    </div>
                                </div>
                                
                                <p class="card-text text-muted small mb-3">
                                    <?php echo e(Str::limit($product->description, 80)); ?>

                                </p>
                                
                                <?php if($product->rating_count > 0): ?>
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-1">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?php echo e($i <= round($product->rating_avg) ? '-fill' : ''); ?> small"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <small class="text-muted">
                                                (<?php echo e($product->rating_count); ?>)
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($product->options->count() > 0): ?>
                                    <small class="text-info">
                                        <i class="bi bi-plus-circle"></i> Options disponibles
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#productModal<?php echo e($product->id); ?>">
                                        <i class="bi bi-eye"></i> Détails
                                    </button>
                                    
                                    <?php if($product->is_in_stock): ?>
                                        <button class="btn btn-sm add-to-cart" 
                                                data-product-id="<?php echo e($product->id); ?>"
                                                style="background:var(--primary);color:#fff;border-radius:50px;">
                                            <i class="bi bi-cart-plus"></i> Ajouter
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rupture de stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="modal fade" id="productModal<?php echo e($product->id); ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php echo e($product->name); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php if($product->primary_image): ?>
                                                <img src="<?php echo e(asset('storage/'.$product->primary_image->path)); ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="<?php echo e($product->name); ?>">
                                            <?php endif; ?>
                                            
                                            
                                            <?php if($product->images->count() > 1): ?>
                                                <div class="row mt-2 g-2">
                                                    <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="col-3">
                                                            <img src="<?php echo e(asset('storage/'.$image->path)); ?>" 
                                                                 class="img-fluid rounded cursor-pointer" 
                                                                 alt="<?php echo e($image->alt_text); ?>">
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?php echo e($product->description); ?></p>
                                            
                                            <div class="mb-3">
                                                <strong>Prix :</strong>
                                                <?php if($product->is_on_sale): ?>
                                                    <span class="text-decoration-line-through me-2">
                                                        <?php echo e(\App\Helpers\CameroonHelper::formatCurrency($product->compare_price)); ?>

                                                    </span>
                                                <?php endif; ?>
                                                <span class="h4 text-primary">
                                                    <?php echo e($product->formatted_price); ?>

                                                </span>
                                            </div>
                                            
                                            
                                            <?php if($product->calories): ?>
                                                <div class="mb-3">
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-fire"></i> <?php echo e($product->calories); ?> cal
                                                    </span>
                                                    <?php if($product->preparation_time): ?>
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-clock"></i> <?php echo e($product->preparation_time); ?> min
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php if($product->allergens): ?>
                                                <div class="mb-3">
                                                    <strong>Allergènes :</strong>
                                                    <div>
                                                        <?php $__currentLoopData = $product->allergens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allergen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="badge bg-danger me-1"><?php echo e($allergen); ?></span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php $__currentLoopData = $product->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="mb-3">
                                                    <strong><?php echo e($option->name); ?> :</strong>
                                                    <?php if($option->is_required): ?>
                                                        <span class="text-danger">*</span>
                                                    <?php endif; ?>
                                                    <div>
                                                        <?php $__currentLoopData = $option->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input product-option" 
                                                                       type="<?php echo e($option->type == 'single' ? 'radio' : 'checkbox'); ?>"
                                                                       name="options[<?php echo e($option->id); ?>][]"
                                                                       value="<?php echo e($item->id); ?>"
                                                                       data-price="<?php echo e($item->price); ?>"
                                                                       data-name="<?php echo e($item->name); ?>">
                                                                <label class="form-check-label">
                                                                    <?php echo e($item->name); ?>

                                                                    <?php if($item->price > 0): ?>
                                                                        (+<?php echo e(\App\Helpers\CameroonHelper::formatCurrency($item->price)); ?>)
                                                                    <?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Fermer
                                    </button>
                                    <button type="button" class="btn btn-primary btn-lg add-to-cart-with-options"
                                            data-product-id="<?php echo e($product->id); ?>">
                                        <i class="bi bi-cart-plus"></i> Ajouter au panier - <?php echo e($product->formatted_price); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-frown text-muted" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">Aucun produit trouvé</h3>
                            <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            
            <div class="mt-4">
                <?php echo e($products->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
.product-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}
.product-card .card-img-top {
    transition: transform 0.3s ease;
}
.product-card:hover .card-img-top {
    transform: scale(1.05);
}
.cursor-pointer {
    cursor: pointer;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Ajouter au panier
    $('.add-to-cart').click(function() {
        const productId = $(this).data('product-id');
        addToCart(productId);
    });
    
    // Ajouter au panier avec options (depuis la modal)
    $('.add-to-cart-with-options').click(function() {
        const productId = $(this).data('product-id');
        const options = collectOptions(productId);
        addToCart(productId, 1, options);
        $('#productModal' + productId).modal('hide');
    });
    
    function addToCart(productId, quantity = 1, options = []) {
        $.ajax({
            url: '<?php echo e(route("cart.add")); ?>',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                options: options,
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                updateCartCount(response.cart_count);
                showToast('Produit ajouté au panier !', 'success');
            },
            error: function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Erreur lors de l\'ajout au panier';
                showToast(message, 'error');
            }
        });
    }
    
    function collectOptions(productId) {
        const options = [];
        $(`#productModal${productId} .product-option:checked`).each(function() {
            options.push({
                option_id: $(this).attr('name').match(/\d+/)[0],
                item_id: $(this).val(),
                price: $(this).data('price'),
                name: $(this).data('name')
            });
        });
        return options;
    }
    
    // Recherche en temps réel
    let searchTimeout;
    $('#searchProducts').on('keyup', function() {
        clearTimeout(searchTimeout);
        const search = $(this).val();
        searchTimeout = setTimeout(function() {
            window.location.href = updateQueryString('search', search);
        }, 500);
    });
    
    // Filtres
    $('.filter-checkbox').change(function() {
        const filter = $(this).data('filter');
        const value = $(this).prop('checked') ? '1' : '';
        window.location.href = updateQueryString(filter, value);
    });
    
    function updateQueryString(key, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        return url.toString();
    }
    
    function updateCartCount(count) {
        $('#cartCount').text(count);
        if (count > 0) {
            $('#cartCount').removeClass('d-none');
        } else {
            $('#cartCount').addClass('d-none');
        }
    }
    
    function showToast(message, type) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/menu/index.blade.php ENDPATH**/ ?>