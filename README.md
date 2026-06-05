<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RestaurantMS') - Gestion Restaurant</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #e74c3c;
            --secondary-color: #2c3e50;
            --accent-color: #f39c12;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .footer {
            background-color: var(--secondary-color);
            color: white;
            margin-top: 50px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        
        .restaurant-card {
            position: relative;
            overflow: hidden;
        }
        
        .restaurant-card img {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .restaurant-card:hover img {
            transform: scale(1.1);
        }
        
        .product-card img {
            height: 180px;
            object-fit: cover;
        }
        
        .price-tag {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.7rem;
        }
        
        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        
        .filter-section {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .star-rating {
            color: #f39c12;
        }
        
        .hover-shadow:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @include('partials.navbar')
    
    <!-- Alertes -->
    <div class="toast-container">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>
    
    @include('partials.footer')
    @include('partials.cart-sidebar')
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Initialiser les toasts
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, { delay: 3000 });
            });
            toastList.forEach(toast => toast.show());
        });
        
        // Gestion du panier
        function addToCart(productId, quantity = 1) {
            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    updateCartCount();
                    showNotification('Produit ajouté au panier !', 'success');
                },
                error: function() {
                    showNotification('Erreur lors de l\'ajout au panier', 'error');
                }
            });
        }
        
        function updateCartCount() {
            $.get('{{ route("cart.index") }}', function(data) {
                const count = $(data).find('.cart-item').length;
                $('#cartCount').text(count);
                if (count > 0) {
                    $('#cartCount').removeClass('d-none');
                } else {
                    $('#cartCount').addClass('d-none');
                }
            });
        }
        
        function showNotification(message, type) {
            const toast = $(`
                <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);
            $('.toast-container').append(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
            bsToast.show();
            toast.on('hidden.bs.toast', function() { $(this).remove(); });
        }
    </script>
    
    @stack('scripts')
</body>
</html>


{{-- resources/views/layouts/app.blade.php --}}

<body>
    {{-- Navigation --}}
    @include('partials.navbar')
    
    {{-- Sidebar pour admin --}}
    @auth
        @if(auth()->user()->hasRole(['admin', 'manager']))
            @include('partials.sidebar')
        @endif
    @endauth
    
    {{-- Contenu principal --}}
    <main class="py-4">
        <div class="container-fluid">
            {{-- Messages flash --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    
    {{-- Footer --}}
    @include('partials.footer')
    
    {{-- Cart Sidebar --}}
    @include('partials.cart-sidebar')
    
    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    @stack('scripts')
    
    {{-- Notifications temps réel --}}
    <script>
        // Configuration des notifications
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        
        // Écoute des événements en temps réel
        Echo.channel('orders')
            .listen('OrderStatusChanged', (e) => {
                Toast.fire({
                    icon: 'info',
                    title: e.message
                });
            });
    </script>
</body>
</html>



--------------------------------------------------------------------------------------------
navbar user
--------------------------------------------------------------------------------------------
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-shop me-2"></i>RestaurantMS
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('restaurants.*') ? 'active' : '' }}" href="{{ route('restaurants.index') }}">
                        <i class="bi bi-building"></i> Restaurants
                    </a>
                </li>
            </ul>
            
            <form class="d-flex me-3" action="{{ route('search') }}" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Rechercher..." value="{{ request('q') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
            
            <ul class="navbar-nav">
                @auth
                    @if(auth()->user()->isStaff())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Admin
                            </a>
                        </li>
                    @endif
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span id="cartCount" class="badge bg-danger rounded-pill cart-badge {{ session('cart') ? '' : 'd-none' }}">
                                {{ count(session('cart', [])) }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;">
                            @if(session('cart'))
                                <h6 class="dropdown-header">Mon Panier</h6>
                                @php $cartItems = session('cart', []); @endphp
                                @foreach(array_slice($cartItems, 0, 3) as $item)
                                    @php $product = App\Models\Product::find($item['product_id']); @endphp
                                    @if($product)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small>{{ Str::limit($product->name, 20) }}</small>
                                            <small class="text-muted">x{{ $item['quantity'] }}</small>
                                        </div>
                                    @endif
                                @endforeach
                                @if(count($cartItems) > 3)
                                    <small class="text-muted">+ {{ count($cartItems) - 3 }} autres articles</small>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm w-100 mb-2">Voir le panier</a>
                                <a href="{{ route('checkout.index') }}" class="btn btn-success btn-sm w-100">Commander</a>
                            @else
                                <p class="text-center text-muted mb-0">Panier vide</p>
                            @endif
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle me-1" width="30" height="30" alt="">
                            {{ auth()->user()->first_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person"></i> Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.orders') }}"><i class="bi bi-box"></i> Mes commandes</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.favorites') }}"><i class="bi bi-heart"></i> Favoris</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> Inscription
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>


--------------------------------------------------------------------------------------------
Footer user
--------------------------------------------------------------------------------------------
<footer class="footer py-5 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-shop me-2"></i>RestaurantMS
                </h5>
                <p class="text-white-50">La plateforme de gestion de restaurant et de livraison la plus complète.</p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-twitter-x fs-5"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h6 class="text-white mb-3">Liens utiles</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('restaurants.index') }}" class="text-white-50 text-decoration-none">Restaurants</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Comment ça marche</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Blog</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-4">
                <h6 class="text-white mb-3">Aide</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Contact</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Support</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h6 class="text-white mb-3">Newsletter</h6>
                <form>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Votre email">
                        <button class="btn btn-primary" type="submit">S'inscrire</button>
                    </div>
                </form>
                <small class="text-white-50">Recevez nos offres et nouveautés</small>
            </div>
        </div>
        <hr class="border-light">
        <div class="row">
            <div class="col-md-6">
                <small class="text-white-50">&copy; {{ date('Y') }} RestaurantMS. Tous droits réservés.</small>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-white-50 text-decoration-none me-3">CGV</a>
                <a href="#" class="text-white-50 text-decoration-none">Confidentialité</a>
            </div>
        </div>
    </div>
</footer>

--------------------------------------------------------------------------------------------
Page d'accueil
--------------------------------------------------------------------------------------------

@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Découvrez les meilleurs restaurants</h1>
        <p class="lead mb-4">Commandez vos plats préférés et faites-vous livrer en quelques clics</p>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{ route('search') }}" method="GET">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" name="city" placeholder="Votre ville...">
                        <input type="text" class="form-control" name="q" placeholder="Quel plat recherchez-vous ?">
                        <button class="btn btn-warning" type="submit">
                            <i class="bi bi-search"></i> Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Restaurants en vedette -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">🏪 Restaurants populaires</h3>
            <a href="{{ route('restaurants.index') }}" class="btn btn-outline-primary">Voir tout <i class="bi bi-arrow-right"></i></a>
        </div>
        
        <div class="row g-4">
            @foreach($featuredRestaurants as $restaurant)
                <div class="col-md-4 col-lg-3">
                    <a href="{{ route('restaurants.show', $restaurant) }}" class="text-decoration-none">
                        <div class="card restaurant-card h-100">
                            <img src="{{ $restaurant->cover_url ?? 'https://via.placeholder.com/400x200?text=Restaurant' }}" 
                                 class="card-img-top" alt="{{ $restaurant->name }}">
                            <div class="card-body">
                                <h5 class="card-title text-dark">{{ $restaurant->name }}</h5>
                                <p class="card-text text-muted small">{{ Str::limit($restaurant->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= round($restaurant->reviews_avg_rating ?? 0) ? '-fill' : '' }}"></i>
                                            @endfor
                                        </span>
                                        <small class="text-muted">({{ $restaurant->reviews_count ?? 0 }})</small>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $restaurant->estimated_delivery_time ?? 30 }} min
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Produits populaires -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="fw-bold mb-4">🍽️ Plats populaires</h3>
        
        <div class="row g-4">
            @foreach($popularProducts as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100">
                        <img src="{{ $product->primary_image_url ?? 'https://via.placeholder.com/300x200?text=Produit' }}" 
                             class="card-img-top" alt="{{ $product->name }}">
                        @if($product->is_on_sale)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">-{{ $product->discount_percentage }}%</span>
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">{{ $product->name }}</h6>
                            <small class="text-muted">{{ $product->restaurant->name }}</small>
                            <p class="card-text small text-muted mt-2">{{ Str::limit($product->description, 50) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($product->is_on_sale)
                                        <span class="original-price">{{ number_format($product->compare_price, 2) }} €</span>
                                    @endif
                                    <span class="price-tag">{{ $product->formatted_price }}</span>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Catégories -->
<section class="py-5">
    <div class="container">
        <h3 class="fw-bold mb-4">📋 Catégories</h3>
        
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-md-3 col-6">
                    <a href="{{ route('restaurants.index') }}?category={{ $category->slug }}" class="text-decoration-none">
                        <div class="card text-center hover-shadow">
                            <div class="card-body py-4">
                                <i class="bi bi-{{ ['cup-hot', 'egg-fried', 'cake2', 'cup-straw', 'basket', 'heart', 'star', 'gem'][$loop->index % 8] }} display-4 text-primary mb-3"></i>
                                <h6 class="card-title text-dark">{{ $category->name }}</h6>
                                <small class="text-muted">{{ $category->available_products_count }} plats</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Comment ça marche -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="fw-bold text-center mb-5">💡 Comment ça marche ?</h3>
        
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="card h-100">
                    <div class="card-body py-5">
                        <i class="bi bi-geo-alt display-3 text-primary mb-3"></i>
                        <h5>1. Choisissez un restaurant</h5>
                        <p class="text-muted">Parcourez les restaurants près de chez vous</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card h-100">
                    <div class="card-body py-5">
                        <i class="bi bi-cart-check display-3 text-primary mb-3"></i>
                        <h5>2. Commandez</h5>
                        <p class="text-muted">Sélectionnez vos plats et personnalisez votre commande</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card h-100">
                    <div class="card-body py-5">
                        <i class="bi bi-truck display-3 text-primary mb-3"></i>
                        <h5>3. Dégustez</h5>
                        <p class="text-muted">Recevez votre commande et régalez-vous !</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


--------------------------------------------------------------------------------------------
Page Menu Public (menu/index.blade.php)
--------------------------------------------------------------------------------------------

@extends('layouts.app')

@section('title', $restaurant->name . ' - Menu')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Catégories -->
        <div class="col-md-3">
            <div class="sidebar sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3">Catégories</h5>
                <div class="list-group list-group-flush">
                    <a href="{{ route('restaurant.menu', $restaurant) }}" 
                       class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">
                        <i class="bi bi-grid me-2"></i>Tout le menu
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('restaurant.menu', ['restaurant' => $restaurant, 'category' => $category->slug]) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == $category->slug ? 'active' : '' }}">
                            {{ $category->name }}
                            <span class="badge bg-secondary float-end">{{ $category->available_products_count }}</span>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Filtres diététiques</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-checkbox" type="checkbox" id="vegetarian" 
                               {{ request('vegetarian') ? 'checked' : '' }}
                               onchange="window.location.href='{{ request()->fullUrlWithQuery(['vegetarian' => request('vegetarian') ? '' : '1']) }}'">
                        <label class="form-check-label" for="vegetarian">🥬 Végétarien</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-checkbox" type="checkbox" id="vegan"
                               {{ request('vegan') ? 'checked' : '' }}
                               onchange="window.location.href='{{ request()->fullUrlWithQuery(['vegan' => request('vegan') ? '' : '1']) }}'">
                        <label class="form-check-label" for="vegan">🌱 Vegan</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-checkbox" type="checkbox" id="gluten_free"
                               {{ request('gluten_free') ? 'checked' : '' }}
                               onchange="window.location.href='{{ request()->fullUrlWithQuery(['gluten_free' => request('gluten_free') ? '' : '1']) }}'">
                        <label class="form-check-label" for="gluten_free">🌾 Sans gluten</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des Produits -->
        <div class="col-md-9">
            <!-- Barre de recherche -->
            <div class="mb-4">
                <form action="{{ route('restaurant.menu', $restaurant) }}" method="GET">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Rechercher un plat dans ce restaurant..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </div>
                </form>
            </div>
            
            <!-- Résultats -->
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="card product-card h-100">
                            <a href="{{ route('products.show', $product) }}">
                                <img src="{{ $product->primary_image_url ?? 'https://via.placeholder.com/300x200?text='.$product->name }}" 
                                     class="card-img-top" alt="{{ $product->name }}">
                            </a>
                            
                            @if($product->is_on_sale)
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            @endif
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $product->name }}</h6>
                                    <div class="text-end">
                                        @if($product->is_on_sale)
                                            <small class="original-price">{{ number_format($product->compare_price, 2) }} €</small>
                                        @endif
                                        <span class="price-tag ms-2">{{ $product->formatted_price }}</span>
                                    </div>
                                </div>
                                
                                <p class="card-text text-muted small">{{ Str::limit($product->description, 60) }}</p>
                                
                                <div class="mb-2">
                                    @if($product->is_vegetarian)
                                        <span class="badge bg-success me-1" title="Végétarien">🥬</span>
                                    @endif
                                    @if($product->is_vegan)
                                        <span class="badge bg-success me-1" title="Vegan">🌱</span>
                                    @endif
                                    @if($product->is_spicy)
                                        <span class="badge bg-danger me-1" title="Épicé">🌶️</span>
                                    @endif
                                    @if($product->calories)
                                        <small class="text-muted">{{ $product->calories }} cal</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Détails
                                    </a>
                                    @if($product->is_in_stock)
                                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                            <i class="bi bi-cart-plus"></i> Ajouter
                                        </button>
                                    @else
                                        <span class="badge bg-danger">Rupture</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-emoji-frown display-1 text-muted"></i>
                        <h3 class="mt-3">Aucun produit trouvé</h3>
                        <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
Page Panier (cart/index.blade.php)
--------------------------------------------------------------------------------------------

@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>Mon Panier</h3>
    
    @if(count($cartItems) > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="cart-item row align-items-center mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <img src="{{ $item['product']->primary_image_url ?? 'https://via.placeholder.com/80' }}" 
                                         class="img-fluid rounded" alt="{{ $item['product']->name }}">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1">{{ $item['product']->name }}</h6>
                                    @if(!empty($item['options']))
                                        <small class="text-muted">
                                            @foreach($item['options'] as $option)
                                                + {{ $option['name'] ?? 'Option' }}<br>
                                            @endforeach
                                        </small>
                                    @endif
                                    @if($item['notes'])
                                        <small class="text-muted fst-italic">"{{ $item['notes'] }}"</small>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity({{ $item['key'] }}, -1)">-</button>
                                        <input type="text" class="form-control text-center" value="{{ $item['quantity'] }}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity({{ $item['key'] }}, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <span class="fw-bold">{{ number_format($item['item_total'], 2) }} €</span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <a href="{{ route('cart.remove', $item['key']) }}" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger" onclick="return confirm('Vider le panier ?')">
                                <i class="bi bi-trash"></i> Vider le panier
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Continuer mes achats
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Résumé -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Résumé</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total</span>
                            <span>{{ number_format($subtotal, 2) }} €</span>
                        </div>
                        
                        @if($discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Réduction</span>
                                <span>-{{ number_format($discount, 2) }} €</span>
                            </div>
                        @endif
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="price-tag">{{ number_format($total, 2) }} €</strong>
                        </div>
                        
                        <!-- Code promo -->
                        @if($couponCode)
                            <div class="alert alert-success mb-3">
                                <small>Code promo : <strong>{{ $couponCode }}</strong></small>
                                <a href="{{ route('cart.coupon.remove') }}" class="float-end text-danger"><i class="bi bi-x-circle"></i></a>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon') }}" method="POST" class="mb-3">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="code" placeholder="Code promo">
                                    <button class="btn btn-outline-primary" type="submit">Appliquer</button>
                                </div>
                            </form>
                        @endif
                        
                        <a href="{{ route('checkout.index') }}" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-check-circle"></i> Commander
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-3">Votre panier est vide</h3>
            <p class="text-muted">Parcourez nos restaurants et ajoutez des plats à votre panier</p>
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-shop"></i> Voir les restaurants
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updateQuantity(key, change) {
    // Logique de mise à jour de quantité via AJAX
    $.ajax({
        url: '{{ route("cart.update") }}',
        method: 'PUT',
        data: {
            items: [{ index: key, quantity: parseInt($('input').eq(key).val()) + change }],
            _token: '{{ csrf_token() }}'
        },
        success: function() {
            location.reload();
        }
    });
}
</script>
@endpush

--------------------------------------------------------------------------------------------
Admin - Gestion des Commandes
--------------------------------------------------------------------------------------------

@extends('layouts.admin')

@section('title', 'Gestion des Commandes')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4"><i class="bi bi-cart-check me-2"></i>Commandes</h3>

    <!-- Stats rapides -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card bg-warning text-white">
                <div class="card-body text-center py-3">
                    <h5 class="mb-0">{{ $stats['pending'] }}</h5>
                    <small>En attente</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-3">
                    <h5 class="mb-0">{{ $stats['preparing'] }}</h5>
                    <small>En préparation</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-3">
                    <h5 class="mb-0">{{ $stats['ready'] }}</h5>
                    <small>Prêtes</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-3">
                    <h5 class="mb-0">{{ $stats['today'] }}</h5>
                    <small>Aujourd'hui</small>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-dark text-white">
                <div class="card-body text-center py-3">
                    <h5 class="mb-0">{{ number_format($stats['today_revenue'], 0) }}€</h5>
                    <small>CA du jour</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En préparation</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Prête</option>
                        <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>En livraison</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="dine_in" {{ request('type') == 'dine_in' ? 'selected' : '' }}>Sur place</option>
                        <option value="takeaway" {{ request('type') == 'takeaway' ? 'selected' : '' }}>À emporter</option>
                        <option value="delivery" {{ request('type') == 'delivery' ? 'selected' : '' }}>Livraison</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="N° commande ou client..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Articles</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->order_number }}</strong></td>
                                <td>
                                    {{ $order->user->full_name }}
                                    <br><small class="text-muted">{{ $order->user->phone }}</small>
                                </td>
                                <td>
                                    @if($order->type == 'dine_in')
                                        <span class="badge bg-info">🏠 Sur place</span>
                                    @elseif($order->type == 'takeaway')
                                        <span class="badge bg-warning">🥡 À emporter</span>
                                    @else
                                        <span class="badge bg-primary">🛵 Livraison</span>
                                    @endif
                                </td>
                                <td>{{ $order->items->sum('quantity') }}</td>
                                <td><strong>{{ number_format($order->total, 2) }} €</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Aucune commande trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
Admin - Gestion des Commandes : show
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Commande #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="bi bi-receipt me-2"></i>Commande #{{ $order->order_number }}
        </h3>
        <div>
            <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-outline-secondary me-2" target="_blank">
                <i class="bi bi-printer"></i> Imprimer
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informations de la commande -->
        <div class="col-md-8">
            <!-- Statut -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Statut de la commande</h5>
                        <span class="badge bg-{{ $order->status_color }} fs-6">{{ $order->status_label }}</span>
                    </div>
                    
                    <!-- Progression -->
                    <div class="progress-steps mb-4">
                        @php
                            $statuses = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'preparing' => 'En préparation', 'ready' => 'Prête', 'in_delivery' => 'En livraison', 'delivered' => 'Livrée'];
                            $currentIndex = array_search($order->status, array_keys($statuses));
                        @endphp
                        <div class="d-flex justify-content-between">
                            @foreach($statuses as $status => $label)
                                @php $index = array_search($status, array_keys($statuses)); @endphp
                                <div class="text-center">
                                    <div class="step-circle {{ $index <= $currentIndex ? 'bg-primary text-white' : 'bg-light' }}" 
                                         style="width:30px;height:30px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
                                        @if($index < $currentIndex)
                                            <i class="bi bi-check"></i>
                                        @elseif($index == $currentIndex)
                                            <i class="bi bi-arrow-right"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <br><small class="text-muted">{{ $label }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="d-flex gap-2 flex-wrap">
                        @if($order->status == 'pending')
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button class="btn btn-info"><i class="bi bi-check-circle"></i> Confirmer</button>
                            </form>
                        @endif
                        
                        @if($order->status == 'confirmed')
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="preparing">
                                <button class="btn btn-primary"><i class="bi bi-fire"></i> En préparation</button>
                            </form>
                        @endif
                        
                        @if($order->status == 'preparing')
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="ready">
                                <button class="btn btn-success"><i class="bi bi-check-all"></i> Prête</button>
                            </form>
                        @endif
                        
                        @if($order->status == 'ready' && $order->type == 'delivery')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignDeliveryModal">
                                <i class="bi bi-truck"></i> Assigner livreur
                            </button>
                        @endif
                        
                        @if(in_array($order->status, ['pending', 'confirmed']))
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle"></i> Annuler
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Articles -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Articles commandés</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <img src="{{ $item->product->primary_image_url ?? 'https://via.placeholder.com/50' }}" 
                                     class="rounded me-3" width="50" height="50" alt="">
                                <div>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if($item->special_instructions)
                                        <br><small class="text-muted fst-italic">"{{ $item->special_instructions }}"</small>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="text-muted">x{{ $item->quantity }}</span>
                                <span class="ms-3 fw-bold">{{ number_format($item->total_price, 2) }} €</span>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="row mt-3">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sous-total</span>
                                <span>{{ number_format($order->subtotal, 2) }} €</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>TVA</span>
                                <span>{{ number_format($order->tax_amount, 2) }} €</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Réduction</span>
                                    <span>-{{ number_format($order->discount_amount, 2) }} €</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span>Livraison</span>
                                <span>{{ number_format($order->delivery_fee, 2) }} €</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="text-primary fs-5">{{ number_format($order->total, 2) }} €</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Historique -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Historique</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($order->statusHistory as $history)
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width:30px;height:30px;">
                                        <i class="bi bi-{{ $loop->first ? 'clock' : 'check' }}"></i>
                                    </div>
                                </div>
                                <div>
                                    <strong>{{ ucfirst($history->status) }}</strong>
                                    @if($history->comment)
                                        <p class="mb-0 text-muted">{{ $history->comment }}</p>
                                    @endif
                                    <small class="text-muted">
                                        {{ $history->created_at->format('d/m/Y H:i') }} 
                                        par {{ $history->user->full_name ?? 'Système' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar infos -->
        <div class="col-md-4">
            <!-- Client -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">👤 Client</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $order->user->avatar_url }}" class="rounded-circle me-2" width="40" height="40" alt="">
                        <div>
                            <strong>{{ $order->user->full_name }}</strong>
                            <br><small>{{ $order->user->email }}</small>
                        </div>
                    </div>
                    <p><i class="bi bi-telephone me-2"></i>{{ $order->user->phone }}</p>
                    <p><i class="bi bi-geo-alt me-2"></i>{{ $order->delivery_address ?? 'N/A' }}</p>
                </div>
            </div>
            
            <!-- Paiement -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">💳 Paiement</h5>
                </div>
                <div class="card-body">
                    <p><strong>Méthode :</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p><strong>Statut :</strong> 
                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ $order->payment_status }}
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Livraison -->
            @if($order->type == 'delivery')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">🛵 Livraison</h5>
                    </div>
                    <div class="card-body">
                        @if($order->deliveryPerson)
                            <p><strong>Livreur :</strong> {{ $order->deliveryPerson->full_name }}</p>
                        @endif
                        <p><strong>Instructions :</strong> {{ $order->delivery_instructions ?? 'Aucune' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Assigner Livreur -->
<div class="modal fade" id="assignDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.delivery', $order) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assigner un livreur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="delivery_person_id" class="form-select" required>
                        <option value="">Choisir un livreur</option>
                        @foreach($deliveryPersons as $person)
                            <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Annuler -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Annuler la commande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Raison de l'annulation</label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
Checkout Page
checkout/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-credit-card me-2"></i>Finaliser la commande</h3>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            <div class="col-md-8">
                <!-- Adresse de livraison -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📍 Adresse de livraison</h5>
                    </div>
                    <div class="card-body">
                        @if($addresses->count() > 0)
                            @foreach($addresses as $address)
                                <div class="form-check mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="address_id" 
                                           id="address{{ $address->id }}" value="{{ $address->id }}"
                                           {{ ($defaultAddress && $defaultAddress->id == $address->id) || $loop->first ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="address{{ $address->id }}">
                                        <strong>{{ $address->label }}</strong><br>
                                        {{ $address->street_address }}, {{ $address->postal_code }} {{ $address->city }}
                                        @if($address->instructions)
                                            <br><small class="text-muted">📝 {{ $address->instructions }}</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Aucune adresse enregistrée. 
                                <a href="{{ route('profile.index') }}">Ajouter une adresse</a>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Instructions de livraison</label>
                            <textarea name="delivery_instructions" class="form-control" rows="2" 
                                      placeholder="Code porte, étage, interphone..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Mode de paiement -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">💳 Mode de paiement</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                            <label class="form-check-label" for="cash">
                                <i class="bi bi-cash fs-4 me-2"></i> Espèces (à la livraison)
                            </label>
                        </div>
                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                            <label class="form-check-label" for="card">
                                <i class="bi bi-credit-card fs-4 me-2"></i> Carte bancaire (à la livraison)
                            </label>
                        </div>
                        <div class="form-check p-3 border rounded">
                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="online">
                            <label class="form-check-label" for="online">
                                <i class="bi bi-phone fs-4 me-2"></i> Paiement en ligne
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Notes pour le restaurant (allergies, préférences...)"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Résumé -->
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">🛒 Résumé de la commande</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">{{ $restaurant->name }}</h6>
                        
                        @foreach($cartItems as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <small>{{ $item['quantity'] }}x {{ $item['product']->name }}</small>
                                <small>{{ number_format($item['total'], 2) }} €</small>
                            </div>
                        @endforeach
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total</span>
                            <span>{{ number_format($subtotal, 2) }} €</span>
                        </div>
                        
                        @if($discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Réduction</span>
                                <span>-{{ number_format($discount, 2) }} €</span>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frais de livraison</span>
                            <span>{{ number_format($deliveryFee, 2) }} €</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>TVA ({{ $restaurant->tax_rate }}%)</span>
                            <span>{{ number_format($taxAmount, 2) }} €</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary fs-5">{{ number_format($total, 2) }} €</strong>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-check-circle"></i> Confirmer la commande
                        </button>
                        
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left"></i> Retour au panier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

--------------------------------------------------------------------------------------------
Profile Pages
profile/index.blade.php
--------------------------------------------------------------------------------------------

@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle mb-3" width="100" height="100" alt="">
                    <h5>{{ auth()->user()->full_name }}</h5>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                    <span class="badge bg-primary">{{ auth()->user()->getRoleNames()->first() }}</span>
                </div>
            </div>
            
            <div class="list-group mt-3">
                <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-person me-2"></i> Mon profil
                </a>
                <a href="{{ route('profile.orders') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-box me-2"></i> Mes commandes
                </a>
                <a href="{{ route('profile.favorites') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-heart me-2"></i> Favoris
                </a>
            </div>
        </div>
        
        <!-- Contenu -->
        <div class="col-md-9">
            <!-- Infos personnelles -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="first_name" class="form-control" value="{{ auth()->user()->first_name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" name="last_name" class="form-control" value="{{ auth()->user()->last_name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="phone" class="form-control" value="{{ auth()->user()->phone }}" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-check-lg"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Adresses -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes adresses</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="bi bi-plus-lg"></i> Ajouter
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($addresses as $address)
                            <div class="col-md-6">
                                <div class="border rounded p-3 position-relative">
                                    @if($address->is_default)
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">Défaut</span>
                                    @endif
                                    <h6>{{ $address->label }}</h6>
                                    <p class="mb-1">{{ $address->street_address }}</p>
                                    <p class="mb-1">{{ $address->postal_code }} {{ $address->city }}</p>
                                    @if($address->instructions)
                                        <small class="text-muted">📝 {{ $address->instructions }}</small>
                                    @endif
                                    <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST" class="mt-2">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Aucune adresse enregistrée</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Mot de passe -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Changer le mot de passe</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirmer</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning mt-3">
                            <i class="bi bi-key"></i> Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Adresse -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('profile.addresses.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle adresse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Libellé</label>
                        <input type="text" name="label" class="form-control" placeholder="Domicile, Travail..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="street_address" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ville</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code postal</label>
                            <input type="text" name="postal_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_default" value="1" class="form-check-input" id="isDefault">
                        <label class="form-check-label" for="isDefault">Définir comme adresse par défaut</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
Suivi de commande
orders/track.blade.php
--------------------------------------------------------------------------------------------

@extends('layouts.app')

@section('title', 'Suivi de commande #' . $order->order_number)

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <i class="bi bi-check-circle display-1 text-success"></i>
        <h3 class="mt-3">Commande confirmée !</h3>
        <p class="text-muted">Votre commande <strong>#{{ $order->order_number }}</strong> a bien été enregistrée.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Statut -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5>Statut actuel</h5>
                    <span class="badge bg-{{ $order->status_color }} fs-5 px-4 py-2">
                        {{ $order->status_label }}
                    </span>
                    <p class="mt-2 text-muted">
                        Estimation de livraison : {{ $order->estimated_delivery_time ?? 30 }} minutes
                    </p>
                </div>
            </div>

            <!-- Progression -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="progress-steps">
                        @php
                            $statuses = [
                                'pending' => 'Commande reçue',
                                'confirmed' => 'Confirmée',
                                'preparing' => 'En préparation',
                                'ready' => 'Prête',
                                'in_delivery' => 'En livraison',
                                'delivered' => 'Livrée'
                            ];
                            $currentIndex = array_search($order->status, array_keys($statuses));
                        @endphp
                        
                        @foreach($statuses as $status => $label)
                            @php $index = array_search($status, array_keys($statuses)); @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white
                                        {{ $index < $currentIndex ? 'bg-success' : ($index == $currentIndex ? 'bg-primary' : 'bg-light text-dark') }}"
                                        style="width:35px;height:35px;">
                                        @if($index < $currentIndex)
                                            <i class="bi bi-check-lg"></i>
                                        @elseif($index == $currentIndex)
                                            <i class="bi bi-arrow-right"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <strong>{{ $label }}</strong>
                                    @if($index < $currentIndex)
                                        <small class="text-success d-block">Terminé</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Détails -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">📋 Détails de la commande</h6>
                        </div>
                        <div class="card-body">
                            @foreach($order->items as $item)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
                                    <span>{{ number_format($item->total_price, 2) }} €</span>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>{{ number_format($order->total, 2) }} €</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">📍 Livraison</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Adresse :</strong><br>{{ $order->delivery_address }}</p>
                            <p><strong>Instructions :</strong><br>{{ $order->delivery_instructions ?? 'Aucune' }}</p>
                            @if($order->deliveryPerson)
                                <p><strong>Livreur :</strong><br>{{ $order->deliveryPerson->full_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-house"></i> Retour à l'accueil
                </a>
                <a href="{{ route('profile.orders') }}" class="btn btn-outline-primary btn-lg ms-2">
                    <i class="bi bi-box"></i> Mes commandes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
Admin - Gestion des Catégories
admin/categories/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-grid me-2"></i>Catégories</h3>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvelle catégorie
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Produits</th>
                            <th>Ordre</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset('storage/'.$category->image) }}" class="rounded" width="50" height="50" style="object-fit:cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                            <i class="bi bi-folder text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                    @if($category->parent)
                                        <br><small class="text-muted">Parent : {{ $category->parent->name }}</small>
                                    @endif
                                </td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $category->products_count }}</span>
                                </td>
                                <td>{{ $category->sort_order }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer cette catégorie ? Les produits associés ne seront pas supprimés.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Aucune catégorie trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
admin/categories/create.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Nouvelle Catégorie')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nouvelle Catégorie</h3>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catégorie parente</label>
                            <select name="parent_id" class="form-select">
                                <option value="">Aucune (catégorie racine)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ordre d'affichage</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Catégorie active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Format carré recommandé</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Créer la catégorie
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
admin/categories/create.blade.php
--------------------------------------------------------------------------------------------

@extends('layouts.admin')

@section('title', 'Nouvelle Catégorie')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nouvelle Catégorie</h3>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catégorie parente</label>
                            <select name="parent_id" class="form-select">
                                <option value="">Aucune (catégorie racine)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ordre d'affichage</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Catégorie active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Format carré recommandé</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Créer la catégorie
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
Admin - Gestion des Coupons
admin/coupons/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Gestion des Coupons')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-ticket-perforated me-2"></i>Coupons de réduction</h3>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau coupon
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Valeur</th>
                            <th>Min. commande</th>
                            <th>Utilisations</th>
                            <th>Validité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>
                                    <span class="badge bg-dark fs-6">{{ $coupon->code }}</span>
                                </td>
                                <td>
                                    @if($coupon->type == 'percentage')
                                        <span class="badge bg-info">Pourcentage</span>
                                    @elseif($coupon->type == 'fixed_amount')
                                        <span class="badge bg-primary">Montant fixe</span>
                                    @else
                                        <span class="badge bg-success">Livraison offerte</span>
                                    @endif
                                </td>
                                <td><strong>{{ $coupon->formatted_value }}</strong></td>
                                <td>{{ number_format($coupon->min_order_amount, 2) }} €</td>
                                <td>
                                    {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                                    <div class="progress" style="height:5px;">
                                        <div class="progress-bar" style="width: {{ $coupon->usage_percentage }}%"></div>
                                    </div>
                                </td>
                                <td>
                                    @if($coupon->expires_at)
                                        <small>{{ $coupon->expires_at->format('d/m/Y') }}</small>
                                        @if($coupon->is_expired)
                                            <span class="badge bg-danger">Expiré</span>
                                        @endif
                                    @else
                                        <small class="text-muted">Illimitée</small>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->is_valid)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer ce coupon ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Aucun coupon créé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
admin/coupons/create.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Nouveau Coupon')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2"></i>Nouveau Coupon</h3>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Code promo *</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" placeholder="ex: ETE2024" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Type *</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" id="couponType" required>
                                    <option value="">Choisir...</option>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>Montant fixe (€)</option>
                                    <option value="free_delivery" {{ old('type') == 'free_delivery' ? 'selected' : '' }}>Livraison gratuite</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4" id="valueField">
                                <label class="form-label">Valeur *</label>
                                <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Montant minimum (€)</label>
                                <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', 0) }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Réduction max (€)</label>
                                <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount') }}">
                                <small class="text-muted">Pour les coupons en %</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Utilisations max</label>
                                <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses') }}">
                                <small class="text-muted">Laisser vide pour illimité</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Utilisations par utilisateur</label>
                                <input type="number" name="max_uses_per_user" class="form-control" value="{{ old('max_uses_per_user', 1) }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Date de début</label>
                                <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Date d'expiration</label>
                                <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="{{ old('description') }}" 
                                       placeholder="Description visible par le client">
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label">Coupon actif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Créer le coupon
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('couponType').addEventListener('change', function() {
    const valueField = document.getElementById('valueField');
    if (this.value === 'free_delivery') {
        valueField.style.display = 'none';
    } else {
        valueField.style.display = 'block';
    }
});
</script>
@endpush
@endsection



--------------------------------------------------------------------------------------------
Admin - Gestion des Avis
admin/reviews/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Gestion des Avis')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4"><i class="bi bi-star me-2"></i>Avis clients</h3>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tous les avis</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvés</option>
                        <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>En vedette</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">Toutes les notes</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} étoiles
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des avis -->
    <div class="card">
        <div class="card-body">
            @forelse($reviews as $review)
                <div class="review-item p-3 mb-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <img src="{{ $review->user->avatar_url }}" class="rounded-circle me-2" width="40" height="40" alt="">
                            <div>
                                <strong>{{ $review->user->full_name }}</strong>
                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                @if($review->product)
                                    <small class="text-muted">Sur : {{ $review->product->name }}</small>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if(!$review->is_approved)
                                <span class="badge bg-warning">En attente</span>
                            @endif
                            @if($review->is_featured)
                                <span class="badge bg-info">⭐ Vedette</span>
                            @endif
                            <small class="text-muted d-block">{{ $review->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    
                    <p class="mt-2 mb-2">{{ $review->comment }}</p>
                    
                    @if($review->admin_response)
                        <div class="bg-light p-2 rounded">
                            <strong>Réponse :</strong> {{ $review->admin_response }}
                        </div>
                    @endif
                    
                    <div class="d-flex gap-2 mt-2">
                        @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check"></i> Approuver
                                </button>
                            </form>
                        @endif
                        
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                data-bs-target="#respondModal{{ $review->id }}">
                            <i class="bi bi-reply"></i> Répondre
                        </button>
                        
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Supprimer cet avis ?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Modal Réponse -->
                <div class="modal fade" id="respondModal{{ $review->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.reviews.respond', $review) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Répondre à l'avis</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea name="response" class="form-control" rows="3" 
                                              placeholder="Votre réponse...">{{ $review->admin_response }}</textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Publier</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-chat-square-text display-4 text-muted"></i>
                    <p class="mt-2">Aucun avis trouvé</p>
                </div>
            @endforelse
            
            <div class="mt-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
Admin - Rapports
admin/reports/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Rapports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-graph-up me-2"></i>Rapports</h3>
        <div>
            <select id="periodSelect" class="form-select d-inline-block w-auto me-2" onchange="window.location.href='?period='+this.value">
                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Cette année</option>
            </select>
            <a href="{{ route('admin.reports.export', ['period' => $period]) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Exporter CSV
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total commandes</h6>
                    <h2>{{ $salesStats['total_orders'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Chiffre d'affaires</h6>
                    <h2>{{ number_format($salesStats['total_revenue'], 0) }} €</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Remises accordées</h6>
                    <h2>{{ number_format($salesStats['total_discounts'], 0) }} €</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Panier moyen</h6>
                    <h2>{{ number_format($salesStats['average_order'], 2) }} €</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Commandes par type -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Commandes par type</h5></div>
                <div class="card-body">
                    <canvas id="ordersByTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Commandes par statut -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Commandes par statut</h5></div>
                <div class="card-body">
                    <canvas id="ordersByStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Ventes quotidiennes -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Ventes quotidiennes</h5></div>
                <div class="card-body">
                    <canvas id="dailySalesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Produits -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Top 10 Produits</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produit</th>
                                    <th>Ventes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td><span class="badge bg-success">{{ $product->total_sold }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Top 10 Clients</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Dépensé</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $index => $customer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $customer->full_name }}</td>
                                        <td>{{ number_format($customer->total_spent ?? 0, 2) }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Commandes par type
new Chart(document.getElementById('ordersByTypeChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($ordersByType->pluck('type')->map(fn($t) => match($t) {'dine_in' => 'Sur place', 'takeaway' => 'À emporter', 'delivery' => 'Livraison', default => $t})) !!},
        datasets: [{
            data: {!! json_encode($ordersByType->pluck('count')) !!},
            backgroundColor: ['#3498db', '#f39c12', '#e74c3c']
        }]
    }
});

// Commandes par statut
new Chart(document.getElementById('ordersByStatusChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($ordersByStatus->pluck('status')) !!},
        datasets: [{
            label: 'Nombre',
            data: {!! json_encode($ordersByStatus->pluck('count')) !!},
            backgroundColor: '#2ecc71'
        }]
    }
});

// Ventes quotidiennes
new Chart(document.getElementById('dailySalesChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($dailySales->pluck('date')) !!},
        datasets: [{
            label: 'Revenus (€)',
            data: {!! json_encode($dailySales->pluck('revenue')) !!},
            borderColor: '#e74c3c',
            tension: 0.3,
            fill: false
        }]
    }
});
</script>
@endpush



--------------------------------------------------------------------------------------------
Admin - Paramètres
admin/settings/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4"><i class="bi bi-gear me-2"></i>Paramètres du restaurant</h3>

    <div class="row g-4">
        <!-- Informations générales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Informations générales</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.restaurant') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du restaurant *</label>
                                <input type="text" name="name" class="form-control" value="{{ $restaurant->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $restaurant->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="phone" class="form-control" value="{{ $restaurant->phone }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Site web</label>
                                <input type="url" name="website" class="form-control" value="{{ $restaurant->website }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $restaurant->description }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="address" class="form-control" value="{{ $restaurant->address }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ville</label>
                                <input type="text" name="city" class="form-control" value="{{ $restaurant->city }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Code postal</label>
                                <input type="text" name="postal_code" class="form-control" value="{{ $restaurant->postal_code }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pays</label>
                                <input type="text" name="country" class="form-control" value="{{ $restaurant->country }}" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-check-lg"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Paramètres de commande -->
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Paramètres de commande</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.restaurant') }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Commande minimum (€)</label>
                                <input type="number" step="0.01" name="minimum_order" class="form-control" 
                                       value="{{ $restaurant->minimum_order }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Frais de livraison (€)</label>
                                <input type="number" step="0.01" name="delivery_fee" class="form-control" 
                                       value="{{ $restaurant->delivery_fee }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Taux de TVA (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control" 
                                       value="{{ $restaurant->tax_rate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Temps de livraison estimé (min)</label>
                                <input type="number" name="estimated_delivery_time" class="form-control" 
                                       value="{{ $restaurant->estimated_delivery_time }}">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="accepts_delivery" value="1" 
                                           {{ $restaurant->accepts_delivery ? 'checked' : '' }}>
                                    <label class="form-check-label">Livraison</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="accepts_takeaway" value="1"
                                           {{ $restaurant->accepts_takeaway ? 'checked' : '' }}>
                                    <label class="form-check-label">À emporter</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="accepts_dine_in" value="1"
                                           {{ $restaurant->accepts_dine_in ? 'checked' : '' }}>
                                    <label class="form-check-label">Sur place</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-check-lg"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Logo et couverture -->
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Logo et couverture</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.restaurant') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            @if($restaurant->logo_url)
                                <img src="{{ $restaurant->logo_url }}" class="img-fluid rounded mb-2" style="max-height:100px;">
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image de couverture</label>
                            @if($restaurant->cover_url)
                                <img src="{{ $restaurant->cover_url }}" class="img-fluid rounded mb-2">
                            @endif
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload"></i> Mettre à jour les images
                        </button>
                    </form>
                </div>
            </div>

            <!-- Horaires -->
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Horaires d'ouverture</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.hours') }}" method="POST">
                        @csrf @method('PUT')
                        
                        @php
                            $days = ['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 
                                    'thursday' => 'Jeudi', 'friday' => 'Vendredi', 'saturday' => 'Samedi', 'sunday' => 'Dimanche'];
                            $hours = $restaurant->opening_hours ?? [];
                        @endphp
                        
                        @foreach($days as $key => $label)
                            <div class="row g-2 mb-2 align-items-center">
                                <div class="col-3">
                                    <strong>{{ $label }}</strong>
                                </div>
                                <div class="col-4">
                                    <input type="time" name="opening_hours[{{ $key }}][open]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $hours[$key]['open'] ?? '09:00' }}">
                                </div>
                                <div class="col-4">
                                    <input type="time" name="opening_hours[{{ $key }}][close]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $hours[$key]['close'] ?? '22:00' }}">
                                </div>
                                <div class="col-1">
                                    <input type="checkbox" name="opening_hours[{{ $key }}][closed]" value="1"
                                           {{ isset($hours[$key]['closed']) && $hours[$key]['closed'] ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-clock"></i> Enregistrer les horaires
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



--------------------------------------------------------------------------------------------
Admin - Édition Catégorie
admin/categories/edit.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Modifier la Catégorie')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $category->name }}</h3>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $category->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $category->description) }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catégorie parente</label>
                            <select name="parent_id" class="form-select">
                                <option value="">Aucune (catégorie racine)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Ne pas sélectionner la catégorie elle-même</small>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ordre d'affichage</label>
                                <input type="number" name="sort_order" class="form-control" 
                                       value="{{ old('sort_order', $category->sort_order) }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Catégorie active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            @if($category->image_url)
                                <div class="mb-2">
                                    <img src="{{ $category->image_url }}" class="rounded" style="max-height:100px;">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
Admin - Édition Catégorie
admin/categories/edit.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Modifier le Coupon')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $coupon->code }}</h3>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Code promo *</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $coupon->code) }}" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Type *</label>
                                <select name="type" class="form-select" id="couponType" required>
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixed_amount" {{ old('type', $coupon->type) == 'fixed_amount' ? 'selected' : '' }}>Montant fixe (€)</option>
                                    <option value="free_delivery" {{ old('type', $coupon->type) == 'free_delivery' ? 'selected' : '' }}>Livraison gratuite</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4" id="valueField" style="{{ $coupon->type == 'free_delivery' ? 'display:none;' : '' }}">
                                <label class="form-label">Valeur *</label>
                                <input type="number" step="0.01" name="value" class="form-control" 
                                       value="{{ old('value', $coupon->value) }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Montant minimum (€)</label>
                                <input type="number" step="0.01" name="min_order_amount" class="form-control" 
                                       value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Réduction max (€)</label>
                                <input type="number" step="0.01" name="max_discount_amount" class="form-control" 
                                       value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Utilisations max</label>
                                <input type="number" name="max_uses" class="form-control" 
                                       value="{{ old('max_uses', $coupon->max_uses) }}">
                                <small class="text-muted">Actuel : {{ $coupon->used_count }} utilisations</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Utilisations par utilisateur</label>
                                <input type="number" name="max_uses_per_user" class="form-control" 
                                       value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Date de début</label>
                                <input type="datetime-local" name="starts_at" class="form-control" 
                                       value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Date d'expiration</label>
                                <input type="datetime-local" name="expires_at" class="form-control" 
                                       value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" 
                                       value="{{ old('description', $coupon->description) }}">
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Coupon actif</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="applies_to_all" value="1" 
                                           {{ old('applies_to_all', $coupon->applies_to_all) ? 'checked' : '' }}>
                                    <label class="form-check-label">Appliquer à tous les produits</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Statistiques du coupon -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Statistiques</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Utilisations</small>
                        <div class="progress" style="height:20px;">
                            <div class="progress-bar bg-success" style="width:{{ $coupon->usage_percentage }}%">
                                {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                            </div>
                        </div>
                    </div>
                    <p><strong>Commandes avec ce coupon :</strong> {{ $coupon->orders_count }}</p>
                    <p><strong>Statut :</strong> 
                        @if($coupon->is_valid)
                            <span class="badge bg-success">Valide</span>
                        @else
                            <span class="badge bg-danger">Invalide</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('couponType').addEventListener('change', function() {
    const valueField = document.getElementById('valueField');
    valueField.style.display = this.value === 'free_delivery' ? 'none' : 'block';
});
</script>
@endpush
@endsection





--------------------------------------------------------------------------------------------
Admin - Gestion des Utilisateurs
admin/users/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-people me-2"></i>Utilisateurs</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Nouvel utilisateur
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Bloqués</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Commandes</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar_url }}" class="rounded-circle me-2" width="35" height="35" alt="">
                                        <div>
                                            <strong>{{ $user->full_name }}</strong>
                                            @if($user->loyalty_level && $user->loyalty_level != 'bronze')
                                                <span class="badge bg-{{ $user->loyalty_level == 'gold' ? 'warning' : 'secondary' }} ms-1">
                                                    {{ ucfirst($user->loyalty_level) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge bg-info">{{ ucfirst($role) }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $user->orders_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($user->is_blocked)
                                        <span class="badge bg-danger">Bloqué</span>
                                    @elseif($user->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-warning">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->is_blocked)
                                            <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-unlock"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    data-bs-toggle="modal" data-bs-target="#blockModal{{ $user->id }}">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Modal Bloquer -->
                                    <div class="modal fade" id="blockModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.users.block', $user) }}" method="POST">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Bloquer {{ $user->full_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Raison du blocage</label>
                                                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-danger">Bloquer</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
Admin - Gestion des Utilisateurs
admin/users/index.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-people me-2"></i>Utilisateurs</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Nouvel utilisateur
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Bloqués</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Commandes</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar_url }}" class="rounded-circle me-2" width="35" height="35" alt="">
                                        <div>
                                            <strong>{{ $user->full_name }}</strong>
                                            @if($user->loyalty_level && $user->loyalty_level != 'bronze')
                                                <span class="badge bg-{{ $user->loyalty_level == 'gold' ? 'warning' : 'secondary' }} ms-1">
                                                    {{ ucfirst($user->loyalty_level) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge bg-info">{{ ucfirst($role) }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $user->orders_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($user->is_blocked)
                                        <span class="badge bg-danger">Bloqué</span>
                                    @elseif($user->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-warning">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->is_blocked)
                                            <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-unlock"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    data-bs-toggle="modal" data-bs-target="#blockModal{{ $user->id }}">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Modal Bloquer -->
                                    <div class="modal fade" id="blockModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.users.block', $user) }}" method="POST">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Bloquer {{ $user->full_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Raison du blocage</label>
                                                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-danger">Bloquer</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection





--------------------------------------------------------------------------------------------
admin/users/show.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Détails utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-person-badge me-2"></i>{{ $user->full_name }}</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row g-4">
        <!-- Infos utilisateur -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100" alt="">
                    <h4>{{ $user->full_name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    <div class="mb-3">
                        @foreach($user->getRoleNames() as $role)
                            <span class="badge bg-info fs-6">{{ ucfirst($role) }}</span>
                        @endforeach
                    </div>
                    
                    <div class="row text-start">
                        <div class="col-12 mb-2">
                            <small class="text-muted">Téléphone</small>
                            <p>{{ $user->phone ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-12 mb-2">
                            <small class="text-muted">Inscrit le</small>
                            <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-12 mb-2">
                            <small class="text-muted">Dernière connexion</small>
                            <p>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</p>
                        </div>
                        <div class="col-12 mb-2">
                            <small class="text-muted">Statut</small>
                            <p>
                                @if($user->is_blocked)
                                    <span class="badge bg-danger">Bloqué</span>
                                    @if($user->blocked_reason)
                                        <br><small>{{ $user->blocked_reason }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">Actif</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Niveau fidélité</small>
                            <p>
                                <span class="badge bg-warning">{{ ucfirst($user->loyalty_level) }}</span>
                                <br>{{ $user->getLoyaltyBalance() }} points
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Commandes récentes</h5>
                    <span class="badge bg-primary">{{ $user->orders->count() }} commandes</span>
                </div>
                <div class="card-body">
                    @forelse($user->orders as $order)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <strong>#{{ $order->order_number }}</strong>
                                <br><small class="text-muted">{{ $order->restaurant->name }}</small>
                            </div>
                            <div>
                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                            </div>
                            <div>
                                <strong>{{ number_format($order->total, 2) }} €</strong>
                                <br><small>{{ $order->created_at->format('d/m/Y') }}</small>
                            </div>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted text-center">Aucune commande</p>
                    @endforelse
                </div>
            </div>

            <!-- Adresses -->
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Adresses</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($user->addresses as $address)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <strong>{{ $address->label }}</strong>
                                    @if($address->is_default)
                                        <span class="badge bg-primary">Défaut</span>
                                    @endif
                                    <p class="mb-0">{{ $address->street_address }}</p>
                                    <small class="text-muted">{{ $address->postal_code }} {{ $address->city }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Aucune adresse</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Avis -->
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Avis</h5></div>
                <div class="card-body">
                    @forelse($user->reviews as $review)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="star-rating mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-1">{{ $review->comment }}</p>
                            <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                        </div>
                    @empty
                        <p class="text-muted">Aucun avis</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


--------------------------------------------------------------------------------------------
admin/users/edit.blade.php
--------------------------------------------------------------------------------------------
@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $user->full_name }}</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Informations</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom *</label>
                                <input type="text" name="first_name" class="form-control" 
                                       value="{{ old('first_name', $user->first_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom *</label>
                                <input type="text" name="last_name" class="form-control" 
                                       value="{{ old('last_name', $user->last_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ old('phone', $user->phone) }}">
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Compte actif</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_blocked" value="1" 
                                           {{ old('is_blocked', $user->is_blocked) ? 'checked' : '' }}>
                                    <label class="form-check-label">Compte bloqué</label>
                                </div>
                            </div>
                            
                            @if($user->is_blocked)
                                <div class="col-12">
                                    <label class="form-label">Raison du blocage</label>
                                    <input type="text" name="blocked_reason" class="form-control" 
                                           value="{{ old('blocked_reason', $user->blocked_reason) }}">
                                </div>
                            @endif
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-check-lg"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rôles -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Rôles</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf @method('PUT')
                        
                        @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="roles[]" 
                                       value="{{ $role->name }}" id="role{{ $role->id }}"
                                       {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role{{ $role->id }}">
                                    {{ ucfirst($role->name) }}
                                </label>
                            </div>
                        @endforeach
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-shield-check"></i> Mettre à jour les rôles
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

--------------------------------------------------------------------------------------------
Admin - Édition Menu
admin/menus/edit.blade.php
--------------------------------------------------------------------------------------------  
@extends('layouts.admin')

@section('title', 'Modifier le Menu')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier : {{ $menu->name }}</h3>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du menu *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type *</label>
                                <select name="type" class="form-select" required>
                                    <option value="regular" {{ old('type', $menu->type) == 'regular' ? 'selected' : '' }}>Régulier</option>
                                    <option value="lunch" {{ old('type', $menu->type) == 'lunch' ? 'selected' : '' }}>Déjeuner</option>
                                    <option value="dinner" {{ old('type', $menu->type) == 'dinner' ? 'selected' : '' }}>Dîner</option>
                                    <option value="weekend" {{ old('type', $menu->type) == 'weekend' ? 'selected' : '' }}>Weekend</option>
                                    <option value="special" {{ old('type', $menu->type) == 'special' ? 'selected' : '' }}>Spécial</option>
                                    <option value="seasonal" {{ old('type', $menu->type) == 'seasonal' ? 'selected' : '' }}>Saisonnier</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $menu->description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de début</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $menu->start_date ? $menu->start_date->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $menu->end_date ? $menu->end_date->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Disponible de</label>
                                <input type="time" name="available_from" class="form-control" value="{{ old('available_from', $menu->available_from) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Disponible jusqu'à</label>
                                <input type="time" name="available_until" class="form-control" value="{{ old('available_until', $menu->available_until) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ordre</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $menu->sort_order) }}">
                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Menu actif</label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <h5>Sélectionner les produits</h5>
                        <div class="row g-3">
                            @foreach($products as $product)
                                @php
                                    $menuItem = $menu->items->where('product_id', $product->id)->first();
                                @endphp
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="products[{{ $loop->index }}][id]" 
                                                   value="{{ $product->id }}"
                                                   id="product{{ $product->id }}"
                                                   {{ $menuItem ? 'checked' : '' }}>
                                            <label class="form-check-label" for="product{{ $product->id }}">
                                                <strong>{{ $product->name }}</strong>
                                                <br><small class="text-muted">{{ $product->formatted_price }}</small>
                                            </label>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label small">Prix spécial (optionnel)</label>
                                            <input type="number" step="0.01" 
                                                   name="products[{{ $loop->index }}][special_price]" 
                                                   class="form-control form-control-sm"
                                                   value="{{ $menuItem ? $menuItem->special_price : '' }}"
                                                   placeholder="Laisser vide pour prix normal">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg mt-4">
                            <i class="bi bi-check-lg"></i> Mettre à jour le menu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



--------------------------------------------------------------------------------------------
Page d'impression commande
admin/orders/print.blade.php
--------------------------------------------------------------------------------------------  
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Commande #{{ $order->order_number }}</title>
    <style>
        @media print {
            @page { margin: 0; size: 80mm auto; }
            body { margin: 10mm; font-family: 'Courier New', monospace; font-size: 12px; }
        }
        body { font-family: Arial, sans-serif; max-width: 300px; margin: 20px auto; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; }
        .info { margin-bottom: 10px; font-size: 12px; }
        .info p { margin: 3px 0; }
        .items { margin-bottom: 10px; }
        .item { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dotted #ccc; font-size: 12px; }
        .total { font-weight: bold; font-size: 16px; text-align: right; margin-top: 10px; padding-top: 10px; border-top: 2px solid #000; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #666; }
        .btn-print { display: block; margin: 20px auto; padding: 10px 30px; font-size: 16px; cursor: pointer; }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimer le ticket</button>
    
    <div class="header">
        <h2>{{ $order->restaurant->name }}</h2>
        <p>{{ $order->restaurant->address }}</p>
        <p>Tél: {{ $order->restaurant->phone }}</p>
        <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    
    <div class="info">
        <p><strong>Commande #{{ $order->order_number }}</strong></p>
        <p>Type: 
            @if($order->type == 'dine_in') Sur place - Table {{ $order->table_number }}
            @elseif($order->type == 'takeaway') À emporter
            @else Livraison
            @endif
        </p>
        <p>Client: {{ $order->user->full_name }}</p>
        @if($order->delivery_address)
            <p>Adresse: {{ $order->delivery_address }}</p>
        @endif
    </div>
    
    <div class="items">
        @foreach($order->items as $item)
            <div class="item">
                <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
                <span>{{ number_format($item->total_price, 2) }} €</span>
            </div>
            @if($item->special_instructions)
                <small style="color:#666;"> ↳ {{ $item->special_instructions }}</small>
            @endif
        @endforeach
    </div>
    
    <div style="font-size:12px; text-align:right;">
        <p>Sous-total: {{ number_format($order->subtotal, 2) }} €</p>
        @if($order->discount_amount > 0)
            <p>Remise: -{{ number_format($order->discount_amount, 2) }} €</p>
        @endif
        @if($order->delivery_fee > 0)
            <p>Livraison: {{ number_format($order->delivery_fee, 2) }} €</p>
        @endif
        <p>TVA: {{ number_format($order->tax_amount, 2) }} €</p>
    </div>
    
    <div class="total">
        TOTAL: {{ number_format($order->total, 2) }} €
    </div>
    
    <div class="footer">
        <p>Merci de votre visite !</p>
        <p>{{ $order->restaurant->name }} - SIRET: XXX XXX XXX</p>
    </div>
    
    <script>
        // Auto-print on load
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>


--------------------------------------------------------------------------------------------
Partials - Cart Sidebar (Offcanvas)
--------------------------------------------------------------------------------------------  
{{-- partials/cart-sidebar.blade.php --}}
@auth
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="bi bi-cart3 me-2"></i>Mon Panier</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @php $cartItems = session('cart', []); @endphp
            @if(count($cartItems) > 0)
                @foreach($cartItems as $key => $item)
                    @php $product = App\Models\Product::find($item['product_id']); @endphp
                    @if($product)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <strong>{{ Str::limit($product->name, 20) }}</strong>
                                <br><small class="text-muted">x{{ $item['quantity'] }} - {{ number_format($product->price * $item['quantity'], 2) }} €</small>
                            </div>
                            <a href="{{ route('cart.remove', $key) }}" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    @endif
                @endforeach
                <a href="{{ route('cart.index') }}" class="btn btn-primary w-100 mb-2">Voir le panier</a>
                <a href="{{ route('checkout.index') }}" class="btn btn-success w-100">Commander</a>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <p class="mt-3">Votre panier est vide</p>
                </div>
            @endif
        </div>
    </div>
@endauth


--------------------------------------------------------------------------------------------
Partials - Cart Sidebar (Offcanvas)
-------------------------------------------------------------------------------------------- 
{{-- partials/cart-sidebar.blade.php --}}
@auth
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="bi bi-cart3 me-2"></i>Mon Panier</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @php $cartItems = session('cart', []); @endphp
            @if(count($cartItems) > 0)
                @foreach($cartItems as $key => $item)
                    @php $product = App\Models\Product::find($item['product_id']); @endphp
                    @if($product)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <strong>{{ Str::limit($product->name, 20) }}</strong>
                                <br><small class="text-muted">x{{ $item['quantity'] }} - {{ number_format($product->price * $item['quantity'], 2) }} €</small>
                            </div>
                            <a href="{{ route('cart.remove', $key) }}" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    @endif
                @endforeach
                <a href="{{ route('cart.index') }}" class="btn btn-primary w-100 mb-2">Voir le panier</a>
                <a href="{{ route('checkout.index') }}" class="btn btn-success w-100">Commander</a>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <p class="mt-3">Votre panier est vide</p>
                </div>
            @endif
        </div>
    </div>
@endauth