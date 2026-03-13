{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN PRODUCT CREATE/EDIT PAGE - ENHANCED v3                     ║
║  Added: SKU, Tags, Collection, Category Modal, Selling Method     ║
║         Modal, config_type-driven unit rendering, extensible      ║
║         selling methods architecture, updated payload             ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Create Product')

@push('styles')
{{-- Quill Rich Text Editor --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<style>
    /* Quill overrides for dark mode */
    .ql-toolbar.ql-snow {
        border-color: #d1d5db;
        border-radius: 8px 8px 0 0;
        background: #f9fafb;
        padding: 6px 8px;
    }
    .dark .ql-toolbar.ql-snow {
        border-color: #374151;
        background: #111827;
    }
    .ql-container.ql-snow {
        border-color: #d1d5db;
        border-radius: 0 0 8px 8px;
        font-size: 0.875rem;
        min-height: 100px;
    }
    .dark .ql-container.ql-snow {
        border-color: #374151;
        background: #111827;
        color: #f9fafb;
    }
    .dark .ql-editor.ql-blank::before { color: #6b7280; }
    .dark .ql-snow .ql-stroke { stroke: #9ca3af; }
    .dark .ql-snow .ql-fill { fill: #9ca3af; }
    .dark .ql-snow .ql-picker { color: #9ca3af; }
    .dark .ql-snow .ql-picker-options {
        background-color: #1f2937;
        border-color: #374151;
    }

    /* Image upload drag zone */
    .upload-zone { transition: border-color 0.2s, background-color 0.2s; }
    .upload-zone.drag-over {
        border-color: var(--color-brand, #10b981) !important;
        background-color: rgba(16, 185, 129, 0.05);
    }

    /* Schema preview */
    .schema-preview {
        font-family: 'Courier New', monospace;
        font-size: 0.7rem;
        line-height: 1.5;
    }

    /* Tag pill animation */
    .tag-pill { animation: tagIn 0.15s ease; }
    @keyframes tagIn {
        from { opacity: 0; transform: scale(0.85); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* Modal backdrop */
    .modal-backdrop {
        backdrop-filter: blur(2px);
    }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E4E4E7; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #D4D4D8; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #3F3F46; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525B; }
</style>
@endpush

@section('content')

<div x-data="productFormManager()" class="space-y-6" @media-selected.window="handleMediaSelected($event.detail)">

    {{-- ════════════════════════════════════════════════════════════
         CATEGORY MODAL
         - Opens when "Add Category" button is clicked
         - Auto-generates slug from name
         - Inserts new category into options immediately
         - "Create & Create Another" keeps modal open and resets form
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="modals.category"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="modals.category = false"
        style="display:none"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-md"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <h2 class="font-semibold text-neutral-900 dark:text-neutral-50">Add New Category</h2>
                <button @click="modals.category = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Category Name *</label>
                    <input
                        type="text"
                        x-model="categoryModal.name"
                        @input="categoryModal.slugEdited || (categoryModal.slug = slugify(categoryModal.name))"
                        placeholder="e.g. Brocade Fabrics"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                        @keydown.enter="createCategory(false)"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug *</label>
                    <input
                        type="text"
                        x-model="categoryModal.slug"
                        @input="categoryModal.slugEdited = true"
                        placeholder="auto-generated"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm font-mono"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description <span class="text-neutral-400 font-normal">(optional)</span></label>
                    <textarea
                        x-model="categoryModal.description"
                        rows="2"
                        placeholder="Brief description of this category"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                    ></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="categoryModal.isActive" class="w-4 h-4">
                    <span class="text-sm text-neutral-700 dark:text-neutral-300">Active</span>
                </label>
            </div>
            <div class="flex items-center gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="createCategory(false)" class="flex-1 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-medium text-sm">
                    Create Now
                </button>
                <button @click="createCategory(true)" class="flex-1 px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">
                    Create &amp; Add Another
                </button>
                <button @click="modals.category = false" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         SELLING METHOD MODAL
         - Opens when "Add Selling Method" button is clicked
         - Admin defines name/slug/description and maps to a config_type
         - Newly created method appears in the list immediately
         - config_type drives unit configuration rendering
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="modals.sellingMethod"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="modals.sellingMethod = false"
        style="display:none"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-md"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="font-semibold text-neutral-900 dark:text-neutral-50">Add Selling Method</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Custom methods must map to a known config type</p>
                </div>
                <button @click="modals.sellingMethod = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Method Name *</label>
                    <input
                        type="text"
                        x-model="sellingMethodModal.name"
                        @input="sellingMethodModal.slugEdited || (sellingMethodModal.slug = slugify(sellingMethodModal.name))"
                        placeholder="e.g. Per Roll, Per Bale"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug *</label>
                    <input
                        type="text"
                        x-model="sellingMethodModal.slug"
                        @input="sellingMethodModal.slugEdited = true"
                        placeholder="auto-generated"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm font-mono"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description <span class="text-neutral-400 font-normal">(optional)</span></label>
                    <textarea
                        x-model="sellingMethodModal.description"
                        rows="2"
                        placeholder="e.g. Fabric sold by the roll"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                    ></textarea>
                </div>
                {{-- Config type — this is the KEY field: controls which unit config UI renders --}}
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Config Type *</label>
                    <select
                        x-model="sellingMethodModal.configType"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                    >
                        <option value="">Select config type...</option>
                        <template x-for="ct in configTypes" :key="ct.value">
                            <option :value="ct.value" x-text="ct.label + ' — ' + ct.hint"></option>
                        </template>
                    </select>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        Determines which fields appear in Unit Configuration. Admin cannot invent new field structures.
                    </p>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="sellingMethodModal.isActive" class="w-4 h-4">
                    <span class="text-sm text-neutral-700 dark:text-neutral-300">Active</span>
                </label>
            </div>
            <div class="flex items-center gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="createSellingMethod(false)" class="flex-1 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-medium text-sm">
                    Create Now
                </button>
                <button @click="createSellingMethod(true)" class="flex-1 px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">
                    Create &amp; Add Another
                </button>
                <button @click="modals.sellingMethod = false" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         HEADER WITH BACK BUTTON
    ════════════════════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <nav class="flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-400 mb-1">
                    <a href="{{ route('admin.products.index') }}" class="hover:text-neutral-900 dark:hover:text-neutral-50">Products</a>
                    <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
                    <span class="text-neutral-900 dark:text-neutral-50 font-medium">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h1>
                <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">Configure details, pricing, inventory, variants and selling method</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <button @click="expandAllSections()" class="text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 font-medium">Expand All</button>
            <span class="text-neutral-400 dark:text-neutral-600">—</span>
            <button @click="collapseAllSections()" class="text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 font-medium">Collapse All</button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FORM CONTAINER
    ════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- MAIN FORM AREA --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- ════════════════════════════════════════════════════════
                 BASIC INFORMATION (COLLAPSIBLE)
                 Updated: SKU field, Collection field, Tags field,
                          Category inline-create button
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.basic = !sections.basic" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Basic Information</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.basic && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.basic" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">

                    {{-- Product Name + Slug --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Product Name *</label>
                            <input type="text" x-model="form.name" @input="autoGenerateSlug(); autoGenerateSku(); generateSchemaMarkup()" placeholder="e.g. Premium Ankara Fabric" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug *</label>
                            <input type="text" x-model="form.slug" @input="slugManuallyEdited = true" placeholder="auto-generated" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1" x-show="!slugManuallyEdited">Auto-generated from name</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1" x-show="slugManuallyEdited">Custom slug (manually set)</p>
                        </div>
                    </div>

                    {{-- SKU Field (NEW) --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">SKU <span class="text-neutral-400 font-normal">(Stock Keeping Unit)</span></label>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                x-model="form.sku"
                                @input="skuManuallyEdited = true"
                                placeholder="e.g. ANK-PREMI-001"
                                class="flex-1 px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm font-mono"
                            >
                            {{-- Auto-generate SKU from name + category --}}
                            <button
                                @click="autoGenerateSku(true)"
                                title="Auto-generate SKU from name and category"
                                class="px-3 py-2 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors text-xs font-medium flex items-center gap-1 whitespace-nowrap"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Generate
                            </button>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">SKU must be unique across all products.</p>
                    </div>

                    {{-- Category + Add Category button --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Category *</label>
                            {{-- Add Category inline action button --}}
                            <button
                                @click="openCategoryModal()"
                                class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 flex items-center gap-1 transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Category
                            </button>
                        </div>
                        <select x-model="form.categoryId" @change="generateSchemaMarkup()" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm">
                            <option value="">Select category</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Collection (NEW) — Men / Women / Both --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Collection</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="form.collection" value="men" class="w-4 h-4">
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">Men</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="form.collection" value="women" class="w-4 h-4">
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">Women</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="form.collection" value="both" class="w-4 h-4">
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">Both</span>
                            </label>
                        </div>
                    </div>

                    {{-- Rich Text Description --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description</label>
                        <div id="quill-editor" class="rounded-lg overflow-hidden border border-neutral-300 dark:border-neutral-700">
                            <div id="quill-toolbar">
                                <span class="ql-formats">
                                    <select class="ql-header"><option selected></option><option value="2">H2</option><option value="3">H3</option></select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-link"></button>
                                    <button class="ql-clean"></button>
                                </span>
                            </div>
                            <div id="quill-body" style="min-height:100px"></div>
                        </div>
                        <textarea x-model="form.description" id="description-hidden" class="hidden"></textarea>
                    </div>

                    {{-- Tags Input (NEW)
                         - Type + Enter or comma to add tags
                         - Renders as removable pills
                         - Prevents duplicates
                         - Stored as array
                    --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Tags</label>
                        <div class="border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 bg-white dark:bg-neutral-900 focus-within:ring-2 focus-within:ring-brand focus-within:border-transparent">
                            {{-- Existing tags as pills --}}
                            <div class="flex flex-wrap gap-1.5 mb-1.5" x-show="form.tags.length > 0">
                                <template x-for="(tag, tIdx) in form.tags" :key="tag">
                                    <span class="tag-pill inline-flex items-center gap-1 px-2.5 py-1 bg-brand/10 dark:bg-brand/20 text-brand dark:text-brand-300 rounded-full text-xs font-medium">
                                        <span x-text="tag"></span>
                                        <button @click="removeTag(tIdx)" class="hover:text-brand-700 dark:hover:text-brand-100 transition-colors leading-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            {{-- Tag input --}}
                            <input
                                type="text"
                                x-model="tagInput"
                                @keydown.enter.prevent="addTag()"
                                @keydown="if($event.key === ',') { $event.preventDefault(); addTag(); }"
                                placeholder="Type a tag and press Enter or comma..."
                                class="w-full border-none outline-none text-sm text-neutral-900 dark:text-neutral-50 bg-transparent placeholder-neutral-400 dark:placeholder-neutral-500"
                            >
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Press Enter or comma to add. Click × to remove.</p>
                    </div>

                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 PRODUCT IMAGES (COLLAPSIBLE) — unchanged
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.images = !sections.images" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Product Images</h3>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">(1 main + up to 4 thumbnails)</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.images && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.images" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        If color variants are defined, each variant can have its own images. These product-level images serve as the default gallery when a variant has no images.
                    </p>

                    {{-- Main Image Upload --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Main Image *</label>
                            <button
                                type="button"
                                @click="$dispatch('open-media-picker', { mode: 'single', target: 'mainImage' })"
                                class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 flex items-center gap-1 transition-colors"
                                title="Pick from media library"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                From Media Library
                            </button>
                        </div>
                        <div
                            class="upload-zone border-2 border-dashed border-neutral-300 dark:border-neutral-700 rounded-lg p-4 text-center cursor-pointer hover:border-brand dark:hover:border-brand-400 transition-colors"
                            @click="$refs.mainImageInput.click()"
                            @dragover.prevent="$el.classList.add('drag-over')"
                            @dragleave.prevent="$el.classList.remove('drag-over')"
                            @drop.prevent="handleMainImageDrop($event); $el.classList.remove('drag-over')"
                        >
                            <template x-if="!form.mainImagePreview">
                                <div class="py-4">
                                    <svg class="w-8 h-8 text-neutral-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs text-neutral-600 dark:text-neutral-400">Click or drag & drop main image</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">JPG, PNG, WebP — Max 5MB</p>
                                </div>
                            </template>
                            <template x-if="form.mainImagePreview">
                                <div class="relative inline-block">
                                    <img :src="form.mainImagePreview" class="h-32 w-auto rounded-lg object-cover mx-auto">
                                    <button @click.stop="clearMainImage()" class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <p class="text-xs text-neutral-500 mt-2" x-text="form.mainImageFile ? form.mainImageFile.name : 'Current image'"></p>
                                </div>
                            </template>
                        </div>
                        <input type="file" x-ref="mainImageInput" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleMainImageSelect($event)">
                    </div>

                    {{-- Thumbnail Images --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-4 flex-1">
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Thumbnail Gallery <span class="text-neutral-400 dark:text-neutral-500 font-normal">(optional, max 4)</span></label>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400" x-text="form.thumbnails.length + '/4 added'"></span>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('open-media-picker', { mode: 'single', target: 'thumbnails' })"
                                class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 flex items-center gap-1 transition-colors"
                                title="Pick from media library"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Add from Media
                            </button>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(thumb, idx) in form.thumbnails" :key="idx">
                                <div class="relative group aspect-square rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-900">
                                    <img :src="thumb.preview" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button @click="removeThumbnail(idx)" class="w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <span class="absolute bottom-1 left-1 text-xs bg-black/50 text-white rounded px-1" x-text="idx + 1"></span>
                                </div>
                            </template>
                            <template x-if="form.thumbnails.length < 4">
                                <div
                                    class="upload-zone aspect-square rounded-lg border-2 border-dashed border-neutral-300 dark:border-neutral-700 flex flex-col items-center justify-center cursor-pointer hover:border-brand dark:hover:border-brand-400 transition-colors"
                                    @click="$refs.thumbInput.click()"
                                    @dragover.prevent="$el.classList.add('drag-over')"
                                    @dragleave.prevent="$el.classList.remove('drag-over')"
                                    @drop.prevent="handleThumbnailDrop($event); $el.classList.remove('drag-over')"
                                >
                                    <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Add</span>
                                </div>
                            </template>
                        </div>
                        <input type="file" x-ref="thumbInput" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleThumbnailSelect($event)">
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 SELLING METHOD (COLLAPSIBLE)
                 Updated: now renders from sellingMethods array (system + custom)
                          "Add Selling Method" button opens modal
                          Custom methods show their config_type badge
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.sellingMethod = !sections.sellingMethod" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Selling Method *</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.sellingMethod && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.sellingMethod" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-3">

                    {{-- Add Selling Method inline action --}}
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">System methods + any custom methods you create</p>
                        <button
                            @click="openSellingMethodModal()"
                            class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 flex items-center gap-1 transition-colors"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Selling Method
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-2">
                        <template x-for="method in sellingMethods.filter(m => m.isActive)" :key="method.id">
                            <label class="flex items-center gap-3 p-3 border border-neutral-200 dark:border-neutral-700 rounded-lg cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                                <input type="radio" x-model="form.sellingMethodId" :value="method.id" @change="onSellingMethodChange(); generateSchemaMarkup()" class="w-4 h-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-sm text-neutral-900 dark:text-neutral-50" x-text="method.name"></p>
                                        {{-- Custom method badge showing config_type --}}
                                        <template x-if="!method.isSystem">
                                            <span class="text-xs bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 px-1.5 py-0.5 rounded font-medium" x-text="method.configType.replace('_', ' ')"></span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-neutral-600 dark:text-neutral-400 truncate" x-text="method.description"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 UNIT CONFIGURATION (ADAPTIVE - COLLAPSIBLE)
                 REFACTORED: Now driven by config_type, not method id.
                 This means custom selling methods work automatically
                 as long as they map to a known config_type.
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.unitConfig = !sections.unitConfig" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v3m-6-1v-7a2 2 0 012-2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50" x-text="getUnitSectionTitle()"></h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.unitConfig && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.unitConfig && form.sellingMethodId" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">

                    {{-- PER LENGTH config_type --}}
                    <template x-if="getSelectedConfigType() === 'per_length'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Length Unit</label>
                                    <select x-model="form.lengthUnit" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand text-sm">
                                        <option value="yards">Yards</option>
                                        <option value="meters">Meters</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1" x-text="form.lengthUnit.charAt(0).toUpperCase() + form.lengthUnit.slice(1) + ' per selling unit'"></label>
                                    <input type="number" x-model.number="form.unitsPerOrder" placeholder="5" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Minimum selling units</label>
                                    <input type="number" x-model.number="form.minQuantity" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity increase step</label>
                                    <input type="number" x-model.number="form.quantityStep" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-2">Customer purchase options:</p>
                                <p>1 unit = <span x-text="form.unitsPerOrder || 0"></span> <span x-text="form.lengthUnit"></span></p>
                                <p>Allowed orders: <span x-text="form.minQuantity || 1"></span>, <span x-text="(form.minQuantity || 1) + (form.quantityStep || 1)"></span>, <span x-text="(form.minQuantity || 1) + ((form.quantityStep || 1) * 2)"></span>... units</p>
                            </div>
                        </div>
                    </template>

                    {{-- PER SET config_type --}}
                    <template x-if="getSelectedConfigType() === 'per_set'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Unit Label</label>
                                    <input type="text" x-model="form.unitLabel" placeholder="e.g. set" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Minimum sets per order</label>
                                    <input type="number" x-model.number="form.minQuantity" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity step</label>
                                <input type="number" x-model.number="form.quantityStep" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                            </div>
                            <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Set Contents:</label>
                                <div class="space-y-2">
                                    <template x-for="(item, idx) in form.setContents" :key="idx">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="item.name" placeholder="Item name" class="flex-1 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                            <input type="number" x-model.number="item.quantity" placeholder="Qty" min="1" class="w-16 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                            <button @click="form.setContents.splice(idx, 1)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <button @click="addSetContentItem()" class="mt-2 text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200">+ Add item</button>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-2">Preview:</p>
                                <template x-if="form.setContents.length > 0">
                                    <div class="space-y-1">
                                        <template x-for="item in form.setContents" :key="item.name">
                                            <p x-text="item.name + ' × ' + (item.quantity || 1)"></p>
                                        </template>
                                        <p class="text-blue-800 dark:text-blue-200 mt-1">Minimum order: <span x-text="form.minQuantity || 1"></span> <span x-text="form.unitLabel || 'set'"></span>(s)</p>
                                    </div>
                                </template>
                                <template x-if="form.setContents.length === 0">
                                    <p>Add items above to show set contents</p>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- PER BUNDLE config_type --}}
                    <template x-if="getSelectedConfigType() === 'per_bundle'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Unit Label</label>
                                    <input type="text" x-model="form.unitLabel" placeholder="e.g. bundle" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Minimum bundles</label>
                                    <input type="number" x-model.number="form.minQuantity" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity step</label>
                                <input type="number" x-model.number="form.quantityStep" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                            </div>
                            <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Bundle Yield:</label>
                                <div class="space-y-2">
                                    <template x-for="(item, idx) in form.bundleYield" :key="idx">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="item.name" placeholder="Item name" class="flex-1 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                            <input type="number" x-model.number="item.quantity" placeholder="Qty" min="1" class="w-16 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                            <button @click="form.bundleYield.splice(idx, 1)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <button @click="addBundleYieldItem()" class="mt-2 text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200">+ Add item</button>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-2">Preview:</p>
                                <template x-if="form.bundleYield.length > 0">
                                    <div class="space-y-1">
                                        <template x-for="item in form.bundleYield" :key="item.name">
                                            <p x-text="item.name + ' × ' + (item.quantity || 1)"></p>
                                        </template>
                                        <p class="text-blue-800 dark:text-blue-200 mt-1">Minimum order: <span x-text="form.minQuantity || 1"></span> <span x-text="form.unitLabel || 'bundle'"></span>(s)</p>
                                    </div>
                                </template>
                                <template x-if="form.bundleYield.length === 0">
                                    <p>Add items above to show bundle yield</p>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- PER PIECE config_type --}}
                    <template x-if="getSelectedConfigType() === 'per_piece'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Unit Label</label>
                                    <input type="text" x-model="form.unitLabel" placeholder="e.g. piece" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Minimum pieces</label>
                                    <input type="number" x-model.number="form.minQuantity" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity step</label>
                                <input type="number" x-model.number="form.quantityStep" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-1">Example:</p>
                                <p>Customers buy per <span x-text="form.unitLabel || 'piece'"></span></p>
                                <p>Minimum: <span x-text="form.minQuantity || 1"></span> <span x-text="form.unitLabel || 'piece'"></span>(s)</p>
                            </div>
                        </div>
                    </template>

                    {{-- PER LOOM config_type --}}
                    <template x-if="getSelectedConfigType() === 'per_loom'">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Unit Label</label>
                                    <input type="text" x-model="form.unitLabel" placeholder="e.g. loom" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Minimum looms</label>
                                    <input type="number" x-model.number="form.minQuantity" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity step</label>
                                    <input type="number" x-model.number="form.quantityStep" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Loom size (e.g. 45 yards)</label>
                                    <input type="text" x-model="form.loomSize" placeholder="e.g. 45 yards" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-1">Customer purchase:</p>
                                <p>1 <span x-text="form.unitLabel || 'loom'"></span> = <span x-text="form.loomSize || '?'"></span></p>
                                <p>Minimum: <span x-text="form.minQuantity || 1"></span> <span x-text="form.unitLabel || 'loom'"></span>(s)</p>
                            </div>
                        </div>
                    </template>

                    {{-- CUSTOM UNIT config_type (admin-defined label, standard piece-like logic) --}}
                    <template x-if="getSelectedConfigType() === 'custom_unit'">
                        <div class="space-y-4">
                            <div class="p-3 bg-purple-50 dark:bg-purple-500/10 rounded-lg text-xs text-purple-900 dark:text-purple-300">
                                <p class="font-medium mb-1">Custom unit method selected.</p>
                                <p>Configure the unit label, minimum quantity and step below.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Unit Label *</label>
                                    <input type="text" x-model="form.unitLabel" placeholder="e.g. roll, bale, pack" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Minimum per order</label>
                                    <input type="number" x-model.number="form.minQuantity" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity step</label>
                                <input type="number" x-model.number="form.quantityStep" placeholder="1" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-1">Example:</p>
                                <p>Customers buy per <span x-text="form.unitLabel || 'unit'"></span></p>
                                <p>Minimum: <span x-text="form.minQuantity || 1"></span> <span x-text="form.unitLabel || 'unit'"></span>(s)</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="!form.sellingMethodId">
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg text-xs text-yellow-900 dark:text-yellow-300">
                            <p>Select a selling method above first</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 COLOR VARIANTS (COLLAPSIBLE) — unchanged structure
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.colorVariants = !sections.colorVariants" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Color Variants</h3>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">(optional)</span>
                            <template x-if="form.colorVariants.length > 0">
                                <span class="text-xs bg-brand/10 text-brand dark:text-brand-300 px-2 py-0.5 rounded-full font-medium" x-text="form.colorVariants.length + ' variant' + (form.colorVariants.length > 1 ? 's' : '')"></span>
                            </template>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.colorVariants && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.colorVariants" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300 space-y-1">
                        <p class="font-medium">How color variants work:</p>
                        <p>• Each color has its own stock quantity and optional images.</p>
                        <p>• On the storefront, selecting a color updates the displayed images.</p>
                        <p>• If a color has no images, the product gallery is used as fallback.</p>
                        <p>• When variants exist, total product stock is aggregated automatically.</p>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(variant, vIdx) in form.colorVariants" :key="vIdx">
                            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg overflow-hidden">
                                <div class="flex items-center gap-3 px-4 py-3 bg-neutral-100 dark:bg-neutral-900/50">
                                    <div class="w-5 h-5 rounded-full border border-neutral-300 dark:border-neutral-600 flex-shrink-0 cursor-pointer" :style="'background-color:' + (variant.hex || '#ccc')"></div>
                                    <span class="font-medium text-sm text-neutral-900 dark:text-neutral-50 flex-1" x-text="variant.name || 'Unnamed Color'"></span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400" x-text="(variant.stock || 0) + ' ' + (variant.stockUnit || 'units')"></span>
                                    <button @click="toggleVariant(vIdx)" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-300 text-xs px-2">
                                        <span x-text="variant._expanded ? 'Collapse' : 'Edit'"></span>
                                    </button>
                                    <button @click="removeColorVariant(vIdx)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div x-show="variant._expanded" class="px-4 py-3 space-y-3 border-t border-neutral-200 dark:border-neutral-700">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Color Name *</label>
                                            <input type="text" x-model="variant.name" placeholder="e.g. Royal Blue" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Hex Color</label>
                                            <div class="flex gap-2">
                                                <input type="color" x-model="variant.hex" class="w-8 h-8 rounded border border-neutral-300 dark:border-neutral-700 cursor-pointer p-0.5 bg-white dark:bg-neutral-900">
                                                <input type="text" x-model="variant.hex" placeholder="#000000" class="flex-1 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Price Adjustment (₦)</label>
                                            <input type="number" x-model.number="variant.priceAdjustment" placeholder="0" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">+ / - from base price</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Stock Quantity</label>
                                            <input type="number" x-model.number="variant.stock" placeholder="0" @input="updateAggregatedStock()" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Stock Unit <span class="text-neutral-400 font-normal">(auto-synced)</span></label>
                                            <input type="text" x-model="variant.stockUnit" :placeholder="getStockUnitPlaceholder()" readonly class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 opacity-75">
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-neutral-200 dark:border-neutral-700 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300">Variant Images <span class="text-neutral-400 font-normal">(optional — falls back to product gallery)</span></label>
                                            <button
                                                type="button"
                                                @click="$dispatch('open-media-picker', { mode: 'single', target: 'variantMainImage_' + vIdx })"
                                                class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 flex items-center gap-1 transition-colors"
                                                title="Pick from media library"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                From Media
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-1">
                                                <div class="upload-zone aspect-square rounded-lg border-2 border-dashed border-neutral-300 dark:border-neutral-700 flex flex-col items-center justify-center cursor-pointer hover:border-brand dark:hover:border-brand-400 transition-colors overflow-hidden relative" @click="triggerVariantMainImage(vIdx)">
                                                    <template x-if="variant.mainImagePreview">
                                                        <div class="absolute inset-0">
                                                            <img :src="variant.mainImagePreview" class="w-full h-full object-cover">
                                                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                                                <span class="text-white text-xs font-medium">Main</span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!variant.mainImagePreview">
                                                        <div class="text-center p-1">
                                                            <svg class="w-4 h-4 text-neutral-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            <span class="text-xs text-neutral-400 mt-0.5 block">Main</span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <template x-for="(vThumb, tIdx) in variant.thumbnails" :key="tIdx">
                                                <div class="relative group aspect-square rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700">
                                                    <img :src="vThumb.preview" class="w-full h-full object-cover">
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                        <button @click="removeVariantThumbnail(vIdx, tIdx)" class="w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="variant.thumbnails.length < 4">
                                                <div class="upload-zone aspect-square rounded-lg border-2 border-dashed border-neutral-300 dark:border-neutral-700 flex flex-col items-center justify-center cursor-pointer hover:border-brand dark:hover:border-brand-400 transition-colors" @click="triggerVariantThumb(vIdx)">
                                                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </div>
                                            </template>
                                        </div>
                                        <input :id="'variantMainImg_' + vIdx" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleVariantMainImage($event, vIdx)">
                                        <input :id="'variantThumb_' + vIdx" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleVariantThumb($event, vIdx)">
                                    </div>

                                    <label class="flex items-center gap-2 cursor-pointer pt-1">
                                        <input type="radio" x-model="form.defaultColorVariantIdx" :value="vIdx" class="w-4 h-4">
                                        <span class="text-xs text-neutral-700 dark:text-neutral-300">Set as default selected color on storefront</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button @click="addColorVariant()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-dashed border-neutral-300 dark:border-neutral-700 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:border-brand dark:hover:border-brand-400 hover:text-brand dark:hover:text-brand-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Color Variant
                    </button>

                    <template x-if="form.colorVariants.length > 0">
                        <div class="p-3 bg-green-50 dark:bg-green-500/10 rounded-lg text-xs text-green-900 dark:text-green-300">
                            <p class="font-medium">Aggregated stock (from all variants): <span x-text="getTotalVariantStock()"></span> <span x-text="getStockUnitPlaceholder()"></span></p>
                            <p class="text-green-700 dark:text-green-400 mt-0.5">Product-level inventory is calculated from variants when variants exist.</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 PRODUCT COMPOSITION (COLLAPSIBLE) — unchanged
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.composition = !sections.composition" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Product Composition</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.composition && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.composition" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-2">Items included in this product:</label>
                        <div class="space-y-2">
                            <template x-for="(item, idx) in form.includedItems" :key="idx">
                                <div class="flex gap-2">
                                    <input type="text" x-model="item.name" placeholder="Item name" class="flex-1 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                    <input type="number" x-model.number="item.quantity" placeholder="1" min="1" class="w-14 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                    <button @click="form.includedItems.splice(idx, 1)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button @click="addIncludedItem()" class="mt-2 text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200">+ Add item</button>
                    </div>
                    <div class="pt-3 border-t border-neutral-200 dark:border-neutral-800">
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Not included / Image disclaimer</label>
                        <textarea x-model="form.excludesText" placeholder="e.g. Model's accessories and shoes not included" rows="2" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900"></textarea>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 PRICING (COLLAPSIBLE) — unchanged
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.pricing = !sections.pricing" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Pricing</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.pricing && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.pricing" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Sale Price (₦) *</label>
                            <input type="number" x-model.number="form.price" @input="generateSchemaMarkup()" placeholder="0" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Compare-at Price (₦)</label>
                            <input type="number" x-model.number="form.comparePrice" placeholder="Optional" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Discount Type</label>
                            <select x-model="form.discountType" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                <option value="">None</option>
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed (₦)</option>
                            </select>
                        </div>
                        <div x-show="form.discountType">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1" x-text="form.discountType === 'percent' ? 'Discount %' : 'Discount (₦)'"></label>
                            <input type="number" x-model.number="form.discountValue" placeholder="0" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                        </div>
                        <div x-show="form.discountType">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Final Price</label>
                            <div class="px-3 py-2 bg-neutral-100 dark:bg-neutral-900 rounded-lg text-sm font-medium text-neutral-900 dark:text-neutral-50">
                                ₦<span x-text="calculateFinalPrice().toLocaleString()"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Cost (₦)</label>
                        <input type="number" x-model.number="form.cost" placeholder="Your cost" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-1" x-show="form.cost">Gross profit: ₦<span x-text="(form.price - form.cost).toLocaleString()"></span> (<span x-text="((form.price - form.cost) / form.price * 100).toFixed(1)"></span>%)</p>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 INVENTORY (COLLAPSIBLE) — unchanged
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.inventory = !sections.inventory" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Inventory</h3>
                        <template x-if="form.colorVariants.length > 0">
                            <span class="text-xs bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Managed per variant</span>
                        </template>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.inventory && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.inventory" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-3">
                    <template x-if="form.colorVariants.length > 0">
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg text-xs text-yellow-900 dark:text-yellow-300 space-y-2">
                            <p class="font-medium">Stock is managed per color variant.</p>
                            <p>Total stock (aggregated): <strong x-text="getTotalVariantStock()"></strong> <span x-text="getStockUnitPlaceholder()"></span></p>
                            <p>Edit individual variant stocks in the Color Variants section above.</p>
                        </div>
                    </template>
                    <template x-if="form.colorVariants.length === 0">
                        <div class="space-y-3">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" x-model="form.trackInventory" class="w-4 h-4">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Track inventory for this product</span>
                            </label>
                            <template x-if="form.trackInventory">
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Quantity on hand</label>
                                            <input type="number" x-model.number="form.stockQuantity" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Low stock alert threshold</label>
                                            <input type="number" x-model.number="form.lowStockThreshold" placeholder="5" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Stock unit <span class="text-xs text-neutral-500 dark:text-neutral-400">(auto-synced with selling method)</span></label>
                                        <input type="text" x-model="form.stockUnit" readonly class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm opacity-75" :placeholder="getStockUnitPlaceholder()">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 RECOMMENDED ADD-ONS (COLLAPSIBLE) — unchanged
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.addOns = !sections.addOns" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Recommended Add-ons</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.addOns && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.addOns" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">
                    <div class="relative">
                        <input type="text" x-model="addOnSearch" @input="filterAddOns()" placeholder="Search for products to add..." class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                        <div x-show="addOnSearch && filteredAddOns.length > 0" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-lg z-10 max-h-48 overflow-y-auto custom-scrollbar">
                            <template x-for="addon in filteredAddOns" :key="addon.id">
                                <button @click="addAddOn(addon)" class="w-full text-left px-3 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 last:border-b-0 text-xs">
                                    <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="addon.name"></p>
                                    <p class="text-neutral-600 dark:text-neutral-400" x-text="'₦' + addon.price.toLocaleString()"></p>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(addon, idx) in form.addOns" :key="idx">
                            <div class="flex items-center justify-between p-2 bg-neutral-100 dark:bg-neutral-900 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-neutral-900 dark:text-neutral-50" x-text="addon.name"></p>
                                    <p class="text-xs text-neutral-600 dark:text-neutral-400" x-text="'₦' + addon.price.toLocaleString()"></p>
                                </div>
                                <button @click="removeAddOn(idx)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="form.addOns.length === 0" class="text-xs text-neutral-600 dark:text-neutral-400">No add-ons selected</p>
                    </div>
                    <div class="border-t border-neutral-200 dark:border-neutral-800 pt-3 space-y-2">
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="form.showAddOnsAfterCheckout" class="w-4 h-4"><span class="text-sm text-neutral-700 dark:text-neutral-300">Show after "Add to Cart" step</span></label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="form.showAddOnsInCart" class="w-4 h-4"><span class="text-sm text-neutral-700 dark:text-neutral-300">Show in shopping cart</span></label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="form.showAddOnsOnPage" class="w-4 h-4"><span class="text-sm text-neutral-700 dark:text-neutral-300">Show on product page</span></label>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 COUPONS (COLLAPSIBLE) — unchanged
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.coupons = !sections.coupons" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Product Coupons</h3>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">(optional)</span>
                            <template x-if="form.coupons.length > 0">
                                <span class="text-xs bg-brand/10 text-brand dark:text-brand-300 px-2 py-0.5 rounded-full font-medium" x-text="form.coupons.length + ' coupon' + (form.coupons.length > 1 ? 's' : '')"></span>
                            </template>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.coupons && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.coupons" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">These coupons apply only to this product. Global coupons can be managed from the Coupons section.</p>
                    <div class="space-y-3">
                        <template x-for="(coupon, cIdx) in form.coupons" :key="cIdx">
                            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg overflow-hidden">
                                <div class="flex items-center gap-3 px-4 py-3 bg-neutral-100 dark:bg-neutral-900/50">
                                    <svg class="w-4 h-4 text-brand dark:text-brand-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span class="font-mono font-medium text-sm text-neutral-900 dark:text-neutral-50 flex-1 uppercase" x-text="coupon.code || 'UNTITLED'"></span>
                                    <span class="text-xs text-brand dark:text-brand-300 font-medium" x-text="coupon.discountPercent ? coupon.discountPercent + '% off' : '—'"></span>
                                    <button @click="coupon._expanded = !coupon._expanded" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-300 text-xs px-2"><span x-text="coupon._expanded ? 'Collapse' : 'Edit'"></span></button>
                                    <button @click="form.coupons.splice(cIdx, 1)" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div x-show="coupon._expanded" class="px-4 py-3 space-y-3 border-t border-neutral-200 dark:border-neutral-700">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Coupon Code *</label>
                                            <div class="flex gap-2">
                                                <input type="text" x-model="coupon.code" placeholder="e.g. SAVE20" maxlength="20" class="flex-1 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 font-mono uppercase" @input="coupon.code = coupon.code.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                                                <button @click="coupon.code = generateCouponCode()" class="px-2 py-1.5 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded text-xs hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors" title="Auto-generate code">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Discount Percentage *</label>
                                            <div class="flex gap-2 items-center">
                                                <input type="number" x-model.number="coupon.discountPercent" placeholder="e.g. 15" min="1" max="100" class="flex-1 px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                                <span class="text-sm text-neutral-500 dark:text-neutral-400">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Expiry Date <span class="text-neutral-400 font-normal">(optional)</span></label>
                                            <input type="date" x-model="coupon.expiryDate" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Max Uses <span class="text-neutral-400 font-normal">(0 = unlimited)</span></label>
                                            <input type="number" x-model.number="coupon.maxUses" placeholder="0" min="0" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Min Order Amount (₦) <span class="text-neutral-400 font-normal">(optional)</span></label>
                                            <input type="number" x-model.number="coupon.minOrderAmount" placeholder="0" min="0" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900">
                                        </div>
                                        <div class="flex flex-col justify-end gap-2">
                                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" x-model="coupon.newUsersOnly" class="w-4 h-4"><span class="text-xs text-neutral-700 dark:text-neutral-300">New customers only</span></label>
                                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" x-model="coupon.isActive" class="w-4 h-4"><span class="text-xs text-neutral-700 dark:text-neutral-300">Active</span></label>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-brand/5 dark:bg-brand/10 border border-brand/20 dark:border-brand/30 rounded-lg text-xs">
                                        <p class="font-medium text-neutral-900 dark:text-neutral-50 mb-1">Coupon summary:</p>
                                        <p class="text-neutral-600 dark:text-neutral-400">
                                            Code: <strong class="text-neutral-900 dark:text-neutral-50 font-mono" x-text="coupon.code || '—'"></strong> —
                                            <span x-text="coupon.discountPercent ? coupon.discountPercent + '% off' : 'no discount set'"></span>
                                            <template x-if="coupon.newUsersOnly"> for new customers</template>
                                            <template x-if="coupon.expiryDate"> · expires <span x-text="coupon.expiryDate"></span></template>
                                            <template x-if="coupon.maxUses > 0"> · max <span x-text="coupon.maxUses"></span> uses</template>
                                            <template x-if="coupon.minOrderAmount > 0"> · min order ₦<span x-text="(coupon.minOrderAmount || 0).toLocaleString()"></span></template>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button @click="addCoupon()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-dashed border-neutral-300 dark:border-neutral-700 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:border-brand dark:hover:border-brand-400 hover:text-brand dark:hover:text-brand-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Coupon
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 SEO & META (COLLAPSIBLE) — unchanged structure
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.seo = !sections.seo" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">SEO & Meta</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.seo && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.seo" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Page Title</label>
                        <input type="text" x-model="form.metaTitle" @input="generateSchemaMarkup()" placeholder="Leave blank to use product name" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1"><span x-text="(form.metaTitle || form.name).length"></span>/60 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Meta Description</label>
                        <textarea x-model="form.metaDescription" @input="generateSchemaMarkup()" placeholder="Compelling description for search engines..." rows="3" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm"></textarea>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1"><span x-text="form.metaDescription.length"></span>/160 characters</p>
                    </div>

                    {{-- Schema Markup --}}
                    <div class="pt-2 border-t border-neutral-200 dark:border-neutral-800">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Schema Markup (JSON-LD)</label>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Auto-generated from your product data.</p>
                            </div>
                            <button @click="generateSchemaMarkup(); sections.schemaPreview = !sections.schemaPreview" class="text-xs text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 font-medium flex items-center gap-1">
                                <span x-text="sections.schemaPreview ? 'Hide preview' : 'Preview'"></span>
                                <svg class="w-3 h-3 transition-transform" :class="sections.schemaPreview && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-xs bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">✓ Product</span>
                            <span class="text-xs bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">✓ Offer</span>
                            <template x-if="form.colorVariants.length > 0">
                                <span class="text-xs bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">✓ Color Variants</span>
                            </template>
                            <template x-if="form.trackInventory || form.colorVariants.length > 0">
                                <span class="text-xs bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">✓ Availability</span>
                            </template>
                            <template x-if="form.comparePrice > 0">
                                <span class="text-xs bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">✓ High Price</span>
                            </template>
                        </div>
                        <div x-show="sections.schemaPreview" class="bg-neutral-900 dark:bg-neutral-950 rounded-lg p-3 overflow-x-auto">
                            <pre class="schema-preview text-green-400" x-text="schemaMarkup"></pre>
                        </div>
                        <input type="hidden" x-model="schemaMarkup" name="schema_markup">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">Schema updates automatically as you fill in product details.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             SIDEBAR
             Updated: shows SKU, collection, tags count,
                      selected selling method label+config type
        ════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Status Card --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4 space-y-3 sticky top-6">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Status & Visibility</h3>
                <select x-model="form.status" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand text-sm">
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>

                <div class="space-y-2 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="form.featured" class="w-4 h-4 rounded">
                        <div>
                            <span class="text-sm text-neutral-700 dark:text-neutral-300">Featured product</span>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Highlighted in featured sections</p>
                        </div>
                    </label>

                    <div class="border-t border-neutral-200 dark:border-neutral-800 pt-2 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="form.isNewArrival" @change="!form.isNewArrival && (form.newArrivalExpiry = '')" class="w-4 h-4 rounded">
                            <div>
                                <span class="text-sm text-neutral-700 dark:text-neutral-300 flex items-center gap-1.5">
                                    New Arrival
                                    <span class="text-xs bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded font-medium">NEW</span>
                                </span>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Shows "New Arrival" badge on product</p>
                            </div>
                        </label>
                        <template x-if="form.isNewArrival">
                            <div>
                                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Badge expiry <span class="text-neutral-400 font-normal">(optional)</span></label>
                                <input type="date" x-model="form.newArrivalExpiry" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-xs">
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                    <template x-if="form.newArrivalExpiry"><span>Badge auto-removes on <span x-text="form.newArrivalExpiry"></span></span></template>
                                    <template x-if="!form.newArrivalExpiry"><span>No expiry — badge stays until manually removed</span></template>
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="space-y-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                    <button onclick="window.history.back()" class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors font-medium text-sm">Cancel</button>
                    <button @click="saveDraft()" class="w-full px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">Save Draft</button>
                    <button @click="publishProduct()" class="w-full px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-medium text-sm">Publish</button>
                </div>
            </div>

            {{-- Selling Detail Summary — UPDATED with new fields --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4 space-y-3">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Product Summary</h3>
                <div class="space-y-2 text-xs">

                    {{-- SKU (NEW) --}}
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">SKU</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50 font-mono" x-text="form.sku || '—'"></p>
                    </div>

                    {{-- Collection (NEW) --}}
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Collection</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50 capitalize" x-text="form.collection || '—'"></p>
                    </div>

                    {{-- Selling Method (updated to show config_type) --}}
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Method</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="getSelectedMethodLabel()"></p>
                        <template x-if="getSelectedConfigType()">
                            <p class="text-neutral-500 dark:text-neutral-500 mt-0.5">Config: <span x-text="getSelectedConfigType().replace('_', ' ')"></span></p>
                        </template>
                    </div>

                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Unit</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="form.unitLabel || '—'"></p>
                    </div>

                    <template x-if="getSelectedConfigType() === 'per_loom' && form.loomSize">
                        <div>
                            <p class="text-neutral-600 dark:text-neutral-400">Loom Size</p>
                            <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="form.loomSize"></p>
                        </div>
                    </template>

                    {{-- Tags summary (NEW) --}}
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Tags</p>
                        <template x-if="form.tags.length > 0">
                            <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="form.tags.length + ' tag' + (form.tags.length > 1 ? 's' : '') + ': ' + form.tags.slice(0,3).join(', ') + (form.tags.length > 3 ? '...' : '')"></p>
                        </template>
                        <template x-if="form.tags.length === 0">
                            <p class="font-medium text-neutral-900 dark:text-neutral-50">—</p>
                        </template>
                    </div>

                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Included Items</p>
                        <template x-if="form.includedItems.length > 0">
                            <div class="space-y-0.5">
                                <template x-for="item in form.includedItems" :key="item.name">
                                    <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="item.name + ' × ' + (item.quantity || 1)"></p>
                                </template>
                            </div>
                        </template>
                        <template x-if="form.includedItems.length === 0">
                            <p class="font-medium text-neutral-900 dark:text-neutral-50">—</p>
                        </template>
                    </div>

                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Color Variants</p>
                        <template x-if="form.colorVariants.length > 0">
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="v in form.colorVariants" :key="v.name">
                                    <div class="flex items-center gap-1">
                                        <div class="w-3 h-3 rounded-full border border-neutral-300 dark:border-neutral-600" :style="'background-color:' + (v.hex || '#ccc')"></div>
                                        <span class="text-neutral-900 dark:text-neutral-50 font-medium" x-text="v.name || '?'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="form.colorVariants.length === 0">
                            <p class="font-medium text-neutral-900 dark:text-neutral-50">—</p>
                        </template>
                    </div>

                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Stock</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="form.colorVariants.length > 0 ? getTotalVariantStock() + ' ' + getStockUnitPlaceholder() + ' (aggregated)' : (form.trackInventory ? form.stockQuantity + ' ' + (form.stockUnit || 'units') : '—')"></p>
                    </div>

                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Badges</p>
                        <div class="flex gap-1 mt-0.5">
                            <template x-if="form.featured">
                                <span class="text-xs bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 px-1.5 py-0.5 rounded font-medium">Featured</span>
                            </template>
                            <template x-if="form.isNewArrival">
                                <span class="text-xs bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded font-medium">New Arrival</span>
                            </template>
                            <template x-if="!form.featured && !form.isNewArrival">
                                <span class="font-medium text-neutral-900 dark:text-neutral-50">—</span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pricing Summary — unchanged --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4 space-y-3">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Pricing Summary</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-neutral-600 dark:text-neutral-400">Sale Price</span>
                        <span class="font-medium text-neutral-900 dark:text-neutral-50">₦<span x-text="(form.price || 0).toLocaleString()"></span></span>
                    </div>
                    <template x-if="form.discountType">
                        <div class="flex justify-between">
                            <span class="text-neutral-600 dark:text-neutral-400">Discount</span>
                            <span class="text-red-600 dark:text-red-400 font-medium" x-text="(form.discountType === 'percent' ? form.discountValue + '%' : '₦' + (form.discountValue || 0))"></span>
                        </div>
                    </template>
                    <div class="flex justify-between border-t border-neutral-200 dark:border-neutral-800 pt-2">
                        <span class="text-neutral-700 dark:text-neutral-300 font-medium">Final Price</span>
                        <span class="font-bold text-neutral-900 dark:text-neutral-50">₦<span x-text="calculateFinalPrice().toLocaleString()"></span></span>
                    </div>
                    <template x-if="form.coupons.length > 0">
                        <div class="pt-1 border-t border-neutral-200 dark:border-neutral-800">
                            <p class="text-neutral-600 dark:text-neutral-400 mb-1">Active Coupons</p>
                            <template x-for="c in form.coupons.filter(x => x.isActive && x.code)" :key="c.code">
                                <div class="flex justify-between items-center">
                                    <span class="font-mono text-xs text-neutral-900 dark:text-neutral-50" x-text="c.code"></span>
                                    <span class="text-brand dark:text-brand-300 font-medium" x-text="c.discountPercent + '% off'"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>

@verbatim
<script>
// ════════════════════════════════════════════════════════════
// ALPINE.JS FORM MANAGER - v3
// Key changes from v2:
//  - sellingMethods now extensible array (system + custom)
//  - config_type drives unit config rendering (not method id)
//  - category is now an array (categories) supporting inline add
//  - form.categoryId replaces form.category (id-based)
//  - form.collection added (men/women/both)
//  - form.sku added with auto-generation
//  - form.tags added with tag input logic
//  - modals{} manages category + sellingMethod modal state
//  - categoryModal{} holds modal form state
//  - sellingMethodModal{} holds modal form state
//  - configTypes[] defines known config type options
//  - buildPayload() updated with new fields
// ════════════════════════════════════════════════════════════
function productFormManager() {
    return {
        addOnSearch: '',
        slugManuallyEdited: false,
        skuManuallyEdited: false,
        schemaMarkup: '',
        quillEditor: null,

        // Tag input buffer
        tagInput: '',

        // ─────────────────────────────────────────────────────────
        // MODAL STATE
        // ─────────────────────────────────────────────────────────
        modals: {
            category: false,
            sellingMethod: false,
        },

        // ─────────────────────────────────────────────────────────
        // CATEGORY MODAL FORM STATE
        // Holds the in-progress new category being created
        // ─────────────────────────────────────────────────────────
        categoryModal: {
            name: '',
            slug: '',
            slugEdited: false,
            description: '',
            isActive: true,
        },

        // ─────────────────────────────────────────────────────────
        // SELLING METHOD MODAL FORM STATE
        // ─────────────────────────────────────────────────────────
        sellingMethodModal: {
            name: '',
            slug: '',
            slugEdited: false,
            description: '',
            configType: '',
            isActive: true,
        },

        // ─────────────────────────────────────────────────────────
        // KNOWN CONFIG TYPES
        // These are the only structures the unit config UI supports.
        // Admin cannot add new ones — they must map custom methods
        // to one of these. This keeps field rendering predictable.
        // ─────────────────────────────────────────────────────────
        configTypes: [
            { value: 'per_piece',  label: 'Per Piece',  hint: 'single items' },
            { value: 'per_set',    label: 'Per Set',    hint: 'grouped items in a set' },
            { value: 'per_bundle', label: 'Per Bundle', hint: 'bundled items with yield' },
            { value: 'per_length', label: 'Per Length', hint: 'yards/meters measurement' },
            { value: 'per_loom',   label: 'Per Loom',   hint: 'loom-based measurement' },
            { value: 'custom_unit',label: 'Custom Unit',hint: 'custom unit label (roll, bale, pack, etc.)' },
        ],

        // ─────────────────────────────────────────────────────────
        // SELLING METHODS — System methods + custom methods added at runtime
        // Each has: id, name, slug, description, configType, isSystem, isActive
        // configType is the key field that drives unit config UI rendering
        // ─────────────────────────────────────────────────────────
        sellingMethods: [
            { id: 'per-piece',  name: 'Per Piece',  slug: 'per-piece',  description: 'Customers buy individual pieces',        configType: 'per_piece',  isSystem: true, isActive: true },
            { id: 'per-set',    name: 'Per Set',    slug: 'per-set',    description: 'Customers buy pre-made sets',             configType: 'per_set',    isSystem: true, isActive: true },
            { id: 'per-bundle', name: 'Per Bundle', slug: 'per-bundle', description: 'Bundle of multiple items',               configType: 'per_bundle', isSystem: true, isActive: true },
            { id: 'per-length', name: 'Per Length', slug: 'per-length', description: 'Fabric sold by yards/meters',            configType: 'per_length', isSystem: true, isActive: true },
            { id: 'per-loom',   name: 'Per Loom',   slug: 'per-loom',   description: 'Sold by loom measurement',              configType: 'per_loom',   isSystem: true, isActive: true },
        ],

        // ─────────────────────────────────────────────────────────
        // CATEGORIES — dynamic list supporting inline creation
        // ─────────────────────────────────────────────────────────
        categories: [
            { id: 'lace',     name: 'Lace Fabrics',    slug: 'lace',     isActive: true },
            { id: 'aso-oke',  name: 'Aso Oke',         slug: 'aso-oke',  isActive: true },
            { id: 'ankara',   name: 'Ankara & Prints',  slug: 'ankara',   isActive: true },
            { id: 'caps',     name: 'Cap Materials',    slug: 'caps',     isActive: true },
            { id: 'headties', name: 'Headties',         slug: 'headties', isActive: true },
        ],

        // Collapsible section state
        sections: {
            basic: true,
            images: true,
            sellingMethod: true,
            unitConfig: true,
            colorVariants: false,
            composition: false,
            pricing: true,
            inventory: false,
            addOns: false,
            coupons: false,
            seo: false,
            schemaPreview: false,
        },

        mockAddOns: [
            { id: 1, name: 'Gift Wrapping',       price: 2000 },
            { id: 2, name: 'Express Delivery',    price: 5000 },
            { id: 3, name: 'Matching Headtie',    price: 8000 },
            { id: 4, name: 'Shoe Accessories Set', price: 6500 },
            { id: 5, name: 'Storage Bag',         price: 3500 }
        ],

        filteredAddOns: [],

        // ─────────────────────────────────────────────────────────
        // FORM STATE
        // ─────────────────────────────────────────────────────────
        form: {
            name: '',
            slug: '',
            // SKU (NEW): unique identifier for the product
            sku: '',
            // categoryId replaces category string — id-based for backend FK
            categoryId: '',
            // collection (NEW): men | women | both
            collection: 'both',
            description: '',
            descriptionHtml: '',
            // tags (NEW): array of strings
            tags: [],

            // Images (product-level)
            mainImagePreview: null,
            mainImageFile: null,
            thumbnails: [],

            // Selling method — id of selected selling method
            sellingMethodId: '',
            unitLabel: '',
            unitsPerOrder: 1,
            minQuantity: 1,
            quantityStep: 1,
            lengthUnit: 'yards',
            loomSize: '',

            // Composition
            includedItems: [],
            excludesText: '',
            setContents: [],
            bundleYield: [],

            // Color variants
            colorVariants: [],
            defaultColorVariantIdx: 0,

            // Pricing
            price: 0,
            comparePrice: 0,
            discountType: '',
            discountValue: 0,
            cost: 0,

            // Inventory
            trackInventory: false,
            stockQuantity: 0,
            stockUnit: '',
            lowStockThreshold: 5,

            // Add-ons
            addOns: [],
            showAddOnsAfterCheckout: false,
            showAddOnsInCart: false,
            showAddOnsOnPage: false,

            // Coupons
            coupons: [],

            // SEO
            metaTitle: '',
            metaDescription: '',

            // Status
            status: 'draft',
            featured: false,
            isNewArrival: false,
            newArrivalExpiry: '',
        },

        // ─────────────────────────────────────────────────────────
        // INIT
        // ─────────────────────────────────────────────────────────
        init() {
            this.filteredAddOns = [...this.mockAddOns];
            this.$nextTick(() => {
                this.quillEditor = new Quill('#quill-body', {
                    modules: { toolbar: '#quill-toolbar' },
                    theme: 'snow',
                    placeholder: 'Write a detailed description of your product...',
                });
                this.quillEditor.on('text-change', () => {
                    this.form.description = this.quillEditor.getText();
                    this.form.descriptionHtml = this.quillEditor.root.innerHTML;
                    document.getElementById('description-hidden').value = this.form.descriptionHtml;
                    this.generateSchemaMarkup();
                });
            });
            this.generateSchemaMarkup();
        },

        // ─────────────────────────────────────────────────────────
        // EXPAND / COLLAPSE
        // ─────────────────────────────────────────────────────────
        expandAllSections()  { Object.keys(this.sections).forEach(k => this.sections[k] = true); },
        collapseAllSections(){ Object.keys(this.sections).forEach(k => this.sections[k] = false); },

        // ─────────────────────────────────────────────────────────
        // UTILITY: slugify
        // Used by both category modal and selling method modal
        // ─────────────────────────────────────────────────────────
        slugify(str) {
            return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        },

        // ─────────────────────────────────────────────────────────
        // CATEGORY MODAL
        // openCategoryModal() — opens modal and resets form
        // createCategory(createAnother) — creates category, inserts
        //   into categories array, auto-selects it. If createAnother
        //   is true, resets form and keeps modal open.
        // ─────────────────────────────────────────────────────────
        openCategoryModal() {
            this.categoryModal = { name: '', slug: '', slugEdited: false, description: '', isActive: true };
            this.modals.category = true;
        },
        createCategory(createAnother) {
            const { name, slug, description, isActive } = this.categoryModal;
            if (!name.trim() || !slug.trim()) {
                alert('Category name and slug are required.');
                return;
            }
            // Check for duplicate slug
            if (this.categories.find(c => c.slug === slug)) {
                alert('A category with this slug already exists.');
                return;
            }
            const newCat = { id: slug, name: name.trim(), slug, description, isActive };
            this.categories.push(newCat);
            // Auto-select the newly created category
            this.form.categoryId = newCat.id;
            this.generateSchemaMarkup();
            if (createAnother) {
                // Reset form but keep modal open
                this.categoryModal = { name: '', slug: '', slugEdited: false, description: '', isActive: true };
            } else {
                this.modals.category = false;
            }
        },

        // ─────────────────────────────────────────────────────────
        // SELLING METHOD MODAL
        // openSellingMethodModal() — opens modal and resets form
        // createSellingMethod(createAnother) — creates method, inserts
        //   into sellingMethods array, makes it selectable immediately.
        //   config_type is stored and drives unit config rendering.
        // ─────────────────────────────────────────────────────────
        openSellingMethodModal() {
            this.sellingMethodModal = { name: '', slug: '', slugEdited: false, description: '', configType: '', isActive: true };
            this.modals.sellingMethod = true;
        },
        createSellingMethod(createAnother) {
            const { name, slug, description, configType, isActive } = this.sellingMethodModal;
            if (!name.trim() || !slug.trim()) {
                alert('Method name and slug are required.');
                return;
            }
            if (!configType) {
                alert('You must select a config type.');
                return;
            }
            if (this.sellingMethods.find(m => m.slug === slug)) {
                alert('A selling method with this slug already exists.');
                return;
            }
            const newMethod = {
                id: slug,
                name: name.trim(),
                slug,
                description,
                configType,
                isSystem: false,
                isActive,
            };
            this.sellingMethods.push(newMethod);
            if (createAnother) {
                this.sellingMethodModal = { name: '', slug: '', slugEdited: false, description: '', configType: '', isActive: true };
            } else {
                this.modals.sellingMethod = false;
            }
        },

        // ─────────────────────────────────────────────────────────
        // CONFIG TYPE HELPERS
        // getSelectedMethod() — returns the selected method object
        // getSelectedConfigType() — returns the configType string
        //   This is the KEY function: unit config UI now depends on
        //   this, not on the raw sellingMethodId. This means custom
        //   methods work automatically.
        // ─────────────────────────────────────────────────────────
        getSelectedMethod() {
            return this.sellingMethods.find(m => m.id === this.form.sellingMethodId) || null;
        },
        getSelectedConfigType() {
            const method = this.getSelectedMethod();
            return method ? method.configType : null;
        },

        // ─────────────────────────────────────────────────────────
        // SELLING METHOD CHANGE
        // Applies sensible defaults based on config_type,
        // not based on a hardcoded method id.
        // ─────────────────────────────────────────────────────────
        onSellingMethodChange() {
            const configType = this.getSelectedConfigType();
            // Default values keyed by config_type
            const defaults = {
                'per_piece':   { unitLabel: 'piece',  unitsPerOrder: 1, quantityStep: 1, stockUnit: 'pieces' },
                'per_set':     { unitLabel: 'set',    unitsPerOrder: 2, quantityStep: 1, stockUnit: 'sets' },
                'per_bundle':  { unitLabel: 'bundle', unitsPerOrder: 1, quantityStep: 1, stockUnit: 'bundles' },
                'per_length':  { unitLabel: 'yards',  unitsPerOrder: 5, quantityStep: 1, stockUnit: 'selling units', lengthUnit: 'yards' },
                'per_loom':    { unitLabel: 'loom',   unitsPerOrder: 1, quantityStep: 1, stockUnit: 'looms' },
                'custom_unit': { unitLabel: '',       unitsPerOrder: 1, quantityStep: 1, stockUnit: 'units' },
            };
            const def = defaults[configType];
            if (def) {
                Object.assign(this.form, def);
                this.form.colorVariants.forEach(v => { v.stockUnit = def.stockUnit; });
            }
        },

        // ─────────────────────────────────────────────────────────
        // UNIT / SECTION LABELS — now config_type driven
        // ─────────────────────────────────────────────────────────
        getUnitSectionTitle() {
            const configType = this.getSelectedConfigType();
            const titles = {
                'per_piece':   'Per Piece Configuration',
                'per_set':     'Per Set Configuration',
                'per_bundle':  'Per Bundle Configuration',
                'per_length':  'Per Length Configuration',
                'per_loom':    'Per Loom Configuration',
                'custom_unit': 'Custom Unit Configuration',
            };
            return titles[configType] || 'Unit Configuration';
        },
        getSelectedMethodLabel() {
            const method = this.getSelectedMethod();
            return method ? method.name : '—';
        },
        // Stock unit placeholder — driven by config_type
        getStockUnitPlaceholder() {
            const configType = this.getSelectedConfigType();
            const units = {
                'per_piece':   'pieces',
                'per_set':     'sets',
                'per_bundle':  'bundles',
                'per_length':  'selling units',
                'per_loom':    'looms',
                'custom_unit': this.form.unitLabel || 'units',
            };
            return units[configType] || 'units';
        },

        // ─────────────────────────────────────────────────────────
        // SLUG AUTO-GENERATE
        // ─────────────────────────────────────────────────────────
        autoGenerateSlug() {
            if (!this.slugManuallyEdited) {
                this.form.slug = this.slugify(this.form.name);
            }
        },

        // ─────────────────────────────────────────────────────────
        // SKU AUTO-GENERATE
        // force=true: always regenerate (from the Generate button)
        // force=false: only auto-generate if not manually edited
        // Format: CATEGORY-NAMEWORDS-XXX (3 digit counter simulation)
        // ─────────────────────────────────────────────────────────
        autoGenerateSku(force = false) {
            if (!force && this.skuManuallyEdited) return;
            const cat = this.form.categoryId
                ? (this.categories.find(c => c.id === this.form.categoryId)?.slug || 'prod').substring(0, 4).toUpperCase()
                : 'PROD';
            const namePart = this.form.name
                .replace(/[^a-zA-Z0-9 ]/g, '')
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map(w => w.substring(0, 3).toUpperCase())
                .join('-');
            const rand = String(Math.floor(Math.random() * 900) + 100);
            this.form.sku = [cat, namePart, rand].filter(Boolean).join('-');
            this.skuManuallyEdited = false;
        },

        // ─────────────────────────────────────────────────────────
        // TAGS INPUT LOGIC
        // addTag() — adds tagInput value as a new tag (deduped)
        // removeTag(idx) — removes a tag by index
        // ─────────────────────────────────────────────────────────
        addTag() {
            const val = this.tagInput.trim().replace(/,+$/, '').trim();
            if (!val) return;
            if (this.form.tags.includes(val)) {
                this.tagInput = '';
                return;
            }
            this.form.tags.push(val);
            this.tagInput = '';
        },
        removeTag(idx) {
            this.form.tags.splice(idx, 1);
        },

        // ─────────────────────────────────────────────────────────
        // PRICING
        // ─────────────────────────────────────────────────────────
        calculateFinalPrice() {
            if (this.form.discountType === 'percent') {
                return Math.round(this.form.price * (1 - this.form.discountValue / 100));
            } else if (this.form.discountType === 'fixed') {
                return Math.max(0, this.form.price - this.form.discountValue);
            }
            return this.form.price;
        },

        // ─────────────────────────────────────────────────────────
        // COMPOSITION HELPERS
        // ─────────────────────────────────────────────────────────
        addSetContentItem()  { this.form.setContents.push({ name: '', quantity: 1 }); },
        addBundleYieldItem() { this.form.bundleYield.push({ name: '', quantity: 1 }); },
        addIncludedItem()    { this.form.includedItems.push({ name: '', quantity: 1 }); },

        // ─────────────────────────────────────────────────────────
        // IMAGE UPLOAD — PRODUCT LEVEL
        // ─────────────────────────────────────────────────────────
        handleMainImageSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) { alert('Image must be under 5MB'); return; }
            const reader = new FileReader();
            reader.onload = (e) => {
                this.form.mainImagePreview = e.target.result;
                this.form.mainImageFile = file;
                this.generateSchemaMarkup();
            };
            reader.readAsDataURL(file);
        },
        handleMainImageDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            this.handleMainImageSelect({ target: { files: [file] } });
        },
        clearMainImage() {
            this.form.mainImagePreview = null;
            this.form.mainImageFile = null;
            if (this.$refs.mainImageInput) this.$refs.mainImageInput.value = '';
        },
        handleThumbnailSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (this.form.thumbnails.length >= 4) return;
            if (file.size > 5 * 1024 * 1024) { alert('Image must be under 5MB'); return; }
            const reader = new FileReader();
            reader.onload = (e) => { this.form.thumbnails.push({ preview: e.target.result, file }); };
            reader.readAsDataURL(file);
            event.target.value = '';
        },
        handleThumbnailDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            this.handleThumbnailSelect({ target: { files: [file] } });
        },
        removeThumbnail(idx) { this.form.thumbnails.splice(idx, 1); },

        // ─────────────────────────────────────────────────────────
        // COLOR VARIANTS
        // ─────────────────────────────────────────────────────────
        addColorVariant() {
            this.form.colorVariants.push({
                name: '', hex: '#000000', priceAdjustment: 0,
                stock: 0, stockUnit: this.getStockUnitPlaceholder(),
                mainImagePreview: null, mainImageFile: null,
                thumbnails: [], _expanded: true,
            });
        },
        removeColorVariant(idx) {
            this.form.colorVariants.splice(idx, 1);
            if (this.form.defaultColorVariantIdx >= this.form.colorVariants.length) {
                this.form.defaultColorVariantIdx = 0;
            }
        },
        toggleVariant(idx) { this.form.colorVariants[idx]._expanded = !this.form.colorVariants[idx]._expanded; },
        getTotalVariantStock() { return this.form.colorVariants.reduce((sum, v) => sum + (v.stock || 0), 0); },
        updateAggregatedStock() { /* reactive, no-op — Alpine auto-updates */ },
        triggerVariantMainImage(vIdx) { document.getElementById('variantMainImg_' + vIdx)?.click(); },
        triggerVariantThumb(vIdx) { document.getElementById('variantThumb_' + vIdx)?.click(); },
        handleVariantMainImage(event, vIdx) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.form.colorVariants[vIdx].mainImagePreview = e.target.result;
                this.form.colorVariants[vIdx].mainImageFile = file;
            };
            reader.readAsDataURL(file);
        },
        handleVariantThumb(event, vIdx) {
            const file = event.target.files[0];
            if (!file) return;
            const variant = this.form.colorVariants[vIdx];
            if (variant.thumbnails.length >= 4) return;
            const reader = new FileReader();
            reader.onload = (e) => { variant.thumbnails.push({ preview: e.target.result, file }); };
            reader.readAsDataURL(file);
            event.target.value = '';
        },
        removeVariantThumbnail(vIdx, tIdx) { this.form.colorVariants[vIdx].thumbnails.splice(tIdx, 1); },

        // ─────────────────────────────────────────────────────────
        // MEDIA LIBRARY SELECTION
        // Handles media picked from the media library
        // target can be: 'mainImage', 'thumbnails', or 'variantMainImage_<vIdx>'
        // mediaItem format: { id, name, url, ... }
        // ─────────────────────────────────────────────────────────
        handleMediaSelected(detail) {
            const { target, items } = detail;
            if (!items || items.length === 0) return;
            
            const mediaItem = items[0];
            const imageUrl = mediaItem.url || mediaItem.path;
            
            if (!imageUrl) return;
            
            if (target === 'mainImage') {
                // Set main product image from media library
                this.form.mainImagePreview = imageUrl;
                this.form.mainImageFile = null; // Mark as from media library
                this.generateSchemaMarkup();
            } else if (target === 'thumbnails') {
                // Add thumbnail from media library
                if (this.form.thumbnails.length < 4) {
                    this.form.thumbnails.push({ preview: imageUrl, file: null }); // Mark as from media library
                }
            } else if (target.startsWith('variantMainImage_')) {
                // Set variant main image from media library
                const vIdx = parseInt(target.replace('variantMainImage_', ''), 10);
                if (vIdx >= 0 && vIdx < this.form.colorVariants.length) {
                    this.form.colorVariants[vIdx].mainImagePreview = imageUrl;
                    this.form.colorVariants[vIdx].mainImageFile = null; // Mark as from media library
                }
            }
        },

        // ─────────────────────────────────────────────────────────
        // COUPONS
        // ─────────────────────────────────────────────────────────
        addCoupon() {
            this.form.coupons.push({
                code: '', discountPercent: 0, expiryDate: '',
                maxUses: 0, minOrderAmount: 0, newUsersOnly: false,
                isActive: true, _expanded: true,
            });
        },
        generateCouponCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            return Array.from({ length: 8 }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
        },

        // ─────────────────────────────────────────────────────────
        // ADD-ONS
        // ─────────────────────────────────────────────────────────
        filterAddOns() {
            if (!this.addOnSearch.trim()) { this.filteredAddOns = [...this.mockAddOns]; return; }
            const q = this.addOnSearch.toLowerCase();
            this.filteredAddOns = this.mockAddOns.filter(a =>
                a.name.toLowerCase().includes(q) && !this.form.addOns.find(x => x.id === a.id)
            );
        },
        addAddOn(addon) {
            if (!this.form.addOns.find(a => a.id === addon.id)) { this.form.addOns.push({ ...addon }); }
            this.addOnSearch = '';
            this.filteredAddOns = [];
        },
        removeAddOn(index) { this.form.addOns.splice(index, 1); },

        // ─────────────────────────────────────────────────────────
        // SCHEMA MARKUP GENERATOR
        // Updated to use:
        //  - form.sku instead of form.slug for SKU field
        //  - form.collection for keywords
        //  - form.tags for keywords
        //  - getSelectedMethodLabel() and getSelectedConfigType()
        //    (config_type driven — works for custom methods too)
        //  - form.categoryId resolved to category name
        // ─────────────────────────────────────────────────────────
        generateSchemaMarkup() {
            const baseUrl = window.location.origin;
            const slug = this.form.slug || 'product';
            const productUrl = `${baseUrl}/products/${slug}`;
            const finalPrice = this.calculateFinalPrice();

            // Availability
            let availability = 'https://schema.org/PreOrder';
            if (this.form.colorVariants.length > 0) {
                availability = this.getTotalVariantStock() > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock';
            } else if (this.form.trackInventory) {
                availability = this.form.stockQuantity > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock';
            } else {
                availability = 'https://schema.org/InStock';
            }

            // Category name from categories array (not hardcoded)
            const catObj = this.categories.find(c => c.id === this.form.categoryId);
            const categoryName = catObj ? catObj.name : (this.form.categoryId || '');

            const offer = {
                '@type': 'Offer',
                'url': productUrl,
                'priceCurrency': 'NGN',
                'price': finalPrice || 0,
                'availability': availability,
                'itemCondition': 'https://schema.org/NewCondition',
            };
            if (this.form.comparePrice > 0) {
                offer['highPrice'] = this.form.comparePrice;
                offer['lowPrice'] = finalPrice || 0;
                offer['@type'] = 'AggregateOffer';
            }

            const schema = {
                '@context': 'https://schema.org',
                '@type': 'Product',
                'name': this.form.name || 'Product Name',
                'url': productUrl,
                'description': this.form.description || '',
                'category': categoryName,
                'brand': { '@type': 'Brand', 'name': '1stDelightSome Fabrics' },
                'offers': offer,
            };

            // SKU — now uses form.sku directly
            if (this.form.sku) {
                schema['sku'] = this.form.sku;
                schema['productID'] = this.form.sku;
            }

            if (this.form.mainImagePreview) {
                schema['image'] = [this.form.mainImagePreview];
                this.form.thumbnails.forEach(t => schema['image'].push(t.preview));
            }

            if (this.form.colorVariants.length > 0) {
                schema['color'] = this.form.colorVariants.filter(v => v.name).map(v => v.name).join(', ');
                schema['hasVariant'] = this.form.colorVariants.filter(v => v.name).map(v => ({
                    '@type': 'ProductModel',
                    'name': `${this.form.name || 'Product'} - ${v.name}`,
                    'color': v.name,
                    'offers': {
                        '@type': 'Offer',
                        'priceCurrency': 'NGN',
                        'price': (finalPrice || 0) + (v.priceAdjustment || 0),
                        'availability': (v.stock || 0) > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                        'itemCondition': 'https://schema.org/NewCondition',
                    },
                    ...(v.mainImagePreview ? { 'image': v.mainImagePreview } : {}),
                }));
            }

            // Selling method additionalProperty — config_type driven
            if (this.form.sellingMethodId) {
                schema['additionalProperty'] = schema['additionalProperty'] || [];
                schema['additionalProperty'].push({
                    '@type': 'PropertyValue',
                    'name': 'Selling Method',
                    'value': this.getSelectedMethodLabel(),
                });
                schema['additionalProperty'].push({
                    '@type': 'PropertyValue',
                    'name': 'Config Type',
                    'value': this.getSelectedConfigType(),
                });
                const configType = this.getSelectedConfigType();
                if (configType === 'per_length' && this.form.unitsPerOrder) {
                    schema['additionalProperty'].push({
                        '@type': 'PropertyValue',
                        'name': 'Length Per Unit',
                        'value': `${this.form.unitsPerOrder} ${this.form.lengthUnit}`,
                        'unitCode': this.form.lengthUnit === 'yards' ? 'YRD' : 'MTR',
                    });
                }
                if (configType === 'per_loom' && this.form.loomSize) {
                    schema['additionalProperty'].push({
                        '@type': 'PropertyValue',
                        'name': 'Loom Size',
                        'value': this.form.loomSize,
                    });
                }
            }

            // Keywords: tags + collection + new arrival
            const keywords = [];
            if (this.form.tags.length > 0) keywords.push(...this.form.tags);
            if (this.form.collection) keywords.push(this.form.collection);
            if (this.form.isNewArrival) keywords.push('New Arrival');
            if (categoryName) keywords.push(categoryName);
            if (keywords.length > 0) schema['keywords'] = keywords.join(', ');

            if (this.form.metaDescription) schema['description'] = this.form.metaDescription;

            this.schemaMarkup = JSON.stringify(schema, null, 2);
        },

        // ─────────────────────────────────────────────────────────
        // SAVE / PUBLISH
        // ─────────────────────────────────────────────────────────
        saveDraft() {
            this.generateSchemaMarkup();
            const payload = this.buildPayload('draft');
            console.log('Save Draft payload:', payload);
            alert('Saved as draft');
        },
        publishProduct() {
            this.generateSchemaMarkup();
            const payload = this.buildPayload('active');
            console.log('Publish payload:', payload);
            alert('Published');
        },

        // ─────────────────────────────────────────────────────────
        // BUILD PAYLOAD
        // Updated with all new fields:
        //  - sku
        //  - tags
        //  - collection
        //  - category_id (replaces category string)
        //  - selling_method_id, selling_method_slug, selling_method_config_type
        // ─────────────────────────────────────────────────────────
        buildPayload(status) {
            const method = this.getSelectedMethod();
            return {
                // Basic
                name:             this.form.name,
                slug:             this.form.slug,
                sku:              this.form.sku,          // NEW
                category_id:      this.form.categoryId,   // NEW (was: category)
                collection:       this.form.collection,   // NEW: men|women|both
                tags:             this.form.tags,          // NEW: string[]
                description:      this.form.description,
                description_html: this.form.descriptionHtml,

                // Images (file objects — handle with FormData when using fetch)
                // main_image: this.form.mainImageFile,
                // thumbnails: this.form.thumbnails.map(t => t.file),

                // Selling — now id + slug + config_type for clean backend integration
                selling_method_id:          this.form.sellingMethodId,          // NEW
                selling_method_slug:        method ? method.slug : null,        // NEW
                selling_method_config_type: method ? method.configType : null,  // NEW
                unit_label:      this.form.unitLabel,
                units_per_order: this.form.unitsPerOrder,
                min_quantity:    this.form.minQuantity,
                quantity_step:   this.form.quantityStep,
                length_unit:     this.form.lengthUnit,
                loom_size:       this.form.loomSize,
                set_contents:    this.form.setContents,
                bundle_yield:    this.form.bundleYield,
                included_items:  this.form.includedItems,
                excludes_text:   this.form.excludesText,

                // Variants
                color_variants: this.form.colorVariants.map((v, i) => ({
                    name:             v.name,
                    hex:              v.hex,
                    price_adjustment: v.priceAdjustment,
                    stock:            v.stock,
                    stock_unit:       v.stockUnit,
                    is_default:       i === this.form.defaultColorVariantIdx,
                })),
                default_color_variant_idx: this.form.defaultColorVariantIdx,

                // Pricing
                price:          this.form.price,
                compare_price:  this.form.comparePrice,
                discount_type:  this.form.discountType,
                discount_value: this.form.discountValue,
                final_price:    this.calculateFinalPrice(),
                cost:           this.form.cost,

                // Inventory
                track_inventory:    this.form.trackInventory,
                stock_quantity:     this.form.colorVariants.length > 0
                                        ? this.getTotalVariantStock()
                                        : this.form.stockQuantity,
                stock_unit:         this.form.stockUnit,
                low_stock_threshold: this.form.lowStockThreshold,

                // Add-ons
                add_on_ids:                 this.form.addOns.map(a => a.id),
                show_add_ons_after_checkout: this.form.showAddOnsAfterCheckout,
                show_add_ons_in_cart:        this.form.showAddOnsInCart,
                show_add_ons_on_page:        this.form.showAddOnsOnPage,

                // Coupons
                coupons: this.form.coupons.map(c => ({
                    code:             c.code,
                    discount_percent: c.discountPercent,
                    expiry_date:      c.expiryDate || null,
                    max_uses:         c.maxUses || null,
                    min_order_amount: c.minOrderAmount || 0,
                    new_users_only:   c.newUsersOnly,
                    is_active:        c.isActive,
                })),

                // SEO & Schema
                meta_title:       this.form.metaTitle,
                meta_description: this.form.metaDescription,
                schema_markup:    this.schemaMarkup,

                // Status
                status:             status || this.form.status,
                featured:           this.form.featured,
                is_new_arrival:     this.form.isNewArrival,
                new_arrival_expiry: this.form.newArrivalExpiry || null,
            };
        }
    };
}
</script>
@endverbatim
@endpush