{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  FRONTEND — CART PAGE                                                        ║
║  resources/views/frontend/cart/index.blade.php                               ║
║                                                                              ║
║  Architecture:                                                               ║
║   • Reads from the same Alpine cartStore defined in cart-panel.blade.php     ║
║   • Reuses identical selling method summary logic (no duplication)           ║
║   • Full expanded layout: item list + order summary sidebar                  ║
║   • Quantity controls, remove, continue shopping, checkout                   ║
║   • Ready for Livewire backend integration                                   ║
║                                                                              ║
║  NOTE: cart-panel.blade.php must be included in your layout BEFORE this     ║
║  page renders (it defines Alpine.store('cart')). If you include the panel    ║
║  in your layout partial that wraps all frontend pages, this is automatic.   ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('content')

{{-- ── DARK MODE INIT (consistent with rest of storefront) ─────────────────── --}}
<script>

    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('theme') === 'dark'
                || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            }
        });
    });
(function() {
    var saved = localStorage.getItem('theme');
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
})();
</script>

<div class="bg-[#FCFCF9] dark:bg-ink min-h-screen pt-40">

    {{-- ── BREADCRUMB ─────────────────────────────────────────────────────────── --}}
    <nav class="mborder-b border-neutral-100 dark:border-neutral-800 bg-white dark:bg-ink" aria-label="Breadcrumb">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-3 flex items-center gap-2 flex-wrap">
            <a href="{{ url('/') }}"
               class="font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand transition-colors">Home</a>
            <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-700 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="font-sans text-xs text-neutral-800 dark:text-neutral-200 font-medium">Cart</span>
        </div>
    </nav>

    {{-- ── PAGE TITLE BAR ──────────────────────────────────────────────────────── --}}
    <div class="border-b border-neutral-100 dark:border-neutral-800 bg-white dark:bg-ink">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-5 flex items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold text-neutral-900 dark:text-white tracking-tight leading-tight">Shopping Cart</h1>
                <p x-data class="font-sans text-xs text-neutral-400 dark:text-neutral-500 mt-0.5"
                   x-show="$store.cart.item_count > 0">
                    <span x-text="$store.cart.item_count"></span>
                    <span x-text="$store.cart.item_count === 1 ? ' item' : ' items'"></span> in your cart
                </p>
            </div>
            <a href="{{ url('/shop') }}"
               class="hidden sm:inline-flex items-center gap-1.5 font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand transition-colors">
                <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                Continue Shopping
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         MAIN CONTENT
         Two-column on desktop: cart items | order summary
    ══════════════════════════════════════════════════════════════════════════════ --}}
    <div x-data class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-8 lg:py-12">

        {{-- ── EMPTY STATE ─────────────────────────────────────────────────────── --}}
        <div x-show="$store.cart.items.length === 0"
             class="flex flex-col items-center justify-center py-20 text-center">
            <svg viewBox="0 0 64 64" fill="none" class="w-16 h-16 text-neutral-200 dark:text-neutral-700 mb-6">
                <path stroke="currentColor" stroke-width="1.5" d="M12 18h44l-5.5 26H17.5L12 18Zm0 0L10 8H2"/>
                <circle cx="22" cy="52" r="3" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="46" cy="52" r="3" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <h2 class="font-display text-xl font-bold text-neutral-800 dark:text-white mb-2">Your cart is empty</h2>
            <p class="font-sans text-sm text-neutral-400 dark:text-neutral-500 max-w-xs mb-8">
                Looks like you haven't added any fabrics yet. Explore our collection to get started.
            </p>
            <a href="{{ url('/shop') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-brand hover:bg-brand-600 text-white font-sans text-sm font-semibold tracking-wide transition-colors">
                Browse Our Fabrics
            </a>
        </div>

        {{-- ── CART CONTENT (2-column) ──────────────────────────────────────────── --}}
        <div x-show="$store.cart.items.length > 0"
             class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-10 xl:gap-14 items-start">

            {{-- ══════════════════════════════════════════════════════════════════
                 LEFT COLUMN — CART ITEMS
            ══════════════════════════════════════════════════════════════════════ --}}
            <div>

                {{-- Column headers (desktop) --}}
                <div class="hidden sm:grid grid-cols-[1fr_auto_auto] gap-4 pb-3 border-b border-neutral-100 dark:border-neutral-800 mb-1">
                    <span class="font-sans text-2xs font-semibold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Product</span>
                    <span class="font-sans text-2xs font-semibold uppercase tracking-widest text-neutral-400 dark:text-neutral-500 w-28 text-center">Quantity</span>
                    <span class="font-sans text-2xs font-semibold uppercase tracking-widest text-neutral-400 dark:text-neutral-500 w-28 text-right">Total</span>
                </div>

                {{-- Items --}}
                <template x-for="(item, index) in $store.cart.items" :key="item.product_id + '-' + index">
                    <div class="py-6 border-b border-neutral-100 dark:border-neutral-800 space-y-5">

                        {{-- ── ITEM ROW ─────────────────────────────────────── --}}
                        <div class="flex gap-4 sm:gap-5">

                            {{-- Image --}}
                            <a :href="'/products/' + item.slug"
                               class="flex-shrink-0 w-[88px] sm:w-[100px] h-[110px] sm:h-[125px] bg-neutral-50 dark:bg-neutral-900 overflow-hidden block">
                                <img :src="item.image"
                                     :alt="item.name"
                                     class="w-full h-full object-cover object-center"
                                     onerror="this.src='https://placehold.co/100x125/F3F3F3/A3A3A3?text=IMG'" />
                            </a>

                            {{-- Details + controls --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1 flex-1 min-w-0">

                                        {{-- Name --}}
                                        <a :href="'/products/' + item.slug"
                                           class="font-display text-sm font-semibold text-neutral-900 dark:text-white hover:text-brand transition-colors line-clamp-2 leading-snug block"
                                           x-text="item.name"></a>

                                        {{-- Category --}}
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider" x-text="item.category"></p>

                                        {{-- Variant / Color --}}
                                        <div x-show="item.selected_variant && item.selected_variant.color" class="flex items-center gap-1.5 pt-0.5">
                                            <span :style="item.selected_variant ? 'background-color:' + item.selected_variant.hex : ''"
                                                  class="w-3 h-3 border border-neutral-200 dark:border-neutral-700 flex-shrink-0"></span>
                                            <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400"
                                                  x-text="item.selected_variant ? item.selected_variant.color : ''"></span>
                                        </div>

                                        {{-- Unit price --}}
                                        <p class="font-sans text-xs text-neutral-400 dark:text-neutral-500 pt-0.5">
                                            &#8358;<span x-text="item.unit_price.toLocaleString()"></span>
                                            &nbsp;/&nbsp;<span x-text="item.unit_label || 'unit'"></span>
                                        </p>
                                    </div>

                                    {{-- Remove (mobile top-right) --}}
                                    <button
                                        @click="$store.cart.removeItem(index)"
                                        class="sm:hidden flex-shrink-0 w-7 h-7 flex items-center justify-center text-neutral-300 dark:text-neutral-600 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                        aria-label="Remove item"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                </div>

                                {{-- Desktop: quantity + total row --}}
                                <div class="hidden sm:flex items-center gap-6 mt-4">

                                    {{-- Quantity controls --}}
                                    <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                                        <button
                                            @click="$store.cart.decreaseQty(index)"
                                            :disabled="item.quantity <= item.min_quantity"
                                            class="w-9 h-9 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                            aria-label="Decrease quantity"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </button>
                                        <span x-text="item.quantity"
                                              class="w-11 text-center font-sans text-sm font-semibold text-neutral-900 dark:text-white select-none"></span>
                                        <button
                                            @click="$store.cart.increaseQty(index)"
                                            class="w-9 h-9 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                                            aria-label="Increase quantity"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </button>
                                    </div>

                                    {{-- Line total --}}
                                    <span class="font-display text-sm font-bold text-neutral-900 dark:text-white ml-auto">
                                        &#8358;<span x-text="$store.cart.lineTotal(item).toLocaleString()"></span>
                                    </span>

                                    {{-- Remove (desktop) --}}
                                    <button
                                        @click="$store.cart.removeItem(index)"
                                        class="flex-shrink-0 w-7 h-7 flex items-center justify-center text-neutral-300 dark:text-neutral-600 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                        aria-label="Remove item"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                </div>

                                {{-- Mobile: qty + total row --}}
                                <div class="flex sm:hidden items-center gap-4 mt-3">
                                    <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                                        <button
                                            @click="$store.cart.decreaseQty(index)"
                                            :disabled="item.quantity <= item.min_quantity"
                                            class="w-8 h-8 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </button>
                                        <span x-text="item.quantity"
                                              class="w-10 text-center font-sans text-xs font-semibold text-neutral-900 dark:text-white select-none"></span>
                                        <button
                                            @click="$store.cart.increaseQty(index)"
                                            class="w-8 h-8 flex items-center justify-center text-neutral-500 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </button>
                                    </div>
                                    <span class="font-display text-sm font-bold text-neutral-900 dark:text-white ml-auto">
                                        &#8358;<span x-text="$store.cart.lineTotal(item).toLocaleString()"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ── SELLING METHOD SUMMARY ───────────────────────── --}}
                        <div class="ml-0 sm:ml-[116px] bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 px-4 py-3 space-y-2">

                            {{-- per-length --}}
                            <template x-if="item.selling_method === 'per-length'">
                                <div class="flex flex-wrap gap-x-6 gap-y-1.5">
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Sold per</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                            <span x-text="item.units_per_order"></span>&nbsp;<span x-text="item.length_unit"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Units ordered</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                            <span x-text="item.quantity"></span>&nbsp;<span x-text="item.quantity === 1 ? 'unit' : 'units'"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-2xs text-brand dark:text-brand-300 uppercase tracking-wider font-semibold">Total Fabric</p>
                                        <p class="font-sans text-xs font-semibold text-brand dark:text-brand-300">
                                            <span x-text="$store.cart.totalFabric(item)"></span>&nbsp;<span x-text="item.length_unit"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>

                            {{-- per-set --}}
                            <template x-if="item.selling_method === 'per-set'">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap gap-x-6 gap-y-1.5">
                                        <div>
                                            <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Sold per</p>
                                            <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="item.unit_label || 'Set'"></p>
                                        </div>
                                        <div>
                                            <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Qty</p>
                                            <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                                <span x-text="item.quantity"></span>&nbsp;<span x-text="(item.unit_label || 'set') + (item.quantity > 1 ? 's' : '')"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <template x-if="item.included_items && item.included_items.length > 0">
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 pt-1.5 border-t border-neutral-100 dark:border-neutral-800">
                                            <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider w-full mb-0.5">Includes</p>
                                            <template x-for="inc in item.included_items" :key="inc.name">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1 h-1 rounded-full bg-brand flex-shrink-0"></span>
                                                    <span class="font-sans text-xs text-neutral-600 dark:text-neutral-400">
                                                        <span x-text="inc.name"></span>
                                                        <template x-if="inc.quantity > 1"><span class="text-brand dark:text-brand-300 font-semibold">&nbsp;&times;<span x-text="inc.quantity"></span></span></template>
                                                    </span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- per-bundle --}}
                            <template x-if="item.selling_method === 'per-bundle'">
                                <div class="flex flex-wrap gap-x-6 gap-y-1.5">
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Sold per</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="item.unit_label || 'Bundle'"></p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Qty</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                            <span x-text="item.quantity"></span>&nbsp;<span x-text="(item.unit_label || 'bundle') + (item.quantity > 1 ? 's' : '')"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>

                            {{-- per-piece --}}
                            <template x-if="item.selling_method === 'per-piece'">
                                <div class="flex flex-wrap gap-x-6 gap-y-1.5">
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Sold per</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="item.unit_label || 'Piece'"></p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Qty</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                            <span x-text="item.quantity"></span>&nbsp;<span x-text="(item.unit_label || 'piece') + (item.quantity > 1 ? 's' : '')"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>

                            {{-- per-loom --}}
                            <template x-if="item.selling_method === 'per-loom'">
                                <div class="flex flex-wrap gap-x-6 gap-y-1.5">
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Sold per</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="item.unit_label || 'Loom'"></p>
                                    </div>
                                    <template x-if="item.loom_size">
                                        <div>
                                            <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Loom Size</p>
                                            <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300" x-text="item.loom_size"></p>
                                        </div>
                                    </template>
                                    <div>
                                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Qty</p>
                                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                            <span x-text="item.quantity"></span>&nbsp;<span x-text="(item.unit_label || 'loom') + (item.quantity > 1 ? 's' : '')"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>

                        </div>

                        {{-- ── ADD-ONS ──────────────────────────────────────── --}}
                        <template x-if="item.add_ons && item.add_ons.length > 0">
                            <div class="ml-0 sm:ml-[116px] space-y-2">
                                <p class="font-sans text-2xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500">Selected Add-ons</p>
                                <div class="space-y-1.5">
                                    <template x-for="ao in item.add_ons" :key="ao.id">
                                        <div class="flex items-center justify-between py-1.5 border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                                            <div class="flex items-center gap-2">
                                                <span class="w-1 h-1 rounded-full bg-brand flex-shrink-0"></span>
                                                <span class="font-sans text-xs text-neutral-600 dark:text-neutral-400" x-text="ao.name"></span>
                                            </div>
                                            <span class="font-sans text-xs font-medium text-neutral-600 dark:text-neutral-400">
                                                +&#8358;<span x-text="ao.price.toLocaleString()"></span>
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>
                {{-- /items --}}

                {{-- Continue shopping link (mobile) --}}
                <div class="sm:hidden pt-4">
                    <a href="{{ url('/shop') }}"
                       class="inline-flex items-center gap-1.5 font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand transition-colors">
                        <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                        Continue Shopping
                    </a>
                </div>

            </div>
            {{-- /left column --}}

            {{-- ══════════════════════════════════════════════════════════════════
                 RIGHT COLUMN — ORDER SUMMARY
            ══════════════════════════════════════════════════════════════════════ --}}
            <div class="lg:sticky lg:top-[88px]">

                <div class="border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-ink">

                    {{-- Summary header --}}
                    <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-800">
                        <h2 class="font-display text-sm font-semibold text-neutral-900 dark:text-white tracking-snug">Order Summary</h2>
                    </div>

                    {{-- Totals --}}
                    <div class="px-5 py-4 space-y-3">

                        {{-- Items subtotal --}}
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">
                                Subtotal (<span x-text="$store.cart.item_count"></span> <span x-text="$store.cart.item_count === 1 ? 'item' : 'items'"></span>)
                            </span>
                            <span class="font-sans text-xs font-medium text-neutral-800 dark:text-neutral-200">
                                &#8358;<span x-text="$store.cart.items_subtotal.toLocaleString()"></span>
                            </span>
                        </div>

                        {{-- Add-ons total --}}
                        <div x-show="$store.cart.add_ons_total > 0" class="flex items-center justify-between">
                            <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">Add-ons</span>
                            <span class="font-sans text-xs font-medium text-neutral-800 dark:text-neutral-200">
                                +&#8358;<span x-text="$store.cart.add_ons_total.toLocaleString()"></span>
                            </span>
                        </div>

                        {{-- Shipping note --}}
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">Shipping</span>
                            <span class="font-sans text-xs text-neutral-400 dark:text-neutral-500 italic">Calculated at checkout</span>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-neutral-100 dark:border-neutral-800 pt-3">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs font-semibold uppercase tracking-wide text-neutral-900 dark:text-white">Estimated Total</span>
                                <span class="font-display text-lg font-bold text-neutral-900 dark:text-white">
                                    &#8358;<span x-text="$store.cart.cart_total.toLocaleString()"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Checkout button --}}
                    <div class="px-5 pb-5 space-y-3">
                        <a href="{{ url('/checkout') }}"
                           class="flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-brand hover:bg-brand-600 active:bg-brand-700 text-white font-sans text-sm font-semibold tracking-wide transition-colors duration-200">
                            Proceed to Checkout
                            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                        </a>

                        {{-- Trust signals --}}
                        <div class="pt-1 space-y-2">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-brand flex-shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="1.3"/></svg>
                                <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Secure & encrypted checkout</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-brand flex-shrink-0"><path d="M5 12h14M15 8l4 4-4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">Fast delivery within Lagos & nationwide</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Promo code (placeholder, wire to backend later) --}}
                <div class="mt-4 border border-neutral-200 dark:border-neutral-800">
                    <div class="px-5 py-4">
                        <p class="font-sans text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-2.5">Have a promo code?</p>
                        <div class="flex gap-0">
                            <input
                                type="text"
                                placeholder="Enter code"
                                class="flex-1 px-3 py-2.5 border border-neutral-200 dark:border-neutral-700 border-r-0 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white font-sans text-xs placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand transition-colors"
                            />
                            <button
                                class="px-4 py-2.5 bg-neutral-900 dark:bg-white hover:bg-neutral-700 dark:hover:bg-neutral-100 text-white dark:text-neutral-900 font-sans text-xs font-semibold tracking-wide transition-colors flex-shrink-0">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            {{-- /right column --}}

        </div>
        {{-- /grid --}}

    </div>
</div>

@endsection