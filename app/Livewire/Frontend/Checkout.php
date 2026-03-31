<?php

namespace App\Livewire\Frontend;

use App\Mail\AccountCreated;
use App\Mail\AdminOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\AppSetting;
use App\Models\Cart;
use App\Models\DhlConfiguration;
use App\Models\Order;
use App\Models\ProductCoupon;
use App\Models\Referral;
use App\Models\RewardPoint;
use App\Models\RewardSetting;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\CustomShippingService;
use App\Services\DHLService;
use App\Services\GiftCardService;
use App\Services\OrderService;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;

class Checkout extends Component
{
    public bool $isGuest = true;

    public string $sessionReferralCode = '';

    public int $userPointBalance = 0;

    public int $maxPointsPerOrder = 0;

    public int $nairaPerPoint = 0;

    public function mount(): void
    {
        $cart = $this->resolveCart();
        if (! $cart || $cart->items()->count() === 0) {
            $this->redirect(route('cart.index'));
        }

        $this->isGuest = ! Auth::check();

        // Load referral code captured by CaptureReferral middleware
        $this->sessionReferralCode = Session::get('referral_code', '');

        // Load reward point info for logged-in users
        if (Auth::check()) {
            $this->userPointBalance = RewardPoint::balanceFor(Auth::id());
            $this->maxPointsPerOrder = RewardSetting::maxPointsPerOrder();
            $this->nairaPerPoint = RewardSetting::nairaPerPoint();
        }
    }

    /**
     * Called from Alpine on page load to get referral/points info.
     * Returns what discounts are available to this checkout session.
     */
    public function getCheckoutDiscounts(): array
    {
        $referralDiscount = 0;
        $referralCode = $this->sessionReferralCode;
        $userId = Auth::id();

        if ($referralCode) {
            $referral = Referral::where('code', $referralCode)->first();

            // Disallow using your own code
            if ($referral && $referral->user_id !== $userId) {
                $percent = RewardSetting::referralDiscountPercent();
                $referralDiscount = $percent; // percent — Alpine calculates NGN amount
            } else {
                // Invalid or own code — clear it
                $referralCode = '';
                Session::forget('referral_code');
            }
        }

        return [
            'referralCode' => $referralCode,
            'referralDiscountPct' => $referralDiscount,
            'pointBalance' => $this->userPointBalance,
            'maxPointsPerOrder' => $this->maxPointsPerOrder,
            'nairaPerPoint' => $this->nairaPerPoint,
            // Max NGN the user can get from points
            'maxPointsDiscountNgn' => $this->maxPointsPerOrder * $this->nairaPerPoint,
        ];
    }

    /**
     * Calculate shipping options for the given address.
     * Called from Alpine via $wire.calculateShipping().
     *
     * @return array<int, array<string, mixed>>
     */
    public function calculateShipping(
        string $country,
        string $state,
        string $city,
        string $postal = '',
        float $totalWeightKg = 0.0,
        string $currency = 'NGN',
    ): array {
        $country = strtoupper(trim($country));
        if (empty($country)) {
            return [];
        }

        // Default weight if not provided
        if ($totalWeightKg <= 0) {
            $totalWeightKg = $this->calculateCartWeight();
        }

        $options = [];

        // Nigeria: custom shipping + store pickup
        if ($country === 'NG') {
            $customService = app(CustomShippingService::class);
            $options = $customService->getOptions($country, $state, $city, $totalWeightKg);
        }

        // International: try DHL
        if ($country !== 'NG' || DhlConfiguration::get('show_for_nigeria', false)) {
            $dhlResult = app(DHLService::class)->getRates([
                'destination_country_code' => $country,
                'destination_city' => $city,
                'destination_postal_code' => $postal,
                'weight' => $totalWeightKg,
                'currency' => $currency,
                'declared_value' => 100,
            ]);

            if ($dhlResult['success'] ?? false) {
                foreach ($dhlResult['products'] as $product) {
                    $totalTransitDays = $product['total_transit_days'] ?? null;
                    $dayLabel = $totalTransitDays ? "{$totalTransitDays} business day(s)" : '3–5 business days';
                    $options[] = [
                        'id' => 'dhl_'.$product['product_code'],
                        'name' => 'DHL — '.$product['product_name'],
                        'description' => "International Shipping · {$dayLabel}",
                        'price' => (float) $product['final_price'],
                        'badge' => null,
                        'badgeCls' => '',
                        'contact_required' => false,
                        'estimated_days' => $totalTransitDays,
                    ];
                }
            } elseif ($country !== 'NG') {
                // DHL not yet configured or returned no rates — show coming-soon placeholder.
                // Once DHL credentials are added to .env and test_mode is turned off in admin,
                // this branch is replaced automatically by live rates above.
                $options[] = [
                    'id' => 'dhl_coming_soon',
                    'name' => 'DHL International Shipping — Coming Soon',
                    'description' => "We'll confirm your shipping cost after your order is placed and reach out to you directly.",
                    'price' => 0,
                    'badge' => 'COMING SOON',
                    'badgeCls' => 'text-[10px] bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400 px-1.5 py-0.5 font-semibold',
                    'contact_required' => true,
                    'estimated_days' => null,
                ];
            }
        }

        return $options;
    }

