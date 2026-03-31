<div class="space-y-6">

    {{-- ════════════════════════════════════════════════════════════
         FLASH NOTIFICATION
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition
        class="fixed top-5 right-5 z-[100] flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg border text-sm font-medium"
        :class="type === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700/50 dark:text-green-300' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700/50 dark:text-red-300'"
        style="display:none"
    >
        <span x-text="message"></span>
        <button @click="show = false" class="ml-2 opacity-60 hover:opacity-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         ADJUST STOCK MODAL
    ════════════════════════════════════════════════════════════════ --}}
    @if($showAdjustModal)
    <div
        x-data
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(2px)"
        wire:click.self="$set('showAdjustModal', false)"
    >
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-md"
             @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">Adjust Stock</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        {{ $adjustProductName }}{{ $adjustVariantName && $adjustVariantName !== 'Default' ? ' — ' . $adjustVariantName : '' }}
                    </p>
                </div>
                <button wire:click="$set('showAdjustModal', false)" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-5 py-4 space-y-4">
                {{-- Current → New preview --}}
                <div class="flex items-center gap-3 p-3 bg-neutral-100 dark:bg-neutral-900/60 rounded-lg">
                    <div class="text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-500">Current</p>
                        <p class="text-xl font-bold text-neutral-900 dark:text-neutral-50 leading-none mt-1">{{ $adjustCurrentQty }}</p>
                        <p class="text-[10px] text-neutral-500 mt-0.5">units</p>
                    </div>
                    <div class="flex-1 text-center text-neutral-400">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-500">New</p>
                        <p class="text-xl font-bold leading-none mt-1 {{ $adjustPreviewQty === 0 ? 'text-neutral-500' : 'text-brand dark:text-brand-300' }}">{{ $adjustPreviewQty }}</p>
                        <p class="text-[10px] text-neutral-500 mt-0.5">units</p>
                    </div>
                </div>

                {{-- Type buttons --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Adjustment Type</label>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button wire:click="$set('adjustType','add')"
                                class="px-2 py-1.5 text-xs font-medium rounded border transition-colors {{ $adjustType === 'add' ? 'bg-brand text-white border-brand' : 'bg-transparent text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-neutral-700 hover:border-neutral-400' }}">
                            + Add
                        </button>
                        <button wire:click="$set('adjustType','remove')"
                                class="px-2 py-1.5 text-xs font-medium rounded border transition-colors {{ $adjustType === 'remove' ? 'bg-red-600 text-white border-red-600' : 'bg-transparent text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-neutral-700 hover:border-neutral-400' }}">
                            − Remove
                        </button>
                        <button wire:click="$set('adjustType','set')"
                                class="px-2 py-1.5 text-xs font-medium rounded border transition-colors {{ $adjustType === 'set' ? 'bg-neutral-800 dark:bg-neutral-100 text-white dark:text-neutral-900 border-neutral-800' : 'bg-transparent text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-neutral-700 hover:border-neutral-400' }}">
                            = Set
                        </button>
                    </div>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        @if($adjustType === 'add') Units to Add
                        @elseif($adjustType === 'remove') Units to Remove
                        @else Set Quantity To
                        @endif
                        <span class="text-red-500"> *</span>
                    </label>
                    <input
                        type="number"
                        wire:model.live="adjustAmount"
                        min="0"
                        placeholder="e.g. 10"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
                    />
                    @error('adjustAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Note <span class="text-neutral-400 font-normal">(optional)</span></label>
                    <input type="text" wire:model="adjustNote" placeholder="e.g. Stock count, supplier delivery…"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button wire:click="$set('showAdjustModal', false)" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">Cancel</button>
                <button wire:click="confirmAdjust" class="px-4 py-2 text-xs font-medium bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmAdjust">Save</span>
                    <span wire:loading wire:target="confirmAdjust">Saving…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         SET THRESHOLD MODAL
    ════════════════════════════════════════════════════════════════ --}}
    @if($showThresholdModal)
    <div
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(2px)"
        wire:click.self="$set('showThresholdModal', false)"
    >
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm"
             @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <h2 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">Set Low-Stock Threshold</h2>
                <button wire:click="$set('showThresholdModal', false)" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-3">
                <p class="text-xs text-neutral-600 dark:text-neutral-400">Alert when stock falls at or below this number for <strong class="text-neutral-900 dark:text-neutral-50">{{ $thresholdProductName }}</strong>.</p>
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Threshold <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="thresholdValue" min="0" placeholder="e.g. 5"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all" />
                    @error('thresholdValue') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button wire:click="$set('showThresholdModal', false)" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">Cancel</button>
                <button wire:click="confirmThreshold" class="px-4 py-2 text-xs font-medium bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmThreshold">Save</span>
                    <span wire:loading wire:target="confirmThreshold">Saving…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         SIDE PANEL (product view)
    ════════════════════════════════════════════════════════════════ --}}
    @if($showSidePanel && $sidePanelData)
    <div class="fixed inset-0 z-[9998] flex justify-end" wire:click.self="closeSidePanel">
        <div class="absolute inset-0 bg-black/40" wire:click="closeSidePanel"></div>
        <div class="relative w-full max-w-sm bg-neutral-50 dark:bg-[#1a2332] border-l border-neutral-200 dark:border-neutral-700 h-full overflow-y-auto flex flex-col shadow-2xl">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 bg-neutral-50 dark:bg-[#1a2332]">
                <h3 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">Product Details</h3>
                <button wire:click="closeSidePanel" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 p-5 space-y-5">
                {{-- Status badge --}}
                {{-- Product image --}}
                @if(!empty($sidePanelData['image_url']))
                <div class="rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700">
                    <img src="{{ $sidePanelData['image_url'] }}" alt="{{ $sidePanelData['product_name'] }}"
                         class="w-full h-40 object-cover">
                </div>
                @else
                <div class="w-full h-40 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-900 flex items-center justify-center">
                    <svg class="w-10 h-10 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                @endif

                <div>
                    @php
                        $statusLabel = match($sidePanelData['status']) {
                            'out' => 'Out of Stock',
                            'low' => 'Low Stock',
                            default => 'In Stock',
                        };
                        $statusClass = match($sidePanelData['status']) {
                            'out' => 'bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300',
                            'low' => 'bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300',
                            default => 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>

                {{-- Product name + variant --}}
                <div>
                    <h4 class="font-semibold text-base text-neutral-900 dark:text-neutral-50">{{ $sidePanelData['product_name'] }}</h4>
                    @if($sidePanelData['variant_name'] && $sidePanelData['variant_name'] !== 'Default')
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Variant: {{ $sidePanelData['variant_name'] }}</p>
                    @endif
                </div>

                {{-- Detail rows --}}
                <dl class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">SKU</dt>
                        <dd class="font-mono font-medium text-neutral-900 dark:text-neutral-50 text-xs">{{ $sidePanelData['sku'] ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Category</dt>
                        <dd class="font-medium text-neutral-900 dark:text-neutral-50 text-xs">{{ $sidePanelData['category'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Selling Method</dt>
                        <dd class="font-medium text-neutral-900 dark:text-neutral-50 text-xs">{{ $sidePanelData['method'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Current Stock</dt>
                        <dd class="font-bold text-neutral-900 dark:text-neutral-50">{{ $sidePanelData['qty'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Low-Stock Threshold</dt>
                        <dd class="font-medium text-neutral-900 dark:text-neutral-50">{{ $sidePanelData['threshold'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Last Adjusted</dt>
                        <dd class="text-neutral-500 dark:text-neutral-400 text-xs">{{ $sidePanelData['last_adjusted'] }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Footer actions --}}
            <div class="p-5 border-t border-neutral-200 dark:border-neutral-700 space-y-2 sticky bottom-0 bg-neutral-50 dark:bg-[#1a2332]">
                <button wire:click="openAdjustModal({{ $sidePanelData['variant_id'] }})"
                        class="w-full px-4 py-2.5 bg-brand text-white text-xs font-semibold rounded-lg hover:bg-brand-600 transition-colors text-center">
                    Adjust Stock
                </button>
                <button wire:click="openThresholdModal({{ $sidePanelData['variant_id'] }})"
                        class="w-full px-4 py-2.5 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 text-xs font-semibold rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors text-center">
                    Set Threshold
                </button>
                <a href="{{ $sidePanelData['edit_url'] }}"
                   class="block w-full px-4 py-2.5 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 text-xs font-semibold rounded-lg hover:border-brand hover:text-brand transition-colors text-center">
                    Edit Product
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         STATS CARDS
    ════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Total SKUs</p>
            <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-50">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">In Stock</p>
            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['in_stock']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Low Stock</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['low_stock']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Out of Stock</p>
            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['out_of_stock']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Est. Value</p>
            <p class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">₦{{ number_format($stats['est_value']) }}</p>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FILTERS & SEARCH
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
        {{-- Search --}}
        <div class="mb-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by product name, SKU, or variant…"
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-500 dark:placeholder-neutral-500 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
            />
        </div>

        {{-- Filter Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">

            {{-- Category --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Category</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ $categoryFilter ? ($categories[array_search($categoryFilter, array_column($categories, 'id'))]->name ?? 'All') : 'All' }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="open = false" wire:click="$set('categoryFilter', '')" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All</button>
                        @foreach($categories as $cat)
                            <button @click="open = false" wire:click="$set('categoryFilter', '{{ $cat->id }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $categoryFilter == $cat->id ? 'font-semibold text-brand' : '' }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Stock Status --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Stock</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ match($stockFilter) { 'in-stock'=>'In Stock','low-stock'=>'Low Stock','out-stock'=>'Out of Stock', default=>'All Stock' } }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        @foreach([''=>'All Stock','in-stock'=>'In Stock','low-stock'=>'Low Stock','out-stock'=>'Out of Stock'] as $val => $label)
                            <button @click="open = false" wire:click="$set('stockFilter', '{{ $val }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $stockFilter === $val ? 'font-semibold text-brand' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Selling Method --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Method</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ $methodFilter ? ucwords(str_replace('-',' ',$methodFilter)) : 'All' }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto custom-scrollbar">
                        <button @click="open = false" wire:click="$set('methodFilter', '')" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All Methods</button>
                        @foreach($sellingMethods as $method)
                            <button @click="open = false" wire:click="$set('methodFilter', '{{ $method->config_type }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $methodFilter === $method->config_type ? 'font-semibold text-brand' : '' }}">
                                {{ $method->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Alert --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Alert</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ match($alertFilter) { 'needs-restock'=>'Needs Restock','ok'=>'OK', default=>'All' } }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        @foreach([''=>'All','needs-restock'=>'Needs Restock','ok'=>'OK'] as $val => $label)
                            <button @click="open = false" wire:click="$set('alertFilter', '{{ $val }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $alertFilter === $val ? 'font-semibold text-brand' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sort --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Sort</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ match($sortBy) { 'qty-asc'=>'Qty Low→High','qty-desc'=>'Qty High→Low','updated'=>'Recently Updated', default=>'Name A-Z' } }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        @foreach(['name-asc'=>'Name A-Z','qty-asc'=>'Qty Low→High','qty-desc'=>'Qty High→Low','updated'=>'Recently Updated'] as $val => $label)
                            <button @click="open = false" wire:click="$set('sortBy', '{{ $val }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $sortBy === $val ? 'font-semibold text-brand' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Clear + Export --}}
            <div class="flex items-end gap-2">
                <button wire:click="clearFilters" class="text-brand dark:text-brand-300 hover:text-brand-600 font-medium text-xs whitespace-nowrap transition-colors">
                    Clear
                </button>
                <button wire:click="exportInventory" class="ml-auto px-2.5 py-1.5 text-xs font-medium bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 rounded hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors whitespace-nowrap"
                        wire:loading.attr="disabled" wire:target="exportInventory">
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         INVENTORY TABLE
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 overflow-hidden">

            {{-- Footer info bar above table --}}
        @if(!$inventory->isEmpty())
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-100/70 dark:bg-neutral-900/60">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                Showing <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $inventory->firstItem() }}</span>–<span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $inventory->lastItem() }}</span> of <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $inventory->total() }}</span> items
            </p>
            <div class="flex items-center gap-3">
                <label class="text-xs text-neutral-500 dark:text-neutral-400 flex items-center gap-1.5">
                    Per page:
                    <select wire:model.live="perPage" class="text-xs border border-neutral-300 dark:border-neutral-700 rounded px-1 py-0.5 dark:bg-neutral-900 dark:text-neutral-300 focus:ring-1 focus:ring-brand">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </label>
                <span class="text-xs text-neutral-400 dark:text-neutral-600">Page {{ $inventory->currentPage() }} of {{ $inventory->lastPage() }}</span>
            </div>
        </div>
        @endif

            {{-- Loading overlay --}}
        <div wire:loading wire:target="search,categoryFilter,stockFilter,methodFilter,alertFilter,sortBy,confirmAdjust,confirmThreshold,perPage"
             class="absolute inset-0 bg-white/50 dark:bg-black/30 z-10 flex items-center justify-center rounded-lg">
            <svg class="w-6 h-6 animate-spin text-brand" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="31.4 31.4" stroke-linecap="round"/></svg>
        </div>

        {{-- Empty State --}}
        @if($inventory->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/>
                </svg>
                <p class="text-neutral-600 dark:text-neutral-400 font-medium text-sm">
                    {{ $search || $categoryFilter || $stockFilter || $methodFilter || $alertFilter ? 'No items match your filters' : 'No inventory items yet' }}
                </p>
                <p class="text-neutral-500 text-xs mt-1">Try adjusting your filters or add products first</p>
            </div>
        @else

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto max-h-[750px] overflow-y-auto custom-scrollbar">
            <table class="w-full">
                <thead class="sticky top-0 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800">
                    <tr>
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
                    @foreach($inventory as $item)
                        @php
                            $qty       = (int) $item->stock;
                            $threshold = (int) ($item->low_stock_threshold ?? 0);
                            $statusKey = $qty === 0 ? 'out' : ($qty <= $threshold ? 'low' : 'in');
                            $qtyClass  = match($statusKey) {
                                'out' => 'bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300',
                                'low' => 'bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300',
                                default => 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300',
                            };
                        @endphp
                        <tr
                            wire:key="inv-{{ $item->id }}"
                            class="hover:bg-neutral-100 dark:hover:bg-neutral-900/50 transition-colors cursor-pointer"
                            wire:click="openSidePanel({{ $item->id }})"
                        >
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2">
                                    @if(!empty($productImages[$item->product_id]))
                                        <img src="{{ $productImages[$item->product_id] }}" alt="{{ $item->product_name }}"
                                             class="w-8 h-8 rounded object-cover flex-shrink-0 bg-neutral-100 dark:bg-neutral-800">
                                    @else
                                        <div class="w-8 h-8 rounded bg-gradient-to-br from-neutral-200 to-neutral-300 dark:from-neutral-800 dark:to-neutral-900 flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50">{{ $item->product_name }}</p>
                                        <p class="text-xs text-neutral-500">{{ $item->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400">{{ $item->category_name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400">{{ $item->variant_name ?? 'Default' }}</td>
                            <td class="px-4 py-2.5 text-xs">
                                @if($item->method_name)
                                    <span class="bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded text-xs font-medium">{{ $item->method_name }}</span>
                                @else
                                    <span class="text-neutral-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-xs">
                                <span class="{{ $qtyClass }} px-2 py-0.5 rounded text-xs font-medium">{{ $qty }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400">{{ $threshold }}</td>
                            <td class="px-4 py-2.5 text-xs">
                                @if($statusKey === 'out')
                                    <span class="bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-medium">Out of Stock</span>
                                @elseif($statusKey === 'low')
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium">Low Stock</span>
                                @else
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium">In Stock</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-500">{{ $item->updated_at?->diffForHumans() }}</td>
                            <td class="px-4 py-2.5 text-center" wire:click.stop>
                                <div x-data="{ open: false }" class="relative inline-block" @click.away="open = false">
                                    <button @click="open = !open" class="p-1 hover:bg-neutral-200 dark:hover:bg-neutral-800 rounded transition-colors">
                                        <svg class="w-4 h-4 text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" class="absolute right-0 mt-1 w-44 bg-neutral-50 dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 z-10">
                                        <button @click="open = false" wire:click="openAdjustModal({{ $item->id }})"
                                                class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-t-lg border-b border-neutral-200 dark:border-neutral-700">
                                            Adjust Stock
                                        </button>
                                        <button @click="open = false" wire:click="openThresholdModal({{ $item->id }})"
                                                class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">
                                            Set Threshold
                                        </button>
                                        <a href="{{ route('admin.products.edit', $item->product_id) }}"
                                           class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">
                                            Edit Product
                                        </a>
                                        <button @click="open = false" wire:click="openSidePanel({{ $item->id }})"
                                                class="w-full text-left px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-b-lg">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="block md:hidden divide-y divide-neutral-200 dark:divide-neutral-800">
            @foreach($inventory as $item)
                @php
                    $qty       = (int) $item->stock;
                    $threshold = (int) ($item->low_stock_threshold ?? 0);
                    $statusKey = $qty === 0 ? 'out' : ($qty <= $threshold ? 'low' : 'in');
                @endphp
                <div wire:key="mob-{{ $item->id }}" class="p-4" wire:click="openSidePanel({{ $item->id }})">
                    <div class="flex items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <div>
                                    <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50 truncate">{{ $item->product_name }}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $item->sku }}{{ $item->variant_name && $item->variant_name !== 'Default' ? ' · ' . $item->variant_name : '' }}</p>
                                </div>
                                @if($statusKey === 'out')
                                    <span class="bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-medium flex-shrink-0">Out</span>
                                @elseif($statusKey === 'low')
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded text-xs font-medium flex-shrink-0">Low</span>
                                @else
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-medium flex-shrink-0">OK</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                <p class="text-neutral-600 dark:text-neutral-400"><strong>Category:</strong> {{ $item->category_name ?? '—' }}</p>
                                <p class="text-neutral-600 dark:text-neutral-400"><strong>Qty:</strong> {{ $qty }}</p>
                                <p class="text-neutral-600 dark:text-neutral-400"><strong>Method:</strong> {{ $item->method_name ?? '—' }}</p>
                                <p class="text-neutral-600 dark:text-neutral-400"><strong>Threshold:</strong> {{ $threshold }}</p>
                            </div>
                            <div class="flex gap-2 mt-2" wire:click.stop>
                                <button wire:click="openAdjustModal({{ $item->id }})"
                                        class="px-2 py-1 text-xs bg-brand text-white rounded hover:bg-brand-600 transition-colors">
                                    Adjust
                                </button>
                                <a href="{{ route('admin.products.edit', $item->product_id) }}"
                                   class="px-2 py-1 text-xs bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 rounded hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════════
         PAGINATION (Prev / Next)
    ════════════════════════════════════════════════════════════════ --}}
    @if($inventory->hasPages())
        <div class="flex items-center justify-between gap-4">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                Page <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $inventory->currentPage() }}</span>
                of <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $inventory->lastPage() }}</span>
            </p>
            <div class="flex items-center gap-2">
                @if($inventory->onFirstPage())
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:border-neutral-400 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Previous
                    </button>
                @endif

                @if($inventory->hasMorePages())
                    <button wire:click="nextPage"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:border-neutral-400 transition-colors">
                        Next
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg cursor-not-allowed">
                        Next
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
    @endif

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
</style>
