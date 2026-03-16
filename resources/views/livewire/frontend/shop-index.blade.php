<div x-data="{ mobileFilters: false }">

    {{-- ── Top bar: search + sort ───────────────────────────────────────────── --}}
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-6 border-b border-neutral-100 dark:border-neutral-800">
        <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">

            {{-- Search --}}
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none" viewBox="0 0 24 24" fill="none">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/>
                    <path d="m16.5 16.5 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <input
                    type="search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search products…"
                    class="w-full pl-9 pr-4 py-2.5 font-sans text-sm bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand"
                />
            </div>

            <div class="flex items-center gap-3">
                {{-- Mobile filter toggle --}}
                <button
                    @click="mobileFilters = !mobileFilters"
                    class="lg:hidden inline-flex items-center gap-2 px-3 py-2.5 border border-neutral-200 dark:border-neutral-700 font-sans text-sm text-neutral-700 dark:text-neutral-300 hover:border-neutral-400 transition-colors"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    Filters
                </button>

                {{-- Sort --}}
                <select
                    wire:model.live="sort"
                    class="font-sans text-sm bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand"
                >
                    <option value="newest">Newest</option>
                    <option value="price_asc">Price: Low → High</option>
                    <option value="price_desc">Price: High → Low</option>
                    <option value="name_asc">Name: A → Z</option>
                </select>

                {{-- Product type filter --}}
                <select
                    wire:model.live="productFilter"
                    class="font-sans text-sm bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand"
                >
                    <option value="">All Products</option>
                    <option value="new_arrival">New Arrivals</option>
                    <option value="featured">Featured</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ── Main content: sidebar + grid ───────────────────────────────────── --}}
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-8">
        <div class="flex gap-8">

            {{-- ═══ SIDEBAR FILTERS ═══ --}}
            <aside
                :class="mobileFilters ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:static inset-0 z-50 lg:z-auto w-72 lg:w-56 xl:w-64 flex-shrink-0
                       bg-white dark:bg-ink lg:bg-transparent
                       overflow-y-auto lg:overflow-visible
                       transition-transform duration-300 lg:transition-none
                       border-r lg:border-r-0 border-neutral-200 dark:border-neutral-800
                       p-6 lg:p-0"
            >
                {{-- Mobile close --}}
                <div class="lg:hidden flex items-center justify-between mb-6">
                    <span class="font-display text-sm font-semibold text-neutral-900 dark:text-white">Filters</span>
                    <button @click="mobileFilters = false" class="w-8 h-8 flex items-center justify-center text-neutral-400 hover:text-neutral-800 dark:hover:text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div class="space-y-7">

                    {{-- ── Categories ─────────────── --}}
                    <div>
                        <h4 class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 mb-3">Category</h4>
                        <div class="space-y-1">
                            @foreach($categories as $category)
                            <label class="flex items-center gap-2.5 cursor-pointer group py-1">
                                <span class="relative flex-shrink-0 w-4 h-4 border border-neutral-300 dark:border-neutral-600 transition-colors
                                             {{ 'group-hover:border-brand' }}"
                                >
                                    <input
                                        type="checkbox"
                                        value="{{ $category->slug }}"
                                        wire:model.live="selectedCategories"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer peer"
                                    />
                                    <span class="absolute inset-0 bg-brand opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3 text-white"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="3"/></svg>
                                    </span>
                                </span>
                                <span class="font-sans text-sm text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ $category->name }}</span>
                            </label>

                            @if($category->children->isNotEmpty())
                            <div class="pl-6 space-y-1">
                                @foreach($category->children as $child)
                                <label class="flex items-center gap-2.5 cursor-pointer group py-0.5">
                                    <span class="relative flex-shrink-0 w-4 h-4 border border-neutral-300 dark:border-neutral-600 transition-colors
                                                 {{ 'group-hover:border-brand' }}"
                                    >
                                        <input
                                            type="checkbox"
                                            value="{{ $child->slug }}"
                                            wire:model.live="selectedCategories"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer peer"
                                        />
                                        <span class="absolute inset-0 bg-brand opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center">
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3 text-white"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="3"/></svg>
                                        </span>
                                    </span>
                                    <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 group-hover:text-neutral-700 dark:group-hover:text-neutral-300 transition-colors">{{ $child->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Collection ─────────────── --}}
                    @if(!empty($availableCollections))
                    <div>
                        <h4 class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 mb-3">Collection</h4>
                        <div class="space-y-1">
                            @foreach($availableCollections as $collection)
                            <label class="flex items-center gap-2.5 cursor-pointer group py-1">
                                <span class="relative flex-shrink-0 w-4 h-4 border border-neutral-300 dark:border-neutral-600 transition-colors
                                             {{ 'group-hover:border-brand' }}"
                                >
                                    <input
                                        type="checkbox"
                                        value="{{ $collection }}"
                                        wire:model.live="selectedCollections"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer peer"
                                    />
                                    <span class="absolute inset-0 bg-brand opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3 text-white"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="3"/></svg>
                                    </span>
                                </span>
                                <span class="font-sans text-sm text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors capitalize">{{ $collection === 'both' ? 'Unisex' : $collection }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- ── Color (by name) ─────────── --}}
                    @if(!empty($availableColors))
                    <div>
                        <h4 class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 mb-3">Color</h4>
                        <div class="space-y-1">
                            @foreach($availableColors as $color)
                            <label class="flex items-center gap-2.5 cursor-pointer group py-1">
                                <span class="relative flex-shrink-0 w-4 h-4 border border-neutral-300 dark:border-neutral-600 transition-colors
                                             {{ 'group-hover:border-brand' }}"
                                >
                                    <input
                                        type="checkbox"
                                        value="{{ $color['name'] }}"
                                        wire:model.live="selectedColors"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer peer"
                                    />
                                    <span class="absolute inset-0 bg-brand opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3 text-white"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="3"/></svg>
                                    </span>
                                </span>
                                <span class="w-3 h-3 rounded-full flex-shrink-0 border border-neutral-200 dark:border-neutral-600" style="background-color: {{ $color['hex'] }}"></span>
                                <span class="font-sans text-sm text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ $color['name'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- ── Price Range ─────────────── --}}
                    <div x-data="{
                        min: {{ $minPrice ?? $priceFloor }},
                        max: {{ $maxPrice ?? $priceCeiling }},
                        floor: {{ $priceFloor }},
                        ceiling: {{ $priceCeiling }},
                        debounce: null,
                        applyMin() {
                            clearTimeout(this.debounce);
                            var self = this;
                            this.debounce = setTimeout(function() {
                                $wire.set('minPrice', self.min > self.floor ? self.min : null);
                            }, 500);
                        },
                        applyMax() {
                            clearTimeout(this.debounce);
                            var self = this;
                            this.debounce = setTimeout(function() {
                                $wire.set('maxPrice', self.max < self.ceiling ? self.max : null);
                            }, 500);
                        }
                    }">
                        <h4 class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 mb-3">Price Range</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 block mb-1">Min</label>
                                <div class="flex items-center gap-2">
                                    <span class="font-sans text-xs text-neutral-400" x-text="$store.currency.symbol"></span>
                                    <input
                                        type="range"
                                        :min="floor"
                                        :max="ceiling"
                                        x-model.number="min"
                                        @input="applyMin()"
                                        class="w-full h-1.5 bg-neutral-200 dark:bg-neutral-700 appearance-none cursor-pointer accent-brand [&::-webkit-slider-thumb]:w-3.5 [&::-webkit-slider-thumb]:h-3.5 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-brand [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-white [&::-webkit-slider-thumb]:shadow"
                                    />
                                    <span class="font-sans text-xs text-neutral-600 dark:text-neutral-400 min-w-[60px] text-right" x-text="$store.currency.format(min)"></span>
                                </div>
                            </div>
                            <div>
                                <label class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 block mb-1">Max</label>
                                <div class="flex items-center gap-2">
                                    <span class="font-sans text-xs text-neutral-400" x-text="$store.currency.symbol"></span>
                                    <input
                                        type="range"
                                        :min="floor"
                                        :max="ceiling"
                                        x-model.number="max"
                                        @input="applyMax()"
                                        class="w-full h-1.5 bg-neutral-200 dark:bg-neutral-700 appearance-none cursor-pointer accent-brand [&::-webkit-slider-thumb]:w-3.5 [&::-webkit-slider-thumb]:h-3.5 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-brand [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-white [&::-webkit-slider-thumb]:shadow"
                                    />
                                    <span class="font-sans text-xs text-neutral-600 dark:text-neutral-400 min-w-[60px] text-right" x-text="$store.currency.format(max)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Clear all ──────────────── --}}
                    @if($hasActiveFilters)
                    <button
                        wire:click="clearFilters"
                        class="w-full py-2 font-sans text-xs font-medium text-neutral-500 dark:text-neutral-400 hover:text-brand dark:hover:text-brand-300 border border-neutral-200 dark:border-neutral-700 hover:border-brand transition-colors"
                    >
                        Clear All Filters
                    </button>
                    @endif

                </div>

                {{-- Mobile: apply button --}}
                <div class="lg:hidden mt-8">
                    <button
                        @click="mobileFilters = false"
                        class="w-full py-3 bg-brand hover:bg-brand-600 text-white font-sans text-sm font-semibold tracking-wide transition-colors"
                    >
                        Apply Filters
                    </button>
                </div>
            </aside>

            {{-- Mobile filter backdrop --}}
            <div
                x-show="mobileFilters"
                x-transition:enter="transition-opacity duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="mobileFilters = false"
                class="fixed inset-0 bg-black/40 z-40 lg:hidden"
                style="display:none"
            ></div>

            {{-- ═══ PRODUCT GRID ═══ --}}
            <div class="flex-1 min-w-0">

                {{-- Loading overlay --}}
                <div wire:loading class="fixed inset-0 z-30 bg-white/40 dark:bg-black/40 flex items-center justify-center pointer-events-none">
                    <svg class="w-8 h-8 text-brand animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </div>

                {{-- Active filters pills --}}
                @if($hasActiveFilters)
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($selectedCategories as $slug)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700 font-sans text-xs text-brand-700 dark:text-brand-300">
                        {{ $slug }}
                        <button wire:click="$set('selectedCategories', {{ json_encode(array_values(array_diff($selectedCategories, [$slug]))) }})" class="hover:text-brand-900 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </span>
                    @endforeach
                    @foreach($selectedColors as $color)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700 font-sans text-xs text-brand-700 dark:text-brand-300">
                        {{ $color }}
                        <button wire:click="$set('selectedColors', {{ json_encode(array_values(array_diff($selectedColors, [$color]))) }})" class="hover:text-brand-900 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </span>
                    @endforeach
                    @foreach($selectedCollections as $col)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700 font-sans text-xs text-brand-700 dark:text-brand-300 capitalize">
                        {{ $col === 'both' ? 'Unisex' : $col }}
                        <button wire:click="$set('selectedCollections', {{ json_encode(array_values(array_diff($selectedCollections, [$col]))) }})" class="hover:text-brand-900 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </span>
                    @endforeach
                    @if($productFilter)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700 font-sans text-xs text-brand-700 dark:text-brand-300">
                        {{ $productFilter === 'new_arrival' ? 'New Arrivals' : 'Featured' }}
                        <button wire:click="$set('productFilter', '')" class="hover:text-brand-900 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </span>
                    @endif
                </div>
                @endif

                {{-- Results count --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="font-sans text-xs text-neutral-400 dark:text-neutral-500">
                        <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $products->total() }}</span> product{{ $products->total() !== 1 ? 's' : '' }}
                    </p>
                </div>

                @if($products->isEmpty())
                <div class="py-24 text-center">
                    <svg viewBox="0 0 64 64" fill="none" class="w-14 h-14 text-neutral-200 dark:text-neutral-700 mx-auto mb-5">
                        <circle cx="26" cy="26" r="18" stroke="currentColor" stroke-width="2"/>
                        <path d="M40 40l16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p class="font-display text-lg text-neutral-400 dark:text-neutral-600 mb-2">No products found</p>
                    <p class="font-sans text-sm text-neutral-400 dark:text-neutral-600 mb-6">Try adjusting your filters or search terms.</p>
                    @if($hasActiveFilters)
                    <button wire:click="clearFilters" class="font-sans text-sm text-brand hover:underline">
                        Clear all filters
                    </button>
                    @endif
                </div>
                @else

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5 md:gap-6">

                    @foreach($products as $product)

                    @php
                        $mainMedia = $product->getFirstMedia('main_image');
                        $image     = $mainMedia
                            ? ($mainMedia->hasGeneratedConversion('medium') ? $mainMedia->getUrl('medium') : $mainMedia->getUrl())
                            : '';
                        $finalPrice = $product->final_price;
                        $oldPrice   = ($product->compare_price && $product->compare_price > $finalPrice) ? $product->compare_price : null;
                        $isNew      = $product->is_new_arrival && (! $product->new_arrival_expiry || $product->new_arrival_expiry->isFuture());
                        $badge      = $oldPrice ? 'Sale' : ($isNew ? 'New' : null);
                        $unit       = $product->unit_label ?: '';

                        $hasVariants = $product->variants->isNotEmpty();
                        $variantData = $product->variants->sortBy('sort_order')->values()->map(function ($v) use ($image) {
                            $vMedia = $v->getFirstMedia('variant_main');
                            $vImage = $vMedia ? ($vMedia->hasGeneratedConversion('medium') ? $vMedia->getUrl('medium') : $vMedia->getUrl()) : '';
                            return [
                                'id'    => $v->id,
                                'color' => $v->name,
                                'hex'   => $v->hex ?? '#ccc',
                                'image' => $vImage ?: $image ?: 'https://placehold.co/300x400/F3F3F3/A3A3A3?text=No+Image',
                            ];
                        })->toArray();
                        $defaultImage = $hasVariants && !empty($variantData[0]['image'])
                            ? $variantData[0]['image']
                            : ($image ?: 'https://placehold.co/300x400/F3F3F3/A3A3A3?text=No+Image');
                    @endphp

                    <article
                        x-data="{ cardVariant: 0, cardImage: '{{ e($defaultImage) }}' }"
                        class="product-item group"
                        wire:key="product-{{ $product->id }}"
                        aria-label="{{ $product->name }}"
                    >
                        {{-- Image --}}
                        <div class="relative overflow-hidden w-full aspect-[3/4] bg-gray-50 dark:bg-white/[0.03]">
                            <img
                                :src="cardImage"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                loading="lazy"
                                width="300"
                                height="400"
                                onerror="this.src='https://placehold.co/300x400/F3F3F3/A3A3A3?text=No+Image'"
                            />

                            @if($badge)
                            <span class="absolute top-3 left-3 font-sans text-[10px] font-semibold tracking-[0.12em] uppercase px-2 py-1
                                         {{ $badge === 'Sale'
                                             ? 'bg-black text-white dark:bg-white dark:text-black'
                                             : 'bg-white text-black dark:bg-black dark:text-white border border-gray-200 dark:border-white/10' }}">
                                {{ $badge }}
                            </span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="pt-3">

                            <h3 class="font-sans text-[13px] font-medium text-gray-800 dark:text-white/80 leading-snug mb-1 truncate">
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-black dark:hover:text-white transition-colors duration-150">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            @if($hasVariants)
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                @foreach($variantData as $vi => $variant)
                                <button
                                    @click="cardVariant = {{ $vi }}; cardImage = '{{ e($variant['image']) }}'"
                                    :class="cardVariant === {{ $vi }} ? 'ring-2 ring-offset-1 ring-brand dark:ring-offset-ink' : 'ring-1 ring-neutral-200 dark:ring-neutral-700'"
                                    class="w-4 h-4 rounded-full transition-all duration-150 focus:outline-none flex-shrink-0"
                                    style="background-color: {{ $variant['hex'] }}"
                                    title="{{ $variant['color'] }}"
                                    aria-label="Select {{ $variant['color'] }}"
                                ></button>
                                @endforeach
                            </div>
                            @endif

                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="font-sans text-[13px] font-semibold text-gray-900 dark:text-white"
                                      x-text="$store.currency ? $store.currency.format({{ $finalPrice }}) : '₦{{ number_format($finalPrice) }}'">
                                </span>
                                @if($oldPrice)
                                <span class="font-sans text-[11px] text-gray-400 dark:text-white/30 line-through"
                                      x-text="$store.currency ? $store.currency.format({{ $oldPrice }}) : '₦{{ number_format($oldPrice) }}'">
                                </span>
                                @endif
                            </div>

                            @if($unit)
                            <p class="font-sans text-[10px] text-gray-400 dark:text-white/30 tracking-wide mb-3">
                                {{ $unit }}
                            </p>
                            @endif

                            {{-- Actions --}}
                            <div class="product-actions flex items-center justify-between">

                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="action-link font-sans text-[11px] font-medium text-gray-700 dark:text-white/60 hover:text-black dark:hover:text-white transition-colors duration-150 pb-0.5">
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 flex-shrink-0">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="1.3"/>
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.3"/>
                                    </svg>
                                    View Product
                                </a>

                                <button
                                    class="action-link font-sans text-[11px] font-medium text-gray-700 dark:text-white/60 hover:text-black dark:hover:text-white transition-colors duration-150 bg-transparent border-none cursor-pointer pb-0.5"
                                    aria-label="Add {{ $product->name }} to cart"
                                    @click="
                                        var variants = @js($variantData);
                                        var sv = variants.length ? { id: variants[cardVariant].id, color: variants[cardVariant].color, hex: variants[cardVariant].hex } : null;
                                        $store.cart.addItem({
                                            product_id:      {{ $product->id }},
                                            slug:            '{{ $product->slug }}',
                                            name:            @js($product->name),
                                            category:        @js($product->category?->name ?? ''),
                                            selling_method:  '{{ $product->sellingMethod?->config_type ?? '' }}',
                                            unit_label:      @js($unit),
                                            units_per_order: {{ (int) $product->units_per_order }},
                                            min_quantity:    {{ (int) $product->min_quantity }},
                                            quantity_step:   {{ (int) $product->quantity_step }},
                                            loom_size:       null,
                                            quantity:        {{ (int) $product->min_quantity }},
                                            unit_price:      {{ $finalPrice }},
                                            selected_variant: sv,
                                            image:           cardImage,
                                            stock_quantity:  {{ (int) $product->effective_stock }},
                                            suggested_add_ons: [],
                                            added_add_ons: []
                                        });
                                        window.dispatchEvent(new CustomEvent('toast:show', { detail: { message: @js($product->name) + ' added to cart', type: 'success' } }));
                                    "
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 flex-shrink-0">
                                        <path d="M5 6.5h16l-2.024 10H7.024L5 6.5Zm0 0L4.364 3H1" stroke="currentColor" stroke-width="1.3"/>
                                        <path d="M7.889 19.71a.65.65 0 1 1 .722 1.08.65.65 0 0 1-.722-1.08ZM16.889 19.71a.65.65 0 1 1 .722 1.08.65.65 0 0 1-.722-1.08Z" stroke="currentColor" stroke-width="1.3"/>
                                    </svg>
                                    Add to Cart
                                </button>

                            </div>

                        </div>
                    </article>

                    @endforeach

                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
                @endif

                @endif

            </div>
        </div>
    </div>
</div>
