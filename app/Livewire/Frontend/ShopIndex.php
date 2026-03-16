<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ShopIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    /** @var array<int, string> */
    #[Url(as: 'categories')]
    public array $selectedCategories = [];

    /** @var array<int, string> */
    #[Url(as: 'colors')]
    public array $selectedColors = [];

    /** @var array<int, string> */
    #[Url(as: 'collections')]
    public array $selectedCollections = [];

    #[Url(as: 'sort')]
    public string $sort = 'newest';

    #[Url(as: 'filter')]
    public string $productFilter = '';

    #[Url(as: 'min_price')]
    public ?int $minPrice = null;

    #[Url(as: 'max_price')]
    public ?int $maxPrice = null;

    /** @var Collection<int, Category> */
    public Collection $categories;

    /** @var array<int, array{name: string, hex: string}> */
    public array $availableColors = [];

    /** @var array<int, string> */
    public array $availableCollections = [];

    public int $priceFloor = 0;

    public int $priceCeiling = 0;

    public function mount(): void
    {
        $this->categories = Category::whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $this->availableColors = ProductVariant::query()
            ->select('name', 'hex')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->map(fn ($v) => ['name' => $v->name, 'hex' => $v->hex ?? '#ccc'])
            ->values()
            ->toArray();

        $this->availableCollections = Product::active()
            ->whereNotNull('collection')
            ->where('collection', '!=', '')
            ->distinct()
            ->pluck('collection')
            ->sort()
            ->values()
            ->toArray();

        $range = Product::active()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $this->priceFloor = (int) ($range->min_price ?? 0);
        $this->priceCeiling = (int) ($range->max_price ?? 100000);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategories(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedColors(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCollections(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedProductFilter(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'selectedCategories', 'selectedColors', 'selectedCollections', 'sort', 'productFilter', 'minPrice', 'maxPrice');
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Product::active()
            ->with(['category', 'sellingMethod', 'variants', 'media']);

        // Search
        $query->when($this->search, fn ($q) => $q->where(function ($q) {
            $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('description', 'like', '%'.$this->search.'%')
                ->orWhere('tags', 'like', '%'.$this->search.'%');
        }));

        // Category filter (multi-select)
        $query->when(! empty($this->selectedCategories), function ($q) {
            $slugs = $this->selectedCategories;
            $q->whereHas('category', function ($q) use ($slugs) {
                $q->whereIn('slug', $slugs)
                    ->orWhereHas('parent', fn ($q) => $q->whereIn('slug', $slugs));
            });
        });

        // Color filter (multi-select by variant name)
        $query->when(! empty($this->selectedColors), function ($q) {
            $q->whereHas('variants', fn ($q) => $q->whereIn('name', $this->selectedColors));
        });

        // Collection filter (men, women, both)
        $query->when(! empty($this->selectedCollections), function ($q) {
            $q->where(function ($q) {
                $q->whereIn('collection', $this->selectedCollections);
                if (in_array('men', $this->selectedCollections) || in_array('women', $this->selectedCollections)) {
                    $q->orWhere('collection', 'both');
                }
            });
        });

        // Price range
        if ($this->minPrice !== null && $this->minPrice > 0) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== null && $this->maxPrice > 0) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Product type filter
        $query->when($this->productFilter === 'featured', fn ($q) => $q->where('featured', true));
        $query->when($this->productFilter === 'new_arrival', fn ($q) => $q->where('is_new_arrival', true));

        // Sort
        $query = match ($this->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            default => $query->orderByDesc('created_at'),
        };

        $products = $query->paginate(20);

        $hasActiveFilters = $this->search !== ''
            || ! empty($this->selectedCategories)
            || ! empty($this->selectedColors)
            || ! empty($this->selectedCollections)
            || $this->productFilter !== ''
            || ($this->minPrice !== null && $this->minPrice > 0)
            || ($this->maxPrice !== null && $this->maxPrice > 0);

        return view('livewire.frontend.shop-index', compact('products', 'hasActiveFilters'));
    }
}
