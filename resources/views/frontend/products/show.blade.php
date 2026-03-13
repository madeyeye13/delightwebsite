{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  FRONTEND — PRODUCT DETAIL PAGE                                              ║
║                                                                              ║
║  IMPROVEMENTS v2:                                                            ║
║   • Gallery: thumbnails below main image (row, max 4 visible)               ║
║   • Gallery: variant switching updates main image correctly                 ║
║   • Gallery: fallback to $p['images'] if variant images empty               ║
║   • Colour swatches: smaller (w-6 h-6) pill style                          ║
║   • Quantity: capped at stockQuantity with live feedback                    ║
║   • Add-ons: selectable inline, price updates dynamically                   ║
║   • Cart payload: computed cartPayload object ready for backend             ║
║   • Trust/meta strip: category, method, availability, unit                  ║
║   • All existing sellingMethod logic preserved untouched                    ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('content')

{{-- ── Alpine store init: theme (dark mode) ───────────────────────────────── --}}
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
    // Apply dark class immediately to avoid flash of unstyled content
    (function() {
        var saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

@php

// ── MOCK DATA ─────────────────────────────────────────────────────────────────
$mock = [
    'name'          => 'Premium French Lace Fabric',
    'slug'          => 'premium-french-lace-fabric',
    'category'      => 'Lace Fabrics',
    'description'   => 'A premium-grade French lace fabric crafted for the modern Nigerian woman. Features intricate floral embroidery with a soft-feel base fabric. Perfect for owambe occasions, aso-ebi co-ordination and bridal wear.',
    'sellingMethod' => 'per-length',
    'unitsPerOrder' => 5,
    'unitLabel'     => 'yards',
    'lengthUnit'    => 'yards',
    'minQuantity'   => 1,
    'quantityStep'  => 1,
    'loomSize'      => null,
    'setContents'   => [],
    'bundleYield'   => [],
    'includedItems' => [
        ['name' => 'French Lace Fabric', 'quantity' => 1],
        ['name' => 'Quality Packaging',  'quantity' => 1],
    ],
    'excludesText'  => 'Accessories, bags and shoes used for styling are not included.',
    'price'         => 28500,
    'comparePrice'  => 34000,
    'discountType'  => 'percent',
    'discountValue' => 16,
    'stockQuantity' => 8,
    'images'        => [],  // ← BACKEND: $product->images — fallback when no variants
    'variants'  => [
        ['color' => 'Ivory White',  'hex' => '#F5F0E8', 'images' => ['images/products/lace-ivory-1.jpg','images/products/lace-ivory-2.jpg','images/products/lace-ivory-3.jpg']],
        ['color' => 'Royal Blue',   'hex' => '#2C4A8F', 'images' => ['images/products/lace-blue-1.jpg','images/products/lace-blue-2.jpg']],
        ['color' => 'Champagne',    'hex' => '#C9A96E', 'images' => ['images/products/lace-champ-1.jpg','images/products/lace-champ-2.jpg']],
        ['color' => 'Forest Green', 'hex' => '#1F6F67', 'images' => ['images/products/lace-green-1.jpg']],
    ],
    // addOns: related products from your catalog, NOT service charges.
    // Each entry mirrors your product architecture so they can be added
    // as independent cart line items (not merged into the main product price).
    'addOns' => [
        [
            'id'            => 1,
            'slug'          => 'matching-headtie',
            'name'          => 'Matching Headtie',
            'image'         => 'images/products/headtie.jpg',
            'price'         => 8000,
            'category'      => 'Headties',
            'sellingMethod' => 'per-piece',
            'unitLabel'     => 'piece',
            'stockQuantity' => 12,
            'minQuantity'   => 1,
            'quantityStep'  => 1,
        ],
        [
            'id'            => 2,
            'slug'          => 'matching-gele',
            'name'          => 'Matching Gele',
            'image'         => 'images/products/gele.jpg',
            'price'         => 12000,
            'category'      => 'Accessories',
            'sellingMethod' => 'per-piece',
            'unitLabel'     => 'piece',
            'stockQuantity' => 8,
            'minQuantity'   => 1,
            'quantityStep'  => 1,
        ],
        [
            'id'            => 3,
            'slug'          => 'lining-fabric',
            'name'          => 'Lining Fabric',
            'image'         => 'images/products/lining.jpg',
            'price'         => 5000,
            'category'      => 'Linings',
            'sellingMethod' => 'per-length',
            'unitLabel'     => 'yards',
            'stockQuantity' => 20,
            'minQuantity'   => 1,
            'quantityStep'  => 1,
        ],
    ],
];

// ── NORMALISE ─────────────────────────────────────────────────────────────────
// Supports: null (use mock) | array | Eloquent model with cast/JSON fields.
// toSafeArray() decodes JSON strings and flattens Eloquent Collection/model to array.
$toSafeArray = function($val, $default = []) {
    if (is_null($val))                    return $default;
    if (is_array($val))                   return $val;
    if (is_string($val))                  { $decoded = json_decode($val, true); return is_array($decoded) ? $decoded : $default; }
    if ($val instanceof \Illuminate\Support\Collection) return $val->toArray();
    if (is_object($val) && method_exists($val, 'toArray')) return $val->toArray();
    return $default;
};

if (!isset($product) || $product === null) {
    $p = $mock;
} elseif (is_array($product)) {
    $p = array_merge($mock, $product);
} else {
    $p = [
        'name'          => (string)  ($product->name          ?? $mock['name']),
        'slug'          => (string)  ($product->slug          ?? $mock['slug']),
        'category'      => (string)  ($product->category      ?? $mock['category']),
        'description'   => (string)  ($product->description   ?? $mock['description']),
        'sellingMethod' => (string)  ($product->sellingMethod ?? $mock['sellingMethod']),
        'unitsPerOrder' => (int)     ($product->unitsPerOrder ?? $mock['unitsPerOrder']),
        'unitLabel'     => (string)  ($product->unitLabel     ?? $mock['unitLabel']),
        'lengthUnit'    => (string)  ($product->lengthUnit    ?? $mock['lengthUnit']),
        // minQuantity: minimum units a customer must order (e.g. 1 set, 2 pieces)
        'minQuantity'   => max(1, (int) ($product->minQuantity  ?? $mock['minQuantity'])),
        // quantityStep: how many units are added/removed per click (e.g. 1, 2, 5)
        'quantityStep'  => max(1, (int) ($product->quantityStep ?? $mock['quantityStep'])),
        'loomSize'      => $product->loomSize ?? null,
        // Nested relation fields — safe regardless of cast type
        'setContents'   => $toSafeArray($product->setContents   ?? null, []),
        'bundleYield'   => $toSafeArray($product->bundleYield   ?? null, []),
        'includedItems' => $toSafeArray($product->includedItems ?? null, []),
        'excludesText'  => (string)  ($product->excludesText  ?? ''),
        'price'         => max(0, (float) ($product->price         ?? 0)),
        'comparePrice'  => max(0, (float) ($product->comparePrice  ?? 0)),
        'discountType'  => $product->discountType  ?? null,
        'discountValue' => max(0, (float) ($product->discountValue ?? 0)),
        // stockQuantity: total orderable units in stock across all variants.
        // For per-length: number of "unit blocks" (e.g. 8 means 8 × 5yards = 40 yards).
        // For per-piece/set/bundle/loom: number of individual sellable units.
        'stockQuantity' => max(0, (int) ($product->stockQuantity ?? 0)),
        'images'        => $toSafeArray($product->images   ?? null, []),
        'variants'      => $toSafeArray($product->variants ?? null, []),
        'addOns'        => $toSafeArray($product->addOns   ?? null, []),
    ];
}

// ── COMPUTED VALUES ────────────────────────────────────────────────────────────
$finalPrice = (float) $p['price'];
if ($p['discountType'] === 'percent' && $p['discountValue'] > 0) {
    $finalPrice = $finalPrice * (1 - $p['discountValue'] / 100);
} elseif ($p['discountType'] === 'fixed' && $p['discountValue'] > 0) {
    $finalPrice = max(0.0, $finalPrice - $p['discountValue']);
}
$finalPriceInt = (int) round($finalPrice);

$stock = (int) $p['stockQuantity'];
$stockStatus = $stock <= 0 ? 'out' : ($stock <= 10 ? 'low' : 'high');

$hasDiscount     = !empty($p['discountType']) && $p['discountValue'] > 0;
$hasComparePrice = ($p['comparePrice'] ?? 0) > $p['price'];
$hasVariants     = count((array) $p['variants']) > 1;
$hasAddOns       = !empty($p['addOns']);
$hasIncluded     = !empty($p['includedItems']);
$hasExcludes     = !empty($p['excludesText']);

@endphp

{{-- ══════════════════════════════════════════════════════════════════════════
     PAGE ROOT
══════════════════════════════════════════════════════════════════════════════ --}}
<script>
window.__pdp = {
    // ── Product identity ─────────────────────────────────────────────────────
    slug:           "{{ e($p['slug']) }}",
    name:           "{{ e($p['name']) }}",
    category:       "{{ e($p['category']) }}",

    // ── Selling architecture ──────────────────────────────────────────────────
    // sellingMethod: per-length | per-piece | per-set | per-bundle | per-loom
    sellingMethod:  "{{ e($p['sellingMethod']) }}",
    unitLabel:      "{{ e($p['unitLabel']) }}",
    lengthUnit:     "{{ e($p['lengthUnit']) }}",
    unitsPerOrder:  {{ (int) $p['unitsPerOrder'] }},
    loomSize:       {{ $p['loomSize'] ? '"' . e($p['loomSize']) . '"' : 'null' }},

    // ── Quantity rules ────────────────────────────────────────────────────────
    minQty:         {{ (int) $p['minQuantity'] }},
    qtyStep:        {{ (int) $p['quantityStep'] }},
    // stockQty: orderable unit count. Per-length = unit-blocks (e.g. 8 × 5yds = 40yds).
    // Per-piece/set/bundle/loom = actual item count.
    stockQty:       {{ $stock }},

    // ── Pricing ───────────────────────────────────────────────────────────────
    basePrice:      {{ $finalPriceInt }},

    // ── Gallery ───────────────────────────────────────────────────────────────
    variants:       {!! json_encode(array_values((array) $p['variants'])) !!},
    images:         {!! json_encode(array_values((array) $p['images'])) !!},

    // ── Related catalog products (NOT service charges) ────────────────────────
    // Each entry is a full product record from your catalog.
    // When selected, each becomes its own cart line item.
    addOns:         {!! json_encode(array_values((array) $p['addOns'])) !!},
};
</script>
<div
    x-data="productDetail()"
    class="bg-[#FCFCF9] dark:bg-ink min-h-screen pt-40"
>

{{-- BREADCRUMB ──────────────────────────────────────────────────────────── --}}
<nav class="border-b border-neutral-100 dark:border-neutral-800 bg-[#FCFCF9] dark:bg-ink" aria-label="Breadcrumb">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-3 flex items-center gap-2 flex-wrap">
        <a href="{{ url('/') }}" class="font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand transition-colors">Home</a>
        <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-700 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ url('/shop') }}" class="font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand transition-colors">Shop</a>
        <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-700 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ url('/shop?category=' . Str::slug($p['category'])) }}"
           class="font-sans text-xs text-neutral-400 dark:text-neutral-500 hover:text-brand transition-colors">
            {{ $p['category'] }}
        </a>
        <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-700 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="font-sans text-xs text-neutral-800 dark:text-neutral-200 font-medium truncate max-w-[200px]">{{ $p['name'] }}</span>
    </div>