    /**
     * Validate a promo/coupon code against the cart total.
     *
     * @return array{valid: bool, message: string, discount_percent?: int, discount_amount?: float}
     */
    public function validatePromo(string $code, float $subtotal = 0.0): array
    {
        $code = strtoupper(trim($code));
        if (empty($code)) {
            return ['valid' => false, 'message' => 'Please enter a code'];
        }

        $coupon = ProductCoupon::where('code', $code)->first();
        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired code'];
        }

        // Check if the coupon's product is in the cart
        $cart = $this->resolveCart();
        if ($cart && $coupon->product_id) {
            $cartProductIds = $cart->items()->pluck('product_id')->toArray();
            if (! in_array($coupon->product_id, $cartProductIds)) {
                return ['valid' => false, 'message' => 'This coupon is not valid for items in your cart'];
            }
        }

        if (! $coupon->isValid()) {
            return ['valid' => false, 'message' => 'This coupon is no longer valid'];
        }

        if ($coupon->min_order_amount > 0 && $subtotal < $coupon->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum order of ₦'.number_format($coupon->min_order_amount, 0).' required',
            ];
        }

        $discountAmount = round($subtotal * ($coupon->discount_percent / 100));

        return [
            'valid' => true,
            'message' => "Code applied! {$coupon->discount_percent}% discount",
            'discount_percent' => $coupon->discount_percent,
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Validate a gift card code and return the available balance.
     *
     * @return array{valid: bool, message: string, balance?: int}
     */
    public function validateGiftCard(string $code): array
    {
        $code = strtoupper(trim($code));
        if (empty($code)) {
            return ['valid' => false, 'message' => 'Please enter a gift card code'];
        }

        return app(GiftCardService::class)->validate($code);
    }

    /**
     * Sync the header currency when the country dropdown changes.
     */
    public function syncCurrency(string $countryCode): void
    {
        $currencyService = app(CurrencyService::class);
        $currency = CurrencyService::COUNTRY_TO_CURRENCY[strtoupper($countryCode)] ?? 'NGN';
        $currencyService->setUserCurrency($currency);
        $this->dispatch('currency:changed', currency: $currency);
    }

    /**
     * Place the order: create user if needed, create order, init payment.
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, order_number?: string, payment_url?: string, error?: string}
     */
    public function placeOrder(array $payload): array
    {
        // Basic validation
        $contact = $payload['contact'] ?? [];
        $address = $payload['address'] ?? [];
        $shippingMethod = $payload['shippingMethod'] ?? [];
        $paymentMethod = $payload['paymentMethod'] ?? '';

        if (empty($contact['fullName']) || empty($contact['email']) || empty($contact['phone'])) {
            return ['success' => false, 'error' => 'Please fill in all required contact fields'];
        }
        if (! in_array($paymentMethod, ['paystack', 'flutterwave'])) {
            return ['success' => false, 'error' => 'Please select a payment method'];
        }

        $cart = $this->resolveCart();
        if (! $cart || $cart->items()->count() === 0) {
            return ['success' => false, 'error' => 'Your cart is empty'];
        }

        $cart->load('items.product');
        $hasOnlyGiftCards = $cart->items->isNotEmpty()
            && $cart->items->every(fn ($item) => (bool) $item->product?->is_gift_card);

        if (! $hasOnlyGiftCards) {
            if (empty($address['street']) || empty($address['city']) || empty($address['country'])) {
                return ['success' => false, 'error' => 'Please fill in your shipping address'];
            }
            if (empty($shippingMethod['id'])) {
                return ['success' => false, 'error' => 'Please select a shipping method'];
            }
        }

        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);

        // Resolve or create user
        if (Auth::check()) {
            $user = Auth::user();
            $isNewAccount = false;
            $generatedPassword = null;
        } else {
            [$user, $isNewAccount, $generatedPassword] = $orderService->createGuestUser($contact);
            Auth::login($user);
        }

        // Calculate subtotal from actual cart
        $cart->load('items.product.sellingMethod', 'items.variant');
        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->product;
            if (! $product) {
                return 0;
            }
            // For gift cards with a custom denomination, use the stored custom_price
            if ($product->is_gift_card && $item->custom_price) {
                return $item->custom_price * $item->quantity;
            }
            $unitPrice = $product->final_price + ($item->variant?->price_adjustment ?? 0);

            return $unitPrice * $item->quantity;
        });

        // ── Referral discount ────────────────────────────────────────────
        $referralCode = Session::get('referral_code', '');
        $referralDiscountAmount = 0;

        if ($referralCode) {
            $referralDiscountAmount = app(ReferralService::class)
                ->calculateDiscount($referralCode, $subtotal, $user->id);
            // Inject into payload so OrderService stores it
            $payload['referralCode'] = $referralCode;
            $payload['referralDiscountAmount'] = $referralDiscountAmount;
        }

        // ── Points redemption ─────────────────────────────────────────────
        $pointsToRedeem = (int) ($payload['pointsToRedeem'] ?? 0);
        $pointsDiscountAmount = 0;

        if ($pointsToRedeem > 0 && Auth::check()) {
            $maxAllowed = RewardSetting::maxPointsPerOrder();
            $userBalance = RewardPoint::balanceFor($user->id);
            $pointsToRedeem = min($pointsToRedeem, $maxAllowed, $userBalance);

            $pointsDiscountAmount = $pointsToRedeem * RewardSetting::nairaPerPoint();

            $payload['pointsRedeemed'] = $pointsToRedeem;
            $payload['pointsDiscountAmount'] = $pointsDiscountAmount;
        }

        // ── Gift card redemption ──────────────────────────────────────────
        $giftCardCode = strtoupper(trim($payload['giftCardCode'] ?? ''));
        $giftCardDiscount = 0;

        if ($giftCardCode) {
            $validation = app(GiftCardService::class)->validate($giftCardCode);
            if ($validation['valid']) {
                $alreadyDiscounted = ($referralDiscountAmount + $pointsDiscountAmount);
                $maxGiftCardDiscount = max(0, $subtotal - $alreadyDiscounted);
                $giftCardDiscount = min((int) $validation['balance'], (int) $maxGiftCardDiscount);

                $payload['giftCardCode'] = $giftCardCode;
                $payload['giftCardDiscountAmount'] = $giftCardDiscount;
            } else {
                // Code became invalid between validation and submit — clear it
                unset($payload['giftCardCode']);
                $payload['giftCardDiscountAmount'] = 0;
            }
        }

        // Create order
        $order = $orderService->createOrder($user, $cart, $payload, (float) $subtotal);

        // Clear cart
        $orderService->clearCart($cart);

        // Send emails (queued)
        $this->dispatchOrderEmails($order, $user, $isNewAccount, $generatedPassword);

        // Initialize payment
        $paymentUrl = $orderService->initializePayment($order, $paymentMethod);

        return [
            'success' => true,
            'order_number' => $order->order_number,
            'payment_url' => $paymentUrl,
        ];
    }

    public function render(): View
    {
        return view('livewire.frontend.checkout', [
            'isGuest' => $this->isGuest,
            'authUser' => Auth::user(),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function resolveCart(): ?Cart
    {
        if (Auth::check()) {
            return Cart::forUser(Auth::id())->first();
        }

        $sessionId = Session::getId();

        return Cart::forSession($sessionId)->first();
    }

    private function calculateCartWeight(): float
    {
        $cart = $this->resolveCart();
        if (! $cart) {
            return 1.5;
        }
        $cart->load('items.product');
        $total = $cart->items->sum(function ($item) {
            $weight = $item->product?->weight ?? 1.5;

            return (float) $weight * $item->quantity;
        });

        return max(0.5, (float) $total);
    }

    private function dispatchOrderEmails(
        Order $order,
        User $user,
        bool $isNewAccount,
        ?string $generatedPassword,
    ): void {
        try {
            if ($isNewAccount) {
                Mail::to($user->email)->queue(new AccountCreated($user, $generatedPassword));
            }
            Mail::to($order->contact_email)->queue(new OrderConfirmation($order));
            if ((bool) AppSetting::get('notify_new_order', '1')) {
                $adminEmail = AppSetting::get('admin_notification_email', config('mail.from.address'));
                Mail::to($adminEmail)->later(now()->addSeconds(30), new AdminOrderNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Order email dispatch failed', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
