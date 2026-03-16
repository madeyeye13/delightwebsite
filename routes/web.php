<?php

use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Storefront ──────────────────────────────────────────────────────────────

Route::get('/shop', function () {
    return view('frontend.products.index');
})->name('shop.index');

Route::get('/products/{product:slug}', function (Product $product) {
    $product->load(['category', 'sellingMethod', 'variants', 'addOns.sellingMethod', 'addOns.category']);

    return view('frontend.products.show', ['product' => $product->toStorefrontArray()]);
})->name('products.show');

Route::get('/cart', function () {
    return view('frontend.cart.index');
})->name('cart.index');

Route::get('/checkout', function () {
    return view('frontend.checkout.index');
})->name('checkout.index');

// ─── Preview helpers (development only) ──────────────────────────────────────

Route::get('/preview/product', function () {
    return view('frontend.products.show', ['product' => null]);
})->name('preview.product');

// ─── Dashboard ────────────────────────────────────────────────────────────────

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Admin ────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/orders', function () {
        return view('admin.orders.index');
    })->name('orders.index');

    Route::get('/products', fn () => view('admin.products.index'))->name('products.index');
    Route::get('/products/create', fn () => view('admin.products.form'))->name('products.create');
    Route::get('/products/{product}/edit', function (Product $product) {
        return view('admin.products.form', compact('product'));
    })->name('products.edit');

    Route::get('/media', function () {
        return view('admin.media.index');
    })->name('media.index');

    Route::get('/inventory', function () {
        return view('admin.inventory.index');
    })->name('inventory.index');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    Route::get('/currencies', function () {
        return view('admin.currencies.index');
    })->name('currencies.index');

});

// ─── Auth profile ─────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
