<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StateShipping extends Model
{
    protected $fillable = [
        'state_name',
        'state_code',
        'shipping_fee',
        'currency',
        'estimated_days',
        'is_active',
        'tier_1_limit',
        'tier_2_limit',
        'tier_2_surcharge',
        'tier_3_limit',
        'tier_3_surcharge',
        'contact_for_heavy',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'contact_for_heavy' => 'boolean',
            'tier_1_limit' => 'decimal:2',
            'tier_2_limit' => 'decimal:2',
            'tier_3_limit' => 'decimal:2',
        ];
    }

    public function cities(): HasMany
    {
        return $this->hasMany(NigerianCityShipping::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('state_name');
    }

    /**
     * Calculate shipping cost for a given weight.
     */
    public function calculateCost(float $weightKg): array
    {
        $fee = $this->shipping_fee;
        $tier1 = (float) $this->tier_1_limit;
        $tier2 = (float) $this->tier_2_limit;
        $tier3 = (float) $this->tier_3_limit;

        if ($weightKg > $tier3 && $this->contact_for_heavy) {
            return [
                'cost' => $fee,
                'contact_required' => true,
                'message' => 'This order is quite weighty — we will contact you to confirm your shipping cost before dispatch.',
            ];
        }

        if ($weightKg > $tier2) {
            $fee += $this->tier_3_surcharge;
        } elseif ($weightKg > $tier1) {
            $fee += $this->tier_2_surcharge;
        }

        return [
            'cost' => $fee,
            'contact_required' => false,
            'message' => null,
        ];
    }
}
