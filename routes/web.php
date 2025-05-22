<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('/products', function () {
//     return Inertia::render('Product');
// })->name('product');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::delete('categories/bulk-delete', [CategoryController::class, 'bulkDelete']);
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
