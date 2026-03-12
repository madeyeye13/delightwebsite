{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN PRODUCT CREATE/EDIT PAGE - REFACTORED                      ║
║  Features: Collapsible sections, adaptive units, smart defaults    ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('content')

<div x-data="productFormManager()" class="space-y-6">
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
            <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">Configure details, pricing, inventory, and selling method</p>
        </div>
        </div>
        {{-- Expand/Collapse All Controls --}}
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Product Name *</label>
                            <input type="text" x-model="form.name" @input="autoGenerateSlug()" placeholder="e.g. Premium Ankara Fabric" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug *</label>
                            <input type="text" x-model="form.slug" @input="slugManuallyEdited = true" placeholder="auto-generated" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1" x-show="!slugManuallyEdited">Auto-generated from name</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1" x-show="slugManuallyEdited">Custom slug (manually set)</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Category *</label>
                        <select x-model="form.category" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm">
                            <option value="">Select category</option>
                            <option value="lace">Lace Fabrics</option>
                            <option value="aso-oke">Aso Oke</option>
                            <option value="ankara">Ankara & Prints</option>
                            <option value="caps">Cap Materials</option>
                            <option value="headties">Headties</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description</label>
                        <textarea x-model="form.description" placeholder="Write a detailed description of your product..." rows="3" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 SELLING METHOD (COLLAPSIBLE)
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
                    <div class="grid grid-cols-1 gap-2">
                        <template x-for="method in sellingMethods" :key="method.id">
                            <label class="flex items-center gap-3 p-3 border border-neutral-200 dark:border-neutral-700 rounded-lg cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                                <input type="radio" x-model="form.sellingMethod" :value="method.id" @change="onSellingMethodChange()" class="w-4 h-4">
                                <div class="flex-1">
                                    <p class="font-medium text-sm text-neutral-900 dark:text-neutral-50" x-text="method.label"></p>
                                    <p class="text-xs text-neutral-600 dark:text-neutral-400" x-text="method.description"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 UNIT CONFIGURATION (ADAPTIVE - COLLAPSIBLE)
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.unitConfig = !sections.unitConfig" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v3m-6-1v-7a2 2 0 012-2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50" x-text="getUnitSectionTitle()"></h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.unitConfig && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.unitConfig && form.sellingMethod" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">
                    {{-- PER LENGTH --}}
                    <template x-if="form.sellingMethod === 'per-length'">
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
                            {{-- Preview --}}
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-2">Customer purchase options:</p>
                                <p>1 unit = <span x-text="form.unitsPerOrder || 0"></span> <span x-text="form.lengthUnit"></span></p>
                                <p>Allowed orders: <span x-text="form.minQuantity || 1"></span>, <span x-text="(form.minQuantity || 1) + (form.quantityStep || 1)"></span>, <span x-text="(form.minQuantity || 1) + ((form.quantityStep || 1) * 2)"></span>... units</p>
                            </div>
                        </div>
                    </template>

                    {{-- PER SET --}}
                    <template x-if="form.sellingMethod === 'per-set'">
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

                            {{-- Set Contents Repeater --}}
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

                            {{-- Preview --}}
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

                    {{-- PER BUNDLE --}}
                    <template x-if="form.sellingMethod === 'per-bundle'">
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

                            {{-- Bundle Yield Repeater --}}
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

                            {{-- Preview --}}
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

                    {{-- PER PIECE --}}
                    <template x-if="form.sellingMethod === 'per-piece'">
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

                    {{-- PER LOOM --}}
                    <template x-if="form.sellingMethod === 'per-loom'">
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
                            {{-- Preview --}}
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs text-blue-900 dark:text-blue-300">
                                <p class="font-medium mb-1">Customer purchase:</p>
                                <p>1 <span x-text="form.unitLabel || 'loom'"></span> = <span x-text="form.loomSize || '?'"></span></p>
                                <p>Minimum: <span x-text="form.minQuantity || 1"></span> <span x-text="form.unitLabel || 'loom'"></span>(s)</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="!form.sellingMethod">
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg text-xs text-yellow-900 dark:text-yellow-300">
                            <p>Select a selling method above first</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 PRODUCT COMPOSITION (COLLAPSIBLE)
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
                    {{-- Included Items --}}
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

                    {{-- Excluded/Not Included --}}
                    <div class="pt-3 border-t border-neutral-200 dark:border-neutral-800">
                        <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Not included / Image disclaimer</label>
                        <textarea x-model="form.excludesText" placeholder="e.g. Model's accessories and shoes not included" rows="2" class="w-full px-2 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900"></textarea>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 PRICING (COLLAPSIBLE)
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
                            <input type="number" x-model.number="form.price" placeholder="0" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand text-sm">
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
                 INVENTORY (COLLAPSIBLE)
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.inventory = !sections.inventory" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Inventory</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.inventory && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.inventory" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-3">
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
            </div>

            {{-- ════════════════════════════════════════════════════════
                 RECOMMENDED ADD-ONS (COLLAPSIBLE)
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

                    {{-- Selected Add-ons --}}
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
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="form.showAddOnsAfterCheckout" class="w-4 h-4">
                            <span class="text-sm text-neutral-700 dark:text-neutral-300">Show after "Add to Cart" step</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="form.showAddOnsInCart" class="w-4 h-4">
                            <span class="text-sm text-neutral-700 dark:text-neutral-300">Show in shopping cart</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="form.showAddOnsOnPage" class="w-4 h-4">
                            <span class="text-sm text-neutral-700 dark:text-neutral-300">Show on product page</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════
                 SEO & META (COLLAPSIBLE)
            ════════════════════════════════════════════════════════════ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.seo = !sections.seo" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">SEO & Meta</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.seo && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.seo" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Page Title</label>
                        <input type="text" x-model="form.metaTitle" placeholder="Leave blank to use product name" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm">
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1"><span x-text="(form.metaTitle || form.name).length"></span>/60 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Meta Description</label>
                        <textarea x-model="form.metaDescription" placeholder="Compelling description for search engines..." rows="3" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 text-sm"></textarea>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1"><span x-text="form.metaDescription.length"></span>/160 characters</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Status Card --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4 space-y-3 sticky top-6">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Status</h3>
                <select x-model="form.status" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand text-sm">
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="form.featured" class="w-4 h-4 rounded">
                    <span class="text-sm text-neutral-700 dark:text-neutral-300">Featured product</span>
                </label>

                {{-- Action Buttons --}}
                <div class="space-y-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                    <button onclick="window.history.back()" class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors font-medium text-sm">
                        Cancel
                    </button>
                    <button @click="saveDraft()" class="w-full px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">
                        Save Draft
                    </button>
                    <button @click="publishProduct()" class="w-full px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-medium text-sm">
                        Publish
                    </button>
                </div>
            </div>

            {{-- Preview Cards --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4 space-y-3">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Selling Detail Summary</h3>
                <div class="space-y-2 text-xs">
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Method</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="getSelectedMethodLabel()"></p>
                    </div>
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Unit</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="form.unitLabel || '—'"></p>
                    </div>
                    <template x-if="form.sellingMethod === 'per-loom' && form.loomSize">
                        <div>
                            <p class="text-neutral-600 dark:text-neutral-400">Loom Size</p>
                            <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="form.loomSize"></p>
                        </div>
                    </template>
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
                    <template x-if="form.sellingMethod === 'per-set'">
                        <div>
                            <p class="text-neutral-600 dark:text-neutral-400">Set Contents</p>
                            <template x-if="form.setContents.length > 0">
                                <div class="space-y-0.5">
                                    <template x-for="item in form.setContents" :key="item.name">
                                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="item.name + ' × ' + (item.quantity || 1)"></p>
                                    </template>
                                </div>
                            </template>
                            <template x-if="form.setContents.length === 0">
                                <p class="font-medium text-neutral-900 dark:text-neutral-50">—</p>
                            </template>
                        </div>
                    </template>
                    <template x-if="form.sellingMethod === 'per-bundle'">
                        <div>
                            <p class="text-neutral-600 dark:text-neutral-400">Bundle Yield</p>
                            <template x-if="form.bundleYield.length > 0">
                                <div class="space-y-0.5">
                                    <template x-for="item in form.bundleYield" :key="item.name">
                                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="item.name + ' × ' + (item.quantity || 1)"></p>
                                    </template>
                                </div>
                            </template>
                            <template x-if="form.bundleYield.length === 0">
                                <p class="font-medium text-neutral-900 dark:text-neutral-50">—</p>
                            </template>
                        </div>
                    </template>
                    <div>
                        <p class="text-neutral-600 dark:text-neutral-400">Stock</p>
                        <p class="font-medium text-neutral-900 dark:text-neutral-50" x-text="(form.trackInventory ? form.stockQuantity + ' ' + (form.stockUnit || 'units') : '—')"></p>
                    </div>
                </div>
            </div>

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
                </div>
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
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E4E4E7; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #D4D4D8; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #3F3F46; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525B; }
</style>

{{-- ════════════════════════════════════════════════════════════
     ALPINE.JS FORM MANAGER
════════════════════════════════════════════════════════════════ --}}
<script>
function productFormManager() {
    return {
        addOnSearch: '',
        slugManuallyEdited: false,

        // Centralized collapsible section state
        sections: {
            basic: true,
            sellingMethod: true,
            unitConfig: true,
            composition: false,
            pricing: true,
            inventory: false,
            addOns: false,
            seo: false
        },
        
        sellingMethods: [
            { id: 'per-piece', label: 'Per Piece', description: 'Customers buy individual pieces' },
            { id: 'per-set', label: 'Per Set', description: 'Customers buy pre-made sets' },
            { id: 'per-bundle', label: 'Per Bundle', description: 'Bundle of multiple items' },
            { id: 'per-length', label: 'Per Length', description: 'Fabric sold by yards/meters' },
            { id: 'per-loom', label: 'Per Loom', description: 'Sold by loom measurement' }
        ],

        mockAddOns: [
            { id: 1, name: 'Gift Wrapping', price: 2000 },
            { id: 2, name: 'Express Delivery', price: 5000 },
            { id: 3, name: 'Matching Headtie', price: 8000 },
            { id: 4, name: 'Shoe Accessories Set', price: 6500 },
            { id: 5, name: 'Storage Bag', price: 3500 }
        ],

        filteredAddOns: [],

        form: {
            name: '',
            slug: '',
            category: '',
            description: '',
            sellingMethod: '',
            
            // Unit config (adaptive)
            unitLabel: '',
            unitsPerOrder: 1,
            minQuantity: 1,
            quantityStep: 1,
            lengthUnit: 'yards',
            loomSize: '', // New: configurable loom size field
            
            // Composition (structured)
            includedItems: [], // New: structured included items
            excludesText: '', // Kept: exclusion disclaimer
            
            // Set/Bundle contents (data-driven)
            setContents: [], // New: repeater for set contents
            bundleYield: [], // New: repeater for bundle yield
            
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
            
            // SEO
            metaTitle: '',
            metaDescription: '',
            
            // Status
            status: 'draft',
            featured: false
        },

        init() {
            this.filteredAddOns = [...this.mockAddOns];
        },

        // Expand/Collapse All
        expandAllSections() {
            Object.keys(this.sections).forEach(key => {
                this.sections[key] = true;
            });
        },

        collapseAllSections() {
            Object.keys(this.sections).forEach(key => {
                this.sections[key] = false;
            });
        },

        onSellingMethodChange() {
            // Apply smart defaults based on method
            const defaults = {
                'per-piece': { unitLabel: 'piece', unitsPerOrder: 1, quantityStep: 1, stockUnit: 'pieces' },
                'per-set': { unitLabel: 'set', unitsPerOrder: 2, quantityStep: 1, stockUnit: 'sets' },
                'per-bundle': { unitLabel: 'bundle', unitsPerOrder: 1, quantityStep: 1, stockUnit: 'bundles' },
                'per-length': { unitLabel: 'yards', unitsPerOrder: 5, quantityStep: 1, stockUnit: 'selling units', lengthUnit: 'yards' },
                'per-loom': { unitLabel: 'loom', unitsPerOrder: 1, quantityStep: 1, stockUnit: 'looms' }
            };
            const def = defaults[this.form.sellingMethod];
            if (def) Object.assign(this.form, def);
        },

        autoGenerateSlug() {
            // Only auto-generate if slug hasn't been manually edited
            if (!this.slugManuallyEdited) {
                const slug = this.form.name
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
                this.form.slug = slug;
            }
        },

        calculateFinalPrice() {
            if (this.form.discountType === 'percent') {
                return this.form.price * (1 - this.form.discountValue / 100);
            } else if (this.form.discountType === 'fixed') {
                return Math.max(0, this.form.price - this.form.discountValue);
            }
            return this.form.price;
        },

        getUnitSectionTitle() {
            const titles = {
                'per-piece': 'Per Piece Configuration',
                'per-set': 'Per Set Configuration',
                'per-bundle': 'Per Bundle Configuration',
                'per-length': 'Per Length Configuration',
                'per-loom': 'Per Loom Configuration'
            };
            return titles[this.form.sellingMethod] || 'Unit Configuration';
        },

        getSelectedMethodLabel() {
            const method = this.sellingMethods.find(m => m.id === this.form.sellingMethod);
            return method ? method.label : '—';
        },

        getStockUnitPlaceholder() {
            const units = {
                'per-piece': 'pieces',
                'per-set': 'sets',
                'per-bundle': 'bundles',
                'per-length': 'selling units',
                'per-loom': 'looms'
            };
            return units[this.form.sellingMethod] || 'units';
        },

        getSetItemCount() {
            return this.form.setContents.reduce((sum, item) => sum + (item.quantity || 1), 0);
        },

        getBundleItemCount() {
            return this.form.bundleYield.reduce((sum, item) => sum + (item.quantity || 1), 0);
        },

        // Set contents management
        addSetContentItem() {
            this.form.setContents.push({ name: '', quantity: 1 });
        },

        // Bundle yield management
        addBundleYieldItem() {
            this.form.bundleYield.push({ name: '', quantity: 1 });
        },

        // Included items management
        addIncludedItem() {
            this.form.includedItems.push({ name: '', quantity: 1 });
        },

        filterAddOns() {
            if (!this.addOnSearch.trim()) {
                this.filteredAddOns = [...this.mockAddOns];
                return;
            }
            const query = this.addOnSearch.toLowerCase();
            this.filteredAddOns = this.mockAddOns.filter(addon =>
                addon.name.toLowerCase().includes(query) &&
                !this.form.addOns.find(a => a.id === addon.id)
            );
        },

        addAddOn(addon) {
            if (!this.form.addOns.find(a => a.id === addon.id)) {
                this.form.addOns.push({ ...addon });
            }
            this.addOnSearch = '';
            this.filteredAddOns = [];
        },

        removeAddOn(index) {
            this.form.addOns.splice(index, 1);
        },

        saveDraft() {
            console.log('Save Draft:', this.form);
            // @todo: POST /admin/api/products with status: draft
            alert('Saved as draft');
        },

        publishProduct() {
            console.log('Publish Product:', this.form);
            // @todo: POST /admin/api/products with status: active OR PATCH existing
            alert('Published');
        }
    };
}
</script>
@endsection
