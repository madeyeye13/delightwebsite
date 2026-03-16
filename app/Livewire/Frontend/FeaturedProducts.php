<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class FeaturedProducts extends Component
{
    public function render(): View
    {
        $products = Product::query()
            ->active()
            ->featured()
            ->with(['category', 'sellingMethod', 'variants', 'media'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(function (Product $product): array {
                $variants = $product->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'color' => $v->name,
                    'hex' => $v->hex ?? '#cccccc',
                    'stock' => $v->stock,
                    'priceAdjustment' => $v->price_adjustment,
                    'images' => array_filter([
                        $v->getFirstMediaUrl('variant_main', 'medium') ?: $v->getFirstMediaUrl('variant_main'),
                    ]),
                ])->values()->toArray();

                $badge = null;
                if ($product->is_new_arrival) {
                    $badge = 'New';
                } elseif ($product->discount_type && $product->discount_value) {
                    $badge = 'Sale';
                }

                // Build unit display string (matches the mock format)
                $unitLabel = $product->unit_label ?: ucfirst(str_replace('per-', '', $product->sellingMethod?->config_type ?? ''));
                $unitDisplay = $product->units_per_order > 1
                    ? 'Multiples of '.$product->units_per_order.' '.$unitLabel
                    : ucfirst($unitLabel);

                return [
                    'id' => $product->id,
                    'image' => $product->thumb_image_url ?? '',
                    'name' => $product->name,
                    'price' => $product->final_price,
                    'old_price' => ($product->compare_price > $product->price) ? $product->compare_price : null,
                    'unit' => $unitDisplay,
                    'badge' => $badge,
                    'slug' => $product->slug,
                    'category' => $product->category?->name ?? '',
                    'description' => $product->description ?? '',
                    'sellingMethod' => $product->sellingMethod?->config_type ?? 'per-piece',
                    'unitsPerOrder' => $product->units_per_order,
                    'lengthUnit' => $product->length_unit ?? '',
                    'loomSize' => $product->loom_size,
                    'setContents' => $product->set_contents ?? [],
                    'bundleYield' => $product->bundle_yield ?? [],
                    'stockQuantity' => $product->effective_stock,
                    'minQuantity' => $product->min_quantity,
                    'quantityStep' => $product->quantity_step,
                    'variants' => $variants,
                ];
            })
            ->toArray();

        return view('livewire.frontend.featured-products', compact('products'));
    }
}
