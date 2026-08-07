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
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;

use Illuminate\Support\Facades\Route;


// Accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Restaurants
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');

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

Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    
    // Commandes
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/review', [OrderController::class, 'reviewForm'])->name('orders.review.create');
    Route::post('/orders/{order}/review', [OrderController::class, 'addReview'])->name('orders.review');
    
    // Réservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/restaurants/{restaurant}/reserver', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/restaurants/{restaurant}/reserver', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');

    // Profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [ProfileController::class, 'orderDetail'])->name('orders.show');
        Route::get('/favorites', [ProfileController::class, 'favorites'])->name('favorites');
        Route::post('/favorites/toggle', [ProfileController::class, 'toggleFavorite'])->name('favorites.toggle');
        Route::post('/addresses', [ProfileController::class, 'addAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [ProfileController::class, 'deleteAddress'])->name('addresses.destroy');
        Route::get('/loyalty', [ProfileController::class, 'loyalty'])->name('loyalty');
    });
});

// Admin
Route::middleware(['auth', 'admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        
        // Produits
        Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])
            ->name('products.images.store');
        Route::delete('/products/images/{image}', [ProductController::class, 'deleteImage'])
            ->name('products.images.destroy');
        Route::post('/products/{product}/stock', [ProductController::class, 'updateStock'])
            ->name('products.stock');
        Route::resource('products', ProductController::class);
            
        // Catégories
        Route::resource('categories', CategoryController::class);
        
        // Menus
        Route::resource('menus', AdminMenuController::class);
        
        // Commandes
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.status');
        Route::post('/orders/{order}/delivery', [AdminOrderController::class, 'assignDelivery'])
            ->name('orders.delivery');
        Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])
            ->name('orders.cancel');
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])
            ->name('orders.print');
        Route::get('/export', [AdminOrderController::class, 'export'])->name('export');
        Route::resource('orders', AdminOrderController::class);
        
        // Coupons
        Route::resource('coupons', CouponController::class);

        
        // Avis
        Route::resource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])
            ->name('reviews.approve');
        Route::post('/reviews/{review}/respond', [ReviewController::class, 'respond'])
            ->name('reviews.respond');
        
        // Utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('/{user}/block', [UserController::class, 'block'])->name('block');
            Route::post('/{user}/unblock', [UserController::class, 'unblock'])->name('unblock');
        });
        Route::resource('users', UserController::class);

        // Staff Management
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::post('/{user}/block', [StaffController::class, 'block'])->name('block');
            Route::post('/{user}/unblock', [StaffController::class, 'unblock'])->name('unblock');
        });
        Route::resource('staff', StaffController::class);
        
        // Réservations
        Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}', [AdminReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/confirm', [AdminReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('/reservations/{reservation}/cancel', [AdminReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::post('/reservations/{reservation}/complete', [AdminReservationController::class, 'complete'])->name('reservations.complete');

        // Rapports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Paramètres
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::put('/settings/restaurant', [SettingController::class, 'updateRestaurant'])->name('settings.restaurant');
        Route::put('/settings/hours', [SettingController::class, 'updateHours'])->name('settings.hours');
        Route::post('/settings/special-days', [SettingController::class, 'addSpecialDay'])->name('settings.special-days.store');
        Route::delete('/settings/special-days/{specialDay}', [SettingController::class, 'removeSpecialDay'])->name('settings.special-days.destroy');

    });

// Espace Livreur
Route::middleware(['auth', 'role:delivery_person'])
    ->prefix('delivery')
    ->name('delivery.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('dashboard');
        Route::get('/orders/{order}', [\App\Http\Controllers\DeliveryController::class, 'show'])->name('show');
        Route::post('/orders/{order}/status', [\App\Http\Controllers\DeliveryController::class, 'updateStatus'])->name('status');
        Route::post('/orders/{order}/location', [\App\Http\Controllers\DeliveryController::class, 'updateLocation'])->name('location');
    });
