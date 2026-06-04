<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\OrderController;

use Illuminate\Support\Facades\Route;


// Route de test - À SUPPRIMER après vérification
// Route::get('/test-auth', function () {
//     $user = auth()->user();
    
//     return response()->json([
//         'authenticated' => auth()->check(),
//         'user_id' => $user->id ?? null,
//         'user_name' => $user->full_name ?? null,
//         'is_active' => $user->is_active ?? null,
//         'is_blocked' => $user->is_blocked ?? null,
//         'roles' => $user ? $user->getRoleNames() : [],
//         'permissions' => $user ? $user->getAllPermissions()->pluck('name') : [],
//     ]);
// })->middleware('auth');

// // Route test admin
// Route::get('/test-admin', function () {
//     return 'Vous avez accès à l\'admin !';
// })->middleware(['auth', 'admin.access']);


// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Restaurants
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/restaurants/{restaurant}/menu', [MenuController::class, 'index'])->name('restaurant.menu');


// Menu
Route::get('/restaurants/{restaurant}/menu', [MenuController::class, 'index'])->name('restaurant.menu');
Route::get('/products/{product}', [MenuController::class, 'show'])->name('products.show');

// Panier
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    
    // Commandes
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/review', [OrderController::class, 'addReview'])->name('orders.review');
    
    // Profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [ProfileController::class, 'orderDetail'])->name('orders.show');
        Route::get('/favorites', [ProfileController::class, 'favorites'])->name('favorites');
        Route::post('/favorites/toggle', [ProfileController::class, 'toggleFavorite'])->name('favorites.toggle');
        Route::post('/addresses', [ProfileController::class, 'addAddress'])->name('addresses.store');
        Route::delete('/addresses/{address}', [ProfileController::class, 'deleteAddress'])->name('addresses.destroy');
    });
});

// Admin
Route::middleware(['auth', 'admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Produits
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])
            ->name('products.images.store');
        Route::delete('/products/images/{image}', [ProductController::class, 'deleteImage'])
            ->name('products.images.destroy');
        
        // Catégories
        Route::resource('categories', CategoryController::class);
        
        // Menus
        Route::resource('menus', AdminMenuController::class);
        
        // Commandes
        Route::resource('orders', AdminOrderController::class);
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.status');
        Route::post('/orders/{order}/delivery', [AdminOrderController::class, 'assignDelivery'])
            ->name('orders.delivery');
        Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])
            ->name('orders.cancel');
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])
            ->name('orders.print');
        
        // Coupons
        Route::resource('coupons', CouponController::class);

        
        // Avis
        Route::resource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])
            ->name('reviews.approve');
        Route::post('/reviews/{review}/respond', [ReviewController::class, 'respond'])
            ->name('reviews.respond');
        
        // Utilisateurs
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
        
        // Rapports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Paramètres
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::put('/settings/restaurant', [SettingController::class, 'updateRestaurant'])->name('settings.restaurant');
        Route::put('/settings/hours', [SettingController::class, 'updateHours'])->name('settings.hours');

    });

