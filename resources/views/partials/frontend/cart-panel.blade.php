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

    // ══════════════════════════════════════════════════════════════════════
    // CURRENCY STORE — rates served from DB via CurrencyService
    // NGN is always the base (1.0). All prices are stored as integer NGN.
    // ══════════════════════════════════════════════════════════════════════
    @php
        $currencyData = app(\App\Services\CurrencyService::class)->getAlpineStoreData();
    @endphp

    Alpine.store('currency', {

        active: @js($currencyData['active']),

        // Rates: { CODE: Number } — how many CODE per 1 NGN
        rates: @json($currencyData['rates']),

        // Per-currency additive markup in the foreign currency (0.0 = no markup)
        // e.g. markup['USD'] = 4 means add $4 to every USD price after conversion
        markup: @json($currencyData['markup']),

        // Currency symbols
        symbols: @json($currencyData['symbols']),

        // ── convert: raw NGN amount → converted amount (Number) ───────────
        // Formula: (amount × rate) + additive_markup
        // For NGN no conversion needed; markup is always 0 for base currency.
        convert: function(ngnAmount) {
            if (this.active === 'NGN') { return ngnAmount; }
            var rate   = this.rates[this.active] || 1;
            var markup = (typeof this.markup === 'object')
                ? (this.markup[this.active] || 0)
                : 0;
            return (ngnAmount * rate) + markup;
        },

        // ── symbol: current currency symbol ──────────────────────────────
        get symbol() {
            return this.symbols[this.active] || this.active;
        },

        // ── format: the ONE function all price displays call ──────────────
        // Returns e.g. "₦28,500" or "$18.52" or "CFA 11,205"
        // Locale formatting: NGN uses no decimal, foreign uses 2 decimal places.
        format: function(ngnAmount) {
            var converted = this.convert(ngnAmount);
            var sym = this.symbol;
            if (this.active === 'NGN') {
                return sym + Math.round(converted).toLocaleString();
            }
            // Foreign currencies: show 2 decimal places
            return sym + converted.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    });

    Alpine.store('cart', {

        open: false,

        // ── CART ITEMS ────────────────────────────────────────────────────────
        // Populated on mount by CartSync Livewire component via cart:initialized event.
        items: [],

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

        // ── ADD MAIN ITEM ─────────────────────────────────────────────────────
        // Adds a product to the cart. Gift cards never merge — each is its own line/code.
        addItem: function(itemData) {
            var variantId = itemData.selected_variant ? itemData.selected_variant.id : null;
            // Only merge non-gift-card items
            if (!itemData.is_gift_card) {
                var existing = this.items.find(function(i) {
                    var existingVid = i.selected_variant ? i.selected_variant.id : null;
                    return i.product_id === itemData.product_id && existingVid === variantId;
                });
                if (existing) {
                    existing.quantity += (itemData.quantity || itemData.min_quantity || 1);
                    if (existing.stock_quantity && existing.quantity > existing.stock_quantity) {
                        existing.quantity = existing.stock_quantity;
                    }
                    this.open = true;
                    Livewire.dispatch('cart:update-qty', { productId: itemData.product_id, variantId: variantId, quantity: existing.quantity });
                    return;
                }
            }
            var newItem = Object.assign({
                cart_line_id: null,
                subcategory: '',
                stock_quantity: 0,
                total_price: 0,
                suggested_add_ons: [],
                added_add_ons: [],
                quantity: itemData.is_gift_card ? 1 : (itemData.min_quantity || 1),
            }, itemData);
            newItem.total_price = (newItem.unit_price || 0) * newItem.quantity;
            this.items.push(newItem);
            this.open = true;
            Livewire.dispatch('cart:add', { productId: itemData.product_id, variantId: variantId, quantity: newItem.quantity, customPrice: newItem.custom_price || null });
        },

        // ── MAIN ITEM QUANTITY ────────────────────────────────────────────────
        increaseQty: function(index) {
            var item = this.items[index];
            if (!item) return;
            if (item.is_gift_card) {
                // Each [+] on a gift card adds a new separate line (one new code at same denomination)
                var newItem = Object.assign({}, item, { cart_line_id: null, quantity: 1 });
                this.items.push(newItem);
                Livewire.dispatch('cart:add', { productId: item.product_id, variantId: null, quantity: 1, customPrice: item.custom_price || item.unit_price || null });
                return;
            }
            var step = item.quantity_step || 1;
            if (item.stock_quantity && item.quantity + step > item.stock_quantity) return;
            item.quantity = item.quantity + step;
            Livewire.dispatch('cart:update-qty', { productId: item.product_id, variantId: item.selected_variant?.id ?? null, quantity: item.quantity });
        },

        decreaseQty: function(index) {
            var item = this.items[index];
            if (!item) return;
            if (item.is_gift_card) {
                // [-] removes this specific gift card line
                if (item.cart_line_id) {
                    Livewire.dispatch('cart:remove-line', { cartLineId: item.cart_line_id });
                } else {
                    Livewire.dispatch('cart:remove', { productId: item.product_id, variantId: null });
                }
                this.items.splice(index, 1);
                return;
            }
            var prev = item.quantity - (item.quantity_step || 1);
            if (prev >= (item.min_quantity || 1)) {
                item.quantity = prev;
                Livewire.dispatch('cart:update-qty', { productId: item.product_id, variantId: item.selected_variant?.id ?? null, quantity: item.quantity });
            }
        },

        // ── REMOVE MAIN ITEM (removes its added add-ons too) ─────────────────
        removeItem: function(index) {
            var item = this.items[index];
            if (item) {
                if (item.is_gift_card && item.cart_line_id) {
                    Livewire.dispatch('cart:remove-line', { cartLineId: item.cart_line_id });
                } else {
                    Livewire.dispatch('cart:remove', { productId: item.product_id, variantId: item.selected_variant?.id ?? null });
                }
            }
            this.items.splice(index, 1);
        },

        // ── REMOVE A SPECIFIC LINE BY ID (for gift cards with multiple same-product lines) ──
        removeItemByLine: function(cartLineId) {
            var index = this.items.findIndex(function(i) { return i.cart_line_id === cartLineId; });
            if (index !== -1) {
                this.items.splice(index, 1);
            }
            Livewire.dispatch('cart:remove-line', { cartLineId: cartLineId });
        },

        // ── UPDATE GIFT CARD DENOMINATION ─────────────────────────────────────
        updateGiftCardPrice: function(cartLineId, rawValue) {
            var amount = parseInt(rawValue, 10) || 0;
            if (amount <= 0) return;
            var item = this.items.find(function(i) { return i.cart_line_id === cartLineId; });
            if (item) {
                item.custom_price = amount;
                item.unit_price = amount;
            }
            Livewire.dispatch('cart:update-gift-card-price', { cartLineId: cartLineId, price: amount });
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
                existing.total_price = existing.unit_price * existing.quantity;
                return;
            }

            var qty = suggestion.min_quantity || 1;
            item.added_add_ons.push({
                cart_line_id:        null,
                parent_cart_line_id: item.cart_line_id || null,
                product_id:      suggestion.product_id,
                slug:            suggestion.slug,
                name:            suggestion.name,
                category:        suggestion.category || '',
                subcategory:     suggestion.subcategory || '',
                image:           suggestion.image,
                selling_method:  suggestion.selling_method,
                unit_label:      suggestion.unit_label,
                length_unit:     suggestion.length_unit,
                units_per_order: suggestion.units_per_order,
                min_quantity:    suggestion.min_quantity,
                quantity_step:   suggestion.quantity_step,
                stock_quantity:  suggestion.stock_quantity || 0,
                loom_size:       suggestion.loom_size,
                unit_price:      suggestion.unit_price,
                quantity:        qty,
                total_price:     (suggestion.unit_price || 0) * qty,
            });
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
            var step = ao.quantity_step || 1;
            if (ao.stock_quantity && ao.quantity + step > ao.stock_quantity) return;
            ao.quantity = ao.quantity + step;
            ao.total_price = ao.unit_price * ao.quantity;
        },

        decreaseAddonQty: function(itemIndex, addonIndex) {
            var ao = this.items[itemIndex] && this.items[itemIndex].added_add_ons[addonIndex];
            if (!ao) return;
            var prev = ao.quantity - (ao.quantity_step || 1);
            if (prev >= (ao.min_quantity || 1)) {
                ao.quantity = prev;
                ao.total_price = ao.unit_price * ao.quantity;
            }
        },

        // ── ADD-ON: REMOVE ────────────────────────────────────────────────────
        removeAddon: function(itemIndex, addonIndex) {
            this.items[itemIndex].added_add_ons.splice(addonIndex, 1);
            // Livewire: this.$wire.removeAddon(item.product_id, addonProductId)
        },

        // ── SUGGESTION: DISMISS (hide from "you might also like") ─────────────
        dismissSuggestion: function(itemIndex, productId) {
            var item = this.items[itemIndex];
            if (!item) return;
            item.suggested_add_ons = item.suggested_add_ons.filter(function(s) {
                return s.product_id !== productId;
            });
        },

        // ── OPEN / CLOSE ──────────────────────────────────────────────────────
        openPanel:  function() { this.open = true;  document.body.style.overflow = 'hidden'; },
        closePanel: function() { this.open = false; document.body.style.overflow = '';       },
    });

    window.addEventListener('cart:open',  function() { Alpine.store('cart').openPanel();  });
    window.addEventListener('cart:close', function() { Alpine.store('cart').closePanel(); });

    // ══════════════════════════════════════════════════════════════════════
    // TOAST STORE — lightweight notification system
    // ══════════════════════════════════════════════════════════════════════
    Alpine.store('toast', {
        visible: false,
        message: '',
        type: 'success',
        _timer: null,
        show: function(message, type) {
            this.message = message;
            this.type = type || 'success';
            this.visible = true;
            var self = this;
            if (self._timer) clearTimeout(self._timer);
            self._timer = setTimeout(function() { self.visible = false; }, 3000);
        }
    });

    window.addEventListener('toast:show', function(e) {
        Alpine.store('toast').show(e.detail.message, e.detail.type);
    });
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
            <template x-for="(item, index) in $store.cart.items" :key="'item-' + (item.cart_line_id || item.product_id)">
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
                                    <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">
                                        <span x-text="item.category"></span><template x-if="item.subcategory"><span> · <span x-text="item.subcategory"></span></span></template>
                                    </p>
                                    <div x-show="item.selected_variant && item.selected_variant.color" class="flex items-center gap-1.5 pt-0.5">
                                        <span :style="item.selected_variant ? 'background-color:' + item.selected_variant.hex : ''"
                                              class="w-2.5 h-2.5 border border-neutral-200 dark:border-neutral-700 flex-shrink-0"></span>
                                        <span class="font-sans text-2xs text-neutral-500 dark:text-neutral-400"
                                              x-text="item.selected_variant ? item.selected_variant.color : ''"></span>
                                    </div>
                                </div>
                                <p class="font-display text-sm font-semibold text-neutral-900 dark:text-white">
                                    <span x-text="$store.currency.format($store.cart.lineTotal(item))"></span>
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

                        {{-- Gift card: custom denomination input --}}
                        <template x-if="item.is_gift_card">
                            <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 px-3 py-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-sans text-2xs text-neutral-500 dark:text-neutral-400">
                                        <svg class="w-3 h-3 inline-block mr-1 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Digital · 1 gift code · Set amount:
                                    </span>
                                    <div class="flex items-center border border-neutral-200 dark:border-neutral-600">
                                        <span class="px-1.5 font-sans text-2xs text-neutral-400 dark:text-neutral-500 bg-neutral-100 dark:bg-neutral-700 border-r border-neutral-200 dark:border-neutral-600 select-none">₦</span>
                                        <input type="number"
                                            :value="item.custom_price || item.unit_price"
                                            @change="$store.cart.updateGiftCardPrice(item.cart_line_id, $event.target.value)"
                                            class="w-24 py-0.5 px-1.5 font-sans text-xs font-semibold text-neutral-900 dark:text-white bg-transparent outline-none"
                                            min="1" step="1000">
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Selling method summary (non-gift-card items only) --}}
                        <div x-show="!item.is_gift_card" class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 px-3 py-2">
                            <template x-if="!item.is_gift_card && item.selling_method === 'per-length'">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold As</span>
                                        <span class="font-sans text-2xs font-medium text-neutral-600 dark:text-neutral-300">Per Length</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Unit</span>
                                        <span class="font-sans text-2xs font-medium text-neutral-600 dark:text-neutral-300"><span x-text="item.units_per_order"></span>&nbsp;<span x-text="item.length_unit"></span></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty</span>
                                        <span class="font-sans text-2xs font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-neutral-100 dark:border-neutral-800 pt-1.5">
                                        <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Total Length</span>
                                        <span class="font-sans text-2xs font-semibold text-brand dark:text-brand-300"><span x-text="$store.cart.totalFabric(item)"></span>&nbsp;<span x-text="item.length_unit"></span></span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!item.is_gift_card && item.selling_method === 'per-set'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold As: <span class="font-medium text-neutral-600 dark:text-neutral-300">Per Set</span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity + (item.quantity === 1 ? ' Set' : ' Sets')"></span></span>
                                </div>
                            </template>
                            <template x-if="!item.is_gift_card && item.selling_method === 'per-bundle'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Bundle'"></span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                            <template x-if="!item.is_gift_card && item.selling_method === 'per-piece'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Piece'"></span></span>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                            <template x-if="!item.is_gift_card && item.selling_method === 'per-loom'">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Sold per <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.unit_label || 'Loom'"></span></span>
                                    <template x-if="item.loom_size"><span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Size: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.loom_size"></span></span></template>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Qty: <span class="font-medium text-neutral-600 dark:text-neutral-300" x-text="item.quantity"></span></span>
                                </div>
                            </template>
                        </div>

                        {{-- Quantity controls: for gift cards shows [−] 1 code [+]; [-] removes this line, [+] adds new card --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                                <button @click="$store.cart.decreaseQty(index)" :disabled="!item.is_gift_card && item.quantity <= item.min_quantity"
                                        class="w-8 h-8 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                                <span x-text="item.quantity" class="w-10 text-center font-sans text-xs font-semibold text-neutral-900 dark:text-white select-none"></span>
                                <button @click="$store.cart.increaseQty(index)"
                                        :disabled="!item.is_gift_card && item.stock_quantity && item.quantity >= item.stock_quantity"
                                        class="w-8 h-8 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                            <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                                <template x-if="item.is_gift_card">
                                    <span class="text-brand dark:text-brand-300 font-medium">1 gift code</span>
                                </template>
                                <template x-if="!item.is_gift_card && item.selling_method === 'per-length'">
                                    <span><span x-text="$store.currency.format(item.unit_price)"></span> per <span x-text="item.units_per_order + ' ' + item.length_unit"></span></span>
                                </template>
                                <template x-if="!item.is_gift_card && item.selling_method !== 'per-length'">
                                    <span><span x-text="$store.currency.format(item.unit_price)"></span> / <span x-text="item.unit_label || (item.selling_method === 'per-set' ? 'Set' : 'unit')"></span></span>
                                </template>
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
                                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                                                    <span x-text="ao.category"></span><template x-if="ao.subcategory"><span> · <span x-text="ao.subcategory"></span></span></template>
                                                </p>
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
                                                <span x-text="$store.currency.format(ao.unit_price)"></span> / <span x-text="ao.unit_label || (ao.selling_method === 'per-set' ? 'Set' : 'unit')"></span>
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
                                                <span x-text="$store.currency.format(ao.unit_price * ao.quantity)"></span>
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
                                                <span class="font-sans text-2xs font-semibold text-neutral-700 dark:text-neutral-300"><span x-text="$store.currency.format(suggestion.unit_price)"></span></span>
                                                <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">/ <span x-text="suggestion.unit_label || (suggestion.selling_method === 'per-set' ? 'Set' : 'unit')"></span></span>
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

                                        {{-- Dismiss button --}}
                                        <button
                                            @click="$store.cart.dismissSuggestion(index, suggestion.product_id)"
                                            class="flex-shrink-0 w-5 h-5 flex items-center justify-center text-neutral-300 dark:text-neutral-600 hover:text-neutral-500 dark:hover:text-neutral-400 transition-colors"
                                            aria-label="Dismiss suggestion"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
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
                    <span class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300"><span x-text="$store.currency.format($store.cart.items_subtotal)"></span></span>
                </div>
                <div x-show="$store.cart.add_ons_total > 0" class="flex items-center justify-between">
                    <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">Add-ons</span>
                    <span class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300"><span x-text="$store.currency.format($store.cart.add_ons_total)"></span></span>
                </div>
                <div class="border-t border-neutral-100 dark:border-neutral-800 pt-2.5 flex items-center justify-between">
                    <span class="font-sans text-xs font-semibold text-neutral-900 dark:text-white uppercase tracking-wide">Total</span>
                    <span class="font-display text-md font-bold text-neutral-900 dark:text-white"><span x-text="$store.currency.format($store.cart.cart_total)"></span></span>
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

{{-- ══════════════════════════════════════════════════════════════════════════
     TOAST NOTIFICATION
══════════════════════════════════════════════════════════════════════════════ --}}
<div
    x-data
    x-cloak
    x-show="$store.toast.visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-6 right-6 z-[9999] max-w-xs"
    style="display:none"
>
    <div class="flex items-center gap-3 px-4 py-3 shadow-lg border"
         :class="$store.toast.type === 'success'
             ? 'bg-brand-50 dark:bg-brand-900/30 border-brand-200 dark:border-brand-700 text-brand-700 dark:text-brand-300'
             : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-700 text-red-700 dark:text-red-300'">
        <template x-if="$store.toast.type === 'success'">
            <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 flex-shrink-0">
                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </template>
        <span class="font-sans text-sm font-medium" x-text="$store.toast.message"></span>
        <button @click="$store.toast.visible = false" class="ml-auto flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
    </div>
</div>