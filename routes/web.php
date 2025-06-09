<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Models\Product;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::prefix('admin')->group(function () {
    Route::resource('products', AdminProductController::class);
});

Route::delete('categories/bulk-delete', [CategoryController::class, 'bulkDelete']);
Route::resource('categories', CategoryController::class);
Route::resource('users', UserController::class);
Route::resource('orders', OrderController::class);

Route::resource('products', ProductController::class);

Route::get('/checkout', function (Request $request) {
    $stripePriceId = 'price_1RX2mME1dy0MotZhKJrrQzqb';
 
    $quantity = 1;
 
    $checkoutSession = $request->user()->checkout([$stripePriceId => $quantity], [
        'success_url' => route('checkout-success'),
        'cancel_url' => route('checkout-cancel'),
    ]);

    // return redirect($checkoutSession->url);
    // return response()->view('checkout-redirect', [
    //     'stripeUrl' => $checkoutSession->url
    // ]);
    return Inertia::render('CheckoutRedirect', [
        'stripeUrl' => $checkoutSession->url,
        'sessionId' => $checkoutSession->id,
    ]);
})->middleware(['auth'])->name('checkout');
 
Route::get('/checkout/success', function () {
    return Inertia::render('checkout/success');
})->name('checkout-success');

Route::get('/checkout/cancel', function () {
    return Inertia::render('checkout/cancel');
})->name('checkout-cancel');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

