<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellingMethod;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductForm extends Component
{
    public ?Product $product = null;

    public function mount(?Product $product = null): void
    {
        $this->product = $product;
    }

    /**
     * Persist a new category from the inline modal and return its real DB id.
     *
     * @param  array<string, mixed>  $data
     */
    public function storeCategory(array $data): array
    {
        $category = Category::create([
            'parent_id' => ! empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? '',
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return [
            'id' => $category->id,
            'parentId' => $category->parent_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'isActive' => $category->is_active,
        ];
    }

    /**
     * Persist a new selling method from the inline modal and return its real DB id.
     *
     * @param  array<string, mixed>  $data
     */
    public function storeSellingMethod(array $data): array
    {
        $method = SellingMethod::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? '',
            'config_type' => $data['config_type'],
            'is_system' => false,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return [
            'id' => $method->id,
            'name' => $method->name,
            'slug' => $method->slug,
            'description' => $method->description,
            'configType' => $method->config_type,
            'isSystem' => false,
            'isActive' => $method->is_active,
        ];
    }

    /**
     * Save or update the product from the Alpine form payload.
     *
     * @param  array<string, mixed>  $payload
     */
    public function saveProduct(array $payload, string $status = 'draft'): array
    {
        try {
            $payload['status'] = $status;

            $productData = $this->extractProductData($payload);

            if ($this->product) {
                $this->product->update($productData);
                $product = $this->product;
            } else {
                $product = Product::create($productData);
                $this->product = $product; // ← keep state for retries
            }

            $this->handleImages($product, $payload);
            $this->handleVariants($product, $payload);
            $this->handleCoupons($product, $payload);
            $this->handleAddOns($product, $payload);

            $this->dispatch('toast', type: 'success', message: $status === 'active' ? 'Product published!' : 'Draft saved.');

            return [
                'success' => true,
                'productId' => $product->id,
                'redirectUrl' => route('admin.products.edit', $product->id),
            ];

        } catch (\Throwable $e) {
            \Log::error('ProductForm::save failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('toast', type: 'error', message: 'Failed to save: '.$e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function render(): View
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $sellingMethods = SellingMethod::active()->get();

        $products = Product::with(['category', 'media'])
            ->when($this->product, fn ($q) => $q->where('id', '!=', $this->product->id))
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'category' => $p->category?->name ?? '',
                'image' => $p->thumb_image_url,
            ]);

        return view('livewire.admin.products.product-form', [
            'categoriesJson' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'parentId' => $c->parent_id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'isActive' => (bool) $c->is_active,
            ])->toJson(),
            'sellingMethodsJson' => $sellingMethods->map(fn ($m) => [
                'id' => (string) $m->id,
                'name' => $m->name,
                'slug' => $m->slug,
                'description' => $m->description,
                'configType' => $m->config_type,
                'isSystem' => (bool) $m->is_system,
                'isActive' => (bool) $m->is_active,
            ])->toJson(),
            'productsJson' => $products->toJson(),
            'productJson' => $this->product ? $this->buildEditPayload($this->product) : 'null',
        ]);
    }

    /** @param  array<string, mixed>  $payload */
    private function extractProductData(array $payload): array
    {
        $categoryId = $payload['category_id'] ?? null;
        if ($categoryId !== null && $categoryId !== '' && is_numeric($categoryId)) {
            $categoryId = (int) $categoryId;
        } else {
            $categoryId = null;
        }

        $sellingMethodId = $payload['selling_method_id'] ?? null;
        if ($sellingMethodId !== null && $sellingMethodId !== '' && is_numeric($sellingMethodId)) {
            $sellingMethodId = (int) $sellingMethodId;
        } else {
            $sellingMethodId = null;
        }

        return [
            'category_id' => $categoryId,
            'selling_method_id' => $sellingMethodId,
            'name' => $payload['name'] ?? '',
            'slug' => $payload['slug'] ?? '',
            'sku' => $payload['sku'] ?? null,
            'collection' => $payload['collection'] ?? 'both',
            'description' => $payload['description'] ?? '',
            'description_html' => $payload['description_html'] ?? '',
            'tags' => $payload['tags'] ?? [],
            'unit_label' => $payload['unit_label'] ?? '',
            'units_per_order' => $payload['units_per_order'] ?? 1,
            'min_quantity' => $payload['min_quantity'] ?? 1,
            'quantity_step' => $payload['quantity_step'] ?? 1,
            'length_unit' => $payload['length_unit'] ?? 'yards',
            'loom_size' => $payload['loom_size'] ?? null,
            'set_contents' => $payload['set_contents'] ?? [],
            'bundle_yield' => $payload['bundle_yield'] ?? [],
            'included_items' => $payload['included_items'] ?? [],
            'excludes_text' => $payload['excludes_text'] ?? '',
            'price' => $payload['price'] ?? 0,
            'compare_price' => $payload['compare_price'] ?? 0,
            'discount_type' => $payload['discount_type'] ?: null,
            'discount_value' => $payload['discount_value'] ?? 0,
            'cost' => $payload['cost'] ?? 0,
            'weight' => isset($payload['weight']) && $payload['weight'] !== '' ? (float) $payload['weight'] : null,
            'weight_unit' => $payload['weight_unit'] ?? 'kg',
            'track_inventory' => (bool) ($payload['track_inventory'] ?? false),
            'stock_quantity' => $payload['stock_quantity'] ?? 0,
            'stock_unit' => $payload['stock_unit'] ?? '',
            'low_stock_threshold' => $payload['low_stock_threshold'] ?? 5,
            'show_add_ons_after_checkout' => (bool) ($payload['show_add_ons_after_checkout'] ?? false),
            'show_add_ons_in_cart' => (bool) ($payload['show_add_ons_in_cart'] ?? false),
            'show_add_ons_on_page' => (bool) ($payload['show_add_ons_on_page'] ?? false),
            'meta_title' => $payload['meta_title'] ?? '',
            'meta_description' => $payload['meta_description'] ?? '',
            'status' => $payload['status'] ?? 'draft',
            'featured' => (bool) ($payload['featured'] ?? false),
            'is_new_arrival' => (bool) ($payload['is_new_arrival'] ?? false),
            'new_arrival_expiry' => $payload['new_arrival_expiry'] ?: null,
        ];
    }

    /** @param  array<string, mixed>  $payload */
    private function handleAddOns(Product $product, array $payload): void
    {
        $ids = collect($payload['add_on_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->toArray();

        $product->addOns()->sync($ids);
    }

    /** @param  array<string, mixed>  $payload */
    private function handleImages(Product $product, array $payload): void
    {
        // Main image — media library pick takes priority over base64 drag-drop
        if (! empty($payload['main_image_media_id'])) {
            $product->clearMediaCollection('main_image');
            Media::findOrFail((int) $payload['main_image_media_id'])
                ->copy($product, 'main_image');
        } elseif (! empty($payload['main_image'])) {
            $imageData = $payload['main_image'];

            if (str_starts_with($imageData, 'data:image')) {
                // Base64 encoded new upload (drag-drop)
                $product->clearMediaCollection('main_image');
                $decoded = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData));
                $tmpPath = sys_get_temp_dir().'/'.uniqid('product_main_', true).'.jpg';
                file_put_contents($tmpPath, $decoded);
                $product->addMedia($tmpPath)->toMediaCollection('main_image');
                @unlink($tmpPath);
            }
        }

        // Thumbnail images
        if (! empty($payload['thumbnails']) && is_array($payload['thumbnails'])) {
            foreach ($payload['thumbnails'] as $thumb) {
                $preview = $thumb['preview'] ?? null;
                $isMedia = $thumb['is_media'] ?? false;

                if ($preview && str_starts_with($preview, 'data:image') && ! $isMedia) {
                    $decoded = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $preview));
                    $tmpPath = sys_get_temp_dir().'/'.uniqid('product_thumb_', true).'.jpg';
                    file_put_contents($tmpPath, $decoded);
                    $product->addMedia($tmpPath)->toMediaCollection('thumbnails');
                    @unlink($tmpPath);
                }
            }
        }
    }

    /** @param  array<string, mixed>  $payload */
    private function handleVariants(Product $product, array $payload): void
    {
        if (empty($payload['color_variants']) || ! is_array($payload['color_variants'])) {
            $product->variants()->delete();

            return;
        }

        $existingIds = $product->variants()->pluck('id')->toArray();
        $savedIds = [];

        foreach ($payload['color_variants'] as $index => $variantData) {
            $variant = $product->variants()->updateOrCreate(
                ['name' => $variantData['name']],   // ← was color_name
                [
                    'name' => $variantData['name'],   // ← was color_name
                    'hex' => $variantData['hex'] ?? '#000000',
                    'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                    'weight' => isset($variantData['weight']) && $variantData['weight'] !== '' ? (float) $variantData['weight'] : null,
                    'stock' => $variantData['stock'] ?? 0,   // ← was stock_quantity
                    'stock_unit' => $variantData['stock_unit'] ?? 'units',
                    'is_default' => $index === (int) ($payload['default_color_variant_idx'] ?? 0),
                    'sort_order' => $index,
                ]
            );

            if (! empty($variantData['main_image_media_id'])) {
                // Media library pick — copy the existing media record to the variant
                $variant->clearMediaCollection('variant_main');
                Media::findOrFail((int) $variantData['main_image_media_id'])
                    ->copy($variant, 'variant_main');
            } elseif (! empty($variantData['main_image'])) {
                $imageData = $variantData['main_image'];

                // Skip if the image hasn't changed (same URL as current media)
                $currentUrl = $variant->getFirstMediaUrl('variant_main');
                if ($currentUrl && $currentUrl === $imageData) {
                    // Image unchanged — do nothing
                } elseif (str_starts_with($imageData, 'data:image')) {
                    $variant->clearMediaCollection('variant_main');
                    $decoded = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData));
                    $tmpPath = sys_get_temp_dir().'/'.uniqid('variant_main_', true).'.jpg';
                    file_put_contents($tmpPath, $decoded);
                    $variant->addMedia($tmpPath)->toMediaCollection('variant_main');
                    @unlink($tmpPath);
                } elseif (str_starts_with($imageData, '/storage/')) {
                    $variant->clearMediaCollection('variant_main');
                    $fullPath = public_path($imageData);
                    if (file_exists($fullPath)) {
                        $variant->addMedia($fullPath)->preservingOriginal()->toMediaCollection('variant_main');
                    }
                }
            }

            $savedIds[] = $variant->id;
        }

        $toDelete = array_diff($existingIds, $savedIds);
        if ($toDelete) {
            $product->variants()->whereIn('id', $toDelete)->delete();
        }
    }

    /** @param  array<string, mixed>  $payload */
    private function handleCoupons(Product $product, array $payload): void
    {
        if (empty($payload['coupons']) || ! is_array($payload['coupons'])) {
            return;
        }

        foreach ($payload['coupons'] as $couponData) {
            if (empty($couponData['code'])) {
                continue;
            }

            $product->coupons()->updateOrCreate(
                ['code' => strtoupper($couponData['code'])],
                [
                    'code' => strtoupper($couponData['code']),
                    'discount_percent' => $couponData['discount_percent'] ?? 0,
                    'expiry_date' => $couponData['expiry_date'] ?: null,
                    'max_uses' => $couponData['max_uses'] ?: null,
                    'min_order_amount' => $couponData['min_order_amount'] ?? 0,
                    'new_users_only' => (bool) ($couponData['new_users_only'] ?? false),
                    'is_active' => (bool) ($couponData['is_active'] ?? true),
                ]
            );
        }
    }

    /** @return string JSON string of product data for Alpine form hydration */
    private function buildEditPayload(Product $product): string
    {
        $product->loadMissing('addOns.category', 'addOns.media');
        $method = $product->sellingMethod;

        $variants = $product->variants()->with('media')->get()->map(fn ($v) => [
            'name' => $v->name,        // ← was color_name
            'hex' => $v->hex,
            'priceAdjustment' => $v->price_adjustment,
            'weight' => $v->weight,
            'stock' => $v->stock,       // ← was stock_quantity
            'stockUnit' => $v->stock_unit,
            'mainImagePreview' => $v->getFirstMediaUrl('variant_main') ?: null,
            'mainImageFile' => null,
            'thumbnails' => $v->getMedia('variant_thumbnails')
                ->map(fn ($m) => ['preview' => $m->getUrl(), 'file' => null])
                ->toArray(),
            '_expanded' => false,
        ])->toArray();

        $defaultIdx = 0;
        foreach ($product->variants as $i => $v) {
            if ($v->is_default) {
                $defaultIdx = $i;
                break;
            }
        }

        $coupons = $product->coupons->map(fn ($c) => [
            'code' => $c->code,
            'discountPercent' => $c->discount_percent,
            'expiryDate' => $c->expiry_date?->format('Y-m-d'),
            'maxUses' => $c->max_uses,
            'minOrderAmount' => $c->min_order_amount,
            'newUsersOnly' => $c->new_users_only,
            'isActive' => $c->is_active,
            '_expanded' => false,
        ])->toArray();

        $data = [
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'categoryId' => $product->category_id,
            'collection' => $product->collection ?? 'both',
            'description' => $product->description,
            'descriptionHtml' => $product->description_html,
            'tags' => $product->tags ?? [],
            'mainImagePreview' => $product->getFirstMediaUrl('main_image') ?: null,
            'mainImageFile' => null,
            'thumbnails' => $product->getMedia('thumbnails')->map(fn ($m) => ['preview' => $m->getUrl(), 'file' => null])->toArray(),
            'sellingMethodId' => $product->selling_method_id,
            'unitLabel' => $product->unit_label,
            'unitsPerOrder' => $product->units_per_order,
            'minQuantity' => $product->min_quantity,
            'quantityStep' => $product->quantity_step,
            'lengthUnit' => $product->length_unit ?? 'yards',
            'loomSize' => $product->loom_size,
            'setContents' => $product->set_contents ?? [],
            'bundleYield' => $product->bundle_yield ?? [],
            'includedItems' => $product->included_items ?? [],
            'excludesText' => $product->excludes_text,
            'colorVariants' => $variants,
            'defaultColorVariantIdx' => $defaultIdx,
            'price' => $product->price,
            'comparePrice' => $product->compare_price,
            'discountType' => $product->discount_type ?? '',
            'discountValue' => $product->discount_value,
            'cost' => $product->cost,
            'weight' => $product->weight,
            'weightUnit' => $product->weight_unit ?? 'kg',
            'trackInventory' => $product->track_inventory,
            'stockQuantity' => $product->stock_quantity,
            'stockUnit' => $product->stock_unit,
            'lowStockThreshold' => $product->low_stock_threshold,
            'showAddOnsAfterCheckout' => $product->show_add_ons_after_checkout,
            'showAddOnsInCart' => $product->show_add_ons_in_cart,
            'showAddOnsOnPage' => $product->show_add_ons_on_page,
            'addOns' => $product->addOns->map(fn (Product $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'price' => $a->price,
                'category' => $a->category?->name ?? '',
                'image' => $a->thumb_image_url,
            ])->toArray(),
            'coupons' => $coupons,
            'metaTitle' => $product->meta_title,
            'metaDescription' => $product->meta_description,
            'status' => $product->status,
            'featured' => $product->featured,
            'isNewArrival' => $product->is_new_arrival,
            'newArrivalExpiry' => $product->new_arrival_expiry?->format('Y-m-d'),
        ];

        return json_encode($data);
    }
}
