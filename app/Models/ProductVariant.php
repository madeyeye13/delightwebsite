<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariant extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'name',
        'hex',
        'price_adjustment',
        'weight',
        'stock',
        'stock_unit',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'price_adjustment' => 'integer',
            'weight' => 'float',
            'stock' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('variant_main')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('variant_thumbnails')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->optimize()
            ->performOnCollections('variant_main', 'variant_thumbnails');

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(800)
            ->optimize()
            ->performOnCollections('variant_main');
    }

    public function getMainImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('variant_main');
        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('medium') ? $media->getUrl('medium') : $media->getUrl();
    }

    public function getThumbImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('variant_main');
        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl();
    }
}
