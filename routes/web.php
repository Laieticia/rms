<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Route publique (tout le monde peut voir)
Route::get('/', function () {
    return view('welcome');
});

// Routes protégées par authentification (utilisateurs connectés)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard accessible à tous les utilisateurs connectés
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Profile (tout utilisateur connecté peut gérer son profil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// ROUTES POUR SUPER ADMIN UNIQUEMENT
// ==========================================
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    
    // Gestion des utilisateurs
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    
    // Gestion des fournisseurs
    Route::get('/suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SuppliersController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SuppliersController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SuppliersController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SuppliersController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');
});

// ==========================================
// ROUTES POUR ADMIN RESTAURANT ET SUPER ADMIN
// ==========================================
Route::middleware(['auth', 'role:super-admin|restaurant-admin'])->group(function () {
    
    // Gestion des catégories
    Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoriesController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoriesController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoriesController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
    
    // Gestion des produits
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductsController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');
});

// ==========================================
// ROUTES POUR CAISSIER / VENDEUR
// ==========================================
Route::middleware(['auth', 'role:super-admin|restaurant-admin|cashier'])->group(function () {
    
    // Ventes
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
    
    // Achats
    Route::get('/purchases', [PurchasesController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchasesController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchasesController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchasesController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}', [PurchasesController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchasesController::class, 'destroy'])->name('purchases.destroy');
    
    // Retours
    Route::get('/returns', [ReturnsController::class, 'index'])->name('returns.index');
    Route::get('/returns/create', [ReturnsController::class, 'create'])->name('returns.create');
    Route::post('/returns', [ReturnsController::class, 'store'])->name('returns.store');
    Route::get('/returns/{return}', [ReturnsController::class, 'show'])->name('returns.show');
    Route::get('/returns/{return}/edit', [ReturnsController::class, 'edit'])->name('returns.edit');
    Route::put('/returns/{return}', [ReturnsController::class, 'update'])->name('returns.update');
    Route::delete('/returns/{return}', [ReturnsController::class, 'destroy'])->name('returns.destroy');
});

// ==========================================
// ROUTES POUR CLIENTS
// ==========================================
Route::middleware(['auth', 'role:super-admin|restaurant-admin|cashier|client'])->group(function () {
    
    // Clients (lecture seule pour les clients)
    Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomersController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomersController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomersController::class, 'show'])->name('customers.show');
    
    // Modification des clients réservée aux admins
    Route::middleware(['role:super-admin|restaurant-admin|cashier'])->group(function () {
        Route::get('/customers/{customer}/edit', [CustomersController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomersController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomersController::class, 'destroy'])->name('customers.destroy');
    });
});

// Routes d'authentification (fournies par Laravel Breeze/Jetstream)
require __DIR__.'/auth.php';