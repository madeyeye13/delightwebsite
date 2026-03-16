<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'category_id',
        'selling_method_id',
        'name',
        'slug',
        'sku',
        'collection',
        'description',
        'description_html',
        'tags',
        'unit_label',
        'units_per_order',
        'min_quantity',
        'quantity_step',
        'length_unit',
        'loom_size',
        'set_contents',
        'bundle_yield',
        'included_items',
        'excludes_text',
        'price',
        'compare_price',
        'discount_type',
        'discount_value',
        'cost',
        'track_inventory',
        'stock_quantity',
        'stock_unit',
        'low_stock_threshold',
        'show_add_ons_after_checkout',
        'show_add_ons_in_cart',
        'show_add_ons_on_page',
        'meta_title',
        'meta_description',
        'status',
        'featured',
        'is_new_arrival',
        'new_arrival_expiry',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'set_contents' => 'array',
            'bundle_yield' => 'array',
            'included_items' => 'array',
            'price' => 'integer',
            'compare_price' => 'integer',
            'discount_value' => 'integer',
            'cost' => 'integer',
            'track_inventory' => 'boolean',
            'show_add_ons_after_checkout' => 'boolean',
            'show_add_ons_in_cart' => 'boolean',
            'show_add_ons_on_page' => 'boolean',
            'featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'new_arrival_expiry' => 'date',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sellingMethod(): BelongsTo
    {
        return $this->belongsTo(SellingMethod::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(ProductCoupon::class);
    }

    public function addOns(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_add_ons',
            'product_id',
            'add_on_product_id'
        )->withPivot('sort_order')->orderBy('sort_order');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ─── Media Collections ─────────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('thumbnails')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(400)
            ->optimize()
            ->performOnCollections('main_image', 'thumbnails');

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(800)
            ->optimize()
            ->performOnCollections('main_image');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1600)
            ->optimize()
            ->performOnCollections('main_image');
    }

    // ─── Accessors ─────────────────────────────────────────────────────────

    public function getMainImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('main_image');
        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('medium') ? $media->getUrl('medium') : $media->getUrl();
    }

    public function getThumbImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('main_image');
        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl();
    }

    public function getFinalPriceAttribute(): int
    {
        if ($this->discount_type === 'percent' && $this->discount_value) {
            return (int) round($this->price * (1 - $this->discount_value / 100));
        }

        if ($this->discount_type === 'fixed' && $this->discount_value) {
            return max(0, $this->price - $this->discount_value);
        }

        return $this->price;
    }

    public function getEffectiveStockAttribute(): int
    {
        if ($this->variants->isNotEmpty()) {
            return $this->variants->sum('stock');
        }

        return $this->stock_quantity;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->effective_stock <= $this->low_stock_threshold
            && $this->effective_stock > 0;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->track_inventory && $this->effective_stock <= 0;
    }

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeWithBaseRelations($query)
    {
        return $query->with(['category', 'sellingMethod', 'variants']);
    }

    /**
     * Format this product as the array shape expected by the frontend show.blade.php.
     * Variants include Spatie media image URLs. Add-ons include their own image + basic fields.
     */
    public function toStorefrontArray(): array
    {
        $variants = $this->variants->map(function (ProductVariant $variant): array {
            $variantImages = $variant->getMedia('variant_main')
                ->merge($variant->getMedia('variant_thumbnails'))
                ->map(fn ($m) => $m->hasGeneratedConversion('medium') ? $m->getUrl('medium') : $m->getUrl())
                ->filter(fn ($url) => $url !== '')
                ->values()
                ->toArray();

            return [
                'id' => $variant->id,
                'color' => $variant->name,
                'hex' => $variant->hex ?? '#cccccc',
                'images' => $variantImages,
                'stock' => $variant->stock,
                'priceAdjustment' => $variant->price_adjustment,
            ];
        })->values()->toArray();

        $addOns = $this->addOns->map(function (Product $addOn): array {
            $unitLabel = $addOn->unit_label ?: ucfirst(str_replace('per-', '', $addOn->sellingMethod?->config_type ?? 'piece'));

            return [
                'id' => $addOn->id,
                'slug' => $addOn->slug,
                'name' => $addOn->name,
                'image' => $addOn->thumb_image_url ?? '',
                'price' => $addOn->final_price,
                'category' => $addOn->category?->name ?? '',
                'sellingMethod' => str_replace('_', '-', $addOn->sellingMethod?->config_type ?? 'per-piece'),
                'unitLabel' => $unitLabel,
                'lengthUnit' => $addOn->length_unit,
                'unitsPerOrder' => $addOn->units_per_order,
                'loomSize' => $addOn->loom_size,
                'stockQuantity' => $addOn->effective_stock,
                'minQuantity' => $addOn->min_quantity,
                'quantityStep' => $addOn->quantity_step,
            ];
        })->values()->toArray();

        $images = $this->getMedia('main_image')
            ->merge($this->getMedia('thumbnails'))
            ->map(fn ($m) => $m->hasGeneratedConversion('medium') ? $m->getUrl('medium') : $m->getUrl())
            ->filter(fn ($url) => $url !== '')
            ->values()
            ->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category?->name ?? '',
            'description' => $this->description ?? '',
            'sellingMethod' => str_replace('_', '-', $this->sellingMethod?->config_type ?? 'per-piece'),
            'unitsPerOrder' => $this->units_per_order,
            'unitLabel' => $this->unit_label ?? '',
            'lengthUnit' => $this->length_unit ?? '',
            'minQuantity' => $this->min_quantity,
            'quantityStep' => $this->quantity_step,
            'loomSize' => $this->loom_size,
            'setContents' => $this->set_contents ?? [],
            'bundleYield' => $this->bundle_yield ?? [],
            'includedItems' => $this->included_items ?? [],
            'excludesText' => $this->excludes_text ?? '',
            'price' => $this->price,
            'finalPrice' => $this->final_price,
            'comparePrice' => $this->compare_price ?? 0,
            'discountType' => $this->discount_type,
            'discountValue' => $this->discount_value ?? 0,
            'stockQuantity' => $this->effective_stock,
            'images' => $images,
            'variants' => $variants,
            'addOns' => $addOns,
        ];
    }
}
