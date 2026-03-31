<?php

use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Dashboard\GiftCards as DashboardGiftCards;
use App\Livewire\Dashboard\OrderDetail;
use App\Livewire\Dashboard\Orders;
use App\Livewire\Dashboard\Profile;
use App\Livewire\Dashboard\ReferralRewards;
use App\Livewire\Dashboard\Wishlist;
use App\Models\BlogPost;
use App\Models\GiftCard;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
*/

// Blog index — /blog
Route::get('/blog', function () {
    return view('blog.index');
})->name('blog.index');

// Blog single post — /blog/{slug}
Route::get('/blog/{post:slug}', function (BlogPost $post) {
    return view('blog.show', ['post' => $post, 'slug' => $post->slug]);
})->name('blog.show');

// ─── Currency preference (no auth required — persists to session / DB)
Route::post('/currency/set', function (Request $request, CurrencyService $currencyService) {
    $raw = strtoupper(trim($request->input('code', 'NGN')));
    $code = in_array($raw, CurrencyService::SUPPORTED_CURRENCIES) ? $raw : CurrencyService::BASE_CURRENCY;
    $currencyService->setUserCurrency($code);

    return response()->json(['ok' => true, 'active' => $code]);
})->name('currency.set');

// ─── Sitemap ──────────────────────────────────────────────────────────────────

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ─── Storefront ──────────────────────────────────────────────────────────────

Route::get('/shop', function () {
    return view('frontend.products.index');
})->name('shop.index');

Route::get('/faq', fn () => view('frontend.faq'))->name('faq');

Route::get('/about', fn () => view('frontend.about'))->name('about');

Route::get('/contact', fn () => view('frontend.contact'))->name('contact');

Route::get('/unsubscribe/{token}', function (string $token) {
    $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

    if ($subscriber->isActive()) {
        $subscriber->update(['unsubscribed_at' => now()]);
    }

    return view('frontend.unsubscribe-success');
})->name('newsletter.unsubscribe');

Route::get('/return-policy', fn () => view('frontend.return-policy'))->name('return-policy');

Route::get('/terms', fn () => view('frontend.terms'))->name('terms');

Route::get('/privacy', fn () => view('frontend.privacy'))->name('privacy');

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
    $order = Order::where('order_number', $orderNumber)->first();
    $giftCodes = $order
        ? GiftCard::where('purchased_order_id', $order->id)
            ->get(['code', 'initial_balance'])->toArray()
        : [];

    return view('frontend.checkout.success', compact('orderNumber', 'giftCodes'));
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

    Route::get('/orders/{order}', function (Order $order) {
        return view('admin.orders.index', ['highlightOrderId' => $order->id]);
    })->name('orders.show');

    Route::get('/shipping', function () {
        return view('admin.shipping.index');
    })->name('shipping.index');

    Route::get('/shipping/dhl', function () {
        return view('admin.shipping.dhl');
    })->name('shipping.dhl');

    Route::get('/rewards', function () {
        return view('admin.rewards.settings');
    })->name('rewards.settings');

    Route::get('/testimonials', fn () => view('admin.testimonials.index'))->name('testimonials.index');

    Route::get('/gift-cards', fn () => view('admin.gift-cards.index'))->name('gift-cards.index');

    Route::get('/blog', fn () => view('admin.blog.index'))->name('blog.index');
    Route::get('/blog/comments', fn () => view('admin.blog.comments'))->name('blog.comments');
    Route::get('/blog/create', fn () => view('admin.blog.form'))->name('blog.create');
    Route::get('/blog/{post}/edit', function (BlogPost $post) {
        return view('admin.blog.form', compact('post'));
    })->name('blog.edit');

    Route::get('/contacts', fn () => view('admin.contacts.index'))->name('contacts.index');

    Route::get('/newsletter', fn () => view('admin.newsletter.index'))->name('newsletter.index');

});

// ─── Auth profile ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])
    ->prefix('account')
    ->name('account.')
    ->group(function () {

        Route::get('/orders', Orders::class)->name('orders');
        Route::get('/orders/{order}', OrderDetail::class)->name('orders.show');
        Route::get('/wishlist', Wishlist::class)->name('wishlist');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/referral', ReferralRewards::class)->name('referral');
        Route::get('/gift-cards', DashboardGiftCards::class)->name('gift-cards');

    });

require __DIR__.'/auth.php';
