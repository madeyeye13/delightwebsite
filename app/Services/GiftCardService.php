<?php

namespace App\Services;

use App\Jobs\SendGiftCardEmail;
use App\Jobs\SendGiftCardRedemptionNotificationEmail;
use App\Models\GiftCard;
use App\Models\GiftCardTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftCardService
{
    /**
     * Generate a cryptographically secure, unambiguous gift card code.
     *
     * Format: DLT-XXXX-XXXX-XXXX
     * Charset: 32 unambiguous characters (no 0/O, 1/I/L)
     * Entropy: 32^12 ≈ 1.15 quintillion combinations
     */
    public function generateCode(): string
    {
        $charset = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $segments = [];

        for ($s = 0; $s < 3; $s++) {
            $segment = '';
            for ($i = 0; $i < 4; $i++) {
                $segment .= $charset[random_int(0, strlen($charset) - 1)];
            }
            $segments[] = $segment;
        }

        $code = 'DLT-'.implode('-', $segments);

        // Ensure uniqueness (collision is astronomically unlikely, but be safe)
        if (GiftCard::where('code', $code)->exists()) {
            return $this->generateCode();
        }

        return $code;
    }

    /**
     * Issue gift card(s) for all gift card products in a paid order.
     * Called from PaymentCallbackController after markOrderPaid.
     */
    public function issueForOrder(Order $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->is_gift_card) {
                continue;
            }

            // The denomination is the unit_price of the order item (custom or fixed)
            $denomination = (int) $item->unit_price;
            if ($denomination <= 0) {
                continue;
            }

            // Issue one gift card per quantity ordered
            for ($i = 0; $i < $item->quantity; $i++) {
                $code = $this->generateCode();

                $giftCard = GiftCard::create([
                    'code' => $code,
                    'status' => 'active',
                    'initial_balance' => $denomination,
                    'current_balance' => $denomination,
                    'purchased_by_user_id' => $order->user_id,
                    'purchased_order_id' => $order->id,
                    'recipient_email' => null,
                    'recipient_name' => null,
                    'personal_message' => null,
                    'is_pos_issued' => false,
                ]);

                SendGiftCardEmail::dispatch($giftCard, $order);

                Log::info("Gift card {$code} issued for order {$order->order_number}");
            }
        }
    }

    /**
     * Validate a gift card code for checkout use.
     *
     * @return array{valid: bool, message: string, card?: GiftCard, balance?: int}
     */
    public function validate(string $code): array
    {
        $code = strtoupper(trim($code));

        if (empty($code)) {
            return ['valid' => false, 'message' => 'Please enter a gift card code'];
        }

        $card = GiftCard::where('code', $code)->first();

        if (! $card) {
            return ['valid' => false, 'message' => 'Gift card not found'];
        }

        if ($card->status === 'redeemed') {
            return ['valid' => false, 'message' => 'This gift card has already been fully redeemed'];
        }

        if ($card->status === 'cancelled') {
            return ['valid' => false, 'message' => 'This gift card has been cancelled'];
        }

        if ($card->status === 'expired') {
            return ['valid' => false, 'message' => 'This gift card has expired'];
        }

        if ($card->expires_at && $card->expires_at->isPast()) {
            $card->update(['status' => 'expired']);

            return ['valid' => false, 'message' => 'This gift card has expired'];
        }

        if ($card->current_balance <= 0) {
            return ['valid' => false, 'message' => 'This gift card has no remaining balance'];
        }

        return [
            'valid' => true,
            'message' => 'Gift card valid',
            'card' => $card,
            'balance' => $card->current_balance,
        ];
    }

    /**
     * Apply a gift card to an online order at payment confirmation.
     * Records the transaction and updates the card balance.
     */
    public function applyToOrder(Order $order): void
    {
        if (! $order->gift_card_code || $order->gift_card_discount_amount <= 0) {
            return;
        }

        $card = GiftCard::where('code', $order->gift_card_code)->first();
        if (! $card || ! $card->isActive()) {
            Log::warning("Gift card {$order->gift_card_code} not valid at payment confirmation for order {$order->order_number}");

            return;
        }

        $amountToUse = min((int) $order->gift_card_discount_amount, $card->current_balance);
        if ($amountToUse <= 0) {
            return;
        }

        DB::transaction(function () use ($card, $amountToUse, $order) {
            $balanceBefore = $card->current_balance;
            $balanceAfter = $balanceBefore - $amountToUse;

            $card->update([
                'current_balance' => $balanceAfter,
                'status' => $balanceAfter <= 0 ? 'redeemed' : 'active',
            ]);

            GiftCardTransaction::create([
                'gift_card_id' => $card->id,
                'amount_used' => $amountToUse,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'order_id' => $order->id,
                'is_pos_redemption' => false,
                'notes' => "Applied to online order {$order->order_number}",
            ]);
        });

        Log::info("Gift card {$card->code} \u2014 ₦{$amountToUse} applied to order {$order->order_number}");
    }

    /**
     * POS in-store redemption: apply gift card against a sale total.
     * Called from admin Livewire GiftCards component.
     *
     * @return array{success: bool, applied: int, remaining: int, message: string}
     */
    public function redeemForPos(GiftCard $card, int $orderAmount, int $adminId, string $notes = ''): array
    {
        if (! $card->isActive()) {
            return ['success' => false, 'applied' => 0, 'remaining' => $card->current_balance, 'message' => 'This gift card is not active'];
        }

        $amountToApply = min($orderAmount, $card->current_balance);
        $remaining = max(0, $orderAmount - $amountToApply);

        DB::transaction(function () use ($card, $amountToApply, $adminId, $notes, $orderAmount) {
            $balanceBefore = $card->current_balance;
            $balanceAfter = $balanceBefore - $amountToApply;

            $card->update([
                'current_balance' => $balanceAfter,
                'status' => $balanceAfter <= 0 ? 'redeemed' : 'active',
            ]);

            GiftCardTransaction::create([
                'gift_card_id' => $card->id,
                'amount_used' => $amountToApply,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'order_id' => null,
                'redeemed_by_admin_id' => $adminId,
                'is_pos_redemption' => true,
                'notes' => $notes ?: 'In-store redemption. Order amount: ₦'.number_format($orderAmount),
            ]);
        });

        // Notify the card holder by email
        $notifyEmail = $card->getNotificationEmail();
        if ($notifyEmail) {
            SendGiftCardRedemptionNotificationEmail::dispatch($card->fresh(), $amountToApply);
        }

        Log::info("Gift card {$card->code} — POS redemption: ₦{$amountToApply} by admin {$adminId}");

        return [
            'success' => true,
            'applied' => $amountToApply,
            'remaining' => $remaining,
            'card_balance_after' => $card->fresh()->current_balance,
            'message' => 'Applied successfully',
        ];
    }
}
