<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellingMethod;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Inventory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public string $stockFilter = '';

    public string $methodFilter = '';

    public string $alertFilter = '';

    public string $sortBy = 'name-asc';

    public int $perPage = 20;

    // ── Side panel ──────────────────────────────────────────────────────────
    public bool $showSidePanel = false;

    public ?int $sidePanelVariantId = null;

    // ── Adjust-stock modal ───────────────────────────────────────────────────
    public bool $showAdjustModal = false;

    public ?int $adjustVariantId = null;

    public string $adjustProductName = '';

    public string $adjustVariantName = '';

    public int $adjustCurrentQty = 0;

    public string $adjustType = 'add';

    public string $adjustAmount = '';

    public string $adjustNote = '';

    // ── Threshold modal ──────────────────────────────────────────────────────
    public bool $showThresholdModal = false;

    public ?int $thresholdVariantId = null;

    public string $thresholdProductName = '';

    public string $thresholdValue = '';

    // ── Bulk selection ───────────────────────────────────────────────────────
    /** @var array<int> */
    public array $selectedRows = [];

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStockFilter(): void
    {
        $this->resetPage();
    }

    public function updatedMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAlertFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    // ── Queries ──────────────────────────────────────────────────────────────

    public function getInventoryProperty(): LengthAwarePaginator
    {
        $query = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('selling_methods', 'products.selling_method_id', '=', 'selling_methods.id')
            ->select(
                'product_variants.id',
                'product_variants.product_id',
                'product_variants.name as variant_name',
                'product_variants.stock',
                'product_variants.updated_at',
                'products.name as product_name',
                'products.sku',
                'products.low_stock_threshold',
                'categories.name as category_name',
                'selling_methods.name as method_name',
                'selling_methods.config_type as method_config',
            )
            ->where('products.deleted_at', null);

        // Search
        if ($this->search !== '') {
            $q = '%'.$this->search.'%';
            $query->where(function ($inner) use ($q): void {
                $inner->where('products.name', 'like', $q)
                    ->orWhere('products.sku', 'like', $q)
                    ->orWhere('product_variants.name', 'like', $q);
            });
        }

        // Category filter
        if ($this->categoryFilter !== '') {
            $query->where('products.category_id', $this->categoryFilter);
        }

        // Stock status filter
        if ($this->stockFilter !== '') {
            if ($this->stockFilter === 'out-stock') {
                $query->where('product_variants.stock', 0);
            } elseif ($this->stockFilter === 'low-stock') {
                $query->where('product_variants.stock', '>', 0)
                    ->whereColumn('product_variants.stock', '<=', 'products.low_stock_threshold');
            } elseif ($this->stockFilter === 'in-stock') {
                $query->where(function ($inner): void {
                    $inner->whereColumn('product_variants.stock', '>', 'products.low_stock_threshold')
                        ->orWhere(function ($sub): void {
                            $sub->whereNull('products.low_stock_threshold')
                                ->where('product_variants.stock', '>', 0);
                        });
                });
            }
        }

        // Selling method filter
        if ($this->methodFilter !== '') {
            $query->where('selling_methods.config_type', $this->methodFilter);
        }

        // Alert filter (needs-restock = at/below threshold)
        if ($this->alertFilter === 'needs-restock') {
            $query->whereColumn('product_variants.stock', '<=', 'products.low_stock_threshold');
        } elseif ($this->alertFilter === 'ok') {
            $query->whereColumn('product_variants.stock', '>', 'products.low_stock_threshold');
        }

        // Sorting
        match ($this->sortBy) {
            'qty-asc' => $query->orderBy('product_variants.stock'),
            'qty-desc' => $query->orderByDesc('product_variants.stock'),
            'updated' => $query->orderByDesc('product_variants.updated_at'),
            default => $query->orderBy('products.name')->orderBy('product_variants.name'),
        };

        return $query->paginate($this->perPage);
    }

    /** @return array<int, object> */
    public function getCategoriesProperty(): array
    {
        return Category::orderBy('name')->get()->all();
    }

    /** @return array<int, object> */
    public function getSellingMethodsProperty(): array
    {
        return SellingMethod::active()->get()->all();
    }

    public function getAdjustPreviewQtyProperty(): int
    {
        $amount = max(0, (int) $this->adjustAmount ?: 0);

        return match ($this->adjustType) {
            'add' => $this->adjustCurrentQty + $amount,
            'remove' => max(0, $this->adjustCurrentQty - $amount),
            'set' => $amount,
            default => $this->adjustCurrentQty,
        };
    }

    /** @return array<string, int|float> */
    public function getStatsProperty(): array
    {
        $total = ProductVariant::count();
        $outOfStock = ProductVariant::where('stock', 0)->count();
        $lowStock = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('product_variants.stock', '>', 0)
            ->whereColumn('product_variants.stock', '<=', 'products.low_stock_threshold')
            ->count();
        $inStock = max(0, $total - $outOfStock - $lowStock);
        $estValue = (int) (ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(product_variants.stock * products.price) as val'))
            ->value('val') ?? 0);

        return [
            'total' => $total,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'est_value' => $estValue,
        ];
    }

    /** @return array<string, mixed>|null */
    public function getSidePanelDataProperty(): ?array
    {
        if ($this->sidePanelVariantId === null) {
            return null;
        }

        $variant = ProductVariant::with(['product.category', 'product.sellingMethod', 'product.media'])
            ->find($this->sidePanelVariantId);

        if (! $variant) {
            return null;
        }

        $product = $variant->product;
        $threshold = $product?->low_stock_threshold ?? 0;
        $qty = $variant->stock;

        return [
            'variant_id' => $variant->id,
            'product_id' => $product?->id,
            'product_name' => $product?->name ?? '',
            'variant_name' => $variant->name,
            'sku' => $product?->sku ?? '',
            'category' => $product?->category?->name ?? '—',
            'method' => $product?->sellingMethod?->name ?? '—',
            'qty' => $qty,
            'threshold' => $threshold,
            'status' => $qty === 0 ? 'out' : ($qty <= $threshold ? 'low' : 'in'),            'image_url' => $product?->thumb_image_url,            'last_adjusted' => $variant->updated_at->diffForHumans(),
            'edit_url' => route('admin.products.edit', $product?->id ?? 0),
        ];
    }

    // ── Actions ──────────────────────────────────────────────────────────────

    public function openAdjustModal(int $variantId): void
    {
        $variant = ProductVariant::with('product')->find($variantId);
        if (! $variant) {
            return;
        }

        $this->adjustVariantId = $variantId;
        $this->adjustProductName = $variant->product?->name ?? '';
        $this->adjustVariantName = $variant->name;
        $this->adjustCurrentQty = $variant->stock;
        $this->adjustType = 'add';
        $this->adjustAmount = '';
        $this->adjustNote = '';
        $this->showAdjustModal = true;
    }

    public function confirmAdjust(): void
    {
        $this->validate([
            'adjustAmount' => ['required', 'integer', 'min:0'],
        ]);

        $variant = ProductVariant::findOrFail($this->adjustVariantId);
        $amount = (int) $this->adjustAmount;

        $newQty = match ($this->adjustType) {
            'add' => $variant->stock + $amount,
            'remove' => max(0, $variant->stock - $amount),
            'set' => $amount,
            default => $variant->stock,
        };

        $variant->update(['stock' => $newQty]);

        $this->showAdjustModal = false;
        $this->adjustVariantId = null;
        $this->adjustAmount = '';
        $this->adjustNote = '';

        $this->dispatch('notify', message: 'Stock updated successfully.', type: 'success');
    }

    public function openThresholdModal(int $variantId): void
    {
        $variant = ProductVariant::with('product')->find($variantId);
        if (! $variant) {
            return;
        }

        $this->thresholdVariantId = $variantId;
        $this->thresholdProductName = $variant->product?->name ?? '';
        $this->thresholdValue = (string) ($variant->product?->low_stock_threshold ?? '');
        $this->showThresholdModal = true;
    }

    public function confirmThreshold(): void
    {
        $this->validate([
            'thresholdValue' => ['required', 'integer', 'min:0'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($this->thresholdVariantId);
        $variant->product?->update(['low_stock_threshold' => (int) $this->thresholdValue]);

        $this->showThresholdModal = false;
        $this->thresholdVariantId = null;
        $this->thresholdValue = '';

        $this->dispatch('notify', message: 'Threshold updated successfully.', type: 'success');
    }

    public function openSidePanel(int $variantId): void
    {
        $this->sidePanelVariantId = $variantId;
        $this->showSidePanel = true;
    }

    public function closeSidePanel(): void
    {
        $this->showSidePanel = false;
        $this->sidePanelVariantId = null;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->stockFilter = '';
        $this->methodFilter = '';
        $this->alertFilter = '';
        $this->sortBy = 'name-asc';
        $this->resetPage();
    }

    public function exportInventory(): mixed
    {
        $variants = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('selling_methods', 'products.selling_method_id', '=', 'selling_methods.id')
            ->select(
                'products.name as product_name',
                'products.sku',
                'categories.name as category_name',
                'product_variants.name as variant_name',
                'selling_methods.name as method_name',
                'product_variants.stock',
                'products.low_stock_threshold',
            )
            ->orderBy('products.name')
            ->get();

        return response()->streamDownload(function () use ($variants): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product', 'SKU', 'Category', 'Variant', 'Selling Method', 'Qty', 'Threshold', 'Status']);

            foreach ($variants as $v) {
                $qty = (int) $v->stock;
                $threshold = (int) $v->low_stock_threshold;
                $status = $qty === 0 ? 'Out of Stock' : ($qty <= $threshold ? 'Low Stock' : 'In Stock');

                fputcsv($handle, [
                    $v->product_name,
                    $v->sku,
                    $v->category_name ?? '',
                    $v->variant_name,
                    $v->method_name ?? '',
                    $qty,
                    $threshold,
                    $status,
                ]);
            }

            fclose($handle);
        }, 'inventory-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render(): View
    {
        $inventory = $this->inventory;
        $productIds = $inventory->pluck('product_id')->unique()->filter()->all();
        $productImages = Product::with('media')
            ->whereIn('id', $productIds)
            ->get()
            ->mapWithKeys(fn (Product $p) => [$p->id => $p->thumb_image_url])
            ->all();

        return view('livewire.admin.inventory', [
            'inventory' => $inventory,
            'categories' => $this->categories,
            'sellingMethods' => $this->sellingMethods,
            'stats' => $this->stats,
            'sidePanelData' => $this->sidePanelData,
            'adjustPreviewQty' => $this->adjustPreviewQty,
            'productImages' => $productImages,
        ]);
    }
}
