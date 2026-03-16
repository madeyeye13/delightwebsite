<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellingMethod;
use Illuminate\View\View;
use Livewire\Component;

class ProductIndex extends Component
{
    public function deleteProduct(int $id): void
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('product-deleted', id: $id);
        $this->dispatch('toast', type: 'success', message: 'Product deleted successfully.');
    }

    public function bulkDelete(array $ids): void
    {
        Product::whereIn('id', $ids)->delete();
        $this->dispatch('products-bulk-deleted', ids: $ids);
        $this->dispatch('toast', type: 'success', message: count($ids).' products deleted.');
    }

    public function updateStatus(int $id, string $status): void
    {
        Product::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('toast', type: 'success', message: 'Product status updated.');
    }

    public function toggleFeatured(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['featured' => ! $product->featured]);
        $this->dispatch('toast', type: 'success', message: 'Product updated.');
    }

    public function bulkUpdateStatus(array $ids, string $status): void
    {
        Product::whereIn('id', $ids)->update(['status' => $status]);
        $this->dispatch('toast', type: 'success', message: count($ids).' products updated to '.$status.'.');
    }

    public function bulkUpdateFeatured(array $ids, bool $featured): void
    {
        Product::whereIn('id', $ids)->update(['featured' => $featured]);
        $this->dispatch('toast', type: 'success', message: $featured ? count($ids).' products featured.' : count($ids).' products unfeatured.');
    }

    public function render(): View
    {
        $products = Product::with(['category', 'sellingMethod', 'variants'])
            ->latest()
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?? '',
                'categoryKey' => $p->category?->slug ?? '',
                'categoryLabel' => $p->category?->name ?? '—',
                'sellingMethodKey' => $p->sellingMethod?->slug ?? '',
                'sellingMethodLabel' => $p->sellingMethod?->name ?? '—',
                'unitSummary' => $this->buildUnitSummary($p),
                'price' => $p->price ?? 0,
                'stock' => $p->effective_stock,
                'stockUnit' => $p->stock_unit ?? 'units',
                'status' => $p->status ?? 'draft',
                'featured' => (bool) $p->featured,
                'addOns' => 0,
                'updated' => $p->updated_at->diffForHumans(),
                'mainImageUrl' => $p->main_image_url,
            ])->values();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'drafts' => Product::where('status', 'draft')->count(),
            'low_stock' => Product::where('track_inventory', true)
                ->whereRaw('stock_quantity > 0')
                ->whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 5)')
                ->count(),
            'featured' => Product::where('featured', true)->count(),
        ];

        $categories = Category::active()->get(['id', 'name', 'slug']);
        $sellingMethods = SellingMethod::active()->get(['id', 'name', 'slug']);

        return view('livewire.admin.products.product-index', [
            'productsJson' => $products->toJson(),
            'categoriesJson' => $categories->toJson(),
            'sellingMethodsJson' => $sellingMethods->toJson(),
            'stats' => $stats,
            'categories' => $categories,
            'sellingMethods' => $sellingMethods,
        ]);
    }

    private function buildUnitSummary(Product $product): string
    {
        if (! $product->sellingMethod) {
            return '';
        }

        return match ($product->sellingMethod->config_type) {
            'per_length' => ($product->units_per_order ?? 1).' '.($product->length_unit ?? 'yards').' per unit',
            'per_set' => 'Set of '.($product->units_per_order ?? 1),
            'per_bundle' => ($product->units_per_order ?? 1).'-piece bundle',
            'per_loom' => ($product->loom_size ?? '45 yards').' per loom',
            'per_piece' => '1 '.($product->unit_label ?? 'piece'),
            default => '1 '.($product->unit_label ?? 'unit'),
        };
    }
}
