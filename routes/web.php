<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/preview/product', function () {
    // $product is null here — the Blade view falls back to its internal @php mock data
    return view('frontend.products.show', ['product' => null]);
})->name('preview.product');


Route::get('/cart', function () {
    return view('frontend.cart.index');
})->name('cart.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Orders
    Route::get('/orders', function () {
        return view('admin.orders.index');
    })->name('orders.index');

    // Products
    Route::get('/products', function () {
        return view('admin.products.index');
    })->name('products.index');

    Route::get('/products/create', function () {
        return view('admin.products.form');
    })->name('products.create');

    Route::get('/products/{id}/edit', function ($id) {
        return view('admin.products.form');
    })->name('products.edit');

    Route::get('/media', function () {
        return view('admin.media.index');
    })->name('media.index');

    // Inventory
    Route::get('/inventory', function () {
        return view('admin.inventory.index');
    })->name('inventory.index');

    // Users
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
