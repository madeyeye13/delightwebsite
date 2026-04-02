<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'selling_method',
        'unit_label',
        'units_per_order',
        'unit_price',
        'quantity',
        'total_price',
        'weight_kg',
        'is_addon',
    ];

    protected function casts(): array
    {
        return [
            'is_addon' => 'boolean',
            'weight_kg' => 'decimal:3',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Best thumbnail URL for this item in emails.
     * Prefers variant-specific image, falls back to product main image.
     */
    public function getEmailImageUrl(): ?string
    {
        if ($this->relationLoaded('variant') && $this->variant) {
            $url = $this->variant->thumb_image_url;
            if ($url) {
                return $url;
            }
        }

        if ($this->relationLoaded('product') && $this->product) {
            return $this->product->thumb_image_url;
        }

        return null;
    }
}
