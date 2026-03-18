<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'shipping_contact_required' => 'boolean',
            'currency_rate' => 'decimal:6',
            'paid_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
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
}
