<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - Sarab Restaurant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="{{url('website/assets/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{url('website/assets/css/all.min.css')}}" rel="stylesheet"/>
    <link href="{{url('website/assets/css/style.css')}}" rel="stylesheet"/>
    
    <style>
        .cart-page {
            padding: 120px 0 80px;
            background: #f9f9f9;
            min-height: 100vh;
        }
        .cart-table {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .cart-table th {
            background: var(--primary);
            color: #fff;
            padding: 15px 20px;
            font-weight: 600;
            border: none;
        }
        .cart-table td {
            padding: 20px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }
        .cart-item-img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
        }
        .cart-item-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .cart-item-cat {
            font-size: 12px;
            color: var(--primary);
        }
        .qty-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: #f0f0f0;
            border: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        .qty-btn:hover {
            background: var(--primary);
            color: #fff;
        }
        .qty-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 0 5px;
            padding: 6px;
        }
        .remove-item {
            color: #ff4757;
            background: none;
            border: none;
            font-size: 18px;
            transition: all 0.3s;
        }
        .remove-item:hover {
            color: #e63946;
            transform: scale(1.1);
        }
        .cart-summary {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #ddd;
        }
        .summary-total {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            border-bottom: none;
            padding-top: 15px;
        }
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
        }
        .empty-cart i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .coupon-input {
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 10px 15px;
            width: 100%;
            margin-bottom: 15px;
        }
        .apply-btn {
            background: #333;
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            width: 100%;
            transition: all 0.3s;
        }
        .apply-btn:hover {
            background: var(--primary);
        }
        @media (max-width: 768px) {
            .cart-page { padding: 100px 0 60px; }
            .cart-table td { padding: 15px; }
            .cart-item-img { width: 60px; height: 60px; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" id="nav">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <div class="blogo">
                <div class="bico"><i class="fas fa-utensils"></i></div>
                <div>
                    <div class="bname">Sar<span>ab</span></div>
                    <div class="bsub">Fast Food & Restaurant</div>
                </div>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
            <i class="fas fa-bars" style="color:var(--primary);font-size:1.35rem;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navmenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/home') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/home') }}">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/home') }}">Chefs</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/home') }}">Reservation</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/home') }}">Reviews</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/home-section') }}">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/cart') }}" class="nav-link position-relative">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">0</span>
                </a>
                @auth
                    <div class="dropdown">
                        <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                        </ul>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Cart Page -->
<section class="cart-page">
    <div class="container">
        <div class="text-center mb-5">
            <span class="slbl">Your Cart</span>
            <h2 class="stitle">My <span>Shopping Cart</span></h2>
            <div class="sline"></div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="cart-table">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Cart items will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ url('/home') }}" class="btn btn-outline-danger">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 class="mb-3">Order Summary</h5>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span id="deliveryFee">$2.99</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%)</span>
                        <span id="tax">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Discount</span>
                        <span id="discount">-$0.00</span>
                    </div>
                    <div class="summary-row summary-total">
                        <strong>Total</strong>
                        <strong id="total">$0.00</strong>
                    </div>
                    
                    <div class="mt-3">
                        <input type="text" id="couponCode" class="coupon-input" placeholder="Enter coupon code">
                        <button class="apply-btn" id="applyCoupon">Apply Coupon</button>
                    </div>
                    
                    <button class="btn-red w-100 mt-3 justify-content-center" id="checkoutBtn">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<footer style="background: #1a1a1a; color: #fff; padding: 60px 0 20px;">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="fnm">Sar<span>ab</span></div>
                <p class="fdesc">We bring the world's finest flavors together in a fast, friendly, and affordable experience.</p>
            </div>
            <div class="col-sm-6 col-lg-2">
                <div class="ftit">Quick Links</div>
                <ul class="flinks ps-0">
                    <li><a href="{{ url('/#hero') }}">Home</a></li>
                    <li><a href="{{ url('/#about') }}">About Us</a></li>
                    <li><a href="{{ url('/#menu') }}">Our Menu</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <div class="ftit">Get In Touch</div>
                <div class="fci">
                    <div class="fciico"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="fciinfo"><strong>Address</strong>42 Flavor Street, Manhattan, NY 10001</div>
                </div>
                <div class="fci">
                    <div class="fciico"><i class="fas fa-phone-alt"></i></div>
                    <div class="fciinfo"><strong>Phone</strong>+1 (800) 123-4567</div>
                </div>
            </div>
        </div>
    </div>
</footer>

<button id="btt" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-chevron-up"></i></button>

<script src="{{url('website/assets/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{url('website/assets/js/bootstrap.bundle.min.js')}}"></script>
<script>
$(document).ready(function() {
    loadCart();
    updateCartBadge();

    function loadCart() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let html = '';
        
        if (cart.length === 0) {
            html = '<tr><td colspan="5"><div class="empty-cart"><i class="fas fa-shopping-cart"></i><h4>Your cart is empty</h4><p>Add some delicious items from our menu!</p><a href="{{ url("/menu") }}" class="btn-red">Browse Menu</a></div></td></tr>';
        } else {
            cart.forEach((item, index) => {
                html += `
                    <tr data-index="${index}">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="${item.img}" class="cart-item-img" alt="${item.title}">
                                <div>
                                    <div class="cart-item-title">${item.title}</div>
                                    <div class="cart-item-cat">${item.cat}</div>
                                </div>
                            </div>
                        </td>
                        <td>$${parseFloat(item.price).toFixed(2)}</td>
                        <td>
                            <button class="qty-btn qty-minus" data-index="${index}">-</button>
                            <input type="number" class="qty-input" id="qty-${index}" value="${item.quantity}" min="1" readonly>
                            <button class="qty-btn qty-plus" data-index="${index}">+</button>
                        </td>
                        <td>$${(item.price * item.quantity).toFixed(2)}</td>
                        <td>
                            <button class="remove-item" data-index="${index}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#cartItems').html(html);
        updateSummary();
    }

    $(document).on('click', '.qty-plus', function() {
        let index = $(this).data('index');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart[index].quantity++;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
        updateCartBadge();
    });

    $(document).on('click', '.qty-minus', function() {
        let index = $(this).data('index');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            updateCartBadge();
        }
    });

    $(document).on('click', '.remove-item', function() {
        let index = $(this).data('index');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
        updateCartBadge();
    });

    function updateSummary() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.price * item.quantity;
        });
        
        let deliveryFee = subtotal > 50 ? 0 : 2.99;
        let tax = subtotal * 0.10;
        let discount = 0;
        let total = subtotal + deliveryFee + tax - discount;
        
        $('#subtotal').text('$' + subtotal.toFixed(2));
        $('#deliveryFee').text('$' + deliveryFee.toFixed(2));
        $('#tax').text('$' + tax.toFixed(2));
        $('#total').text('$' + total.toFixed(2));
    }

    function updateCartBadge() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let count = cart.reduce((sum, item) => sum + item.quantity, 0);
        $('#cartBadge').text(count);
    }

    $('#applyCoupon').click(function() {
        let coupon = $('#couponCode').val();
        alert('Coupon applied: ' + coupon + ' (demo)');
    });

    $('#checkoutBtn').click(function() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }
        window.location.href = "{{ url('/checkout') }}";
    });
});
</script>
</body>
</html>