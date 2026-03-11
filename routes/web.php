<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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