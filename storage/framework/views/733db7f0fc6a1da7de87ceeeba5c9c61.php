<!DOCTYPE html>
<html lang="fr">
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
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('home')); ?>">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('restaurants.index')); ?>">Restaurants</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('home')); ?>#contact-section">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <form action="<?php echo e(route('search')); ?>" method="GET" class="d-none d-md-flex">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Rechercher..." value="<?php echo e(request('q')); ?>" style="border-radius:50px 0 0 50px;">
                        <button class="btn btn-sm" style="background:var(--primary);color:#fff;border-radius:0 50px 50px 0;" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="<?php echo e(route('cart.index')); ?>" class="position-relative" style="font-size:20px;color:inherit;">
                        <i class="fas fa-shopping-cart"></i>
                        <?php $cartCount = session('cart') ? count(session('cart')) : 0; ?>
                        <span id="cartCount" class="badge rounded-pill bg-danger position-absolute <?php echo e($cartCount == 0 ? 'd-none' : ''); ?>" style="top:-8px;right:-10px;font-size:11px;"><?php echo e($cartCount); ?></span>
                    </a>
                    <?php if(auth()->guard()->check()): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?php echo e(Auth::user()->first_name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.index')); ?>"><i class="fas fa-id-card me-2"></i>Mon profil</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.orders')); ?>"><i class="fas fa-receipt me-2"></i>Mes commandes</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('reservations.index')); ?>"><i class="fas fa-calendar-check me-2"></i>Mes réservations</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('profile.favorites')); ?>"><i class="fas fa-heart me-2"></i>Mes favoris</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="nav-link nav-cta" style="background: var(--primary); color: #fff; padding: 10px 25px; border-radius: 50px; border: none;">
                            Connexion
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
                    <p class="fdesc">La marketplace qui connecte les restaurants camerounais à leurs clients.</p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="ftit">Navigation</h6>
                    <div class="flinks">
                        <a href="<?php echo e(route('home')); ?>">Accueil</a>
                        <a href="<?php echo e(route('restaurants.index')); ?>">Restaurants</a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="ftit">Mon compte</h6>
                    <div class="flinks">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('profile.index')); ?>">Mon profil</a>
                            <a href="<?php echo e(route('profile.orders')); ?>">Mes commandes</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>">Connexion</a>
                            <a href="<?php echo e(route('register')); ?>">Créer un compte</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h6 class="ftit">Contact</h6>
                    <div class="fci"><div class="fciico"><i class="fas fa-map-marker-alt"></i></div><div class="fciinfo">Bafoussam, Région de l'Ouest, Cameroun</div></div>
                    <div class="fci"><div class="fciico"><i class="fas fa-phone-alt"></i></div><div class="fciinfo">+237 6XX XXX XXX</div></div>
                </div>
            </div>
            <div class="fbot">© <?php echo e(date('Y')); ?> RMS Cameroun. Tous droits réservés.</div>
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