{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN INVENTORY PAGE                                             ║
║  Frontend-ready with Alpine.js state, custom dropdowns,          ║
║  bulk actions, adjust-stock modal, and mobile card view          ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.admin')
@section('title', 'Inventory')
@section('page-title', 'Inventory')
@section('breadcrumb')
    <span class="text-xs text-neutral-400 dark:text-neutral-500">Home</span>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Inventory</span>
@endsection

@section('content')
<div x-data="inventoryManager()" class="space-y-6">

    {{-- ════════════════════════════════════════════════════════════
         ADJUST STOCK MODAL
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="modal.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(2px); display:none"
        @click.self="modal.open = false"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-md"
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">Adjust Stock</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5" x-text="modal.productName + (modal.variantName !== 'Default' ? ' — ' + modal.variantName : '')"></p>
                </div>
                <button @click="modal.open = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-5 py-4 space-y-4">

                {{-- Current qty display --}}
                <div class="flex items-center gap-3 p-3 bg-neutral-100 dark:bg-neutral-900/60 rounded-lg">
                    <div class="text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">Current</p>
                        <p class="text-xl font-bold text-neutral-900 dark:text-neutral-50 leading-none mt-1" x-text="modal.currentQty"></p>
                        <p class="text-[10px] text-neutral-500 dark:text-neutral-400 mt-0.5">units</p>
                    </div>
                    <div class="flex-1 text-center text-neutral-400 dark:text-neutral-600">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">New</p>
                        <p class="text-xl font-bold leading-none mt-1"
                           :class="previewQty < 0 ? 'text-red-600 dark:text-red-400' : previewQty === 0 ? 'text-neutral-500' : 'text-brand dark:text-brand-300'"
                           x-text="previewQty < 0 ? '—' : previewQty"></p>
                        <p class="text-[10px] text-neutral-500 dark:text-neutral-400 mt-0.5">units</p>
                    </div>
                </div>

                {{-- Adjustment type --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Adjustment Type</label>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button @click="modal.type = 'add'" :class="modal.type === 'add' ? 'bg-brand text-white border-brand' : 'bg-transparent text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-600'"
                            class="px-2 py-1.5 text-xs font-medium rounded border transition-colors">
                            + Add
                        </button>
                        <button @click="modal.type = 'remove'" :class="modal.type === 'remove' ? 'bg-red-600 text-white border-red-600' : 'bg-transparent text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-600'"
                            class="px-2 py-1.5 text-xs font-medium rounded border transition-colors">
                            − Remove
                        </button>
                        <button @click="modal.type = 'set'" :class="modal.type === 'set' ? 'bg-neutral-800 dark:bg-neutral-100 text-white dark:text-neutral-900 border-neutral-800 dark:border-neutral-100' : 'bg-transparent text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-600'"
                            class="px-2 py-1.5 text-xs font-medium rounded border transition-colors">
                            = Set
                        </button>
                    </div>
                </div>

                {{-- Amount input --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        <span x-text="modal.type === 'add' ? 'Units to Add' : modal.type === 'remove' ? 'Units to Remove' : 'Set Quantity To'"></span>
                        <span class="text-red-500"> *</span>
                    </label>
                    <input
                        type="number"
                        x-model.number="modal.amount"
                        @input="calcPreview()"
                        min="0"
                        placeholder="0"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
                    />
                    <p x-show="modal.type === 'remove' && previewQty < 0" class="text-xs text-red-600 dark:text-red-400 mt-1">Cannot remove more than current stock.</p>
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Note <span class="text-neutral-400">(optional)</span></label>
                    <input
                        type="text"
                        x-model="modal.note"
                        placeholder="e.g. New restock delivery, Sale adjustment..."
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
                    />
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="modal.open = false" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                    Cancel
                </button>
                <button @click="confirmAdjust()"
                    :disabled="modal.amount === '' || modal.amount === null || (modal.type === 'remove' && previewQty < 0)"
                    :class="(modal.amount === '' || modal.amount === null || (modal.type === 'remove' && previewQty < 0)) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-brand-600'"
                    class="px-4 py-2 text-xs font-medium bg-brand text-white rounded-lg transition-colors">
                    Save Adjustment
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         HEADER & TITLE
    ════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Inventory</h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Track and manage stock levels across all products</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="exportInventory()"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors font-medium text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </button>
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors font-medium text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                Products
            </a>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         STATS OVERVIEW
    ════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Total Variants</p>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">18</p>
                </div>
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">In Stock</p>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">12</p>
                </div>
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Low Stock</p>
                    <p class="mt-2 text-3xl font-bold text-accent-600 dark:text-accent-300">4</p>
                </div>
                <svg class="w-5 h-5 text-accent-600 dark:text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Out of Stock</p>
                    <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">2</p>
                </div>
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Est. Value</p>
                    <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">₦4.2M</p>
                </div>
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FILTERS & SEARCH
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
        {{-- Search Bar --}}
        <div class="mb-4">
            <input
                type="text"
                x-model="search"
                @input="applyFilters()"
                placeholder="Search by product name, SKU, or variant..."
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-500 dark:placeholder-neutral-500 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
            />
        </div>

        {{-- Filter Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">

            {{-- Category Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Category</label>
                <div class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="filters.category ? getCategoryLabel(filters.category) : 'All'"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.category = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All</button>
                        <button @click="filters.category = 'lace'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Lace</button>
                        <button @click="filters.category = 'aso-oke'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Aso Oke</button>
                        <button @click="filters.category = 'ankara'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Ankara</button>
                        <button @click="filters.category = 'caps'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Caps</button>
                        <button @click="filters.category = 'headties'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Headties</button>
                    </div>
                </div>
            </div>

            {{-- Stock Status Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Stock</label>
                <div class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="filters.stock ? getStockLabel(filters.stock) : 'All Stock'"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.stock = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All Stock</button>
                        <button @click="filters.stock = 'in-stock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">In Stock</button>
                        <button @click="filters.stock = 'low-stock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Low Stock</button>
                        <button @click="filters.stock = 'out-stock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Out of Stock</button>
                    </div>
                </div>
            </div>

            {{-- Selling Method Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Method</label>
                <div class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="filters.method ? getMethodLabel(filters.method) : 'All'"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.method = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All Methods</button>
                        <button @click="filters.method = 'per-piece'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Piece</button>
                        <button @click="filters.method = 'per-set'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Set</button>
                        <button @click="filters.method = 'per-bundle'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Bundle</button>
                        <button @click="filters.method = 'per-length'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Length</button>
                        <button @click="filters.method = 'per-loom'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Loom</button>
                    </div>
                </div>
            </div>

            {{-- Threshold Alert Dropdown (at/below threshold) --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Alert</label>
                <div class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="filters.alert ? getAlertLabel(filters.alert) : 'All'"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        <button @click="filters.alert = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All</button>
                        <button @click="filters.alert = 'needs-restock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Needs Restock</button>
                        <button @click="filters.alert = 'ok'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">OK</button>
                    </div>
                </div>
            </div>

            {{-- Sort Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Sort</label>
                <div class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="getSortLabel()"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.sortBy = 'name-asc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Name A-Z</button>
                        <button @click="filters.sortBy = 'qty-asc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Qty Low → High</button>
                        <button @click="filters.sortBy = 'qty-desc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Qty High → Low</button>
                        <button @click="filters.sortBy = 'updated'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Recently Updated</button>
                    </div>
                </div>
            </div>

            {{-- Clear Filters --}}
            <div class="flex items-end">
                <button @click="clearFilters()" class="text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 font-medium text-xs whitespace-nowrap transition-colors">
                    Clear
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         BULK ACTIONS BAR
    ════════════════════════════════════════════════════════════════ --}}
    <div x-show="selectedRows.length > 0"
         x-transition
         class="bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700/50 rounded-lg p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="text-xs text-brand-900 dark:text-brand-200 font-medium">
            <span x-text="selectedRows.length"></span>
            <span x-text="selectedRows.length === 1 ? ' item selected' : ' items selected'"></span>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="bulkRestock()" class="px-3 py-1.5 bg-brand text-white text-xs rounded hover:bg-brand-600 transition-colors font-medium">Restock</button>
            <button @click="bulkSetThreshold()" class="px-3 py-1.5 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 text-xs rounded hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors font-medium">Set Threshold</button>
            <button @click="bulkExport()" class="px-3 py-1.5 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 text-xs rounded hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors font-medium">Export</button>
            <button @click="selectedRows = []" class="px-3 py-1.5 text-neutral-600 dark:text-neutral-400 text-xs hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors font-medium">Clear</button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         INVENTORY TABLE / MOBILE CARDS
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 overflow-hidden">

        {{-- Empty State --}}
        <div x-show="filteredItems.length === 0" class="p-12 text-center">
            <svg class="w-12 h-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/>
            </svg>
            <p class="text-neutral-600 dark:text-neutral-400 font-medium text-sm" x-text="search || Object.values(filters).some(v => v) ? 'No items match your filters' : 'No inventory items yet'"></p>
            <p class="text-neutral-500 dark:text-neutral-500 text-xs mt-1">Try adjusting your filters or add products first</p>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto max-h-[750px] overflow-y-auto custom-scrollbar">
            <table class="w-full">
                <thead class="sticky top-0 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800">
                    <tr>
                        <th class="px-4 py-2.5 text-left w-4">
                            <input type="checkbox"
                                @change="toggleSelectAll()"
                                :checked="selectedRows.length === filteredItems.length && filteredItems.length > 0"
                                class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 accent-brand">
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Product</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Category</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Variant</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Method</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Qty</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Threshold</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Last Adjusted</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    <template x-for="item in filteredItems" :key="item.id">
                        <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-900/50 transition-colors">
                            <td class="px-4 py-2.5">
                                <input type="checkbox"
                                    @change="toggleRow(item.id)"
                                    :checked="selectedRows.includes(item.id)"
                                    class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 accent-brand">
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded bg-gradient-to-br from-neutral-200 to-neutral-300 dark:from-neutral-800 dark:to-neutral-900 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50" x-text="item.productName"></p>
                                        <p class="text-xs text-neutral-500" x-text="item.sku"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="item.categoryLabel"></td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="item.variantName"></td>
                            <td class="px-4 py-2.5 text-xs">
                                <span class="bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded inline-block text-xs font-medium" x-text="item.methodLabel"></span>
                            </td>
                            <td class="px-4 py-2.5 text-xs">
                                <template x-if="item.qty > item.threshold">
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium" x-text="item.qty"></span>
                                </template>
                                <template x-if="item.qty <= item.threshold && item.qty > 0">
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium" x-text="item.qty"></span>
                                </template>
                                <template x-if="item.qty === 0">
                                    <span class="bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-medium">0</span>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="item.threshold"></td>
                            <td class="px-4 py-2.5 text-xs">
                                <template x-if="item.qty === 0">
                                    <span class="bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-medium">Out of Stock</span>
                                </template>
                                <template x-if="item.qty > 0 && item.qty <= item.threshold">
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium">Low Stock</span>
                                </template>
                                <template x-if="item.qty > item.threshold">
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium">In Stock</span>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-500" x-text="item.lastAdjusted"></td>
                            <td class="px-4 py-2.5 text-center">
                                <div x-data="{ open: false }" class="relative inline-block" @click.away="open = false">
                                    <button @click="open = !open" class="p-1 hover:bg-neutral-200 dark:hover:bg-neutral-800 rounded transition-colors">
                                        <svg class="w-4 h-4 text-neutral-500 dark:text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" class="absolute right-0 mt-1 w-44 bg-neutral-50 dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 z-10">
                                        <button @click="openAdjustModal(item); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-t-lg border-b border-neutral-200 dark:border-neutral-700">Adjust Stock</button>
                                        <button @click="openThresholdModal(item); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Set Threshold</button>
                                        <a :href="'/admin/products/' + item.productId + '/edit'" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Edit Product</a>
                                        <button @click="viewHistory(item); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-b-lg">View History</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="block md:hidden divide-y divide-neutral-200 dark:divide-neutral-800">
            <template x-for="item in filteredItems" :key="item.id">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <input type="checkbox"
                            @change="toggleRow(item.id)"
                            :checked="selectedRows.includes(item.id)"
                            class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 accent-brand mt-0.5 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <div>
                                    <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50 truncate" x-text="item.productName"></p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400" x-text="item.sku + (item.variantName !== 'Default' ? ' · ' + item.variantName : '')"></p>
                                </div>
                                <template x-if="item.qty === 0">
                                    <span class="bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-medium flex-shrink-0">Out</span>
                                </template>
                                <template x-if="item.qty > 0 && item.qty <= item.threshold">
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium flex-shrink-0">Low</span>
                                </template>
                                <template x-if="item.qty > item.threshold">
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium flex-shrink-0">OK</span>
                                </template>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                <div class="space-y-0.5">
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Category:</strong> <span x-text="item.categoryLabel"></span></p>
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Qty:</strong> <span x-text="item.qty + ' units'"></span></p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Method:</strong> <span x-text="item.methodLabel"></span></p>
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Threshold:</strong> <span x-text="item.threshold"></span></p>
                                </div>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2" x-text="'Adjusted ' + item.lastAdjusted"></p>
                            <div x-data="{ open: false }" class="relative" @click.away="open = false">
                                <button @click="open = !open" class="w-full text-left px-2 py-1.5 text-xs bg-neutral-100 dark:bg-neutral-900 hover:bg-neutral-200 dark:hover:bg-neutral-800 rounded text-neutral-900 dark:text-neutral-50 font-medium transition-colors">
                                    ⋯ Actions
                                </button>
                                <div x-show="open" class="absolute bottom-full right-0 mb-1 w-48 bg-neutral-50 dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 z-10">
                                    <button @click="openAdjustModal(item); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-t-lg border-b border-neutral-200 dark:border-neutral-700">Adjust Stock</button>
                                    <button @click="openThresholdModal(item); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Set Threshold</button>
                                    <a :href="'/admin/products/' + item.productId + '/edit'" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Edit Product</a>
                                    <button @click="viewHistory(item); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-b-lg">View History</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         SET THRESHOLD MINI MODAL
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="thresholdModal.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(2px); display:none"
        @click.self="thresholdModal.open = false"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <h2 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">Set Low-Stock Threshold</h2>
                <button @click="thresholdModal.open = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-3">
                <p class="text-xs text-neutral-600 dark:text-neutral-400">Alert when stock falls at or below this number for <strong class="text-neutral-900 dark:text-neutral-50" x-text="thresholdModal.productName"></strong>.</p>
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Threshold <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        x-model.number="thresholdModal.value"
                        min="0"
                        placeholder="e.g. 5"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
                    />
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="thresholdModal.open = false" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">Cancel</button>
                <button @click="confirmThreshold()" class="px-4 py-2 text-xs font-medium bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors">Save</button>
            </div>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════
     CUSTOM SCROLLBAR STYLE
════════════════════════════════════════════════════════════════ --}}
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
</style>

{{-- ════════════════════════════════════════════════════════════
     ALPINE.JS STATE MANAGEMENT
════════════════════════════════════════════════════════════════ --}}
<script>
function inventoryManager() {
    return {
        search: '',
        selectedRows: [],
        filters: {
            category: '',
            stock: '',
            method: '',
            alert: '',
            sortBy: 'name-asc',
        },

        // Adjust-stock modal state
        modal: {
            open: false,
            itemId: null,
            productName: '',
            variantName: '',
            currentQty: 0,
            type: 'add',  // add | remove | set
            amount: '',
            note: '',
        },

        // Threshold modal state
        thresholdModal: {
            open: false,
            itemId: null,
            productName: '',
            value: '',
        },

        // Computed preview quantity (reactive)
        get previewQty() {
            const amt = Number(this.modal.amount) || 0;
            if (this.modal.type === 'add')    { return this.modal.currentQty + amt; }
            if (this.modal.type === 'remove') { return this.modal.currentQty - amt; }
            if (this.modal.type === 'set')    { return amt; }
            return this.modal.currentQty;
        },

        // Mock inventory data — backend-ready structure
        // @todo: replace with Livewire/API data source
        mockItems: [
            { id: 1,  productId: 1, productName: 'Premium Ankara Fabric',  sku: 'ARK-001', categoryKey: 'ankara',   categoryLabel: 'Ankara & Prints', variantName: 'Default',     methodKey: 'per-length', methodLabel: 'Per Length', qty: 32, threshold: 10, lastAdjusted: '2 hours ago' },
            { id: 2,  productId: 2, productName: 'Sego Headtie Set',       sku: 'SEG-001', categoryKey: 'headties', categoryLabel: 'Headties',        variantName: 'Red',         methodKey: 'per-set',    methodLabel: 'Per Set',    qty: 5,  threshold: 8,  lastAdjusted: '1 day ago' },
            { id: 3,  productId: 2, productName: 'Sego Headtie Set',       sku: 'SEG-002', categoryKey: 'headties', categoryLabel: 'Headties',        variantName: 'Blue',        methodKey: 'per-set',    methodLabel: 'Per Set',    qty: 3,  threshold: 8,  lastAdjusted: '1 day ago' },
            { id: 4,  productId: 3, productName: 'Aso Oke Bundle',         sku: 'ASO-001', categoryKey: 'aso-oke',  categoryLabel: 'Aso Oke',         variantName: 'Default',     methodKey: 'per-bundle', methodLabel: 'Per Bundle', qty: 8,  threshold: 5,  lastAdjusted: '2 days ago' },
            { id: 5,  productId: 4, productName: 'Aso Oke Complete Set',   sku: 'ASO-002', categoryKey: 'aso-oke',  categoryLabel: 'Aso Oke',         variantName: 'Gold',        methodKey: 'per-set',    methodLabel: 'Per Set',    qty: 3,  threshold: 5,  lastAdjusted: '3 days ago' },
            { id: 6,  productId: 4, productName: 'Aso Oke Complete Set',   sku: 'ASO-003', categoryKey: 'aso-oke',  categoryLabel: 'Aso Oke',         variantName: 'Silver',      methodKey: 'per-set',    methodLabel: 'Per Set',    qty: 0,  threshold: 5,  lastAdjusted: '3 days ago' },
            { id: 7,  productId: 5, productName: 'Cap Material Lace',      sku: 'CAP-001', categoryKey: 'caps',     categoryLabel: 'Caps',            variantName: 'White',       methodKey: 'per-piece',  methodLabel: 'Per Piece',  qty: 45, threshold: 10, lastAdjusted: '1 week ago' },
            { id: 8,  productId: 5, productName: 'Cap Material Lace',      sku: 'CAP-002', categoryKey: 'caps',     categoryLabel: 'Caps',            variantName: 'Cream',       methodKey: 'per-piece',  methodLabel: 'Per Piece',  qty: 12, threshold: 10, lastAdjusted: '1 week ago' },
            { id: 9,  productId: 6, productName: 'Gele and Ipele Set',     sku: 'GEL-001', categoryKey: 'aso-oke',  categoryLabel: 'Aso Oke',         variantName: 'Default',     methodKey: 'per-set',    methodLabel: 'Per Set',    qty: 0,  threshold: 5,  lastAdjusted: '1 week ago' },
            { id: 10, productId: 7, productName: 'Premium Lace Fabric',    sku: 'LAC-001', categoryKey: 'lace',     categoryLabel: 'Lace Fabrics',    variantName: 'Default',     methodKey: 'per-length', methodLabel: 'Per Length', qty: 22, threshold: 8,  lastAdjusted: '3 hours ago' },
            { id: 11, productId: 7, productName: 'Premium Lace Fabric',    sku: 'LAC-002', categoryKey: 'lace',     categoryLabel: 'Lace Fabrics',    variantName: 'Ivory',       methodKey: 'per-length', methodLabel: 'Per Length', qty: 7,  threshold: 8,  lastAdjusted: '3 hours ago' },
            { id: 12, productId: 8, productName: 'Traditional Adire',      sku: 'ADI-001', categoryKey: 'ankara',   categoryLabel: 'Ankara & Prints', variantName: 'Indigo',      methodKey: 'per-loom',   methodLabel: 'Per Loom',   qty: 5,  threshold: 3,  lastAdjusted: '5 days ago' },
        ],

        filteredItems: [],

        init() {
            this.applyFilters();
        },

        applyFilters() {
            let result = [...this.mockItems];

            // Search
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                result = result.filter(i =>
                    i.productName.toLowerCase().includes(q) ||
                    i.sku.toLowerCase().includes(q) ||
                    i.variantName.toLowerCase().includes(q) ||
                    i.categoryLabel.toLowerCase().includes(q)
                );
            }

            // Category
            if (this.filters.category) {
                result = result.filter(i => i.categoryKey === this.filters.category);
            }

            // Stock level
            if (this.filters.stock) {
                result = result.filter(i => {
                    if (this.filters.stock === 'in-stock')  { return i.qty > i.threshold; }
                    if (this.filters.stock === 'low-stock') { return i.qty <= i.threshold && i.qty > 0; }
                    if (this.filters.stock === 'out-stock') { return i.qty === 0; }
                    return true;
                });
            }

            // Selling method
            if (this.filters.method) {
                result = result.filter(i => i.methodKey === this.filters.method);
            }

            // Alert (needs restock = at or below threshold)
            if (this.filters.alert) {
                result = result.filter(i => {
                    if (this.filters.alert === 'needs-restock') { return i.qty <= i.threshold; }
                    if (this.filters.alert === 'ok')            { return i.qty > i.threshold; }
                    return true;
                });
            }

            // Sort
            result = this.applySorting(result);
            this.filteredItems = result;
            // @todo: Backend — GET /admin/api/inventory with filters
        },

        applySorting(items) {
            const sorted = [...items];
            switch (this.filters.sortBy) {
                case 'qty-asc':   return sorted.sort((a, b) => a.qty - b.qty);
                case 'qty-desc':  return sorted.sort((a, b) => b.qty - a.qty);
                case 'updated':   return sorted; // @todo: sort by actual timestamp
                default:          return sorted.sort((a, b) => a.productName.localeCompare(b.productName));
            }
        },

        getSortLabel() {
            const l = { 'name-asc': 'Name A-Z', 'qty-asc': 'Qty Low → High', 'qty-desc': 'Qty High → Low', 'updated': 'Recently Updated' };
            return l[this.filters.sortBy] || 'Name A-Z';
        },

        getCategoryLabel(key) {
            const l = { 'lace': 'Lace', 'aso-oke': 'Aso Oke', 'ankara': 'Ankara', 'caps': 'Caps', 'headties': 'Headties' };
            return l[key] || key;
        },

        getStockLabel(key) {
            const l = { 'in-stock': 'In Stock', 'low-stock': 'Low Stock', 'out-stock': 'Out of Stock' };
            return l[key] || 'All Stock';
        },

        getMethodLabel(key) {
            const l = { 'per-piece': 'Per Piece', 'per-set': 'Per Set', 'per-bundle': 'Per Bundle', 'per-length': 'Per Length', 'per-loom': 'Per Loom' };
            return l[key] || key;
        },

        getAlertLabel(key) {
            const l = { 'needs-restock': 'Needs Restock', 'ok': 'OK' };
            return l[key] || key;
        },

        clearFilters() {
            this.search = '';
            this.filters = { category: '', stock: '', method: '', alert: '', sortBy: 'name-asc' };
            this.selectedRows = [];
            this.applyFilters();
        },

        toggleSelectAll() {
            if (this.selectedRows.length === this.filteredItems.length && this.filteredItems.length > 0) {
                this.selectedRows = [];
            } else {
                this.selectedRows = this.filteredItems.map(i => i.id);
            }
        },

        toggleRow(id) {
            const idx = this.selectedRows.indexOf(id);
            idx > -1 ? this.selectedRows.splice(idx, 1) : this.selectedRows.push(id);
        },

        // ── Adjust-stock modal ──────────────────────────────────

        openAdjustModal(item) {
            if (!item) { return; } // @todo: support bulk open
            this.modal = {
                open: true,
                itemId: item.id,
                productName: item.productName,
                variantName: item.variantName,
                currentQty: item.qty,
                type: 'add',
                amount: '',
                note: '',
            };
        },

        calcPreview() {
            // Reactive via getter — nothing to do here, kept for clarity
        },

        confirmAdjust() {
            // @todo: POST /admin/api/inventory/{id}/adjust
            const item = this.mockItems.find(i => i.id === this.modal.itemId);
            if (!item) { return; }
            if (this.modal.type === 'add')    { item.qty = item.qty + Number(this.modal.amount); }
            if (this.modal.type === 'remove') { item.qty = Math.max(0, item.qty - Number(this.modal.amount)); }
            if (this.modal.type === 'set')    { item.qty = Number(this.modal.amount); }
            item.lastAdjusted = 'just now';
            this.modal.open = false;
            this.applyFilters();
        },

        // ── Threshold modal ─────────────────────────────────────

        openThresholdModal(item) {
            this.thresholdModal = {
                open: true,
                itemId: item.id,
                productName: item.productName + (item.variantName !== 'Default' ? ' — ' + item.variantName : ''),
                value: item.threshold,
            };
        },

        confirmThreshold() {
            // @todo: PATCH /admin/api/inventory/{id}
            const item = this.mockItems.find(i => i.id === this.thresholdModal.itemId);
            if (item) { item.threshold = Number(this.thresholdModal.value); }
            this.thresholdModal.open = false;
            this.applyFilters();
        },

        // ── Bulk actions ────────────────────────────────────────

        bulkRestock() {
            // @todo: POST /admin/api/inventory/bulk/restock
            console.log('Bulk restock:', this.selectedRows);
            this.selectedRows = [];
        },

        bulkSetThreshold() {
            // @todo: open threshold modal for all selected
            console.log('Bulk set threshold:', this.selectedRows);
        },

        bulkExport() {
            // @todo: GET /admin/api/inventory/export?ids[]=...
            console.log('Export:', this.selectedRows);
        },

        exportInventory() {
            // @todo: GET /admin/api/inventory/export
            console.log('Export all inventory');
        },

        viewHistory(item) {
            // @todo: navigate to /admin/inventory/{id}/history or open slide-over
            console.log('View history for:', item.sku);
        },
    };
}
</script>
@endsection
