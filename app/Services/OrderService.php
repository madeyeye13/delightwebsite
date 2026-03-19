<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly PaystackService $paystackService,
        private readonly FlutterwaveService $flutterwaveService,
    ) {}

    /**
     * Create an order from the pending cart and raw payload from checkout.
     *
     * @param  User  $user  The buyer (pre-created guest or existing user)
     * @param  Cart  $cart  The cart to convert to an order
     * @param  array<string, mixed>  $payload  Validated checkout payload
     * @param  float  $subtotalNGN  Cart subtotal in NGN (used for coupon discount calc)
     */
    public function createOrder(User $user, Cart $cart, array $payload, float $subtotalNGN): Order
    {
        $contact = $payload['contact'];
        $address = $payload['address'];
        $shipping = $payload['shippingMethod'];
        $promoCode = $payload['promoCode'] ?? null;

        // Resolve promo
        $discountAmount = 0.0;
        $coupon = null;
        if ($promoCode) {
            $coupon = ProductCoupon::where('code', strtoupper($promoCode))->first();
            if ($coupon && $coupon->isValid()) {
                $discountAmount = round($subtotalNGN * ($coupon->discount_percent / 100), 2);
            }
        }

        $shippingCost = (float) ($shipping['price'] ?? 0);
        $referralDiscount = (float) ($payload['referralDiscountAmount'] ?? 0);
        $pointsDiscount   = (float) ($payload['pointsDiscountAmount'] ?? 0);

        $total = max(0, $subtotalNGN - $discountAmount - $referralDiscount - $pointsDiscount + $shippingCost);

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,

            // Contact snapshot
            'contact_name' => $contact['fullName'],
            'contact_email' => $contact['email'],
            'contact_phone' => ($contact['phoneCode'] ?? '+234').' '.ltrim($contact['phone'] ?? '', '0'),

            // Address snapshot
            'shipping_street' => trim(($address['street'] ?? '').(! empty($address['houseNo']) ? ', '.$address['houseNo'] : '')),
            'shipping_city' => $address['city'],
            'shipping_state' => $address['state'],
            'shipping_country' => $address['country'],
            'shipping_postal' => $address['postal'] ?? null,
            'shipping_notes' => $address['notes'] ?? null,

            // Shipping
            'shipping_method_id' => $shipping['id'],
            'shipping_carrier' => $this->resolveCarrierName($shipping['id']),
            'shipping_method_name' => $shipping['name'] ?? null,
            'shipping_cost' => (int) round($shippingCost),
            'shipping_estimated_days' => $shipping['estimated_days'] ?? null,
            'shipping_contact_required' => (bool) ($shipping['contact_required'] ?? false),

            // Payment
            'payment_method' => $payload['paymentMethod'],
            'payment_status' => 'pending',
            'currency' => 'NGN',

            // Totals
            'subtotal' => (int) round($subtotalNGN),
            'discount_amount' => (int) round($discountAmount),
            'coupon_code' => $promoCode ? strtoupper($promoCode) : null,
            'total' => (int) round($total),

            'status' => 'pending',

            'referral_code'             => $payload['referralCode'] ?? null,
            'referral_discount_amount'  => $payload['referralDiscountAmount'] ?? 0,
            'points_redeemed'           => $payload['pointsRedeemed'] ?? 0,
            'points_discount_amount'    => $payload['pointsDiscountAmount'] ?? 0,
        ]);

        // Create order items from cart
        $cart->load('items.product.sellingMethod', 'items.variant');
        foreach ($cart->items as $cartItem) {
            $product = $cartItem->product;
            if (! $product) {
                continue;
            }
            $variant = $cartItem->variant;
            $unitPrice = $product->final_price + ($variant?->price_adjustment ?? 0);

            $order->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_name' => $variant?->name,
                'selling_method' => $product->sellingMethod?->config_type ?? 'per_piece',
                'unit_label' => $product->unit_label,
                'units_per_order' => $product->units_per_order ?? 1,
                'unit_price' => $unitPrice,
                'quantity' => $cartItem->quantity,
                'total_price' => $unitPrice * $cartItem->quantity,
                'weight_kg' => $product->weight ?? 1.5,
                'is_addon' => false,
            ]);
        }

        // Record coupon usage
        if ($coupon) {
            $coupon->increment('uses_count');
        }

        Log::info("Order {$order->order_number} created for user {$user->id}");

        return $order;
    }

    /**
     * Create a guest user account for checkout (no prior account).
     * Returns [User, generatedPassword|null].
     *
     * @return array{0: User, 1: string|null}
     */
    public function createGuestUser(array $contact): array
    {
        $existingUser = User::where('email', strtolower($contact['email']))->first();
        if ($existingUser) {
            return [$existingUser, false, null];
        }

        $chosenPassword = ! empty($contact['password']) && strlen($contact['password']) >= 8
            ? $contact['password']
            : null;

        $plainPassword = $chosenPassword ?? Str::password(12);

        $user = User::create([
            'name' => $contact['fullName'],
            'email' => strtolower($contact['email']),
            'password' => Hash::make($plainPassword),
            'role' => 'customer',
        ]);

        Log::info("Guest user created at checkout: {$user->email}");

        // Return: [user, isNew, temporaryPassword|null]
        // temporaryPassword is only set when we generated one (user didn't choose their own)
        return [$user, true, $chosenPassword ? null : $plainPassword];
    }

    /**
     * Initialize payment gateway and return the redirect URL.
     */
    public function initializePayment(Order $order, string $paymentMethod): string
    {
        $callbackUrl = route('payment.'.$paymentMethod.'.callback');

        if ($paymentMethod === 'paystack') {
            $data = [
                'email' => $order->contact_email,
                'amount' => (int) round($order->total * 100), // kobo
                'reference' => $order->order_number,
                'callback_url' => $callbackUrl,
                'metadata' => ['order_number' => $order->order_number, 'order_id' => $order->id],
            ];
            $result = $this->paystackService->initializeTransaction($data);

            return $result['data']['authorization_url'] ?? route('checkout.index');
        }

        if ($paymentMethod === 'flutterwave') {
            $data = [
                'tx_ref' => $order->order_number,
                'amount' => $order->total,
                'currency' => 'NGN',
                'redirect_url' => $callbackUrl,
                'customer' => [
                    'email' => $order->contact_email,
                    'name' => $order->contact_name,
                    'phonenumber' => $order->contact_phone,
                ],
                'customizations' => [
                    'title' => '1st Delightsome Order',
                    'description' => 'Order '.$order->order_number,
                ],
                'meta' => ['order_id' => $order->id],
            ];
            $result = $this->flutterwaveService->initializePayment($data);

            return $result['data']['link'] ?? route('checkout.index');
        }

        return route('checkout.index');
    }

    /**
     * Mark an order as paid after payment gateway callback verification.
     */
    public function markOrderPaid(Order $order, string $paymentReference): void
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_reference' => $paymentReference,
            'paid_at' => now(),
            'status' => 'processing',
        ]);

        Log::info("Order {$order->order_number} marked paid. Ref: {$paymentReference}");
    }

    private function resolveCarrierName(string $shippingId): string
    {
        if (str_starts_with($shippingId, 'dhl_')) {
            return 'DHL';
        }
        if ($shippingId === 'store_pickup') {
            return 'Store Pickup';
        }

        return 'Standard Delivery';
    }

    private function resolveMethodType(string $shippingId): string
    {
        if (str_starts_with($shippingId, 'dhl_')) {
            return 'dhl';
        }
        if ($shippingId === 'store_pickup') {
            return 'store_pickup';
        }

        return 'custom';
    }

    /**
     * Clear the cart after a successful order.
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        Auth::check()
            ? Log::info("Cart cleared for user {$cart->user_id} after order")
            : Log::info("Cart cleared for session {$cart->session_id} after order");
    }
}
