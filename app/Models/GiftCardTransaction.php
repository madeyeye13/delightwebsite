<?php

namespace App\Models;

use Database\Factories\GiftCardTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCardTransaction extends Model
{
    /** @use HasFactory<GiftCardTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'gift_card_id',
        'amount_used',
        'balance_before',
        'balance_after',
        'order_id',
        'redeemed_by_admin_id',
        'is_pos_redemption',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_used' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
            'is_pos_redemption' => 'boolean',
        ];
    }

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function redeemedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_admin_id');
    }
}
