{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN PRODUCT LIST PAGE - REFACTORED                             ║
║  Unified Alpine state with real filtering & custom dropdowns      ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')
@section('breadcrumb')
    <span class="text-xs text-neutral-400 dark:text-neutral-500">Home</span>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Products</span>
@endsection

@section('content')
<div x-data="productListManager()" class="space-y-6">
    {{-- ════════════════════════════════════════════════════════════
         HEADER & TITLE
    ════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Products</h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Manage your fabric and material inventory</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors duration-200 font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Product
        </a>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         STATS OVERVIEW (NO HOVER SHADOW)
    ════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">24</p>
                </div>
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 0a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Active</p>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">18</p>
                </div>
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Drafts</p>
                    <p class="mt-2 text-3xl font-bold text-accent-600 dark:text-accent-300">4</p>
                </div>
                <svg class="w-5 h-5 text-accent-600 dark:text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Low Stock</p>
                    <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">3</p>
                </div>
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.343 3.665c.886-.887 2.318-.887 3.203 0l6.364 6.364c.884.884.884 2.319 0 3.203l-6.364 6.364c-.884.884-2.319.884-3.203 0L.343 12.93c-.884-.884-.884-2.319 0-3.203l6-6.062z"/></svg>
            </div>
        </div>

        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Featured</p>
                    <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">7</p>
                </div>
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
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
                placeholder="Search by name, SKU, or category..."
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-500 dark:placeholder-neutral-500 focus:ring-2 focus:ring-brand focus:border-transparent transition-ring"
            />
        </div>

        {{-- Filter Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-2.5">
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

            {{-- Selling Method Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Method</label>
                <div class="relative">
                    <button 
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="filters.sellingMethod ? getSellingMethodLabel(filters.sellingMethod) : 'All'"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.sellingMethod = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All Methods</button>
                        <button @click="filters.sellingMethod = 'per-piece'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Piece</button>
                        <button @click="filters.sellingMethod = 'per-set'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Set</button>
                        <button @click="filters.sellingMethod = 'per-bundle'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Bundle</button>
                        <button @click="filters.sellingMethod = 'per-length'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Length</button>
                        <button @click="filters.sellingMethod = 'per-loom'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Per Loom</button>
                    </div>
                </div>
            </div>

            {{-- Status Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Status</label>
                <div class="relative">
                    <button 
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="!filters.status ? 'All' : filters.status.charAt(0).toUpperCase() + filters.status.slice(1)"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.status = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All Status</button>
                        <button @click="filters.status = 'active'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Active</button>
                        <button @click="filters.status = 'draft'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Draft</button>
                        <button @click="filters.status = 'archived'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Archived</button>
                    </div>
                </div>
            </div>

            {{-- Featured Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Featured</label>
                <div class="relative">
                    <button 
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="!filters.featured ? 'All' : getFeaturedLabel(filters.featured)"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.featured = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All</button>
                        <button @click="filters.featured = 'featured-only'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Featured</button>
                        <button @click="filters.featured = 'not-featured'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Not Featured</button>
                    </div>
                </div>
            </div>

            {{-- Stock Dropdown --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Stock</label>
                <div class="relative">
                    <button 
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span x-text="!filters.stock ? 'All Stock' : getStockLabel(filters.stock)"></span>
                        <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="filters.stock = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All Stock</button>
                        <button @click="filters.stock = 'in-stock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">In Stock</button>
                        <button @click="filters.stock = 'low-stock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Low Stock</button>
                        <button @click="filters.stock = 'out-stock'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Out</button>
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
                        <button @click="filters.sortBy = 'newest'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Newest</button>
                        <button @click="filters.sortBy = 'oldest'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Oldest</button>
                        <button @click="filters.sortBy = 'name-asc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Name A-Z</button>
                        <button @click="filters.sortBy = 'price-asc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Price Low</button>
                        <button @click="filters.sortBy = 'price-desc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Price High</button>
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
            <span x-text="selectedRows.length === 1 ? ' product selected' : ' products selected'"></span>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="bulkFeature()" class="px-3 py-1.5 bg-brand text-white text-xs rounded hover:bg-brand-600 transition-colors font-medium">Feature</button>
            <button @click="bulkUnfeature()" class="px-3 py-1.5 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 text-xs rounded hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors font-medium">Unfeature</button>
            <button @click="bulkActivate()" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors font-medium">Activate</button>
            <button @click="bulkDraft()" class="px-3 py-1.5 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700 transition-colors font-medium">Draft</button>
            <button @click="bulkDelete()" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors font-medium">Delete</button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         PRODUCT TABLE / MOBILE CARDS
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 overflow-hidden">

        {{-- Empty State --}}
        <div x-show="filteredProducts.length === 0" class="p-12 text-center">
            <svg class="w-12 h-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-neutral-600 dark:text-neutral-400 font-medium text-sm" x-text="search || Object.values(filters).some(v => v) ? 'No products match your filters' : 'No products yet'"></p>
            <p class="text-neutral-500 dark:text-neutral-500 text-xs mt-1">Try adjusting your filters or create your first product</p>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto max-h-[750px] overflow-y-auto custom-scrollbar">
            <table class="w-full">
                <thead class="sticky top-0 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800">
                    <tr>
                        <th class="px-4 py-2.5 text-left w-4">
                            <input type="checkbox" @change="toggleSelectAll()" :checked="selectedRows.length === filteredProducts.length && filteredProducts.length > 0" class="w-4 h-4 rounded border-neutral-300">
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Product</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Category</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Method</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Unit</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Price</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Stock</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Featured</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Add-ons</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Updated</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-900/50 transition-colors">
                            <td class="px-4 py-2.5">
                                <input type="checkbox" @change="toggleRow(product.id)" :checked="selectedRows.includes(product.id)" class="w-4 h-4 rounded border-neutral-300">
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded bg-gradient-to-br from-neutral-200 to-neutral-300 dark:from-neutral-800 dark:to-neutral-900 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50" x-text="product.name"></p>
                                        <p class="text-xs text-neutral-500" x-text="product.sku"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="product.categoryLabel"></td>
                            <td class="px-4 py-2.5 text-xs"><span x-text="product.sellingMethodLabel" class="bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded inline-block text-xs font-medium"></span></td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="product.unitSummary"></td>
                            <td class="px-4 py-2.5 text-xs font-medium text-neutral-900 dark:text-neutral-50" x-text="'₦' + product.price.toLocaleString()"></td>
                            <td class="px-4 py-2.5 text-xs">
                                <template x-if="product.stock > 10">
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium" x-text="product.stock + ' ' + product.stockUnit"></span>
                                </template>
                                <template x-if="product.stock <= 10 && product.stock > 0">
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium" x-text="product.stock + ' ' + product.stockUnit"></span>
                                </template>
                                <template x-if="product.stock === 0">
                                    <span class="bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-medium">Out</span>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-xs">
                                <template x-if="product.status === 'active'">
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium">Active</span>
                                </template>
                                <template x-if="product.status === 'draft'">
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium">Draft</span>
                                </template>
                                <template x-if="product.status === 'archived'">
                                    <span class="bg-neutral-100 dark:bg-neutral-700/20 text-neutral-700 dark:text-neutral-300 px-2 py-0.5 rounded text-xs font-medium">Archived</span>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <template x-if="product.featured">
                                    <svg class="w-4 h-4 text-accent-500 mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-center text-xs text-neutral-600 dark:text-neutral-400" x-text="product.addOns"></td>
                            <td class="px-4 py-2.5 text-xs text-neutral-500" x-text="product.updated"></td>
                            <td class="px-4 py-2.5 text-center">
                                <div x-data="{ open: false }" class="relative inline-block" @click.away="open = false">
                                    <button @click="open = !open" class="p-1 hover:bg-neutral-200 dark:hover:bg-neutral-800 rounded transition-colors">
                                        <svg class="w-4 h-4 text-neutral-500 dark:text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" class="absolute right-0 mt-1 w-40 bg-neutral-50 dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 z-10">
                                        <a href="#" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 first:rounded-t-lg">View</a>
                                        <a :href="'/admin/products/' + product.id + '/edit'" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Edit</a>
                                        <button @click="duplicateProduct(product.id)" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Duplicate</button>
                                        <button @click="toggleProductFeatured(product.id)" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700" x-text="product.featured ? 'Unfeature' : 'Feature'"></button>
                                        <button @click="archiveProduct(product.id)" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Archive</button>
                                        <button @click="deleteProduct(product.id)" class="w-full text-left px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-b-lg">Delete</button>
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
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" @change="toggleRow(product.id)" :checked="selectedRows.includes(product.id)" class="w-4 h-4 rounded border-neutral-300 mt-0.5 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1">
                                <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50 truncate" x-text="product.name"></p>
                                <template x-if="product.featured">
                                    <svg class="w-3 h-3 text-accent-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </template>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2" x-text="product.sku"></p>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                <div class="space-y-0.5">
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Category:</strong> <span x-text="product.categoryLabel"></span></p>
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Price:</strong> <span x-text="'₦' + product.price.toLocaleString()"></span></p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Stock:</strong> <span x-text="product.stock + ' ' + product.stockUnit"></span></p>
                                    <p class="text-neutral-600 dark:text-neutral-400"><strong>Status:</strong> <span :class="product.status === 'active' ? 'text-green-600 dark:text-green-400' : product.status === 'draft' ? 'text-yellow-600 dark:text-yellow-400' : 'text-neutral-600 dark:text-neutral-400'" x-text="product.status"></span></p>
                                </div>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-2" x-text="'Updated ' + product.updated"></p>
                            {{-- Mobile Actions Menu --}}
                            <div x-data="{ open: false }" class="relative" @click.away="open = false">
                                <button @click="open = !open" class="w-full text-left px-2 py-1.5 text-xs bg-neutral-100 dark:bg-neutral-900 hover:bg-neutral-200 dark:hover:bg-neutral-800 rounded text-neutral-900 dark:text-neutral-50 font-medium transition-colors">
                                    ⋯ Actions
                                </button>
                                <div x-show="open" class="absolute bottom-full right-0 mb-1 w-48 bg-neutral-50 dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 z-10">
                                    <a href="#" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 first:rounded-t-lg">View</a>
                                    <a :href="'/admin/products/' + product.id + '/edit'" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Edit</a>
                                    <button @click="duplicateProduct(product.id); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Duplicate</button>
                                    <button @click="toggleProductFeatured(product.id); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700" x-text="product.featured ? 'Unfeature' : 'Feature'"></button>
                                    <button @click="archiveProduct(product.id); open = false" class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">Archive</button>
                                    <button @click="deleteProduct(product.id); open = false" class="w-full text-left px-3 py-2 text-xs text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-b-lg font-medium">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     CUSTOM SCROLLBAR STYLE
════════════════════════════════════════════════════════════════ --}}
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.25);
    }
</style>

{{-- ════════════════════════════════════════════════════════════
     ALPINE.JS UNIFIED STATE MANAGEMENT
════════════════════════════════════════════════════════════════ --}}
<script>
function productListManager() {
    return {
        search: '',
        selectedRows: [],
        filters: {
            category: '',
            sellingMethod: '',
            status: '',
            featured: '',
            stock: '',
            sortBy: 'newest'
        },
        
        // Mock product data - normalized with categoryKey/Label and sellingMethodKey/Label
        mockProducts: [
            { id: 1, name: 'Premium Ankara Fabric', sku: 'ARK-001', categoryKey: 'ankara', categoryLabel: 'Ankara & Prints', sellingMethodKey: 'per-length', sellingMethodLabel: 'Per Length', unitSummary: '5 yards per unit', price: 25000, stock: 32, stockUnit: 'selling units', status: 'active', featured: true, addOns: 2, updated: '2 hours ago' },
            { id: 2, name: 'Sego Headtie Set', sku: 'SEG-001', categoryKey: 'lace', categoryLabel: 'Lace Fabrics', sellingMethodKey: 'per-set', sellingMethodLabel: 'Per Set', unitSummary: 'Set of 2', price: 15000, stock: 5, stockUnit: 'sets', status: 'active', featured: false, addOns: 1, updated: '1 day ago' },
            { id: 3, name: 'Aso Oke Bundle', sku: 'ASO-001', categoryKey: 'aso-oke', categoryLabel: 'Aso Oke', sellingMethodKey: 'per-bundle', sellingMethodLabel: 'Per Bundle', unitSummary: '3-piece bundle', price: 85000, stock: 8, stockUnit: 'bundles', status: 'active', featured: true, addOns: 3, updated: '2 days ago' },
            { id: 4, name: 'Aso Oke Complete Set', sku: 'ASO-002', categoryKey: 'aso-oke', categoryLabel: 'Aso Oke', sellingMethodKey: 'per-set', sellingMethodLabel: 'Per Set', unitSummary: '3-Piece Set', price: 120000, stock: 3, stockUnit: 'sets', status: 'active', featured: true, addOns: 2, updated: '3 days ago' },
            { id: 5, name: 'Cap Material Lace', sku: 'CAP-001', categoryKey: 'caps', categoryLabel: 'Caps', sellingMethodKey: 'per-piece', sellingMethodLabel: 'Per Piece', unitSummary: '1 Piece', price: 8500, stock: 45, stockUnit: 'pieces', status: 'active', featured: false, addOns: 0, updated: '1 week ago' },
            { id: 6, name: 'Gele and Ipele Set', sku: 'GEL-001', categoryKey: 'aso-oke', categoryLabel: 'Aso Oke', sellingMethodKey: 'per-set', sellingMethodLabel: 'Per Set', unitSummary: 'Set of 2', price: 65000, stock: 0, stockUnit: 'sets', status: 'active', featured: false, addOns: 1, updated: '1 week ago' },
            { id: 7, name: 'Premium Lace Fabric', sku: 'LAC-001', categoryKey: 'lace', categoryLabel: 'Lace Fabrics', sellingMethodKey: 'per-length', sellingMethodLabel: 'Per Length', unitSummary: '5 yards per unit', price: 35000, stock: 22, stockUnit: 'selling units', status: 'draft', featured: false, addOns: 1, updated: '3 hours ago' },
            { id: 8, name: 'Traditional Adire Fabric', sku: 'ADI-001', categoryKey: 'ankara', categoryLabel: 'Ankara & Prints', sellingMethodKey: 'per-loom', sellingMethodLabel: 'Per Loom', unitSummary: '45 yards', price: 180000, stock: 5, stockUnit: 'looms', status: 'active', featured: false, addOns: 2, updated: '5 days ago' }
        ],

        filteredProducts: [],

        init() {
            this.applyFilters();
        },

        applyFilters() {
            let result = [...this.mockProducts];

            // Search
            if (this.search.trim()) {
                const query = this.search.toLowerCase();
                result = result.filter(p =>
                    p.name.toLowerCase().includes(query) ||
                    p.sku.toLowerCase().includes(query) ||
                    p.categoryLabel.toLowerCase().includes(query)
                );
            }

            // Category - use categoryKey for filtering
            if (this.filters.category) {
                result = result.filter(p => p.categoryKey === this.filters.category);
            }

            // Selling method - use sellingMethodKey for filtering
            if (this.filters.sellingMethod) {
                result = result.filter(p => p.sellingMethodKey === this.filters.sellingMethod);
            }

            // Status
            if (this.filters.status) {
                result = result.filter(p => p.status === this.filters.status);
            }

            // Featured filter
            if (this.filters.featured === 'featured-only') {
                result = result.filter(p => p.featured);
            } else if (this.filters.featured === 'not-featured') {
                result = result.filter(p => !p.featured);
            }

            // Stock
            if (this.filters.stock) {
                result = result.filter(p => {
                    if (this.filters.stock === 'in-stock') return p.stock > 10;
                    if (this.filters.stock === 'low-stock') return p.stock <= 10 && p.stock > 0;
                    if (this.filters.stock === 'out-stock') return p.stock === 0;
                    return true;
                });
            }

            // Sort
            result = this.applySorting(result);
            this.filteredProducts = result;
            // @todo: Backend - GET /admin/api/products with filters
        },

        applySorting(products) {
            const sorted = [...products];
            switch (this.filters.sortBy) {
                case 'name-asc':
                    return sorted.sort((a, b) => a.name.localeCompare(b.name));
                case 'price-asc':
                    return sorted.sort((a, b) => a.price - b.price);
                case 'price-desc':
                    return sorted.sort((a, b) => b.price - a.price);
                case 'oldest':
                    return sorted.reverse();
                default:
                    return sorted;
            }
        },

        getSortLabel() {
            const labels = { 'newest': 'Newest', 'oldest': 'Oldest', 'name-asc': 'Name A-Z', 'price-asc': 'Price Low', 'price-desc': 'Price High' };
            return labels[this.filters.sortBy] || 'Newest';
        },

        getCategoryLabel(key) {
            const labels = { 'lace': 'Lace', 'aso-oke': 'Aso Oke', 'ankara': 'Ankara', 'caps': 'Caps', 'headties': 'Headties' };
            return labels[key] || key.charAt(0).toUpperCase() + key.slice(1);
        },

        getSellingMethodLabel(key) {
            const labels = { 'per-piece': 'Per Piece', 'per-set': 'Per Set', 'per-bundle': 'Per Bundle', 'per-length': 'Per Length', 'per-loom': 'Per Loom' };
            return labels[key] || key;
        },

        getFeaturedLabel(value) {
            const labels = { 'featured-only': 'Featured', 'not-featured': 'Not Featured' };
            return labels[value] || 'All';
        },

        getStockLabel(value) {
            const labels = { 'in-stock': 'In Stock', 'low-stock': 'Low Stock', 'out-stock': 'Out of Stock' };
            return labels[value] || 'All Stock';
        },

        clearFilters() {
            this.search = '';
            this.filters = { category: '', sellingMethod: '', status: '', featured: '', stock: '', sortBy: 'newest' };
            this.selectedRows = [];
            this.applyFilters();
        },

        toggleSelectAll() {
            if (this.selectedRows.length === this.filteredProducts.length && this.filteredProducts.length > 0) {
                this.selectedRows = [];
            } else {
                this.selectedRows = this.filteredProducts.map(p => p.id);
            }
        },

        toggleRow(id) {
            const idx = this.selectedRows.indexOf(id);
            idx > -1 ? this.selectedRows.splice(idx, 1) : this.selectedRows.push(id);
        },

        bulkFeature() { // @todo: POST /admin/api/products/bulk/feature
            this.mockProducts.forEach(p => { if (this.selectedRows.includes(p.id)) p.featured = true; });
            this.selectedRows = []; this.applyFilters();
        },

        bulkUnfeature() { // @todo: POST /admin/api/products/bulk/unfeature
            this.mockProducts.forEach(p => { if (this.selectedRows.includes(p.id)) p.featured = false; });
            this.selectedRows = []; this.applyFilters();
        },

        bulkActivate() { // @todo: POST /admin/api/products/bulk/activate
            this.mockProducts.forEach(p => { if (this.selectedRows.includes(p.id)) p.status = 'active'; });
            this.selectedRows = []; this.applyFilters();
        },

        bulkDraft() { // @todo: POST /admin/api/products/bulk/draft
            this.mockProducts.forEach(p => { if (this.selectedRows.includes(p.id)) p.status = 'draft'; });
            this.selectedRows = []; this.applyFilters();
        },

        bulkDelete() { // @todo: DELETE /admin/api/products/bulk
            if (confirm('Delete ' + this.selectedRows.length + ' product(s)?')) {
                this.mockProducts = this.mockProducts.filter(p => !this.selectedRows.includes(p.id));
                this.selectedRows = []; this.applyFilters();
            }
        },

        duplicateProduct(id) { // @todo: POST /admin/api/products/{id}/duplicate
            console.log('Duplicate product:', id);
        },

        toggleProductFeatured(id) { // @todo: PATCH /admin/api/products/{id}
            const p = this.mockProducts.find(x => x.id === id);
            if (p) { p.featured = !p.featured; this.applyFilters(); }
        },

        archiveProduct(id) { // @todo: PATCH /admin/api/products/{id}
            const p = this.mockProducts.find(x => x.id === id);
            if (p) { p.status = 'archived'; this.applyFilters(); }
        },

        deleteProduct(id) { // @todo: DELETE /admin/api/products/{id}
            if (confirm('Delete this product?')) {
                this.mockProducts = this.mockProducts.filter(p => p.id !== id);
                this.applyFilters();
            }
        }
    };
}
</script>
@endsection
