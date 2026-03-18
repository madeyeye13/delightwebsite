<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NigerianCityShipping extends Model
{
    protected $fillable = [
        'state_shipping_id',
        'city_name',
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

    public function stateShipping(): BelongsTo
    {
        return $this->belongsTo(StateShipping::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate shipping cost for a given weight,
     * inheriting tier config from state when not set on city.
     */
    public function calculateCost(float $weightKg): array
    {
        $state = $this->stateShipping;
        $fee = $this->shipping_fee;

        $tier1 = (float) ($this->tier_1_limit ?? $state->tier_1_limit);
        $tier2 = (float) ($this->tier_2_limit ?? $state->tier_2_limit);
        $tier3 = (float) ($this->tier_3_limit ?? $state->tier_3_limit);
        $surcharge2 = $this->tier_2_surcharge ?? $state->tier_2_surcharge;
        $surcharge3 = $this->tier_3_surcharge ?? $state->tier_3_surcharge;
        $contactHeavy = $this->contact_for_heavy ?? $state->contact_for_heavy;

        if ($weightKg > $tier3 && $contactHeavy) {
            return [
                'cost' => $fee,
                'contact_required' => true,
                'message' => 'This order is quite weighty — we will contact you to confirm your shipping cost before dispatch.',
            ];
        }

        if ($weightKg > $tier2) {
            $fee += $surcharge3;
        } elseif ($weightKg > $tier1) {
            $fee += $surcharge2;
        }

        return [
            'cost' => $fee,
            'contact_required' => false,
            'message' => null,
        ];
    }
}
