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

@section('content')

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
    'addOns' => [
        ['id' => 1, 'name' => 'Matching Headtie', 'price' => 8000, 'image' => 'images/products/headtie.jpg'],
        ['id' => 2, 'name' => 'Gift Wrapping',     'price' => 2000, 'image' => 'images/products/gift-wrap.jpg'],
        ['id' => 3, 'name' => 'Express Delivery',  'price' => 5000, 'image' => 'images/products/express.jpg'],
    ],
];

// ── NORMALISE ─────────────────────────────────────────────────────────────────
if (!isset($product) || $product === null) {
    $p = $mock;
} elseif (is_array($product)) {
    $p = array_merge($mock, $product);
} else {
    $p = [
        'name'          => $product->name          ?? $mock['name'],
        'slug'          => $product->slug          ?? $mock['slug'],
        'category'      => $product->category      ?? $mock['category'],
        'description'   => $product->description   ?? $mock['description'],
        'sellingMethod' => $product->sellingMethod ?? $mock['sellingMethod'],
        'unitsPerOrder' => $product->unitsPerOrder ?? $mock['unitsPerOrder'],
        'unitLabel'     => $product->unitLabel     ?? $mock['unitLabel'],
        'lengthUnit'    => $product->lengthUnit    ?? $mock['lengthUnit'],
        'minQuantity'   => $product->minQuantity   ?? $mock['minQuantity'],
        'quantityStep'  => $product->quantityStep  ?? $mock['quantityStep'],
        'loomSize'      => $product->loomSize      ?? null,
        'setContents'   => $product->setContents   ?? [],
        'bundleYield'   => $product->bundleYield   ?? [],
        'includedItems' => $product->includedItems ?? [],
        'excludesText'  => $product->excludesText  ?? '',
        'price'         => $product->price         ?? 0,
        'comparePrice'  => $product->comparePrice  ?? 0,
        'discountType'  => $product->discountType  ?? null,
        'discountValue' => $product->discountValue ?? 0,
        'stockQuantity' => $product->stockQuantity ?? 0,
        'images'        => $product->images        ?? [],
        'variants'      => $product->variants      ?? [],
        'addOns'        => $product->addOns        ?? [],
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
    variants:       {!! json_encode(array_values((array) $p['variants'])) !!},
    images:         {!! json_encode(array_values((array) $p['images'])) !!},
    minQty:         {{ (int) $p['minQuantity'] }},
    qtyStep:        {{ (int) $p['quantityStep'] }},
    sellingMethod:  "{{ e($p['sellingMethod']) }}",
    unitsPerOrder:  {{ (int) $p['unitsPerOrder'] }},
    lengthUnit:     "{{ e($p['lengthUnit']) }}",
    stockQty:       {{ $stock }},
    addOns:         {!! json_encode(array_values((array) $p['addOns'])) !!},
    basePrice:      {{ $finalPriceInt }},
    slug:           "{{ e($p['slug']) }}"
};
</script>
<div
    x-data="productDetail()"
    class="bg-white dark:bg-ink min-h-screen"
>

{{-- BREADCRUMB ──────────────────────────────────────────────────────────── --}}
<nav class="mt-16 border-b border-neutral-100 dark:border-neutral-800 bg-white dark:bg-ink" aria-label="Breadcrumb">
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
                <span x-show="addOnTotal > 0" class="font-sans text-xs text-neutral-400 dark:text-neutral-500">incl. add-ons</span>
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
                        @click="toggleAddOn({{ $addon['id'] }}, {{ $addon['price'] }})"
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
                            <p class="font-sans text-xs text-neutral-500 dark:text-neutral-400">+&#8358;{{ number_format($addon['price']) }}</p>
                        </div>
                        <div :class="isAddOnSelected({{ $addon['id'] }}) ? 'bg-brand border-brand' : 'bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-600'"
                             class="w-4 h-4 border flex-shrink-0 flex items-center justify-center transition-colors duration-200">
                            <svg x-show="isAddOnSelected({{ $addon['id'] }})" class="w-2.5 h-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p x-show="addOnTotal > 0" class="font-sans text-2xs text-brand dark:text-brand-300 font-medium">
                    Add-ons: +&#8358;<span x-text="addOnTotal.toLocaleString()"></span>
                </p>
            </div>
            @endif

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
                        @click="toggleAddOn({{ $addon['id'] }}, {{ $addon['price'] }})"
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
    var _variants      = Array.isArray(d.variants)  ? d.variants  : [];
    var _fallback      = Array.isArray(d.images)    ? d.images    : [];
    var _minQty        = d.minQty        || 1;
    var _qtyStep       = d.qtyStep       || 1;
    var _unitsPerOrder = d.unitsPerOrder || 1;
    var _sellingMethod = d.sellingMethod || '';
    var _stockQty      = (typeof d.stockQty === 'number') ? d.stockQty : 0;
    var _basePrice     = (typeof d.basePrice === 'number' && d.basePrice > 0) ? d.basePrice : 0;
    var _slug          = d.slug          || '';

    // Resolve starting images
    var _firstVariant  = _variants[0];
    var _startImages   = (_firstVariant && Array.isArray(_firstVariant.images) && _firstVariant.images.length)
                           ? _firstVariant.images
                           : (_fallback.length ? _fallback : ['https://placehold.co/600x750/F3F3F3/A3A3A3?text=No+Image']);

    return {
        // ── state — ALL correct values from the start ─────────────────────
        variants:       _variants,
        fallbackImages: _fallback,
        activeVariant:  0,
        activeImage:    0,
        currentImages:  _startImages,
        qty:            _minQty,
        minQty:         _minQty,
        qtyStep:        _qtyStep,
        unitsPerOrder:  _unitsPerOrder,
        sellingMethod:  _sellingMethod,
        stockQty:       _stockQty,
        activeTab:      'details',
        selectedAddOns: [],
        basePrice:      _basePrice,
        productSlug:    _slug,

        // ── computed ──────────────────────────────────────────────────────
        get totalFabric() {
            return this.qty * this.unitsPerOrder;
        },
        get addOnTotal() {
            return this.selectedAddOns.reduce(function(sum, a) { return sum + a.price; }, 0);
        },
        get grandTotal() {
            return (this.basePrice * this.qty) + this.addOnTotal;
        },

        // ── cart payload — connect to your backend here ───────────────────
        get cartPayload() {
            return {
                slug:          this.productSlug,
                quantity:      this.qty,
                sellingMethod: this.sellingMethod,
                variant:       this.variants[this.activeVariant] || null,
                addOns:        this.selectedAddOns,
                totalFabric:   this.sellingMethod === 'per-length' ? this.totalFabric : null,
                unitPrice:     this.basePrice,
                totalPrice:    this.grandTotal,
            };
        },

        // ── no separate init needed ───────────────────────────────────────
        init: function() {},

        // ── gallery ───────────────────────────────────────────────────────
        _resolveImages: function() {
            var v = this.variants[this.activeVariant];
            var vImgs = v && Array.isArray(v.images) && v.images.length ? v.images : [];
            if (vImgs.length) {
                this.currentImages = vImgs;
            } else if (this.fallbackImages.length) {
                this.currentImages = this.fallbackImages;
            } else {
                this.currentImages = ['https://placehold.co/600x750/F3F3F3/A3A3A3?text=No+Image'];
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
        increaseQty: function() {
            var next = this.qty + this.qtyStep;
            if (next <= this.stockQty) this.qty = next;
        },
        decreaseQty: function() {
            var prev = this.qty - this.qtyStep;
            if (prev >= this.minQty) this.qty = prev;
        },

        // ── add-ons ───────────────────────────────────────────────────────
        isAddOnSelected: function(id) {
            return this.selectedAddOns.some(function(a) { return a.id === id; });
        },
        toggleAddOn: function(id, price) {
            if (this.isAddOnSelected(id)) {
                this.selectedAddOns = this.selectedAddOns.filter(function(a) { return a.id !== id; });
            } else {
                this.selectedAddOns.push({ id: id, price: price });
            }
        },

        // ── cart actions ──────────────────────────────────────────────────
        addToCart: function() {
            // TODO: wire to your cart route
            // fetch('/cart/add', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            //     body: JSON.stringify(this.cartPayload)
            // })
            console.log('Cart payload:', this.cartPayload);
        },
        buyNow: function() {
            // TODO: wire to your checkout route
            console.log('Buy now payload:', this.cartPayload);
        },
    };
}
</script>
@endverbatim

@endsection