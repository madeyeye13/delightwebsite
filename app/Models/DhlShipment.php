<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DhlShipment extends Model
{
    protected $fillable = [
        'order_id',
        'dhl_tracking_number',
        'tracking_url',
        'shipment_id',
        'product_code',
        'product_name',
        'base_rate',
        'base_currency',
        'markup_percentage',
        'markup_amount',
        'final_rate',
        'billing_currency',
        'estimated_delivery_date',
        'total_transit_days',
        'total_weight',
        'weight_unit',
        'rate_response',
        'shipment_response',
        'status',
        'label_data',
        'label_format',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'markup_percentage' => 'decimal:2',
            'markup_amount' => 'decimal:2',
            'final_rate' => 'decimal:2',
            'total_weight' => 'decimal:3',
            'total_transit_days' => 'integer',
            'rate_response' => 'array',
            'shipment_response' => 'array',
            'estimated_delivery_date' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isCreated(): bool
    {
        return $this->shipment_id !== null;
    }

    public function trackingUrl(): string
    {
        if ($this->tracking_url) {
            return $this->tracking_url;
        }
        if ($this->dhl_tracking_number) {
            return "https://www.dhl.com/en/express/tracking.html?AWB={$this->dhl_tracking_number}";
        }

        return '#';
    }
}
