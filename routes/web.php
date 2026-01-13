<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/contactus', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contactus', [ContactController::class, 'store'])->name('contact.store');

// Auth routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes (require authentication)
Route::middleware(['web'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.index');
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
        Route::put('/profile/photo', [AuthController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    });
});

// Admin routes (require authentication and admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Products management
    Route::get('/products', [AdminController::class, 'indexProducts'])->name('products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    
    // Orders management
    Route::get('/orders', [AdminController::class, 'indexOrders'])->name('orders.index');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    
    // Contacts management
    Route::get('/contacts', [AdminController::class, 'indexContacts'])->name('contacts.index');
    Route::get('/contacts/{contact}', [AdminController::class, 'showContact'])->name('contacts.show');
    Route::delete('/contacts/{contact}', [AdminController::class, 'destroyContact'])->name('contacts.destroy');
    
    // Users management
    Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    
    // Categories management
    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('categories.index');
});
