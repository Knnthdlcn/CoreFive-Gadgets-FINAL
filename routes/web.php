<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;

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
