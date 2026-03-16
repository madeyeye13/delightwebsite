<?php

namespace App\Services;

use App\Models\ProductCoupon;
use Illuminate\Support\Facades\Log;

class CouponService
{
    /**
     * Validate and apply a coupon to a product.
     *
     * @param  int  $productId  Product to validate against
     * @param  int|float  $cartTotal  Total amount being purchased
     * @return array{valid: bool, message: string, discount: float}
     */
    public function validateAndApply(?ProductCoupon $coupon, int $productId, $cartTotal = 0): array
    {
        if (! $coupon) {
            return ['valid' => false, 'message' => 'Coupon not found', 'discount' => 0];
        }

        // Check if coupon is for this product
        if ($coupon->product_id !== $productId) {
            return ['valid' => false, 'message' => 'This coupon is not valid for this product', 'discount' => 0];
        }

        // Check if coupon is valid
        if (! $coupon->isValid()) {
            if (! $coupon->is_active) {
                return ['valid' => false, 'message' => 'This coupon is inactive', 'discount' => 0];
            }
            if ($coupon->expiry_date && $coupon->expiry_date->isPast()) {
                return ['valid' => false, 'message' => 'This coupon has expired', 'discount' => 0];
            }
            if ($coupon->max_uses > 0 && $coupon->uses_count >= $coupon->max_uses) {
                return ['valid' => false, 'message' => 'This coupon has reached its usage limit', 'discount' => 0];
            }

            return ['valid' => false, 'message' => 'This coupon is no longer valid', 'discount' => 0];
        }

        // Check new users only restriction
        if ($coupon->new_users_only && auth()->check()) {
            if (auth()->user()->created_at->diffInDays(now()) > 30) {
                return ['valid' => false, 'message' => 'This coupon is only valid for new users', 'discount' => 0];
            }
        }

        // Check minimum order amount
        if ($coupon->min_order_amount > 0 && $cartTotal < $coupon->min_order_amount) {
            $formatted = app(CurrencyService::class)->format($coupon->min_order_amount, 'NGN', false);

            return [
                'valid' => false,
                'message' => "Minimum order of {$formatted} required for this coupon",
                'discount' => 0,
            ];
        }

        // Calculate discount
        $discount = round($cartTotal * ($coupon->discount_percent / 100));

        return [
            'valid' => true,
            'message' => "Coupon applied! {$coupon->discount_percent}% discount",
            'discount' => $discount,
            'coupon' => $coupon,
        ];
    }

    /**
     * Get coupon by code for a specific product.
     */
    public function getCouponByCode(string $code, int $productId): ?ProductCoupon
    {
        return ProductCoupon::where('code', strtoupper($code))
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Apply a coupon code and return validation result.
     *
     * @param  string  $code  Coupon code
     * @param  int  $productId  Product ID
     * @param  int|float  $cartTotal  Cart total amount
     * @return array Validation result with discount
     */
    public function applyCouponCode(string $code, int $productId, $cartTotal = 0): array
    {
        $coupon = $this->getCouponByCode($code, $productId);

        return $this->validateAndApply($coupon, $productId, $cartTotal);
    }

    /**
     * Mark a coupon as used (increment uses_count).
     * Call this after successful checkout with the coupon.
     */
    public function recordCouponUsage(ProductCoupon $coupon): void
    {
        $coupon->increment('uses_count');
        Log::info("Coupon {$coupon->code} used for product {$coupon->product_id}", [
            'coupon_id' => $coupon->id,
            'uses_count' => $coupon->uses_count,
        ]);
    }

    /**
     * Get all available coupons for a product (not expired, not maxed out, active).
     */
    public function getAvailableCouponsForProduct(int $productId): array
    {
        return ProductCoupon::where('product_id', $productId)
            ->active()
            ->get()
            ->map(fn ($coupon) => [
                'code' => $coupon->code,
                'discount' => $coupon->discount_percent,
                'expiry' => $coupon->expiry_date?->format('M d, Y'),
                'min_amount' => $coupon->min_order_amount,
            ])
            ->toArray();
    }
}
