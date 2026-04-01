<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;   // ← add this

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_street',
        'shipping_house_no',
        'shipping_postal',
        'shipping_notes',
        'shipping_method_id',
        'shipping_carrier',
        'shipping_method_name',
        'shipping_cost',
        'shipping_currency',
        'shipping_estimated_days',
        'shipping_contact_required',
        'payment_method',
        'payment_reference',
        'payment_status',
        'paid_at',
        'currency',
        'currency_rate',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'total',
        'status',
        'reminder_sent_at',
        'second_reminder_sent_at',
        'admin_notes',
        'referral_code',
        'referral_discount_amount',
        'points_redeemed',
        'points_discount_amount',
        'gift_card_code',
        'gift_card_discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'shipping_contact_required' => 'boolean',
            'currency_rate' => 'decimal:6',
            'paid_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'second_reminder_sent_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ✅ NEW
    public function dhlShipment(): HasOne
    {
        return $this->hasOne(DhlShipment::class);
    }

    public function giftCardTransactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    // ✅ NEW — total weight from order items
    public function getTotalWeight(): float
    {
        $this->loadMissing('items');

        return (float) $this->items->sum(
            fn ($item) => ($item->weight_kg ?? 1.5) * $item->quantity
        );
    }

    // ✅ NEW — true when shipping_carrier is DHL
    public function isDhlOrder(): bool
    {
        return $this->shipping_carrier === 'DHL';
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'DF-'.strtoupper(substr(md5(uniqid('', true)), 0, 7));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Order can be cancelled only if it hasn't shipped or been cancelled yet.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing'])
            && $this->status !== 'cancelled';
    }

    public function canChangeAddress(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'text-amber-400 bg-amber-400/10',
            'processing' => 'text-blue-400 bg-blue-400/10',
            'shipped' => 'text-violet-400 bg-violet-400/10',
            'delivered' => 'text-emerald-400 bg-emerald-400/10',
            'cancelled' => 'text-red-400 bg-red-400/10',
            default => 'text-white/50 bg-white/5',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Awaiting Payment',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->payment_status),
        };
    }

    public function paymentStatusColor(): string
    {
        return match ($this->payment_status) {
            'paid' => 'text-emerald-400 bg-emerald-400/10',
            'pending' => 'text-amber-400 bg-amber-400/10',
            'refunded' => 'text-blue-400 bg-blue-400/10',
            'failed' => 'text-red-400 bg-red-400/10',
            default => 'text-white/50 bg-white/5',
        };
    }
}
