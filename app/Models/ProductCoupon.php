<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCoupon extends Model
{
    protected $fillable = [
        'product_id',
        'code',
        'discount_percent',
        'expiry_date',
        'max_uses',
        'uses_count',
        'min_order_amount',
        'new_users_only',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'new_users_only' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->where(function ($q) {
                $q->where('max_uses', 0)->orWhereRaw('uses_count < max_uses');
            });
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }
        if ($this->max_uses > 0 && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
