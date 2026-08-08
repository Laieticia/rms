<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $__env->yieldContent('title', 'RMS'); ?> — RMS Cameroun</title>
    <link rel="icon" href="<?php echo e(url('website/assets/img/favicon.png')); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/bootstrap.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/all.min.css')); ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/aos.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/style.css')); ?>" />
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body style="font-family:'Poppins',sans-serif;background:#faf7f2;">

    <!-- NAV -->
     <nav class="navbar navbar-expand-lg" id="nav">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
                <div class="blogo"><i class="fas fa-utensils bico"></i>
                    <span class="bname">MontRoyal<em class="bsub"></em></span>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('home')); ?>"><?php echo e(__('app.home')); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('restaurants.index')); ?>"><?php echo e(__('app.restaurants') ?? 'Restaurants'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('home')); ?>#contact-section"><?php echo e(__('app.contact')); ?></a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <form action="<?php echo e(route('search')); ?>" method="GET" class="d-none d-md-flex">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="<?php echo e(__('messages.search')); ?>..." value="<?php echo e(request('q')); ?>" style="border-radius:50px 0 0 50px;">
                        <button class="btn btn-sm" style="background:var(--primary);color:#fff;border-radius:0 50px 50px 0;" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="<?php echo e(route('cart.index')); ?>" class="position-relative" style="font-size:20px;color:inherit;">
                        <i class="fas fa-shopping-cart"></i>
                        <?php $cartCount = session('cart') ? count(session('cart')) : 0; ?>
                        <span id="cartCount" class="badge rounded-pill bg-danger position-absolute <?php echo e($cartCount == 0 ? 'd-none' : ''); ?>" style="top:-8px;right:-10px;font-size:11px;"><?php echo e($cartCount); ?></span>
                    </a>

                    
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 px-2 py-1"
                           href="#" role="button" data-bs-toggle="dropdown"
                           style="font-size:.85rem;border:1px solid rgba(0,0,0,.15);border-radius:50px;">
                            <?php if(app()->getLocale() === 'fr'): ?>
                                <span>🇫🇷</span> <span class="d-none d-md-inline">FR</span>
                            <?php else: ?>
                                <span>🇬🇧</span> <span class="d-none d-md-inline">EN</span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:130px;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 <?php echo e(app()->getLocale() === 'fr' ? 'active fw-semibold' : ''); ?>"
                                   href="<?php echo e(route('language.switch', 'fr')); ?>">
                                    <span>🇫🇷</span> Français
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 <?php echo e(app()->getLocale() === 'en' ? 'active fw-semibold' : ''); ?>"
                                   href="<?php echo e(route('language.switch', 'en')); ?>">
                                    <span>🇬🇧</span> English
                                </a>
                            </li>
                        </ul>
                    </div>
                    

                    <?php if(auth()->guard()->check()): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?php echo e(Auth::user()->first_name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.index')); ?>"><i class="fas fa-id-card me-2"></i><?php echo e(__('app.profile')); ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.orders')); ?>"><i class="fas fa-receipt me-2"></i><?php echo e(__('app.orders')); ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('reservations.index')); ?>"><i class="fas fa-calendar-check me-2"></i><?php echo e(__('storefront.my_reservations') ?? 'Mes réservations'); ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.favorites')); ?>"><i class="fas fa-heart me-2"></i><?php echo e(__('storefront.my_favorites') ?? 'Mes favoris'); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i><?php echo e(__('messages.logout')); ?></button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="nav-link nav-cta" style="background: var(--primary); color: #fff; padding: 10px 25px; border-radius: 50px; border: none;">
                            <?php echo e(__('messages.login')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="blogo mb-3"><i class="fas fa-utensils bico"></i><span class="bname">RMS<em class="bsub">Cameroun</em></span></div>
                    <p class="fdesc"><?php echo e(__('storefront.footer_tagline') ?? 'La marketplace qui connecte les restaurants camerounais à leurs clients.'); ?></p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="ftit">Navigation</h6>
                    <div class="flinks">
                        <a href="<?php echo e(route('home')); ?>"><?php echo e(__('app.home')); ?></a>
                        <a href="<?php echo e(route('restaurants.index')); ?>">Restaurants</a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="ftit"><?php echo e(__('storefront.my_account') ?? 'Mon compte'); ?></h6>
                    <div class="flinks">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('profile.index')); ?>"><?php echo e(__('app.profile')); ?></a>
                            <a href="<?php echo e(route('profile.orders')); ?>"><?php echo e(__('app.orders')); ?></a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>"><?php echo e(__('messages.login')); ?></a>
                            <a href="<?php echo e(route('register')); ?>"><?php echo e(__('messages.register')); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h6 class="ftit"><?php echo e(__('app.contact')); ?></h6>
                    <div class="fci"><div class="fciico"><i class="fas fa-map-marker-alt"></i></div><div class="fciinfo">Bafoussam, Région de l'Ouest, Cameroun</div></div>
                    <div class="fci"><div class="fciico"><i class="fas fa-phone-alt"></i></div><div class="fciinfo">+237 6XX XXX XXX</div></div>
                </div>
            </div>
            <div class="fbot">© <?php echo e(date('Y')); ?> RMS Cameroun. <?php echo e(__('storefront.all_rights_reserved') ?? 'Tous droits réservés.'); ?></div>
        </div>
    </footer>

    <script src="<?php echo e(url('website/assets/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/aos.js')); ?>"></script>
    <script>if (typeof AOS !== 'undefined') { AOS.init({ duration: 700, once: true }); }</script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/layouts/storefront.blade.php ENDPATH**/ ?>