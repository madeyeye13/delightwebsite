{{--
    product-quickview.blade.php
    Self-contained quickview modal — design system aligned with show.blade.php.
    Sharp corners, same badges, same swatches, same trust strip, same CTAs.

    To open from ANY element:
    @click="$dispatch('open-quickview', {
        name, slug, category, description,
        image, images:[],
        price, old_price,
        unit, sellingMethod,
        stockQuantity, minQuantity, quantityStep,
        variants: [{color, hex, images:[]}]
    })"
--}}

<style>
    [x-cloak] { display: none !important; }
    @keyframes qvUp {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .qv-panel { animation: qvUp 0.26s cubic-bezier(0.16,1,0.3,1) both; }
</style>

<div
    x-data="{
        isOpen:        false,
        product:       {},
        activeVariant: 0,
        activeImage:   null,
        qty:           1,

        open(p) {
            this.product       = p;
            this.activeVariant = 0;
            this.qty           = p.minQuantity || 1;
            this.activeImage   = p.image || null;
            this.isOpen        = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        get currentVariant() {
            return (this.product.variants && this.product.variants[this.activeVariant]) || null;
        },
        get allImages() {
            var imgs = [];
            if (this.product.image) imgs.push(this.product.image);
            if (Array.isArray(this.product.images)) {
                this.product.images.forEach(function(i){ if(imgs.indexOf(i)===-1) imgs.push(i); });
            }
            if (this.currentVariant && Array.isArray(this.currentVariant.images)) {
                var self = imgs;
                this.currentVariant.images.forEach(function(i){ if(self.indexOf(i)===-1) self.push(i); });
            }
            return imgs;
        },
        switchVariant(idx) {
            this.activeVariant = idx;
            var v = this.product.variants && this.product.variants[idx];
            if (v && Array.isArray(v.images) && v.images.length) {
                this.activeImage = v.images[0];
            }
        },
        addToCart() {
            console.log('Quick add:', {
                product: this.product,
                variant: this.currentVariant,
                qty:     this.qty
            });
        }
    }"
    @open-quickview.window="open($event.detail)"
    x-cloak
    x-show="isOpen"
    @click.self="close()"
    @keydown.escape.window="close()"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 dark:bg-black/70 z-[9999] flex items-end sm:items-center justify-center sm:p-6"
    role="dialog"
    aria-modal="true"
    :aria-labelledby="'qv-title'"
>

    {{-- ── MODAL PANEL ──────────────────────────────────────────── --}}
    <div
        @click.stop
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-280"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-[0.97]"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-180"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-[0.97]"
        class="qv-panel bg-white dark:bg-ink
               w-full sm:max-w-[900px]
               max-h-[92vh] sm:max-h-[88vh]
               overflow-hidden
               shadow-2xl
               flex flex-col"
    >

        {{-- ── TOP BAR ──────────────────────────────────────────── --}}
        <div class="flex items-center justify-between px-5 sm:px-8 py-4 border-b border-neutral-100 dark:border-neutral-800 flex-shrink-0">
            <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand dark:text-brand-300"
                  x-text="product.category || 'Product'"></span>
            <button
                @click="close()"
                class="w-8 h-8 flex items-center justify-center border border-neutral-200 dark:border-neutral-700
                       text-neutral-500 dark:text-neutral-400
                       hover:border-neutral-900 dark:hover:border-white
                       hover:text-neutral-900 dark:hover:text-white
                       transition-colors duration-200 flex-shrink-0"
                aria-label="Close"
            >
                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        {{-- ── BODY — scrolls as one unit on mobile, split on desktop ── --}}
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 sm:divide-x divide-neutral-100 dark:divide-neutral-800">

                {{-- ════════════════
                     LEFT — GALLERY
                ════════════════ --}}
                <div class="flex flex-col gap-3 p-5 sm:p-8">

                    {{-- Main image --}}
                    <div class="relative overflow-hidden bg-neutral-50 dark:bg-neutral-900 aspect-[4/5] w-full">
                        <img
                            :src="activeImage || product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover object-center"
                            onerror="this.src='https://placehold.co/600x750/F3F3F3/A3A3A3?text=No+Image'"
                        />
                        <template x-if="product.old_price && product.old_price > product.price">
                            <span class="absolute top-4 left-4 z-10 font-sans text-2xs font-semibold tracking-wider uppercase px-2.5 py-1 bg-brand text-white pointer-events-none"
                                  x-text="`-${Math.round(((product.old_price - product.price) / product.old_price) * 100)}%`">
                            </span>
                        </template>
                    </div>

                    {{-- Thumbnails row — always render, show when >1 image --}}
                    <div x-show="allImages.length > 1" class="grid grid-cols-4 gap-2">
                        <template x-for="(img, idx) in allImages.slice(0, 4)" :key="'qvth-' + idx">
                            <button
                                @click="activeImage = img"
                                :class="activeImage === img
                                    ? 'border-brand dark:border-brand-400 opacity-100'
                                    : 'border-neutral-200 dark:border-neutral-700 opacity-50 hover:opacity-100'"
                                class="aspect-square border-2 overflow-hidden transition-all duration-200 focus:outline-none"
                            >
                                <img :src="img" :alt="'Image ' + (idx + 1)"
                                     class="w-full h-full object-cover object-center"
                                     onerror="this.src='https://placehold.co/120x120/F3F3F3/A3A3A3?text=IMG'" />
                            </button>
                        </template>
                    </div>

                </div>

                {{-- ════════════════
                     RIGHT — DETAILS
                ════════════════ --}}
                <div class="flex flex-col gap-5 p-5 sm:p-8 pb-10 sm:pb-10">

                    {{-- Stock badge --}}
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <template x-if="product.stockQuantity > 10">
                            <span class="inline-flex items-center gap-1.5 font-sans text-2xs font-semibold tracking-wide uppercase px-2.5 py-1 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-300 border border-brand-200 dark:border-brand-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>In Stock
                            </span>
                        </template>
                        <template x-if="product.stockQuantity > 0 && product.stockQuantity <= 10">
                            <span class="inline-flex items-center gap-1.5 font-sans text-2xs font-semibold tracking-wide uppercase px-2.5 py-1 bg-accent-50 dark:bg-accent-500/10 text-accent-600 dark:text-accent-300 border border-accent-200 dark:border-accent-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-400 animate-pulse flex-shrink-0"></span>
                                Only <span x-text="product.stockQuantity" class="mx-0.5"></span> left
                            </span>
                        </template>
                        <template x-if="product.stockQuantity === 0">
                            <span class="inline-flex items-center gap-1.5 font-sans text-2xs font-semibold tracking-wide uppercase px-2.5 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>Out of Stock
                            </span>
                        </template>
                    </div>

                    {{-- Name --}}
                    <h2 id="qv-title"
                        x-text="product.name"
                        class="font-display text-xl sm:text-2xl font-bold text-neutral-900 dark:text-white leading-tight tracking-tight">
                    </h2>

                    {{-- Price --}}
                    <div class="flex items-end gap-3 flex-wrap">
                        <span class="font-display text-2xl font-extrabold text-neutral-900 dark:text-white leading-none tracking-tighter">
                            &#8358;<span x-text="(product.price || 0).toLocaleString()"></span>
                        </span>
                        <template x-if="product.old_price && product.old_price > product.price">
                            <span class="font-sans text-base text-neutral-400 dark:text-neutral-500 line-through leading-none">
                                &#8358;<span x-text="(product.old_price || 0).toLocaleString()"></span>
                            </span>
                        </template>
                        <template x-if="product.old_price && product.old_price > product.price">
                            <span class="font-sans text-xs font-semibold text-brand dark:text-brand-300 bg-brand-50 dark:bg-brand-900/30 px-2 py-0.5"
                                  x-text="`-${Math.round(((product.old_price - product.price) / product.old_price) * 100)}%`">
                            </span>
                        </template>
                    </div>

                    {{-- Description --}}
                    <template x-if="product.description">
                        <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed"
                           x-text="product.description.length > 160 ? product.description.substring(0,160) + '...' : product.description">
                        </p>
                    </template>

                    {{-- Trust / meta strip --}}
                    <div class="grid grid-cols-2 gap-x-4 gap-y-3 border-y border-neutral-100 dark:border-neutral-800 py-3.5">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0"><path d="M4 6h16M4 12h16M4 18h7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <div>
                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Category</p>
                                <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="product.category || '—'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0"><rect x="2" y="7" width="20" height="14" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="1.5"/></svg>
                            <div>
                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Sold As</p>
                                <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300 capitalize"
                                   x-text="(product.sellingMethod || 'unit').replace(/-/g,' ')"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0"><path d="M21 10H3M16 2v4M8 2v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/></svg>
                            <div>
                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Unit</p>
                                <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="product.unit || '—'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-brand flex-shrink-0"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <div>
                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Availability</p>
                                <p class="font-sans text-xs font-medium"
                                   :class="product.stockQuantity === 0 ? 'text-red-500' : product.stockQuantity <= 10 ? 'text-accent-600 dark:text-accent-400' : 'text-brand'"
                                   x-text="product.stockQuantity === 0 ? 'Unavailable' : product.stockQuantity <= 10 ? product.stockQuantity + ' remaining' : 'Available'">
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ── COLOUR SWATCHES ───────────────────────────────── --}}
                    {{-- activeVariant lives on x-data (not on product obj) so reactivity works --}}
                    <div x-show="product.variants && product.variants.length > 0">
                        <p class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 mb-2.5">
                            Colour:&nbsp;<span
                                class="text-neutral-900 dark:text-white font-semibold normal-case tracking-normal"
                                x-text="product.variants && product.variants[activeVariant] ? product.variants[activeVariant].color : ''">
                            </span>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(variant, idx) in (product.variants || [])" :key="'qvsw-' + idx">
                                <button
                                    @click="switchVariant(idx)"
                                    :class="activeVariant === idx
                                        ? 'ring-2 ring-offset-2 ring-brand dark:ring-offset-ink'
                                        : 'ring-1 ring-neutral-200 dark:ring-neutral-700 hover:ring-neutral-400'"
                                    class="relative w-6 h-6 rounded-full transition-all duration-200 focus:outline-none flex-shrink-0"
                                    :style="'background-color:' + (variant.hex || '#ccc')"
                                    :title="variant.color"
                                    :aria-label="'Select ' + variant.color"
                                >
                                    <template x-if="activeVariant === idx">
                                        <svg class="w-2.5 h-2.5 absolute inset-0 m-auto"
                                             :class="['#F5F0E8','#FFFFFF','#F0F0F0','#FAF9F6'].includes((variant.hex||'').toUpperCase()) ? 'text-neutral-700' : 'text-white'"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </template>
                                </button>
                            </template>
                        </div>

                        {{-- View full details — after swatches --}}
                        <a :href="'/shop/' + (product.slug || '')"
                           class="inline-flex items-center gap-1 font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand dark:hover:text-brand-300 transition-colors mt-3">
                            View full details
                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>

                    {{-- View full details when no variants --}}
                    <div x-show="!product.variants || product.variants.length === 0">
                        <a :href="'/shop/' + (product.slug || '')"
                           class="inline-flex items-center gap-1 font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand dark:hover:text-brand-300 transition-colors">
                            View full details
                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>

                    {{-- ── QUANTITY ──────────────────────────────────────── --}}
                    <div x-show="product.stockQuantity > 0" class="space-y-3">
                        <div class="flex items-center gap-4">
                            <span class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 w-20 flex-shrink-0">Quantity</span>
                            <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                                <button
                                    @click="qty = Math.max(product.minQuantity || 1, qty - (product.quantityStep || 1))"
                                    :disabled="qty <= (product.minQuantity || 1)"
                                    class="w-10 h-10 flex items-center justify-center text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                                <span x-text="qty" class="w-12 text-center font-sans text-sm font-semibold text-neutral-900 dark:text-white select-none"></span>
                                <button
                                    @click="qty = Math.min(product.stockQuantity, qty + (product.quantityStep || 1))"
                                    :disabled="qty >= product.stockQuantity"
                                    class="w-10 h-10 flex items-center justify-center text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                            <span x-show="qty >= product.stockQuantity" class="font-sans text-2xs text-accent-600 dark:text-accent-400 font-medium">Max reached</span>
                            <span x-show="qty < product.stockQuantity && product.stockQuantity <= 10"
                                  class="font-sans text-2xs text-neutral-400 dark:text-neutral-500"
                                  x-text="(product.stockQuantity - qty) + ' left'"></span>
                        </div>

                        {{-- Order mini-summary --}}
                        <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 px-4 py-3 flex items-center justify-between flex-wrap gap-3">
                            <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">
                                <span class="font-semibold text-neutral-800 dark:text-white" x-text="qty"></span>
                                &times; <span x-text="product.unit || 'unit'"></span>
                            </span>
                            <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">
                                Total: <span class="font-semibold text-neutral-800 dark:text-white">&#8358;<span x-text="((product.price || 0) * qty).toLocaleString()"></span></span>
                            </span>
                        </div>
                    </div>

                    {{-- ── CTA BUTTONS ──────────────────────────────────── --}}
                    <div class="flex flex-row gap-3">
                        <template x-if="product.stockQuantity > 0">
                            <div class="flex flex-row gap-3 w-full">
                                <button
                                    @click="addToCart(); close()"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-brand hover:bg-brand-600 active:bg-brand-700 text-white font-sans text-sm font-semibold tracking-wide transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 dark:focus:ring-offset-ink"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M5 6.5h16l-2.024 10H7.024L5 6.5Zm0 0L4.364 3H1" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="20.5" r="1" stroke="currentColor" stroke-width="1.4"/><circle cx="17.5" cy="20.5" r="1" stroke="currentColor" stroke-width="1.4"/></svg>
                                    Add to Cart
                                </button>
                                <a
                                    :href="'/shop/' + (product.slug || '')"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-neutral-900 dark:bg-white hover:bg-neutral-700 dark:hover:bg-neutral-100 text-white dark:text-neutral-900 font-sans text-sm font-semibold tracking-wide transition-colors duration-200"
                                >
                                    Buy Now
                                </a>
                            </div>
                        </template>
                        <template x-if="product.stockQuantity === 0">
                            <div class="flex flex-row gap-3 w-full">
                                <button disabled class="flex-1 inline-flex items-center justify-center px-4 py-3.5 bg-neutral-200 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500 font-sans text-sm font-semibold tracking-wide cursor-not-allowed">
                                    Out of Stock
                                </button>
                                <button class="flex-1 inline-flex items-center justify-center px-4 py-3.5 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 font-sans text-sm font-semibold tracking-wide hover:border-brand hover:text-brand transition-colors duration-200">
                                    Notify Me
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- ── SECONDARY ACTIONS ────────────────────────────── --}}
                    <div class="flex items-center gap-5">
                        <button class="inline-flex items-center gap-1.5 font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand dark:hover:text-brand-300 transition-colors">
                            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="1.3"/></svg>
                            Add to Wishlist
                        </button>
                        <button class="inline-flex items-center gap-1.5 font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand dark:hover:text-brand-300 transition-colors">
                            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="1.3"/><circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="1.3"/><circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="1.3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke="currentColor" stroke-width="1.3"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke="currentColor" stroke-width="1.3"/></svg>
                            Share
                        </button>
                    </div>

                </div>
                {{-- /right column --}}

            </div>
        </div>
        {{-- /body --}}

    </div>
    {{-- /modal panel --}}

</div>
{{-- /backdrop --}}