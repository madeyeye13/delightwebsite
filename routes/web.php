<?php

use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Currency preference (no auth required — persists to session / DB)
Route::post('/currency/set', function (Request $request, CurrencyService $currencyService) {
    $raw = strtoupper(trim($request->input('code', 'NGN')));
    $code = in_array($raw, CurrencyService::SUPPORTED_CURRENCIES) ? $raw : CurrencyService::BASE_CURRENCY;
    $currencyService->setUserCurrency($code);

    return response()->json(['ok' => true, 'active' => $code]);
})->name('currency.set');

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

Route::get('/checkout/success/{orderNumber}', function (string $orderNumber) {
    return view('frontend.checkout.success', compact('orderNumber'));
})->name('checkout.success');

// ─── Payment Callbacks ────────────────────────────────────────────────────────

Route::get('/payment/paystack/callback', [PaymentCallbackController::class, 'paystack'])
    ->name('payment.paystack.callback');

Route::get('/payment/flutterwave/callback', [PaymentCallbackController::class, 'flutterwave'])
    ->name('payment.flutterwave.callback');

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

    Route::get('/orders', function () {
        return view('admin.orders.index');
    })->name('orders.index');

    Route::get('/shipping', function () {
        return view('admin.shipping.index');
    })->name('shipping.index');

    Route::get('/shipping/dhl', function () {
        return view('admin.shipping.dhl');
    })->name('shipping.dhl');

});

// ─── Auth profile ─────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
