<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\EmailVerificationOtpController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/contactus', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contactus', [ContactController::class, 'store'])->name('contact.store');

// Customer service pages
Route::view('/shipping-delivery', 'pages.shipping-delivery')->name('pages.shipping');
Route::view('/returns-refunds', 'pages.returns-refunds')->name('pages.returns');
Route::view('/faqs', 'pages.faqs')->name('pages.faqs');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('pages.privacy');
Route::view('/terms-conditions', 'pages.terms-conditions')->name('pages.terms');

// Newsletter subscribe (simple endpoint)
Route::post('/newsletter/subscribe', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'newsletter_email' => ['required', 'email'],
    ]);

    return back()->with('newsletter_success', 'Thanks — you are subscribed!');
})->name('newsletter.subscribe');

Route::get('/account-disabled', function () {
    return view('auth.account-disabled');
})->name('account.disabled');

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login.page');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Password reset routes
Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');

// Password reset via Email OTP
Route::get('/forgot-password-otp', [PasswordResetController::class, 'requestOtp'])->name('password.otp.request');
Route::post('/forgot-password-otp', [PasswordResetController::class, 'sendOtp'])->name('password.otp.email');
Route::get('/reset-password-otp', [PasswordResetController::class, 'otpResetForm'])->name('password.otp.reset');
Route::post('/reset-password-otp', [PasswordResetController::class, 'otpUpdate'])->name('password.otp.update');

// Email verification (OTP)
Route::get('/email/verify', [EmailVerificationOtpController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

// Email verification (OTP) for guests (e.g., Forgot Password flow)
Route::get('/email/verify-guest', [EmailVerificationOtpController::class, 'guestNotice'])
    ->name('verification.guest.notice');

Route::post('/email/verification-notification-guest', [EmailVerificationOtpController::class, 'guestSend'])
    ->middleware(['throttle:6,1'])
    ->name('verification.guest.send');

Route::post('/email/verify-otp-guest', [EmailVerificationOtpController::class, 'guestVerify'])
    ->middleware(['throttle:10,1'])
    ->name('verification.guest.verify');

Route::post('/email/verification-notification', [EmailVerificationOtpController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/email/verify-otp', [EmailVerificationOtpController::class, 'verify'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('verification.otp.verify');

// Legacy link route: redirect into OTP flow (keeps old links from breaking).
Route::get('/email/verify/{id}/{hash}', function () {
    return redirect()->route('verification.notice');
})->middleware(['signed'])->name('verification.verify');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Protected routes (require authentication)
Route::middleware(['web'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/get', [CartController::class, 'getCart'])->name('cart.get');
    
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
        Route::post('/buy-now', [OrderController::class, 'buyNow'])->name('buy-now');
        Route::post('/buy-now/cancel', [OrderController::class, 'cancelBuyNow'])->name('buy-now.cancel');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.index');

        // Interactive receipt actions
        Route::post('/orders/{order}/buy-again', [OrderController::class, 'buyAgain'])->name('orders.buy-again');
        Route::post('/orders/{order}/review', [OrderController::class, 'submitReview'])->name('orders.review');
        Route::post('/orders/{order}/return', [OrderController::class, 'requestReturn'])->name('orders.return');
        Route::post('/orders/{order}/complete', [OrderController::class, 'markComplete'])->name('orders.complete');

        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
        Route::put('/profile/photo', [AuthController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    });
});

// Admin authentication routes (not protected)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin routes (require admin authentication)
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Products management
    Route::get('/products', [AdminController::class, 'indexProducts'])->name('products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::put('/products/{product}/stock', [AdminController::class, 'updateProductStock'])->name('products.stock.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    
    // Orders management
    Route::get('/orders', [AdminController::class, 'indexOrders'])->name('orders.index');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/shipping-updates', [AdminController::class, 'storeOrderShippingUpdate'])->name('orders.shipping-updates.store');

    // Returns management
    Route::get('/returns', [AdminController::class, 'indexReturns'])->name('returns.index');
    Route::get('/returns/{orderReturn}', [AdminController::class, 'showReturn'])->name('returns.show');
    Route::patch('/returns/{orderReturn}/status', [AdminController::class, 'updateReturnStatus'])->name('returns.update-status');
    
    // Contacts management
    Route::get('/contacts', [AdminController::class, 'indexContacts'])->name('contacts.index');
    Route::get('/contacts/archived', [AdminController::class, 'indexArchivedContacts'])->name('contacts.archived');
    Route::get('/contacts/{contact}', [AdminController::class, 'showContact'])->name('contacts.show');
    Route::post('/contacts/{contact}/email', [AdminController::class, 'emailContact'])->name('contacts.email');
    Route::delete('/contacts/{contact}', [AdminController::class, 'destroyContact'])->name('contacts.destroy');
    Route::patch('/contacts/{contact}/restore', [AdminController::class, 'restoreContact'])->name('contacts.restore');
    Route::delete('/contacts/{contact}/force', [AdminController::class, 'forceDestroyContact'])->name('contacts.force-destroy');
    
    // Users management
    Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/users/{user}/restore', [AdminController::class, 'restoreUser'])->name('users.restore');
    
    // Categories management
    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
});

