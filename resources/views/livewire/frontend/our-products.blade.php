{{--
    livewire/frontend/our-products.blade.php
    "Our Products" grid section — random non-featured active products from DB.
    Grid: 2 cols mobile / 3 cols tablet / 5 cols desktop.
    Product card is identical to featured-products. Section hidden when empty.
    Quick View dispatches the window-level open-quickview event (handled by the
    modal already included in the featured-products component above it).
--}}

<div>
@if(count($products) > 0)

{{-- Inject products data for Alpine quick-view --}}
<script>
    window.ourProductsData = @json($products);
</script>

<section
    x-data="{
        products: window.ourProductsData,
        openQuickView(index) {
            if (this.products[index]) {
                window.dispatchEvent(new CustomEvent('open-quickview', {
                    detail: this.products[index]
                }));
            }
        }
    }"
    class="py-16 md:py-24 bg-neutral-50 dark:bg-[#0a0c10]"
    aria-labelledby="our-products-heading"
>
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16">

        {{-- ── Section header ─────────────────────────────── --}}
        <div class="mb-10 md:mb-14">
            <p class="font-sans text-[10px] tracking-[0.2em] uppercase text-gray-400 dark:text-white/30 mb-3">
                Explore Our Range
            </p>
            <div class="flex items-end justify-between">
                <h2
                    id="our-products-heading"
                    class="font-display text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white leading-tight tracking-tight"
                >
                    Our Products
                    <span class="block font-sans text-sm font-normal text-gray-400 dark:text-white/40 mt-1 tracking-normal">
                        Fresh Selection
                    </span>
                </h2>
                <a
                    href="{{ route('shop.index') }}"
                    class="hidden md:inline-flex items-center gap-2 font-sans text-xs font-medium tracking-[0.15em] uppercase
                           text-gray-500 dark:text-white/40 hover:text-black dark:hover:text-white
                           transition-colors duration-200 pb-0.5 border-b border-current"
                >
                    View All
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>

        {{-- ── Product grid ────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-4 gap-y-10 md:gap-x-6 md:gap-y-12">

            @foreach($products as $product)

            <article
                x-data="{
                    activeVar: 0,
                    variants: @js($product['variants']),
                    fallbackImg: @js($product['image'] ?: 'https://placehold.co/300x400/F3F3F3/A3A3A3?text=No+Image'),
                    get cardImage() {
                        const v = this.variants[this.activeVar];
                        if (v && v.images && v.images.length && v.images[0]) return v.images[0];
                        return this.fallbackImg;
                    }
                }"
                class="product-item group"
                aria-label="{{ $product['name'] }}"
            >
                {{-- ── Image ───────────────────────────────── --}}
                <div class="relative overflow-hidden w-full aspect-[3/4] bg-gray-50 dark:bg-white/[0.03]">
                    <img
                        :src="cardImage"
                        alt="{{ $product['name'] }}"
                        class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                        loading="lazy"
                        width="300"
                        height="400"
                        onerror="this.src='https://placehold.co/300x400/F3F3F3/A3A3A3?text=No+Image'"
                    />

                    @if($product['badge'])
                    <span class="absolute top-3 left-3 font-sans text-[10px] font-semibold tracking-[0.12em] uppercase
                                 px-2 py-1
                                 {{ $product['badge'] === 'Sale'
                                     ? 'bg-black text-white dark:bg-white dark:text-black'
                                     : 'bg-white text-black dark:bg-black dark:text-white border border-gray-200 dark:border-white/10' }}">
                        {{ $product['badge'] }}
                    </span>
                    @endif
                </div>

                {{-- ── Product info ─────────────────────────── --}}
                <div class="pt-3">

                    <h3 class="font-sans text-[13px] font-medium text-gray-800 dark:text-white/80 leading-snug mb-1 truncate">
                        <a href="{{ route('products.show', $product['slug']) }}" class="hover:text-black dark:hover:text-white transition-colors duration-150">
                            {{ $product['name'] }}
                        </a>
                    </h3>

                    {{-- Color variant swatches --}}
                    <template x-if="variants.length > 0">
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <template x-for="(v, idx) in variants" :key="idx">
                                <button
                                    @click="activeVar = idx"
                                    :class="activeVar === idx ? 'ring-2 ring-offset-1 ring-black dark:ring-white dark:ring-offset-gray-900' : 'ring-1 ring-neutral-200 dark:ring-neutral-700'"
                                    class="w-4 h-4 rounded-full transition-all duration-150 focus:outline-none flex-shrink-0"
                                    :style="'background-color: ' + v.hex"
                                    :title="v.color"
                                    :aria-label="'Select ' + v.color"
                                ></button>
                            </template>
                        </div>
                    </template>

                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="font-sans text-[13px] font-semibold text-gray-900 dark:text-white"
                              x-text="$store.currency ? $store.currency.format({{ (int)$product['price'] }}) : '₦{{ number_format($product['price']) }}'">
                        </span>
                        @if($product['old_price'])
                        <span class="font-sans text-[11px] text-gray-400 dark:text-white/30 line-through"
                              x-text="$store.currency ? $store.currency.format({{ (int)$product['old_price'] }}) : '₦{{ number_format($product['old_price']) }}'">
                        </span>
                        @endif
                    </div>

                    <p class="font-sans text-[10px] text-gray-400 dark:text-white/30 tracking-wide mb-3">
                        {{ $product['unit'] }}
                    </p>

                    {{-- ── Actions ──────────────────────────── --}}
                    <div class="product-actions flex items-center justify-between">

                        <button
                            type="button"
                            @click="openQuickView({{ $loop->index }})"
                            class="action-link font-sans text-[11px] font-medium text-gray-700 dark:text-white/60
                                   hover:text-black dark:hover:text-white transition-colors duration-150
                                   bg-transparent border-none cursor-pointer pb-0.5"
                            aria-label="Quick view {{ $product['name'] }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 flex-shrink-0">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="1.3"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.3"/>
                            </svg>
                            Quick View
                        </button>

                        <button
                            class="action-link font-sans text-[11px] font-medium text-gray-700 dark:text-white/60
                                   hover:text-black dark:hover:text-white transition-colors duration-150
                                   bg-transparent border-none cursor-pointer pb-0.5"
                            aria-label="Add {{ $product['name'] }} to cart"
                            @click="$store.cart.addItem({
                                product_id:      {{ $product['id'] }},
                                slug:            '{{ $product['slug'] }}',
                                name:            @js($product['name']),
                                category:        @js($product['category']),
                                selling_method:  '{{ $product['sellingMethod'] }}',
                                unit_label:      @js($product['unit']),
                                units_per_order: 1,
                                min_quantity:    {{ $product['minQuantity'] }},
                                quantity_step:   {{ $product['quantityStep'] }},
                                loom_size:       null,
                                quantity:        {{ $product['minQuantity'] }},
                                unit_price:      {{ (int) $product['price'] }},
                                selected_variant: null,
                                image:           @js($product['image']),
                                suggested_add_ons: [],
                                added_add_ons: []
                            })"
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

        {{-- ── View all (mobile) ───────────────────────────── --}}
        <div class="mt-12 flex justify-center md:hidden">
            <a
                href="{{ route('shop.index') }}"
                class="action-link font-sans text-xs font-medium tracking-[0.15em] uppercase
                       text-gray-600 dark:text-white/50 hover:text-black dark:hover:text-white
                       transition-colors duration-200 pb-0.5"
            >
                View All Products
            </a>
        </div>

    </div>

</section>

@endif
</div>
