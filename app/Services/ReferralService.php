<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Referral;
use App\Models\ReferralUse;
use App\Models\RewardPoint;
use App\Models\RewardSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Get the referral code stored in the session (if any).
     */
    public function getSessionCode(Request $request): ?string
    {
        return $request->session()->get('referral_code');
    }

    /**
     * Calculate the discount amount (NGN) for a given referral code and subtotal.
     * Returns 0 if the code is invalid or the referrer is the same user.
     */
    public function calculateDiscount(string $code, float $subtotalNGN, ?int $currentUserId = null): int
    {
        $referral = Referral::where('code', strtoupper($code))->first();

        if (! $referral) {
            return 0;
        }

        // Don't let users get a discount using their own code
        if ($currentUserId && $referral->user_id === $currentUserId) {
            return 0;
        }

        $percent = RewardSetting::referralDiscountPercent();

        return (int) round($subtotalNGN * ($percent / 100));
    }

    /**
     * After an order is created and paid, process the referral:
     * - Record the use in referral_uses
     * - Award points to the referrer
     * - Clear the session
     */
    public function processReferralForOrder(Order $order, Request $request): void
    {
        if (empty($order->referral_code)) {
            return;
        }

        $referral = Referral::where('code', $order->referral_code)->first();

        if (! $referral) {
            return;
        }

        $pointsToAward = RewardSetting::pointsPerReferral();

        ReferralUse::create([
            'referral_id' => $referral->id,
            'order_id' => $order->id,
            'used_by_user_id' => $order->user_id,
            'discount_amount' => $order->referral_discount_amount,
            'points_awarded' => $pointsToAward,
        ]);

        RewardPoint::create([
            'user_id' => $referral->user_id,
            'points' => $pointsToAward,
            'type' => 'earned',
            'description' => "Referral code {$order->referral_code} used on order #{$order->order_number}",
            'order_id' => $order->id,
        ]);

        Log::info("Referral processed: code {$order->referral_code} → {$pointsToAward} pts to user {$referral->user_id}");

        // Clear session
        $request->session()->forget('referral_code');
    }

    /**
     * Process points redemption for an order.
     * Returns the NGN discount amount from points.
     */
    public function processPointsRedemption(Order $order): void
    {
        if ($order->points_redeemed <= 0 || ! $order->user_id) {
            return;
        }

        RewardPoint::create([
            'user_id' => $order->user_id,
            'points' => -$order->points_redeemed, // negative = spent
            'type' => 'redeemed',
            'description' => "Redeemed {$order->points_redeemed} pts on order #{$order->order_number}",
            'order_id' => $order->id,
        ]);
    }

    /**
     * Reverse points when an order is cancelled:
     * - Remove points awarded to the referrer for this order
     * - Restore redeemed points to the user
     */
    public function reversePointsForOrder(Order $order): void
    {
        // Reverse referrer points earned from this order
        $use = ReferralUse::where('order_id', $order->id)->first();
        if ($use) {
            $referral = $use->referral;
            RewardPoint::create([
                'user_id' => $referral->user_id,
                'points' => -$use->points_awarded,
                'type' => 'redeemed',
                'description' => "Reversed: referral points from cancelled order #{$order->order_number}",
                'order_id' => $order->id,
            ]);
        }

        // Restore redeemed points to the buyer
        if ($order->points_redeemed > 0 && $order->user_id) {
            RewardPoint::create([
                'user_id' => $order->user_id,
                'points' => $order->points_redeemed,
                'type' => 'earned',
                'description' => "Restored: {$order->points_redeemed} pts from cancelled order #{$order->order_number}",
                'order_id' => $order->id,
            ]);
        }
    }
}
