{{--
    livewire/frontend/featured-products.blade.php
    Featured products section — data from DB via FeaturedProducts Livewire component.
    HTML structure identical to the original partial; only the @php mock block is replaced.
--}}

<style>
    /* ── Hide scrollbar but keep scroll behaviour ─────────── */
    .products-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
    }
    .products-scroll::-webkit-scrollbar { display: none; }

    /* ── Each product snaps into place ───────────────────── */
    .product-item { scroll-snap-align: start; }

    /* ── Action row: ALWAYS VISIBLE on mobile, hidden on desktop until hover ── */
    .product-actions {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.25s ease, transform 0.25s ease;
        pointer-events: auto;
    }

    /* Desktop only: hide actions by default, show on hover */
    @media (min-width: 768px) {
        .product-actions {
            opacity: 0;
            transform: translateY(6px);
            pointer-events: none;
        }
        .product-item:hover .product-actions {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
    }

    /* ── Action link underline shimmer ───────────────────── */
    .action-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .action-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 1px;
        background: currentColor;
        opacity: 0.35;
    }
    .action-link::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: -20%;
        width: 28%;
        height: 1px;
        background: currentColor;
        transform: skewX(-18deg);
        transition: left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
    }
    .action-link:hover::after  { opacity: 1; }
    .action-link:hover::before { left: 110%; opacity: 0.6; }

    /* ── Arrow button ─────────────────────────────────────── */
    .scroll-arrow {
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .scroll-arrow:hover { transform: scale(1.08); }
    .scroll-arrow:disabled { opacity: 0.2; cursor: default; }
    .scroll-arrow:disabled:hover { transform: none; }
</style>

{{-- Inject real product data for Alpine quick-view --}}
<script>
    window.productsData = @json($products);
</script>

<section
    x-data="{
        products: window.productsData,
        openQuickView(index) {
            if (this.products[index]) {
                window.dispatchEvent(new CustomEvent('open-quickview', {
                    detail: this.products[index]
                }));
            }
        }
    }"
    class="py-16 md:py-24 bg-white dark:bg-[#0a0c10]"
    aria-labelledby="featured-heading"
>
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16">

        {{-- ── Section header ─────────────────────────────── --}}
        <div class="flex items-end justify-between mb-10 md:mb-14">

            <div>
                <p class="font-sans text-[10px] tracking-[0.2em] uppercase text-gray-400 dark:text-white/30 mb-3">
                    Curated Selection
                </p>
                <h2
                    id="featured-heading"
                    class="font-display text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white leading-tight tracking-tight"
                >
                    Featured Products
                    <span class="block font-sans text-sm font-normal text-gray-400 dark:text-white/40 mt-1 tracking-normal">
                        Quality Materials
                    </span>
                </h2>
            </div>

            {{-- Arrows — desktop --}}
            <div class="hidden md:flex items-center gap-3" aria-label="Scroll products">
                <button
                    id="prod-prev"
                    onclick="scrollProducts(-1)"
                    class="scroll-arrow w-10 h-10 rounded-full border border-gray-200 dark:border-white/10
                           flex items-center justify-center text-gray-700 dark:text-white/70
                           hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white
                           transition-colors duration-200 bg-transparent cursor-pointer"
                    aria-label="Previous products"
                    disabled
                >
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
                <button
                    id="prod-next"
                    onclick="scrollProducts(1)"
                    class="scroll-arrow w-10 h-10 rounded-full border border-gray-200 dark:border-white/10
                           flex items-center justify-center text-gray-700 dark:text-white/70
                           hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white
                           transition-colors duration-200 bg-transparent cursor-pointer"
                    aria-label="Next products"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

        </div>

        {{-- ── Product scroll track ────────────────────────── --}}
        <div
            id="products-track"
            class="products-scroll flex gap-6 md:gap-8 overflow-x-auto pb-2"
        >

            @forelse($products as $product)

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
                class="product-item flex-shrink-0 w-[260px] sm:w-[280px] md:w-[300px] group"
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

                    {{-- Color variant swatches (Alpine-rendered to avoid Blade compile issues) --}}
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

                        {{-- Quick View — calls section method with product index --}}
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

                        {{-- Add to cart --}}
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
                                selected_variant: {{ count($product['variants']) ? 'null' : 'null' }},
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

            @empty

            {{-- Empty state: no featured products in DB yet --}}
            <p class="font-sans text-sm text-gray-400 dark:text-white/30 py-10">
                No featured products yet. Add some from the admin panel.
            </p>

            @endforelse

        </div>

        {{-- ── Mobile arrows ───────────────────────────────── --}}
        <div class="flex md:hidden items-center justify-center gap-4 mt-8">
            <button
                onclick="scrollProducts(-1)"
                class="scroll-arrow w-10 h-10 rounded-full border border-gray-200 dark:border-white/10
                       flex items-center justify-center text-gray-700 dark:text-white/60
                       bg-transparent cursor-pointer"
                aria-label="Previous products"
            >
                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>

            <div id="scroll-dots" class="flex gap-1.5">
                @foreach($products as $i => $product)
                <span class="scroll-dot w-1.5 h-1.5 rounded-full bg-gray-200 dark:bg-white/10 transition-colors duration-200 {{ $i === 0 ? 'bg-gray-900 dark:bg-white' : '' }}"></span>
                @endforeach
            </div>

            <button
                onclick="scrollProducts(1)"
                class="scroll-arrow w-10 h-10 rounded-full border border-gray-200 dark:border-white/10
                       flex items-center justify-center text-gray-700 dark:text-white/60
                       bg-transparent cursor-pointer"
                aria-label="Next products"
            >
                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        {{-- ── View all ─────────────────────────────────────── --}}
        <div class="mt-12 md:mt-16 flex justify-center">
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

    {{-- Quick view modal — self-contained, listens for open-quickview event --}}
    @include('components.product-quickview')

</section>

<script>
(function () {
    const track   = document.getElementById('products-track');
    const btnPrev = document.getElementById('prod-prev');
    const btnNext = document.getElementById('prod-next');
    const dots    = document.querySelectorAll('.scroll-dot');

    function cardWidth() {
        const first = track.querySelector('.product-item');
        if (!first) return 320;
        const gap = parseFloat(getComputedStyle(track).columnGap) || 32;
        return first.offsetWidth + gap;
    }

    window.scrollProducts = function (dir) {
        track.scrollBy({ left: dir * cardWidth(), behavior: 'smooth' });
    };

    function onScroll() {
        const atStart = track.scrollLeft <= 4;
        const atEnd   = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        if (btnPrev) btnPrev.disabled = atStart;
        if (btnNext) btnNext.disabled = atEnd;
        if (dots.length) {
            const idx = Math.round(track.scrollLeft / cardWidth());
            dots.forEach((d, i) => {
                d.classList.toggle('bg-gray-900', i === idx);
                d.classList.toggle('dark:bg-white', i === idx);
                d.classList.toggle('bg-gray-200', i !== idx);
                d.classList.toggle('dark:bg-white/10', i !== idx);
            });
        }
    }

    track.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>
