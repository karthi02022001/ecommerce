<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// ============================================
// Currency Switching Routes
// ============================================
Route::get('/currency/switch/{code}', [CurrencyController::class, 'switch'])
    ->name('currency.switch');

Route::get('/currency/rate', [CurrencyController::class, 'getRate'])
    ->name('currency.rate');

// ============================================
// Google OAuth Routes
// ============================================
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('products.category');
// Category routes (public access)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

     // OTP Verification
    Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('verify.otp.form');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])->name('resend.otp');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
    Route::post('/resend-password-otp', [ForgotPasswordController::class, 'resendOtp'])->name('password.resend.otp');

});

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/page/{slug}', [CmsController::class, 'show'])->name('cms.page');

// Cart routes (accessible for guests and authenticated users)


Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('applyCoupon');
    Route::get('/count', [CartController::class, 'getCount'])->name('count'); // AJAX endpoint
});

// Protected customer routes
Route::middleware(['auth'])->group(function () {

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    });

    // Orders
    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        // Address Management Routes
        Route::post('/store-address', [ProfileController::class, 'storeAddress'])->name('store-address');
        Route::put('/update-address/{id}', [ProfileController::class, 'updateAddress'])->name('update-address');
        Route::delete('/delete-address/{id}', [ProfileController::class, 'deleteAddress'])->name('delete-address');
    });

    // AJAX count endpoint - must be first
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');

    // Clear wishlist - POST method
    Route::post('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');

    // Add to wishlist
    Route::post('/wishlist/add', [WishlistController::class, 'store'])->name('wishlist.add');

    // Wishlist index page - must come after specific routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    // Move to cart - specific route with /move-to-cart suffix
    Route::post('/wishlist/{id}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

    // Remove from wishlist by product ID
    Route::delete('/wishlist/product/{productId}', [WishlistController::class, 'removeProduct'])->name('wishlist.remove.product');

    // Remove from wishlist by wishlist ID - MUST come last
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.remove');

    Route::prefix('reviews')->name('reviews.')->group(function () {
        // List all user's reviews
        Route::get('/', [ReviewController::class, 'index'])->name('index');

        // Show specific review
        Route::get('/{id}', [ReviewController::class, 'show'])->name('show');

        // Create review for a product from an order
        Route::get('/create/{orderId}/{productId}', [ReviewController::class, 'create'])->name('create');
        Route::post('/store/{orderId}/{productId}', [ReviewController::class, 'store'])->name('store');

        // Edit/Update review (only if not approved yet)
        Route::get('/{id}/edit', [ReviewController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReviewController::class, 'update'])->name('update');

        // Delete review (only if not approved yet)
        Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');

        // Mark review as helpful (AJAX)
        Route::post('/{id}/helpful', [ReviewController::class, 'markHelpful'])->name('helpful');
    });
});
