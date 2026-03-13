{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  FRONTEND — CART PANEL (SLIDE-OUT)                                           ║
║  resources/views/partials/frontend/cart-panel.blade.php                      ║
║                                                                              ║
║  Add-on Architecture:                                                        ║
║   • suggested_add_ons  → real store products the store recommends            ║
║     (same selling method architecture as main cart items)                    ║
║   • added_add_ons      → products the buyer has chosen to add                ║
║     (own qty controls, min_quantity, quantity_step enforced, removable)      ║
║   • add-ons are attached to parent; removed when parent is removed           ║
║   • add-on totals roll up into cart_total                                    ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<script>
document.addEventListener('alpine:init', () => {

    Alpine.store('cart', {

        open: false,

        // ── CART ITEMS ────────────────────────────────────────────────────────
        // suggested_add_ons → real store products to upsell (buyer chooses freely)
        // added_add_ons     → products buyer has actually added (starts empty)
        //
        // Replace mock with: @@json($cartItems) or Livewire payload later.
        items: [
            {
                product_id:       1,
                slug:             'premium-french-lace-fabric',
                name:             'Premium French Lace Fabric',
                category:         'Lace Fabrics',
                selling_method:   'per-length',
                unit_label:       'yards',
                length_unit:      'yards',
                units_per_order:  5,
                min_quantity:     1,
                quantity_step:    1,
                loom_size:        null,
                quantity:         2,
                unit_price:       28500,
                selected_variant: { color: 'Ivory White', hex: '#F5F0E8' },
                image:            'https://placehold.co/80x100/F3F3F3/A3A3A3?text=Lace',

                suggested_add_ons: [
                    {
                        product_id:      10,
                        slug:            'matching-aso-oke-headtie',
                        name:            'Matching Aso-oke Headtie',
                        category:        'Aso-oke',
                        selling_method:  'per-piece',
                        unit_label:      'Piece',
                        length_unit:     null,
                        units_per_order: 1,
                        min_quantity:    1,
                        quantity_step:   1,
                        loom_size:       null,
                        unit_price:      8500,
                        image:           'https://placehold.co/56x68/E6F3F2/1F6F67?text=Headtie',
                    },
                    {
                        product_id:      11,
                        slug:            'french-lace-lining-fabric',
                        name:            'French Lace Lining Fabric',
                        category:        'Plain & Solid',
                        selling_method:  'per-length',
                        unit_label:      'yards',
                        length_unit:     'yards',
                        units_per_order: 3,
                        min_quantity:    1,
                        quantity_step:   1,
                        loom_size:       null,
                        unit_price:      4200,
                        image:           'https://placehold.co/56x68/F9F9F9/737373?text=Lining',
                    },
                ],

                added_add_ons: [],
            },
            {
                product_id:       2,
                slug:             'handwoven-aso-oke-set',
                name:             'Handwoven Aso-oke Set',
                category:         'Aso-oke',
                selling_method:   'per-set',
                unit_label:       'Set',
                length_unit:      null,
                units_per_order:  1,
                min_quantity:     1,
                quantity_step:    1,
                loom_size:        null,
                quantity:         1,
                unit_price:       65000,
                selected_variant: { color: 'Royal Blue', hex: '#2C4A8F' },
                image:            'https://placehold.co/80x100/F3F3F3/A3A3A3?text=Aso-oke',

                suggested_add_ons: [
                    {
                        product_id:      12,
                        slug:            'aso-oke-cap-mens',
                        name:            "Men's Aso-oke Cap",
                        category:        'Caps',
                        selling_method:  'per-piece',
                        unit_label:      'Cap',
                        length_unit:     null,
                        units_per_order: 1,
                        min_quantity:    1,
                        quantity_step:   1,
                        loom_size:       null,
                        unit_price:      5500,
                        image:           'https://placehold.co/56x68/E6F3F2/1F6F67?text=Cap',
                    },
                ],

                added_add_ons: [],
            },
            {
                product_id:       3,
                slug:             'ankara-print-bundle',
                name:             'Ankara Mixed Print Bundle',
                category:         'Ankara & Prints',
                selling_method:   'per-bundle',
                unit_label:       'Bundle',
                length_unit:      null,
                units_per_order:  1,
                min_quantity:     1,
                quantity_step:    1,
                loom_size:        null,
                quantity:         1,
                unit_price:       18000,
                selected_variant: null,
                image:            'https://placehold.co/80x100/F3F3F3/A3A3A3?text=Ankara',

                suggested_add_ons: [
                    {
                        product_id:      13,
                        slug:            'plain-cotton-lining',
                        name:            'Plain Cotton Lining Fabric',
                        category:        'Plain & Solid',
                        selling_method:  'per-length',
                        unit_label:      'yards',
                        length_unit:     'yards',
                        units_per_order: 2,
                        min_quantity:    1,
                        quantity_step:   1,
                        loom_size:       null,
                        unit_price:      2800,
                        image:           'https://placehold.co/56x68/F9F9F9/737373?text=Cotton',
                    },
                ],

                added_add_ons: [],
            },
        ],

        // ── COMPUTED TOTALS ───────────────────────────────────────────────────
        get item_count() {
            return this.items.reduce(function(sum, item) {
                return sum + item.quantity;
            }, 0);
        },

        get items_subtotal() {
            return this.items.reduce(function(sum, item) {
                return sum + (item.unit_price * item.quantity);
            }, 0);
        },

        get add_ons_total() {
            return this.items.reduce(function(sum, item) {
                var aos = Array.isArray(item.added_add_ons) ? item.added_add_ons : [];
                return sum + aos.reduce(function(s, ao) {
                    return s + (ao.unit_price * ao.quantity);
                }, 0);
            }, 0);
        },

        get cart_total() {
            return this.items_subtotal + this.add_ons_total;
        },

        // ── HELPERS ───────────────────────────────────────────────────────────
        totalFabric: function(item) {
            return item.quantity * (item.units_per_order || 1);
        },

        addOnTotalFabric: function(ao) {
            return ao.quantity * (ao.units_per_order || 1);
        },

        lineTotal: function(item) {
            var aoSum = Array.isArray(item.added_add_ons)
                ? item.added_add_ons.reduce(function(s, ao) { return s + (ao.unit_price * ao.quantity); }, 0)
                : 0;
            return (item.unit_price * item.quantity) + aoSum;
        },

        // ── MAIN ITEM QUANTITY ────────────────────────────────────────────────
        increaseQty: function(index) {
            var item = this.items[index];
            if (!item) return;
            item.quantity = item.quantity + (item.quantity_step || 1);
            // Livewire: this.$wire.updateCartQty(item.product_id, item.quantity)
        },

        decreaseQty: function(index) {
            var item = this.items[index];
            if (!item) return;
            var prev = item.quantity - (item.quantity_step || 1);
            if (prev >= (item.min_quantity || 1)) item.quantity = prev;
        },

        // ── REMOVE MAIN ITEM (removes its added add-ons too) ─────────────────
        removeItem: function(index) {
            this.items.splice(index, 1);
            // Livewire: this.$wire.removeCartItem(productId)
        },

        // ── ADD-ON: ADD from suggestions ──────────────────────────────────────
        // If already added, bumps qty instead of duplicating
        addAddon: function(itemIndex, suggestion) {
            var item = this.items[itemIndex];
            if (!item) return;

            var existing = item.added_add_ons.find(function(ao) {
                return ao.product_id === suggestion.product_id;
            });

            if (existing) {
                existing.quantity += (suggestion.quantity_step || 1);
                return;
            }

            item.added_add_ons.push({
                product_id:      suggestion.product_id,
                slug:            suggestion.slug,
                name:            suggestion.name,
                category:        suggestion.category,
                selling_method:  suggestion.selling_method,
                unit_label:      suggestion.unit_label,
                length_unit:     suggestion.length_unit,
                units_per_order: suggestion.units_per_order,
                min_quantity:    suggestion.min_quantity,
                quantity_step:   suggestion.quantity_step,
                loom_size:       suggestion.loom_size,
                unit_price:      suggestion.unit_price,
                image:           suggestion.image,
                quantity:        suggestion.min_quantity || 1,
            });
            // Livewire: this.$wire.addAddon(item.product_id, suggestion.product_id)
        },

        // Check if a suggestion has already been added
        isAddonAdded: function(itemIndex, addonProductId) {
            var item = this.items[itemIndex];
            if (!item) return false;
            return item.added_add_ons.some(function(ao) {
                return ao.product_id === addonProductId;
            });
        },

        // ── ADD-ON QUANTITY ───────────────────────────────────────────────────
        increaseAddonQty: function(itemIndex, addonIndex) {
            var ao = this.items[itemIndex] && this.items[itemIndex].added_add_ons[addonIndex];
            if (!ao) return;
            ao.quantity = ao.quantity + (ao.quantity_step || 1);
        },

        decreaseAddonQty: function(itemIndex, addonIndex) {
            var ao = this.items[itemIndex] && this.items[itemIndex].added_add_ons[addonIndex];
            if (!ao) return;
            var prev = ao.quantity - (ao.quantity_step || 1);
            if (prev >= (ao.min_quantity || 1)) ao.quantity = prev;
        },

        // ── ADD-ON: REMOVE ────────────────────────────────────────────────────
        removeAddon: function(itemIndex, addonIndex) {
            this.items[itemIndex].added_add_ons.splice(addonIndex, 1);
            // Livewire: this.$wire.removeAddon(item.product_id, addonProductId)
        },

        // ── OPEN / CLOSE ──────────────────────────────────────────────────────
        openPanel:  function() { this.open = true;  document.body.style.overflow = 'hidden'; },
        closePanel: function() { this.open = false; document.body.style.overflow = '';       },
    });

    window.addEventListener('cart:open',  function() { Alpine.store('cart').openPanel();  });
    window.addEventListener('cart:close', function() { Alpine.store('cart').closePanel(); });
});
</script>


{{-- ══════════════════════════════════════════════════════════════════════════
     CART PANEL MARKUP
══════════════════════════════════════════════════════════════════════════════ --}}
<div x-data x-cloak>

    {{-- Backdrop --}}
    <div
        x-show="$store.cart.open"
        x-transition:enter="transition-opacity duration-300 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.cart.closePanel()"
        class="fixed inset-0 bg-black/40 z-[2100]"
        style="display:none"
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <aside
        x-show="$store.cart.open"
        x-transition:enter="transition-transform duration-300 ease-out"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-200 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="$store.cart.closePanel()"
        class="fixed top-0 right-0 h-full w-full max-w-[420px] bg-white dark:bg-ink z-[2101] flex flex-col border-l border-neutral-200 dark:border-neutral-800"
        style="display:none"
        role="dialog"
        aria-label="Shopping cart"
        aria-modal="true"
    >

        {{-- ── PANEL HEADER ──────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-100 dark:border-neutral-800 flex-shrink-0">
            <div class="flex items-center gap-3">
                <svg viewBox="0 0 24 24" fill="none" class="w-[18px] h-[18px] text-neutral-700 dark:text-neutral-300">
                    <path stroke="currentColor" stroke-width="1.3" d="M5 6.5h16l-2.024 10H7.024L5 6.5Zm0 0L4.364 3H1"/>
                    <path stroke="currentColor" stroke-width="1.3" d="M7.889 19.71a.65.65 0 1 1 .722 1.08.65.65 0 0 1-.722-1.08ZM16.889 19.71a.65.65 0 1 1 .722 1.08.65.65 0 0 1-.722-1.08Z"/>
                </svg>
                <span class="font-display text-sm font-semibold text-neutral-900 dark:text-white tracking-snug">Your Cart</span>
                <span
                    x-show="$store.cart.item_count > 0"
                    x-text="$store.cart.item_count"
                    class="min-w-[20px] h-5 px-1.5 flex items-center justify-center bg-brand text-white font-sans text-2xs font-semibold"
                ></span>
            </div>
            <button
                @click="$store.cart.closePanel()"
                class="w-8 h-8 flex items-center justify-center text-neutral-400 dark:text-neutral-500 hover:text-neutral-800 dark:hover:text-white transition-colors"
                aria-label="Close cart"
            >
                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        {{-- ── EMPTY STATE ────────────────────────────────────────────────────── --}}
        <div
            x-show="$store.cart.items.length === 0"
            class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center"
        >
            <svg viewBox="0 0 64 64" fill="none" class="w-14 h-14 text-neutral-200 dark:text-neutral-700 mb-5">
                <path stroke="currentColor" stroke-width="1.5" d="M12 18h44l-5.5 26H17.5L12 18Zm0 0L10 8H2"/>
                <circle cx="22" cy="52" r="3" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="46" cy="52" r="3" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <p class="font-display text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Your cart is empty</p>
            <p class="font-sans text-xs text-neutral-400 dark:text-neutral-500 mb-6">Browse our collection and add items to get started.</p>
            <a href="{{ url('/shop') }}"
               @click="$store.cart.closePanel()"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand hover:bg-brand-600 text-white font-sans text-xs font-semibold tracking-wide transition-colors">
                Browse Shop
            </a>
        </div>

        {{-- ── ITEMS LIST ──────────────────────────────────────────────────────── --}}
        <div
            x-show="$store.cart.items.length > 0"
            class="flex-1 overflow-y-auto overscroll-contain"
        >
            <template x-for="(item, index) in $store.cart.items" :key="'item-' + item.product_id">
                <div class="border-b border-neutral-100 dark:border-neutral-800">

                    {{-- ════════════════════════════════════════════════
                         MAIN CART ITEM
                    ════════════════════════════════════════════════ --}}
                    <div class="px-5 pt-4 pb-3 space-y-3">

                        {{-- Row: image + info + remove --}}
                        <div class="flex gap-3">
                            <a :href="'/products/' + item.slug"
                               @click="$store.cart.closePanel()"
                               class="flex-shrink-0 w-[68px] h-[84px] bg-neutral-50 dark:bg-neutral-900 overflow-hidden block">
                                <img :src="item.image" :alt="item.name"
                                     class="w-full h-full object-cover object-center"
                                     onerror="this.src='https://placehold.co/68x84/F3F3F3/A3A3A3?text=IMG'" />
                            </a>

                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div class="space-y-0.5">
                                    <a :href="'/products/' + item.slug"
                                       @click="$store.cart.closePanel()"
                                       class="font-display text-xs font-semibold text-neutral-900 dark:text-white hover:text-brand transition-colors line-clamp-2 leading-snug block"
                                       x-text="item.name"></a>
                                    <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider" x-text="item.category"></p>
                                    <div x-show="item.selected_variant && item.selected_variant.color" class="flex items-center gap-1.5 pt-0.5">
                                        <span :style="item.selected_variant ? 'background-color:' + item.selected_variant.hex : ''"
                                              class="w-2.5 h-2.5 border border-neutral-200 dark:border-neutral-700 flex-shrink-0"></span>
                                        <span class="font-sans text-2xs text-neutral-500 dark:text-neutral-400"
                                              x-text="item.selected_variant ? item.selected_variant.color : ''"></span>
                                    </div>
                                </div>
                                <p class="font-display text-sm font-semibold text-neutral-900 dark:text-white">
                                    &#8358;<span x-text="$store.cart.lineTotal(item).toLocaleString()"></span>
                                </p>
                            </div>

                            <button
                                @click="$store.cart.removeItem(index)"
                                class="self-start flex-shrink-0 w-6 h-6 flex items-center justify-center text-neutral-300 dark:text-neutral-600 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                aria-label="Remove item"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5">
                                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Selling method summary --}}
                        <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 px-3 py-2">
                            <template x-if="item.selling_method === 'per-length'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.units_per_order + ' ' + item.length_unit"></span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity + (item.quantity === 1 ? ' unit' : ' units')"></span></span>
                                    <span class="font-sans text-2xs font-semibold text-brand dark:text-brand-300"><span x-text="$store.cart.totalFabric(item)"></span>&nbsp;<span x-text="item.length_unit"></span> total</span>
                                </div>
                            </template>
                            <template x-if="item.selling_method === 'per-set'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Set'"></span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                            <template x-if="item.selling_method === 'per-bundle'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Bundle'"></span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                            <template x-if="item.selling_method === 'per-piece'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Piece'"></span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                            <template x-if="item.selling_method === 'per-loom'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Loom'"></span></span>
                                    <template x-if="item.loom_size"><span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Size: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.loom_size"></span></span></template>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                        </div>

                        {{-- Quantity controls --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                                <button @click="$store.cart.decreaseQty(index)" :disabled="item.quantity <= item.min_quantity"
                                        class="w-8 h-8 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                                <span x-text="item.quantity" class="w-10 text-center font-sans text-xs font-semibold text-neutral-900 dark:text-white select-none"></span>
                                <button @click="$store.cart.increaseQty(index)"
                                        class="w-8 h-8 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                            <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                                &#8358;<span x-text="item.unit_price.toLocaleString()"></span> / <span x-text="item.unit_label || 'unit'"></span>
                            </span>
                        </div>

                    </div>
                    {{-- /main item --}}

                    {{-- ════════════════════════════════════════════════
                         ADDED ADD-ONS — chosen products attached to this item
                    ════════════════════════════════════════════════ --}}
                    <template x-if="item.added_add_ons && item.added_add_ons.length > 0">
                        <div class="bg-neutral-50/70 dark:bg-neutral-900/30 border-t border-neutral-100 dark:border-neutral-800">
                            <template x-for="(ao, aoIndex) in item.added_add_ons" :key="'ao-' + ao.product_id">
                                <div class="px-5 py-3 flex gap-3 border-b border-neutral-100 dark:border-neutral-800 last:border-0">

                                    {{-- Add-on image --}}
                                    <a :href="'/products/' + ao.slug"
                                       @click="$store.cart.closePanel()"
                                       class="flex-shrink-0 w-[48px] h-[60px] bg-neutral-100 dark:bg-neutral-800 overflow-hidden block">
                                        <img :src="ao.image" :alt="ao.name"
                                             class="w-full h-full object-cover object-center"
                                             onerror="this.src='https://placehold.co/48x60/F3F3F3/A3A3A3?text=+'" />
                                    </a>

                                    {{-- Add-on info --}}
                                    <div class="flex-1 min-w-0 space-y-1.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="font-sans text-2xs font-semibold uppercase tracking-wider text-brand dark:text-brand-300 mb-0.5">Add-on</p>
                                                <a :href="'/products/' + ao.slug"
                                                   @click="$store.cart.closePanel()"
                                                   class="font-display text-xs font-semibold text-neutral-800 dark:text-white hover:text-brand transition-colors line-clamp-1 block"
                                                   x-text="ao.name"></a>
                                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500" x-text="ao.category"></p>
                                            </div>
                                            <button
                                                @click="$store.cart.removeAddon(index, aoIndex)"
                                                class="flex-shrink-0 w-5 h-5 flex items-center justify-center text-neutral-300 dark:text-neutral-600 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                                aria-label="Remove add-on"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                            </button>
                                        </div>

                                        {{-- Add-on selling hint --}}
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <template x-if="ao.selling_method === 'per-length'">
                                                <span class="font-sans text-2xs text-brand dark:text-brand-300 font-medium">
                                                    <span x-text="$store.cart.addOnTotalFabric(ao)"></span>&nbsp;<span x-text="ao.length_unit"></span> total
                                                </span>
                                            </template>
                                            <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                                                &#8358;<span x-text="ao.unit_price.toLocaleString()"></span> / <span x-text="ao.unit_label || 'unit'"></span>
                                            </span>
                                        </div>

                                        {{-- Add-on qty + line total --}}
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                                                <button @click="$store.cart.decreaseAddonQty(index, aoIndex)" :disabled="ao.quantity <= ao.min_quantity"
                                                        class="w-7 h-7 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                                    <svg viewBox="0 0 24 24" fill="none" class="w-2.5 h-2.5"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                </button>
                                                <span x-text="ao.quantity" class="w-8 text-center font-sans text-2xs font-semibold text-neutral-900 dark:text-white select-none"></span>
                                                <button @click="$store.cart.increaseAddonQty(index, aoIndex)"
                                                        class="w-7 h-7 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                                                    <svg viewBox="0 0 24 24" fill="none" class="w-2.5 h-2.5"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                </button>
                                            </div>
                                            <span class="font-display text-xs font-semibold text-neutral-800 dark:text-white">
                                                +&#8358;<span x-text="(ao.unit_price * ao.quantity).toLocaleString()"></span>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </template>
                    {{-- /added add-ons --}}

                    {{-- ════════════════════════════════════════════════
                         UPSELL SUGGESTIONS — "You might also like"
                         Hides suggestions already added by the buyer
                    ════════════════════════════════════════════════ --}}
                    <template x-if="item.suggested_add_ons && item.suggested_add_ons.some(function(s) { return !$store.cart.isAddonAdded(index, s.product_id); })">
                        <div class="px-5 py-3 border-t border-dashed border-neutral-200 dark:border-neutral-700 bg-white dark:bg-ink">

                            <p class="font-sans text-2xs font-semibold uppercase tracking-widest text-neutral-400 dark:text-neutral-500 mb-2.5">You might also like</p>

                            <div class="space-y-2.5">
                                <template x-for="suggestion in item.suggested_add_ons" :key="'sug-' + suggestion.product_id">
                                    <div x-show="!$store.cart.isAddonAdded(index, suggestion.product_id)" class="flex items-center gap-3">

                                        {{-- Suggestion image --}}
                                        <div class="flex-shrink-0 w-[44px] h-[54px] bg-neutral-50 dark:bg-neutral-900 overflow-hidden">
                                            <img :src="suggestion.image" :alt="suggestion.name"
                                                 class="w-full h-full object-cover object-center"
                                                 onerror="this.src='https://placehold.co/44x54/F3F3F3/A3A3A3?text=+'" />
                                        </div>

                                        {{-- Suggestion info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="font-display text-2xs font-semibold text-neutral-800 dark:text-white line-clamp-1" x-text="suggestion.name"></p>
                                            <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500" x-text="suggestion.category"></p>
                                            <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                <span class="font-sans text-2xs font-semibold text-neutral-700 dark:text-neutral-300">&#8358;<span x-text="suggestion.unit_price.toLocaleString()"></span></span>
                                                <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">/ <span x-text="suggestion.unit_label || 'unit'"></span></span>
                                                <template x-if="suggestion.selling_method === 'per-length'">
                                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">&middot; <span x-text="suggestion.units_per_order + ' ' + suggestion.length_unit"></span></span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Add button --}}
                                        <button
                                            @click="$store.cart.addAddon(index, suggestion)"
                                            class="flex-shrink-0 flex items-center gap-1 px-2.5 py-1.5 border border-brand text-brand dark:border-brand-400 dark:text-brand-400 hover:bg-brand hover:text-white dark:hover:bg-brand dark:hover:text-white font-sans text-2xs font-semibold tracking-wide transition-colors"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3 flex-shrink-0"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            Add
                                        </button>

                                    </div>
                                </template>
                            </div>

                        </div>
                    </template>
                    {{-- /upsell suggestions --}}

                </div>
            </template>
        </div>
        {{-- /items list --}}

        {{-- ── CART FOOTER ────────────────────────────────────────────────────── --}}
        <div
            x-show="$store.cart.items.length > 0"
            class="flex-shrink-0 border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-ink"
        >
            <div class="px-5 py-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">Items (<span x-text="$store.cart.item_count"></span>)</span>
                    <span class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">&#8358;<span x-text="$store.cart.items_subtotal.toLocaleString()"></span></span>
                </div>
                <div x-show="$store.cart.add_ons_total > 0" class="flex items-center justify-between">
                    <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">Add-ons</span>
                    <span class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">+&#8358;<span x-text="$store.cart.add_ons_total.toLocaleString()"></span></span>
                </div>
                <div class="border-t border-neutral-100 dark:border-neutral-800 pt-2.5 flex items-center justify-between">
                    <span class="font-sans text-xs font-semibold text-neutral-900 dark:text-white uppercase tracking-wide">Total</span>
                    <span class="font-display text-md font-bold text-neutral-900 dark:text-white">&#8358;<span x-text="$store.cart.cart_total.toLocaleString()"></span></span>
                </div>
                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Shipping calculated at checkout</p>
            </div>

            <div class="px-5 pb-5 space-y-2.5">
                <a href="{{ url('/checkout') }}"
                   class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-brand hover:bg-brand-600 active:bg-brand-700 text-white font-sans text-sm font-semibold tracking-wide transition-colors duration-200">
                    Proceed to Checkout
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                </a>
                <a href="{{ url('/cart') }}"
                   @click="$store.cart.closePanel()"
                   class="flex items-center justify-center w-full px-5 py-2.5 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:border-neutral-400 dark:hover:border-neutral-500 hover:text-neutral-900 dark:hover:text-white font-sans text-xs font-medium tracking-wide transition-colors duration-200">
                    View Full Cart
                </a>
            </div>
        </div>

    </aside>
</div>