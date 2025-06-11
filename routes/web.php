<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Models\Product;
use App\Models\Order;

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
Route::resource('tags', TagController::class);
Route::resource('users', UserController::class);
Route::resource('orders', OrderController::class);

Route::resource('products', ProductController::class);

Route::get('/checkout', function (Request $request) {
    // $stripePriceId = 'price_1RX2mME1dy0MotZhKJrrQzqb';
 
    // $quantity = 1;
 
    // $checkoutSession = $request->user()->checkout([$stripePriceId => $quantity], [
    //     'success_url' => route('checkout-success'),
    //     'cancel_url' => route('checkout-cancel'),
    // ]);

    // return Inertia::render('CheckoutRedirect', [
    //     'stripeUrl' => $checkoutSession->url,
    //     'sessionId' => $checkoutSession->id,
    // ]);

    $order = Order::create([
        'user_id' => auth()->id(),
        'status' => 'pending',
        'notes' => 'This is a test order',
        'payment_method' => 'stripe',
        'payment_status' => 'unpaid',
        'total_amount' => 5,
        'shipping_address' => 'vietnam',
        'discount_amount' => 0,
    ]);

    $checkoutSession = $request->user()->checkout([
        [
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $order->total_amount * 100,
                'product_data' => [
                    'name' => 'Order #' . $order->id,
                ],
            ],
            'quantity' => 1,
        ]
    ], [
        'success_url' => route('checkout-success'),
        'cancel_url' => route('checkout-cancel'),
        'metadata' => [
            'order_id' => $order->id,
        ],
    ]);

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

Route::get('/search', [ProductController::class, 'search'])
    ->name('products.search');