</nav>

{{-- MAIN PRODUCT SECTION ────────────────────────────────────────────────── --}}
<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16 py-10 lg:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-16">

        {{-- LEFT: Gallery ────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-3">

            {{-- Main image --}}
            <div class="relative overflow-hidden bg-neutral-50 dark:bg-neutral-900 aspect-[4/5]">
                <template x-for="(img, idx) in currentImages" :key="'mi-' + idx">
                    <div x-show="activeImage === idx"
                         x-transition:enter="transition-opacity duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0">
                        <img :src="img" :alt="'{{ addslashes($p['name']) }} ' + (idx + 1)"
                             class="w-full h-full object-cover object-center"
                             onerror="this.src='https://placehold.co/600x750/F3F3F3/A3A3A3?text=No+Image'" />
                    </div>
                </template>

                @if($hasDiscount)
                <span class="absolute top-4 left-4 z-10 font-sans text-2xs font-semibold tracking-wider uppercase px-2.5 py-1 bg-brand text-white pointer-events-none">
                    @if($p['discountType'] === 'percent'){{ $p['discountValue'] }}% OFF@else SALE @endif
                </span>
                @endif

                <button @click="prevImage()" x-show="currentImages.length > 1"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white/80 dark:bg-ink/80 flex items-center justify-center"
                        aria-label="Previous">
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <button @click="nextImage()" x-show="currentImages.length > 1"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white/80 dark:bg-ink/80 flex items-center justify-center"
                        aria-label="Next">
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>

                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10 sm:hidden" x-show="currentImages.length > 1">
                    <template x-for="(img, idx) in currentImages" :key="'dot-' + idx">
                        <button @click="activeImage = idx"
                                :class="activeImage === idx ? 'bg-white w-4' : 'bg-white/50 w-1.5'"
                                class="h-1.5 transition-all duration-200"></button>
                    </template>
                </div>
            </div>

            {{-- Thumbnail row — max 4, below main image --}}
            <div x-show="currentImages.length >= 1" class="grid grid-cols-4 gap-2">
                <template x-for="(img, idx) in currentImages.slice(0, 4)" :key="'th-' + idx">
                    <button
                        @click="activeImage = idx"
                        :class="activeImage === idx
                            ? 'border-brand dark:border-brand-400 opacity-100'
                            : 'border-neutral-200 dark:border-neutral-700 opacity-50 hover:opacity-100'"
                        class="aspect-square border-2 overflow-hidden transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1"
                    >
                        <img :src="img" :alt="'Thumbnail ' + (idx + 1)"
                             class="w-full h-full object-cover object-center"
                             onerror="this.src='https://placehold.co/120x120/F3F3F3/A3A3A3?text=IMG'" />
                    </button>
                </template>
            </div>

        </div>

        {{-- RIGHT: Details ───────────────────────────────────────────── --}}
        <div class="flex flex-col gap-6">

            {{-- Category + stock badge --}}
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand dark:text-brand-300">
                    {{ $p['category'] }}
                </span>

                @if($stockStatus === 'out')
                <span class="inline-flex items-center gap-1.5 font-sans text-2xs font-semibold tracking-wide uppercase px-2.5 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>Out of Stock
                </span>
                @elseif($stockStatus === 'low')
                <span class="inline-flex items-center gap-1.5 font-sans text-2xs font-semibold tracking-wide uppercase px-2.5 py-1 bg-accent-50 dark:bg-accent-500/10 text-accent-600 dark:text-accent-300 border border-accent-200 dark:border-accent-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-400 animate-pulse flex-shrink-0"></span>Only {{ $stock }} left
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 font-sans text-2xs font-semibold tracking-wide uppercase px-2.5 py-1 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-300 border border-brand-200 dark:border-brand-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>In Stock
                </span>
                @endif
            </div>

            {{-- Name --}}
            <h1 class="font-display text-2xl md:text-3xl font-bold text-neutral-900 dark:text-white leading-tight tracking-tight">
                {{ $p['name'] }}
            </h1>

            {{-- Price — driven by Alpine grandTotal --}}
            <div class="flex items-end gap-3 flex-wrap">
                <span class="font-display text-3xl font-extrabold text-neutral-900 dark:text-white leading-none tracking-tighter">
                    &#8358;<span x-text="grandTotal.toLocaleString()"></span>
                </span>
                @if($hasComparePrice)
                <span class="font-sans text-base text-neutral-400 dark:text-neutral-500 line-through leading-none">
                    &#8358;{{ number_format($p['comparePrice']) }}
                </span>
                @endif
                @if($hasDiscount)
                <span class="font-sans text-xs font-semibold text-brand dark:text-brand-300 bg-brand-50 dark:bg-brand-900/30 px-2 py-0.5">
                    @if($p['discountType'] === 'percent')&minus;{{ $p['discountValue'] }}%@else&minus;&#8358;{{ number_format($p['discountValue']) }}@endif
                </span>
                @endif
                {{-- Add-ons are separate catalog products / cart lines — not merged into main price --}}
            </div>

            {{-- Short description --}}
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                {{ Str::limit($p['description'], 220) }}
            </p>

            {{-- ── TRUST / META STRIP ───────────────────────────────────── --}}
            <div class="grid grid-cols-2 gap-x-4 gap-y-3 border-y border-neutral-100 dark:border-neutral-800 py-4">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0"><path d="M4 6h16M4 12h16M4 18h7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <div>
                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Category</p>
                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ $p['category'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0"><rect x="2" y="7" width="20" height="14" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="1.5"/></svg>
                    <div>
                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Sold As</p>
                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300 capitalize">{{ str_replace('-', ' ', $p['sellingMethod']) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0"><path d="M21 10H3M16 2v4M8 2v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/></svg>
                    <div>
                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Unit</p>
                        <p class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ $p['unitLabel'] ?: ucfirst(str_replace('per-', '', $p['sellingMethod'])) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($stockStatus === 'out')
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-red-400 flex-shrink-0"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    @else
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-brand flex-shrink-0"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @endif
                    <div>
                        <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider leading-none mb-0.5">Availability</p>
                        @if($stockStatus === 'out')
                        <p class="font-sans text-xs font-medium text-red-500">Unavailable</p>
                        @elseif($stockStatus === 'low')
                        <p class="font-sans text-xs font-medium text-accent-600 dark:text-accent-400">{{ $stock }} remaining</p>
                        @else
                        <p class="font-sans text-xs font-medium text-brand">Available</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Colour variants — smaller swatches --}}
            @if($hasVariants)
            <div>
                <p class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 mb-2.5">
                    Colour:&nbsp;<span class="text-neutral-900 dark:text-white font-semibold normal-case tracking-normal" x-text="variants[activeVariant] ? variants[activeVariant].color : ''"></span>
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach((array) $p['variants'] as $vi => $variant)
                    @php
                        $lightHex = ['#F5F0E8','#FFFFFF','#F0F0F0','#FAF9F6'];
                        $tickClass = in_array(strtoupper($variant['hex']), array_map('strtoupper', $lightHex)) ? 'text-neutral-700' : 'text-white';
                    @endphp
                    <button
                        @click="switchVariant({{ $vi }})"
                        :class="activeVariant === {{ $vi }}
                            ? 'ring-2 ring-offset-2 ring-brand dark:ring-offset-ink'
                            : 'ring-1 ring-neutral-200 dark:ring-neutral-700 hover:ring-neutral-400'"
                        class="relative w-6 h-6 rounded-full transition-all duration-200 focus:outline-none flex-shrink-0"
                        style="background-color: {{ $variant['hex'] }};"
                        title="{{ $variant['color'] }}"
                        aria-label="Select {{ $variant['color'] }}"
                    >
                        <template x-if="activeVariant === {{ $vi }}">
                            <svg class="w-2.5 h-2.5 absolute inset-0 m-auto {{ $tickClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </template>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Selling details --}}
            <div class="border border-neutral-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/40 p-4 space-y-2.5">

                @if($p['sellingMethod'] === 'per-length')
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Sold Per</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['unitsPerOrder'] }} {{ $p['lengthUnit'] }}</span></div>
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Min. Order</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['minQuantity'] }} {{ $p['minQuantity'] == 1 ? 'unit' : 'units' }}</span></div>
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Step</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['quantityStep'] }} unit(s) at a time</span></div>

                @elseif($p['sellingMethod'] === 'per-set')
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Sold Per</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['unitLabel'] ?: 'Set' }}</span></div>
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Min. Order</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['minQuantity'] }} {{ Str::plural($p['unitLabel'] ?: 'set', $p['minQuantity']) }}</span></div>
                    @if(!empty($p['setContents']))
                    <div class="pt-2 border-t border-neutral-100 dark:border-neutral-800">
                        <p class="font-sans text-2xs font-semibold tracking-widest uppercase text-neutral-400 dark:text-neutral-500 mb-2">1 {{ $p['unitLabel'] ?: 'set' }} contains</p>
                        @foreach((array) $p['setContents'] as $item)
                        <div class="flex items-center gap-2 mt-1"><span class="w-1 h-1 rounded-full bg-brand flex-shrink-0"></span><span class="font-sans text-xs text-neutral-700 dark:text-neutral-300">{{ $item['name'] }} &times;{{ $item['quantity'] }}</span></div>
                        @endforeach
                    </div>
                    @endif

                @elseif($p['sellingMethod'] === 'per-bundle')
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Sold Per</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['unitLabel'] ?: 'Bundle' }}</span></div>
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Min. Order</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['minQuantity'] }} {{ Str::plural($p['unitLabel'] ?: 'bundle', $p['minQuantity']) }}</span></div>
                    @if(!empty($p['bundleYield']))
                    <div class="pt-2 border-t border-neutral-100 dark:border-neutral-800">
                        <p class="font-sans text-2xs font-semibold tracking-widest uppercase text-neutral-400 dark:text-neutral-500 mb-2">1 {{ $p['unitLabel'] ?: 'bundle' }} gives you</p>
                        @foreach((array) $p['bundleYield'] as $item)
                        <div class="flex items-center gap-2 mt-1"><span class="w-1 h-1 rounded-full bg-brand flex-shrink-0"></span><span class="font-sans text-xs text-neutral-700 dark:text-neutral-300">{{ $item['name'] }} &times;{{ $item['quantity'] }}</span></div>
                        @endforeach
                    </div>
                    @endif

                @elseif($p['sellingMethod'] === 'per-piece')
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Sold Per</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['unitLabel'] ?: 'Piece' }}</span></div>
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Min. Order</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['minQuantity'] }} {{ Str::plural($p['unitLabel'] ?: 'piece', $p['minQuantity']) }}</span></div>

                @elseif($p['sellingMethod'] === 'per-loom')
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Sold Per</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['unitLabel'] ?: 'Loom' }}</span></div>
                    @if(!empty($p['loomSize']))
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Loom Size</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['loomSize'] }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="font-sans text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Min. Order</span><span class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $p['minQuantity'] }} {{ Str::plural($p['unitLabel'] ?: 'loom', $p['minQuantity']) }}</span></div>

                @else
                    <p class="font-sans text-xs text-neutral-500 dark:text-neutral-400">Selling method: {{ $p['sellingMethod'] }}</p>
                @endif
            </div>

            {{-- Quantity selector + live summary --}}
            @if($stockStatus !== 'out')
            <div class="space-y-3">
                <div class="flex items-center gap-4">
                    <span class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400 w-20 flex-shrink-0">Quantity</span>
                    <div class="flex items-center border border-neutral-200 dark:border-neutral-700">
                        <button @click="decreaseQty()" :disabled="qty <= minQty"
                                class="w-10 h-10 flex items-center justify-center text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                aria-label="Decrease quantity">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                        <span x-text="qty" class="w-12 text-center font-sans text-sm font-semibold text-neutral-900 dark:text-white select-none" aria-live="polite"></span>
                        <button @click="increaseQty()" :disabled="qty >= stockQty"
                                class="w-10 h-10 flex items-center justify-center text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                aria-label="Increase quantity">
                            <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                    <span x-show="qty >= stockQty" class="font-sans text-2xs text-accent-600 dark:text-accent-400 font-medium">Max reached</span>
                    <span x-show="qty < stockQty && stockQty <= 10" class="font-sans text-2xs text-neutral-400 dark:text-neutral-500" x-text="(stockQty - qty) + ' left'"></span>
                </div>

                {{-- ── Quantity buying hints ── --}}
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                    @if($p['minQuantity'] > 1)
                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                        Min. order: <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $p['minQuantity'] }} {{ $p['minQuantity'] == 1 ? 'unit' : 'units' }}</span>
                    </span>
                    @endif
                    @if($p['quantityStep'] > 1)
                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                        In steps of: <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $p['quantityStep'] }}</span>
                    </span>
                    @endif
                    @if($p['sellingMethod'] === 'per-length' && $p['unitsPerOrder'] > 1)
                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                        1 unit = <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $p['unitsPerOrder'] }} {{ $p['lengthUnit'] }}</span>
                    </span>
                    @endif
                </div>

                @if($p['sellingMethod'] === 'per-length')
                {{-- Per-length live summary --}}
                <div class="bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-800/40 px-4 py-3">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="font-sans text-xs text-brand-700 dark:text-brand-300 font-medium">Order Summary</span>
                        <div class="flex items-center gap-4 sm:gap-6">
                            <div class="text-right">
                                <p class="font-sans text-2xs text-brand-500 dark:text-brand-400 uppercase tracking-wider">Units</p>
                                <p class="font-display text-sm font-bold text-brand-700 dark:text-brand-200" x-text="qty"></p>
                            </div>
                            <div class="w-px h-7 bg-brand-200 dark:bg-brand-700 flex-shrink-0"></div>
                            <div class="text-right">
                                <p class="font-sans text-2xs text-brand-500 dark:text-brand-400 uppercase tracking-wider">Total Fabric</p>
                                <p class="font-display text-sm font-bold text-brand-700 dark:text-brand-200"><span x-text="totalFabric"></span> <span class="font-normal text-xs">{{ $p['lengthUnit'] }}</span></p>
                            </div>
                            <div class="w-px h-7 bg-brand-200 dark:bg-brand-700 flex-shrink-0"></div>
                            <div class="text-right">
                                <p class="font-sans text-2xs text-brand-500 dark:text-brand-400 uppercase tracking-wider">Total Price</p>
                                <p class="font-display text-sm font-bold text-brand-700 dark:text-brand-200">&#8358;<span x-text="grandTotal.toLocaleString()"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                {{-- Generic live summary --}}
                <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 px-4 py-3 flex items-center justify-between flex-wrap gap-3">
                    <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">
                        <span class="font-semibold text-neutral-800 dark:text-white" x-text="qty"></span> &times; {{ $p['unitLabel'] ?: 'unit' }}
                    </span>
                    <span class="font-sans text-xs text-neutral-500 dark:text-neutral-400">
                        Total: <span class="font-semibold text-neutral-800 dark:text-white">&#8358;<span x-text="grandTotal.toLocaleString()"></span></span>
                    </span>
                </div>
                @endif
            </div>
            @endif

            {{-- ── INLINE ADD-ONS ───────────────────────────────────────── --}}
            @if($hasAddOns)
            <div class="space-y-2.5">
                <p class="font-sans text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400">Complete the Look</p>
                <div class="space-y-2">
                    @foreach((array) $p['addOns'] as $addon)
                    <div
                        @click="toggleAddOn({{ $addon['id'] }})"
                        :class="isAddOnSelected({{ $addon['id'] }})
                            ? 'border-brand dark:border-brand-400 bg-brand-50 dark:bg-brand-900/20'
                            : 'border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900/30 hover:border-neutral-300 dark:hover:border-neutral-600'"
                        class="flex items-center gap-3 p-2.5 border cursor-pointer transition-all duration-200 select-none"
                    >
                        <div class="w-10 h-10 flex-shrink-0 overflow-hidden bg-neutral-100 dark:bg-neutral-800">
                            <img src="{{ asset($addon['image']) }}" alt="{{ $addon['name'] }}"
                                 class="w-full h-full object-cover object-center"
                                 onerror="this.src='https://placehold.co/40x40/F3F3F3/A3A3A3?text=+'" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-sans text-xs font-medium text-neutral-800 dark:text-white truncate">{{ $addon['name'] }}</p>
                            <p class="font-sans text-xs text-neutral-500 dark:text-neutral-400">
                                &#8358;{{ number_format($addon['price']) }}
                                @if(!empty($addon['unitLabel'])) <span class="text-neutral-400 dark:text-neutral-600">&middot; per {{ $addon['unitLabel'] }}</span>@endif
                            </p>
                        </div>
                        <div :class="isAddOnSelected({{ $addon['id'] }}) ? 'bg-brand border-brand' : 'bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-600'"
                             class="w-4 h-4 border flex-shrink-0 flex items-center justify-center transition-colors duration-200">
                            <svg x-show="isAddOnSelected({{ $addon['id'] }})" class="w-2.5 h-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p x-show="selectedAddOns.length > 0" class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">
                    <span x-text="selectedAddOns.length"></span> item(s) will be added as separate cart lines
                </p>
            </div>
            @endif

            {{-- ── Selected related products — each becomes its own cart line ── --}}
            <div x-show="selectedAddOns.length > 0"
                 class="border border-neutral-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/40 p-3 space-y-1.5">
                <p class="font-sans text-2xs font-semibold tracking-wider uppercase text-neutral-400 dark:text-neutral-500">Also Adding to Cart</p>
                <template x-for="ao in selectedAddOns" :key="ao.id">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1 h-1 rounded-full bg-brand flex-shrink-0"></span>
                            <div>
                                <span class="font-sans text-xs text-neutral-700 dark:text-neutral-300" x-text="ao.name"></span>
                                <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 ml-1" x-text="ao.unitLabel ? '· per ' + ao.unitLabel : ''"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-sans text-xs font-medium text-neutral-700 dark:text-neutral-300">&#8358;<span x-text="ao.price.toLocaleString()"></span></span>
                            <button @click="toggleAddOn(ao.id)"
                                    class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                    aria-label="Remove">
                                <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 pt-1 border-t border-neutral-100 dark:border-neutral-800">
                    Each item above will be a separate line in your cart
                </p>
            </div>

            {{-- CTA buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-1">
                @if($stockStatus !== 'out')
                <button @click="addToCart()" class="flex-1 inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-brand hover:bg-brand-600 active:bg-brand-700 text-white font-sans text-sm font-semibold tracking-wide transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 dark:focus:ring-offset-ink">
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M5 6.5h16l-2.024 10H7.024L5 6.5Zm0 0L4.364 3H1" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="20.5" r="1" stroke="currentColor" stroke-width="1.4"/><circle cx="17.5" cy="20.5" r="1" stroke="currentColor" stroke-width="1.4"/></svg>
                    Add to Cart
                </button>
                <button @click="buyNow()" class="flex-1 inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-neutral-900 dark:bg-white hover:bg-neutral-700 dark:hover:bg-neutral-100 text-white dark:text-neutral-900 font-sans text-sm font-semibold tracking-wide transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:ring-offset-2 dark:focus:ring-offset-ink">
                    Buy Now
                </button>
                @else
                <button disabled class="flex-1 inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-neutral-200 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500 font-sans text-sm font-semibold tracking-wide cursor-not-allowed">Out of Stock</button>
                <button class="flex-1 inline-flex items-center justify-center gap-2.5 px-6 py-3.5 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 font-sans text-sm font-semibold tracking-wide hover:border-brand hover:text-brand transition-colors duration-200">Notify Me</button>
                @endif
            </div>

            {{-- Secondary actions --}}
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
</section>

{{-- TABS SECTION ──────────────────────────────────────────────────────────── --}}
<section class="border-t border-neutral-100 dark:border-neutral-800">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16">

        @php
        $tabs = ['details' => 'Details', 'includes' => 'What&#39;s Included'];
        if ($hasAddOns) $tabs['addons'] = 'Complete the Look';
        $tabs['reviews'] = 'Reviews';
        @endphp

        <div class="flex border-b border-neutral-100 dark:border-neutral-800 overflow-x-auto" role="tablist">
            @foreach($tabs as $tk => $tl)
            <button
                @click="activeTab = '{{ $tk }}'"
                :class="activeTab === '{{ $tk }}'
                    ? 'border-b-2 border-brand text-brand dark:text-brand-300 font-semibold'
                    : 'border-b-2 border-transparent text-neutral-500 dark:text-neutral-400 hover:text-neutral-800 dark:hover:text-white'"
                class="flex-shrink-0 px-5 py-4 font-sans text-sm transition-colors duration-200 focus:outline-none"
                role="tab"
            >{!! $tl !!}</button>
            @endforeach
        </div>

        <div class="py-10">

            {{-- DETAILS TAB --}}
            <div x-show="activeTab === 'details'" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="max-w-2xl space-y-8">
                    <p class="font-sans text-base text-neutral-700 dark:text-neutral-300 leading-relaxed">{{ $p['description'] }}</p>
                    <div>
                        <h3 class="font-display text-md font-semibold text-neutral-900 dark:text-white mb-4">Selling Information</h3>
                        <div class="grid grid-cols-2 gap-x-8 gap-y-4 border-t border-neutral-100 dark:border-neutral-800 pt-4">
                            <div><p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">Method</p><p class="font-sans text-sm text-neutral-800 dark:text-white font-medium capitalize">{{ str_replace('-', ' ', $p['sellingMethod']) }}</p></div>
                            <div><p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">Unit</p><p class="font-sans text-sm text-neutral-800 dark:text-white font-medium">{{ $p['unitLabel'] ?: ucfirst(str_replace('per-', '', $p['sellingMethod'])) }}</p></div>
                            @if($p['sellingMethod'] === 'per-length')
                            <div><p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">Per Unit</p><p class="font-sans text-sm text-neutral-800 dark:text-white font-medium">{{ $p['unitsPerOrder'] }} {{ $p['lengthUnit'] }}</p></div>
                            @endif
                            @if($p['sellingMethod'] === 'per-loom' && !empty($p['loomSize']))
                            <div><p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">Loom Size</p><p class="font-sans text-sm text-neutral-800 dark:text-white font-medium">{{ $p['loomSize'] }}</p></div>
                            @endif
                            <div><p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">Min. Order</p><p class="font-sans text-sm text-neutral-800 dark:text-white font-medium">{{ $p['minQuantity'] }} {{ $p['minQuantity'] == 1 ? 'unit' : 'units' }}</p></div>
                            <div><p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">Category</p><p class="font-sans text-sm text-neutral-800 dark:text-white font-medium">{{ $p['category'] }}</p></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INCLUDED TAB --}}
            <div x-show="activeTab === 'includes'" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">
                    @if($hasIncluded)
                    <div class="border border-brand-100 dark:border-brand-800/40 bg-brand-50 dark:bg-brand-900/10 p-6">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-6 h-6 bg-brand flex items-center justify-center flex-shrink-0">
                                <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-white"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2.5"/></svg>
                            </div>
                            <h3 class="font-display text-md font-semibold text-neutral-900 dark:text-white">What&#39;s Included</h3>
                        </div>
                        <div class="space-y-2.5">
                            @foreach((array) $p['includedItems'] as $item)
                            <div class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>
                                <span class="font-sans text-sm text-neutral-700 dark:text-neutral-300">
                                    {{ $item['name'] }}@if(($item['quantity'] ?? 1) > 1)<span class="text-brand dark:text-brand-300 font-semibold ml-1">&times;{{ $item['quantity'] }}</span>@endif
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($hasExcludes)
                    <div class="border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/40 p-6">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-6 h-6 bg-neutral-200 dark:bg-neutral-700 flex items-center justify-center flex-shrink-0">
                                <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5 text-neutral-500 dark:text-neutral-400"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/></svg>
                            </div>
                            <h3 class="font-display text-md font-semibold text-neutral-900 dark:text-white">Not Included</h3>
                        </div>
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed italic">{{ $p['excludesText'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ADD-ONS TAB --}}
            @if($hasAddOns)
            <div x-show="activeTab === 'addons'" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 mb-8">Frequently paired with <span class="text-neutral-800 dark:text-white font-medium">{{ $p['name'] }}</span></p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach((array) $p['addOns'] as $addon)
                    <article
                        @click="toggleAddOn({{ $addon['id'] }})"
                        :class="isAddOnSelected({{ $addon['id'] }}) ? 'ring-2 ring-brand dark:ring-brand-400' : 'ring-0'"
                        class="group cursor-pointer transition-all duration-200"
                    >
                        <div class="relative overflow-hidden aspect-[3/4] bg-neutral-50 dark:bg-neutral-900 mb-3">
                            <img src="{{ asset($addon['image']) }}" alt="{{ $addon['name'] }}"
                                 class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-[1.04]"
                                 onerror="this.src='https://placehold.co/300x400/F3F3F3/A3A3A3?text={{ urlencode($addon['name']) }}'"
                                 loading="lazy" />
                            <div :class="isAddOnSelected({{ $addon['id'] }}) ? 'translate-y-0' : 'translate-y-full group-hover:translate-y-0'"
                                 class="absolute inset-x-0 bottom-0 transition-transform duration-300 bg-brand/90 py-2.5 flex items-center justify-center gap-1.5">
                                <svg x-show="!isAddOnSelected({{ $addon['id'] }})" class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                <svg x-show="isAddOnSelected({{ $addon['id'] }})" class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span class="font-sans text-xs font-semibold text-white tracking-wide" x-text="isAddOnSelected({{ $addon['id'] }}) ? 'Added' : 'Add'"></span>
                            </div>
                        </div>
                        <h4 class="font-sans text-sm font-medium text-neutral-800 dark:text-white leading-snug mb-1 group-hover:text-brand dark:group-hover:text-brand-300 transition-colors">{{ $addon['name'] }}</h4>
                        <p class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">&#8358;{{ number_format($addon['price']) }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- REVIEWS TAB --}}
            <div x-show="activeTab === 'reviews'" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="max-w-2xl space-y-8">

                    {{-- Placeholder — wire to your Livewire reviews component later --}}
                    {{-- <livewire:product.reviews :product-id="$p['slug']" /> --}}

                    <div class="space-y-6">
                        {{-- Summary bar --}}
                        <div class="flex items-center gap-6 pb-6 border-b border-neutral-100 dark:border-neutral-800">
                            <div class="text-center flex-shrink-0">
                                <p class="font-display text-5xl font-extrabold text-neutral-900 dark:text-white leading-none">4.8</p>
                                <div class="flex items-center justify-center gap-0.5 mt-1.5">
                                    @for($s = 1; $s <= 5; $s++)
                                    <svg class="w-3.5 h-3.5 {{ $s <= 4 ? 'text-brand' : 'text-neutral-200 dark:text-neutral-700' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @endfor
                                </div>
                                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 mt-1">24 reviews</p>
                            </div>
                            <div class="flex-1 space-y-1.5">
                                @foreach([5=>83, 4=>10, 3=>4, 2=>2, 1=>1] as $star => $pct)
                                <div class="flex items-center gap-2">
                                    <span class="font-sans text-2xs text-neutral-500 dark:text-neutral-400 w-2">{{ $star }}</span>
                                    <div class="flex-1 h-1.5 bg-neutral-100 dark:bg-neutral-800">
                                        <div class="h-full bg-brand" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 w-6 text-right">{{ $pct }}%</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Individual reviews --}}
                        @foreach([
                            ['name' => 'Adaeze O.', 'rating' => 5, 'date' => '2 weeks ago', 'text' => 'Absolutely stunning fabric. The quality exceeded my expectations — the lace pattern is intricate and the base fabric is soft. Will definitely order again for my next owambe.'],
                            ['name' => 'Funmi B.',  'rating' => 5, 'date' => '1 month ago',  'text' => 'My tailor loved working with this. The colour is exactly as pictured and the packaging was very neat. Fast delivery too.'],
                            ['name' => 'Ngozi K.',  'rating' => 4, 'date' => '2 months ago', 'text' => 'Great quality overall. Only reason for 4 stars is I wished the ivory was slightly brighter, but the fabric itself is premium and well worth the price.'],
                        ] as $review)
                        <div class="pb-6 border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <p class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $review['name'] }}</p>
                                    <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">{{ $review['date'] }}</p>
                                </div>
                                <div class="flex items-center gap-0.5 flex-shrink-0">
                                    @for($s = 1; $s <= 5; $s++)
                                    <svg class="w-3 h-3 {{ $s <= $review['rating'] ? 'text-brand' : 'text-neutral-200 dark:text-neutral-700' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ $review['text'] }}</p>
                        </div>
                        @endforeach

                        {{-- Write a review CTA — wire to Livewire modal later --}}
                        <button class="inline-flex items-center gap-2 font-sans text-sm font-semibold text-brand dark:text-brand-300 hover:underline">
                            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Write a Review
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

</div>

{{-- ALPINE COMPONENT ─────────────────────────────────────────────────────── --}}
@verbatim
<script>
function productDetail() {
    var d = window.__pdp || {};

    // ── Read all product data from window.__pdp ────────────────────────────
    var _variants      = Array.isArray(d.variants)  ? d.variants  : [];
    var _fallback      = Array.isArray(d.images)    ? d.images    : [];
    var _minQty        = (typeof d.minQty  === 'number' && d.minQty  >= 1) ? d.minQty  : 1;
    var _qtyStep       = (typeof d.qtyStep === 'number' && d.qtyStep >= 1) ? d.qtyStep : 1;
    var _stockQty      = (typeof d.stockQty  === 'number') ? d.stockQty  : 0;
    var _basePrice     = (typeof d.basePrice === 'number' && d.basePrice > 0) ? d.basePrice : 0;
    var _unitsPerOrder = (typeof d.unitsPerOrder === 'number') ? d.unitsPerOrder : 1;
    var _sellingMethod = d.sellingMethod || '';

    // ── Quantity safe initialisation ───────────────────────────────────────
    // qty must be: >= minQty, <= stockQty, aligned to qtyStep
    // e.g. if backend sends minQty=2, step=2: safe start = 2
    // if minQty is not a multiple of step, snap up to nearest valid step
    var _safeQty = _minQty;
    if (_safeQty > _stockQty) _safeQty = _stockQty;                          // clamp to stock
    var _stepRem = (_safeQty - _minQty) % _qtyStep;
    if (_stepRem !== 0) _safeQty = _safeQty + (_qtyStep - _stepRem);         // align to step
    if (_safeQty > _stockQty) _safeQty = _safeQty - _qtyStep;               // clamp again if step pushed over
    if (_safeQty < _minQty)   _safeQty = _minQty;                            // final floor

    // ── Image fallback priority ────────────────────────────────────────────
    // 1. Selected variant images
    // 2. Product-level images ($p['images'])
    // 3. First available variant's images
    // 4. Placeholder
    var _firstVariant = _variants[0];
    var _startImages;
    if (_firstVariant && Array.isArray(_firstVariant.images) && _firstVariant.images.length) {
        _startImages = _firstVariant.images;                                  // priority 1 (variant 0)
    } else if (_fallback.length) {
        _startImages = _fallback;                                             // priority 2 (product images)
    } else {
        // priority 3: scan all variants for first with images
        var _anyVariantImages = null;
        for (var _vi = 0; _vi < _variants.length; _vi++) {
            if (Array.isArray(_variants[_vi].images) && _variants[_vi].images.length) {
                _anyVariantImages = _variants[_vi].images;
                break;
            }
        }
        _startImages = _anyVariantImages || ['https://placehold.co/600x750/F3F3F3/A3A3A3?text=No+Image'];
    }

    return {
        // ── state ─────────────────────────────────────────────────────────
        variants:       _variants,
        fallbackImages: _fallback,
        activeVariant:  0,
        activeImage:    0,
        currentImages:  _startImages,
        qty:            _safeQty,
        minQty:         _minQty,
        qtyStep:        _qtyStep,
        stockQty:       _stockQty,
        unitsPerOrder:  _unitsPerOrder,
        sellingMethod:  _sellingMethod,
        activeTab:      'details',
        // selectedAddOns: array of full catalog product objects (not price modifiers)
        // Each entry is a separate cart line item when submitted.
        selectedAddOns: [],
        basePrice:      _basePrice,

        // ── computed ──────────────────────────────────────────────────────

        // Total fabric for per-length: qty units × yards-per-unit
        get totalFabric() {
            return this.qty * this.unitsPerOrder;
        },

        // grandTotal = main product only (add-ons are separate line items)
        get grandTotal() {
            return this.basePrice * this.qty;
        },

        // addOnTotal = informational only — shown in summary, not merged into grandTotal
        get addOnTotal() {
            return this.selectedAddOns.reduce(function(sum, a) { return sum + (a.price || 0); }, 0);
        },

        // ── selectedImage: the URL of the currently displayed image ───────
        get selectedImage() {
            return this.currentImages[this.activeImage] || this.currentImages[0] || null;
        },

        // ── cartPayload: full backend-ready structure ─────────────────────
        // mainLine:     the product being viewed — one cart line
        // relatedLines: each selected add-on as its own independent cart line
        //               (same architecture as a regular product add to cart)
        get cartPayload() {
            var d = window.__pdp || {};
            var v = this.variants[this.activeVariant] || null;

            // Main product line
            var mainLine = {
                slug:          d.slug,
                name:          d.name,
                category:      d.category,
                sellingMethod: d.sellingMethod,
                unitLabel:     d.unitLabel,
                lengthUnit:    d.lengthUnit    || null,
                unitsPerOrder: d.unitsPerOrder || null,
                loomSize:      d.loomSize      || null,
                minQuantity:   d.minQty,
                quantityStep:  d.qtyStep,
                stockQty:      d.stockQty,
                quantity:      this.qty,
                // Full selected variant object (id if available, color, hex — no images)
                variant: v ? {
                    id:    v.id    || null,
                    color: v.color || null,
                    hex:   v.hex   || null,
                } : null,
                // Image displayed when added — for cart panel thumbnail
                selectedImage: this.selectedImage,
                // Per-length: total fabric for confirmation display
                totalFabric:   this.sellingMethod === 'per-length' ? this.totalFabric : null,
                unitPrice:     this.basePrice,
                totalPrice:    this.grandTotal,
            };

            // Related product lines — each add-on is an independent catalog product
            var relatedLines = this.selectedAddOns.map(function(ao) {
                return {
                    // Full catalog product fields — backend can resolve from slug/id
                    id:            ao.id,
                    slug:          ao.slug          || null,
                    name:          ao.name,
                    category:      ao.category      || null,
                    sellingMethod: ao.sellingMethod || null,
                    unitLabel:     ao.unitLabel     || null,
                    stockQty:      ao.stockQuantity || null,
                    // Related products always add 1 unit (user can adjust in cart)
                    quantity:      1,
                    unitPrice:     ao.price,
                    totalPrice:    ao.price,
                    // type flag: lets backend/cart treat this as a related line
                    lineType:      'related_product',
                };
            });

            return {
                mainLine:     mainLine,
                relatedLines: relatedLines,
            };
        },

        // ── no separate init needed ───────────────────────────────────────
        init: function() {},

        // ── gallery ───────────────────────────────────────────────────────
        // Image priority: selected variant → product images → any variant → placeholder
        _resolveImages: function() {
            var v = this.variants[this.activeVariant];
            var vImgs = v && Array.isArray(v.images) && v.images.length ? v.images : [];

            if (vImgs.length) {
                this.currentImages = vImgs;                                   // selected variant images
            } else if (this.fallbackImages.length) {
                this.currentImages = this.fallbackImages;                     // product-level images
            } else {
                // scan other variants for any images
                var found = null;
                for (var i = 0; i < this.variants.length; i++) {
                    if (Array.isArray(this.variants[i].images) && this.variants[i].images.length) {
                        found = this.variants[i].images;
                        break;
                    }
                }
                this.currentImages = found || ['https://placehold.co/600x750/F3F3F3/A3A3A3?text=No+Image'];
            }
            this.activeImage = 0;
        },

        switchVariant: function(idx) {
            if (!this.variants[idx]) return;
            this.activeVariant = idx;
            this._resolveImages();
        },

        prevImage: function() {
            this.activeImage = this.activeImage > 0
                ? this.activeImage - 1
                : this.currentImages.length - 1;
        },
        nextImage: function() {
            this.activeImage = this.activeImage < this.currentImages.length - 1
                ? this.activeImage + 1
                : 0;
        },

        // ── quantity ──────────────────────────────────────────────────────
        // stockQty = total orderable units.
        // For per-length: 8 means 8 × unitsPerOrder yards available.
        // For per-piece/set/bundle/loom: 8 means 8 individual units.
        increaseQty: function() {
            var next = this.qty + this.qtyStep;
            if (next <= this.stockQty) this.qty = next;     // strict ≤ prevents overselling
        },
        decreaseQty: function() {
            var prev = this.qty - this.qtyStep;
            if (prev >= this.minQty) this.qty = prev;       // floor at minimum order qty
        },

        // ── validateQty: call before submitting ───────────────────────────
        validateQty: function() {
            if (this.qty < this.minQty)   this.qty = this.minQty;
            if (this.qty > this.stockQty) this.qty = this.stockQty;
            // Snap to nearest valid step above minQty
            var offset = (this.qty - this.minQty) % this.qtyStep;
            if (offset !== 0) this.qty = this.qty - offset;  // round down to valid step
            if (this.qty < this.minQty) this.qty = this.minQty;
            return this.qty >= this.minQty && this.qty <= this.stockQty;
        },

        // ── add-ons: catalog products, each becomes its own cart line ─────
        // selectedAddOns stores the full add-on product object so cartPayload
        // has everything it needs without extra lookups.
        isAddOnSelected: function(id) {
            return this.selectedAddOns.some(function(a) { return a.id === id; });
        },
        // toggleAddOn: pass the full add-on product object
        toggleAddOn: function(id) {
            if (this.isAddOnSelected(id)) {
                this.selectedAddOns = this.selectedAddOns.filter(function(a) { return a.id !== id; });
            } else {
                // Find the full add-on object from window.__pdp.addOns
                var allAddOns = Array.isArray(window.__pdp.addOns) ? window.__pdp.addOns : [];
                var ao = null;
                for (var i = 0; i < allAddOns.length; i++) {
                    if (allAddOns[i].id === id) { ao = allAddOns[i]; break; }
                }
                if (ao) this.selectedAddOns.push(ao);
            }
        },

        // ── cart actions ──────────────────────────────────────────────────
        addToCart: function() {
            // Guard: cannot add if out of stock
            if (this.stockQty <= 0) {
                console.warn('Cannot add to cart: out of stock');
                return;
            }
            // Guard: validate quantity before sending
            if (!this.validateQty()) {
                console.warn('Cannot add to cart: invalid quantity', this.qty);
                return;
            }
            var payload = this.cartPayload;
            // Livewire (uncomment when ready):
            // this.$wire.addToCart(payload)
            // Fetch:
            // fetch('/cart/add', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            //     body: JSON.stringify(payload)
            // }).then(r => r.json()).then(function(data) { /* handle */ })
            console.log('addToCart payload:', JSON.stringify(payload, null, 2));
            // Open the cart panel
            window.dispatchEvent(new CustomEvent('cart:open'));
        },
        buyNow: function() {
            // Guard: cannot buy if out of stock
            if (this.stockQty <= 0) {
                console.warn('Cannot buy: out of stock');
                return;
            }
            // Guard: validate quantity
            if (!this.validateQty()) {
                console.warn('Cannot buy: invalid quantity', this.qty);
                return;
            }
            var payload = this.cartPayload;
            // Livewire: this.$wire.buyNow(payload)
            // Fetch: fetch('/checkout/now', { method:'POST', ... body: JSON.stringify(payload) })
            console.log('buyNow payload:', JSON.stringify(payload, null, 2));
        },
    };
}
</script>
@endverbatim

@endsection