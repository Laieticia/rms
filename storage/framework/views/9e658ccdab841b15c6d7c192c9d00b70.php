<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RMS — Commandez auprès des meilleurs restaurants près de chez vous</title>
    <meta name="description" content="RMS met en relation les meilleurs restaurants du Cameroun avec leurs clients : commandez en ligne, payez par Mobile Money, suivez votre livraison en temps réel." />
    <link rel="icon" href="<?php echo e(url('website/assets/img/favicon.png')); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/bootstrap.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/all.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/aos.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/swiper-bundle.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/magnific-popup.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('website/assets/css/style.css')); ?>" />
</head>
<body>

    <!-- TOPBAR -->
    <div class="topbar d-none d-lg-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4">
                <span><i class="fas fa-phone-alt me-2"></i>+237 655585802</span>
                <span><i class="fas fa-envelope me-2"></i>contact@montroyal.cm</span>
                <span><i class="fas fa-map-marker-alt me-2"></i>Bafoussam, Région de l'Ouest, Cameroun</span>
            </div>
            <div class="d-flex gap-3">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>

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
                        <?php if(session('cart') && count(session('cart')) > 0): ?>
                            <span class="badge rounded-pill bg-danger position-absolute" style="top:-8px;right:-10px;font-size:11px;"><?php echo e(count(session('cart'))); ?></span>
                        <?php endif; ?>
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

    <!-- HERO -->
    <section id="hero">
        <div class="hs hs1"></div>
        <div class="hs hs2"></div>
        <div class="hbgtxt">FOOD</div>
        <div class="container">
            <div class="row align-items-center g-5" style="min-height:80vh;">
                <div class="col-lg-6">
                    <div class="hbadge">
                        <div class="hbi"><i class="fas fa-star"></i></div>
                        <span>La marketplace food n°1 au Cameroun</span>
                    </div>
                    <h1 class="htitle">Vos plats préférés,<br /><span class="hl">livrés chez vous</span></h1>
                    <p class="hdesc">Ndolé, poulet DG, éru, brochettes ou pizza : commandez auprès des meilleurs restaurants de votre ville, payez par Mobile Money et suivez votre livraison en direct.</p>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <a href="<?php echo e(route('restaurants.index')); ?>" class="btn-red"><i class="fas fa-utensils"></i>Voir les restaurants</a>
                        <a href="#devenir-partenaire" class="btn-play">
                            <div class="pico"><i class="fas fa-store"></i></div>
                            <span>Devenir partenaire</span>
                        </a>
                    </div>
                    <div class="hstats d-flex gap-3 flex-wrap mt-4">
                        <div class="hstat"><span class="snum"><?php echo e(\App\Models\Restaurant::active()->count()); ?><em>+</em></span><small>Restaurants partenaires</small></div>
                        <div class="sdiv"></div>
                        <div class="hstat"><span class="snum"><?php echo e(\App\Models\Product::available()->count()); ?><em>+</em></span><small>Plats disponibles</small></div>
                        <div class="sdiv"></div>
                        <div class="hstat"><span class="snum">30<em>min</em></span><small>Livraison moyenne</small></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="position:relative;text-align:center;">
                        <div class="hcircle">
                            <img src="<?php echo e(url('website/assets/img/banner-img.jpg')); ?>" alt="Plat camerounais" />
                        </div>
                        <div class="fcard fc1">
                            <div class="fcoi r"><i class="fas fa-fire"></i></div>
                            <div><span class="fcnum">Offres du jour</span><span class="fcsm">Jusqu'à -30%</span></div>
                        </div>
                        <div class="fcard fc2">
                            <div class="fcoi y"><i class="fas fa-mobile-alt"></i></div>
                            <div><span class="fcnum">MTN / Orange</span><span class="fcsm">Mobile Money</span></div>
                        </div>
                        <div class="fcard fc3">
                            <div class="fcoi g"><i class="fas fa-clock"></i></div>
                            <div><span class="fcnum">30 min</span><span class="fcsm">Livraison rapide</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MARQUEE -->
    <div class="mqsec">
        <div class="mqtrack">
            <?php $dishes = ['Ndolé au poisson fumé','Poulet DG','Éru & Waterfoufou','Achu soup','Koki','Brochettes grillées','Beignets haricot','Poisson braisé','Riz sauté','Pizza & Burgers']; ?>
            <?php for($i = 0; $i < 2; $i++): ?>
                <?php $__currentLoopData = $dishes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dish): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mqitem"><i class="fas fa-circle"></i><?php echo e($dish); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endfor; ?>
        </div>
    </div>

    <!-- CATÉGORIES (dynamique) -->
    <section id="category">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Que voulez-vous manger ?</span>
                <h2 class="stitle">Parcourir par <span>catégorie</span></h2>
                <div class="sline"></div>
                <p class="sdesc mx-auto" style="max-width:480px;">Des spécialités camerounaises aux classiques internationaux, trouvez votre bonheur.</p>
            </div>
            <div class="row g-3 justify-content-center">
                <div class="col-6 col-sm-4 col-md-3 col-lg-2" data-aos="zoom-in">
                    <a href="<?php echo e(route('restaurants.index')); ?>" class="text-decoration-none">
                        <div class="catcard active">
                            <img class="catimg" src="<?php echo e(url('website/assets/img/category/1.jpg')); ?>" alt="Tous" />
                            <div class="catnm">Tout voir</div>
                            <div class="catct">Tous les restaurants</div>
                        </div>
                    </a>
                </div>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2" data-aos="zoom-in" data-aos-delay="<?php echo e(($loop->index + 1) * 70); ?>">
                        <a href="<?php echo e(route('restaurants.index', ['category' => $category->slug])); ?>" class="text-decoration-none">
                            <div class="catcard">
                                <img class="catimg" src="<?php echo e($category->image_url ?? url('website/assets/img/category/2.jpg')); ?>" alt="<?php echo e($category->name); ?>" />
                                <div class="catnm"><?php echo e($category->name); ?></div>
                                <div class="catct"><?php echo e($category->products_count); ?> plat<?php echo e($category->products_count > 1 ? 's' : ''); ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    <!-- RESTAURANTS EN VEDETTE (dynamique) -->
    <section id="restaurants">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Sélection du moment</span>
                <h2 class="stitle">Restaurants <span>en vedette</span></h2>
                <div class="sline"></div>
            </div>
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $featuredRestaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 80); ?>">
                        <a href="<?php echo e(route('restaurants.show', $restaurant)); ?>" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                                <div style="height:180px;overflow:hidden;">
                                    <img src="<?php echo e($restaurant->cover_url ?? url('website/assets/img/about1.jpg')); ?>" alt="<?php echo e($restaurant->name); ?>" class="w-100 h-100" style="object-fit:cover;">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="mb-0" style="color:#1a1a1a;"><?php echo e($restaurant->name); ?></h5>
                                        <span class="badge" style="background:rgba(255,193,7,0.15);color:#c99400;">
                                            <i class="fas fa-star"></i> <?php echo e($restaurant->average_rating); ?>

                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-1"></i><?php echo e($restaurant->city); ?></p>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><i class="fas fa-clock me-1"></i><?php echo e($restaurant->estimated_delivery_time); ?> min</span>
                                        <span><i class="fas fa-motorcycle me-1"></i><?php echo e(\App\Helpers\CameroonHelper::formatCurrency($restaurant->delivery_fee)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center text-muted py-5">
                        Aucun restaurant disponible pour le moment. Revenez bientôt !
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-5">
                <a href="<?php echo e(route('restaurants.index')); ?>" class="btn-red"><i class="fas fa-th-large"></i>Voir tous les restaurants</a>
            </div>
        </div>
    </section>

    <!-- PLATS POPULAIRES (dynamique) -->
    <section id="menu" style="background:#faf7f2;padding:80px 0;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Coups de cœur</span>
                <h2 class="stitle">Plats les plus <span>commandés</span></h2>
                <div class="sline"></div>
            </div>
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $popularProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 60); ?>">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                            <a href="<?php echo e(route('products.show', $product)); ?>" class="text-decoration-none">
                                <div style="height:140px;overflow:hidden;">
                                    <img src="<?php echo e($product->primary_image_url ?? url('website/assets/img/category/3.jpg')); ?>" alt="<?php echo e($product->name); ?>" class="w-100 h-100" style="object-fit:cover;">
                                </div>
                            </a>
                            <div class="card-body p-3">
                                <p class="text-muted small mb-1"><?php echo e($product->restaurant->name); ?></p>
                                <a href="<?php echo e(route('products.show', $product)); ?>" class="text-decoration-none">
                                    <h6 class="mb-2" style="color:#1a1a1a;"><?php echo e($product->name); ?></h6>
                                </a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="color:var(--primary);"><?php echo e($product->formatted_price); ?></strong>
                                    <button class="btn btn-sm btn-add-cart" data-product-id="<?php echo e($product->id); ?>" style="background:var(--primary);color:#fff;border:none;border-radius:50%;width:34px;height:34px;">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center text-muted py-4">Aucun plat mis en avant pour le moment.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- COMMENT ÇA MARCHE -->
    <section id="comment-ca-marche" style="padding:80px 0;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Simple et rapide</span>
                <h2 class="stitle">Comment ça <span>marche</span></h2>
                <div class="sline"></div>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                    <div class="fti flex-column text-center">
                        <div class="ftico r mx-auto"><i class="fas fa-search"></i></div>
                        <h6 class="mt-3">1. Choisissez</h6>
                        <p>Parcourez les restaurants proches de vous et composez votre commande.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="fti flex-column text-center">
                        <div class="ftico y mx-auto"><i class="fas fa-mobile-alt"></i></div>
                        <h6 class="mt-3">2. Payez</h6>
                        <p>Réglez en toute sécurité par MTN Mobile Money ou Orange Money.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="fti flex-column text-center">
                        <div class="ftico g mx-auto"><i class="fas fa-motorcycle"></i></div>
                        <h6 class="mt-3">3. Suivez</h6>
                        <p>Suivez votre livreur en temps réel jusqu'à votre porte.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AVIS CLIENTS (dynamique) -->
    <?php if($recentReviews->count() > 0): ?>
    <section id="testimonials" style="background:#faf7f2;padding:80px 0;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Ils nous font confiance</span>
                <h2 class="stitle">Avis de nos <span>clients</span></h2>
                <div class="sline"></div>
            </div>
            <div class="row g-4">
                <?php $__currentLoopData = $recentReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 70); ?>">
                        <div class="card h-100 border-0 shadow-sm p-4" style="border-radius:16px;">
                            <div class="mb-2" style="color:#ffc107;">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star<?php echo e($i < $review->rating ? '' : '-o far'); ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-muted">"<?php echo e(\Illuminate\Support\Str::limit($review->comment, 140)); ?>"</p>
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <strong><?php echo e($review->user->first_name ?? 'Client'); ?></strong>
                                    <div class="small text-muted"><?php echo e($review->restaurant->name ?? ''); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- DEVENIR PARTENAIRE -->
    <section id="devenir-partenaire" style="padding:70px 0;background:var(--primary);color:#fff;">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="mb-3" style="font-family:'Playfair Display',serif;font-weight:700;">Vous gérez un restaurant ?</h2>
            <p class="mb-4" style="max-width:560px;margin:0 auto;opacity:.9;">Rejoignez RMS et faites découvrir votre carte à des milliers de clients partout au Cameroun.</p>
            <a href="mailto:contact@rms.cm" class="btn btn-light rounded-pill px-4 py-2 fw-bold">Devenir partenaire <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </section>

    <!-- CONTACT -->
    <section id="contact-section" style="padding:80px 0;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">Une question ?</span>
                <h2 class="stitle">Contactez-<span>nous</span></h2>
                <div class="sline"></div>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="fti flex-column"><div class="ftico r mx-auto"><i class="fas fa-map-marker-alt"></i></div>
                        <h6 class="mt-3">Adresse</h6><p>Bafoussam, Région de l'Ouest, Cameroun</p></div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="fti flex-column"><div class="ftico y mx-auto"><i class="fas fa-phone-alt"></i></div>
                        <h6 class="mt-3">Téléphone</h6><p>+237 6XX XXX XXX</p></div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="fti flex-column"><div class="ftico g mx-auto"><i class="fas fa-envelope"></i></div>
                        <h6 class="mt-3">Email</h6><p>contact@rms.cm</p></div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="blogo mb-3"><i class="fas fa-utensils bico"></i><span class="bname">RMS<em class="bsub">Cameroun</em></span></div>
                    <p class="fdesc">La marketplace qui connecte les restaurants camerounais à leurs clients : commande en ligne, paiement Mobile Money, livraison suivie en temps réel.</p>
                    <div class="fsoc"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-whatsapp"></i></a><a href="#"><i class="fab fa-instagram"></i></a></div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="ftit">Navigation</h6>
                    <div class="flinks">
                        <a href="<?php echo e(route('home')); ?>">Accueil</a>
                        <a href="<?php echo e(route('restaurants.index')); ?>">Restaurants</a>
                        <a href="#comment-ca-marche">Comment ça marche</a>
                        <a href="#devenir-partenaire">Devenir partenaire</a>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="ftit">Mon compte</h6>
                    <div class="flinks">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('profile.index')); ?>">Mon profil</a>
                            <a href="<?php echo e(route('profile.orders')); ?>">Mes commandes</a>
                            <a href="<?php echo e(route('cart.index')); ?>">Mon panier</a>
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
                    <div class="fci"><div class="fciico"><i class="fas fa-envelope"></i></div><div class="fciinfo">contact@rms.cm</div></div>
                </div>
            </div>
            <div class="fbot">© <?php echo e(date('Y')); ?> RMS Cameroun. Tous droits réservés.</div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0" style="background: var(--primary); padding: 20px;">
                    <h5 class="modal-title text-white"><i class="fas fa-utensils me-2"></i> Bienvenue sur RMS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div class="text-center mb-4">
                        <div style="width: 70px; height: 70px; background: rgba(232,40,26,0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fas fa-sign-in-alt" style="font-size: 30px; color: var(--primary);"></i>
                        </div>
                        <h4>Connexion à votre compte</h4>
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Adresse email" required style="padding: 12px; border-radius: 12px;">
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Mot de passe" required style="padding: 12px; border-radius: 12px;">
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="<?php echo e(route('password.request')); ?>" style="color: var(--primary);">Mot de passe oublié ?</a>
                        </div>
                        <button type="submit" class="btn-red w-100 justify-content-center" style="padding: 12px;">
                            <i class="fas fa-sign-in-alt me-2"></i> Connexion
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p>Pas encore de compte ?
                            <a href="<?php echo e(route('register')); ?>" style="color: var(--primary);">Inscrivez-vous</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(url('website/assets/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/aos.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/swiper-bundle.min.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/jquery.magnific-popup.min.js')); ?>"></script>
    <script src="<?php echo e(url('website/assets/js/main.js')); ?>"></script>
    <script>
        if (typeof AOS !== 'undefined') { AOS.init({ duration: 700, once: true }); }

        document.querySelectorAll('.btn-add-cart').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const productId = this.dataset.productId;
                fetch('<?php echo e(route('cart.add')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId, quantity: 1 }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        setTimeout(() => { this.innerHTML = '<i class="fas fa-plus"></i>'; location.reload(); }, 700);
                    } else {
                        alert(data.message || "Impossible d'ajouter ce produit au panier.");
                    }
                })
                .catch(() => alert('Une erreur est survenue.'));
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/home.blade.php ENDPATH**/ ?>