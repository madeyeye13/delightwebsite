{{-- livewire/admin/settings/currency-manager.blade.php --}}

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Currencies</h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Manage exchange rates and per-currency additive markup. All product prices are stored in NGN.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="refreshRates" wire:loading.attr="disabled" wire:target="refreshRates"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-white dark:bg-neutral-900 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="refreshRates">↻ Refresh Rates</span>
                <span wire:loading wire:target="refreshRates">Fetching…</span>
            </button>
            @if(! $showCreateForm)
            <button wire:click="$set('showCreateForm', true)"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors">
                + Add Currency
            </button>
            @endif
        </div>
    </div>

    {{-- Create form (collapsible) --}}
    @if($showCreateForm)
    <div class="bg-white dark:bg-[#111827] border border-neutral-200 dark:border-neutral-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-white mb-4">Add New Currency</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">ISO Code <span class="text-red-500">*</span></label>
                <input type="text" wire:model="newCode" placeholder="e.g. KES" maxlength="6"
                       class="w-full px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-brand uppercase" />
                @error('newCode') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="newName" placeholder="e.g. Kenyan Shilling"
                       class="w-full px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-brand" />
                @error('newName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Symbol <span class="text-red-500">*</span></label>
                <input type="text" wire:model="newSymbol" placeholder="e.g. KSh" maxlength="10"
                       class="w-full px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-brand" />
                @error('newSymbol') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Markup (additive)</label>
                <input type="number" wire:model="newMarkup" step="0.0001" min="0" placeholder="0.00"
                       class="w-full px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-brand" />
                @error('newMarkup') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="newIsActive" class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 text-brand focus:ring-brand" />
                <span class="text-xs text-neutral-600 dark:text-neutral-400">Active immediately</span>
            </label>
            <div class="flex items-center gap-2">
                <button wire:click="$set('showCreateForm', false)"
                        class="px-4 py-2 text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                    Cancel
                </button>
                <button wire:click="createCurrency" wire:loading.attr="disabled" wire:target="createCurrency"
                        class="px-4 py-2 text-xs font-semibold bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="createCurrency">Add Currency</span>
                    <span wire:loading wire:target="createCurrency">Fetching rate…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Currency table --}}
    <div class="bg-white dark:bg-[#111827] border border-neutral-200 dark:border-neutral-800 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 dark:border-neutral-800">
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Currency</th>
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Rate (per 1 NGN)</th>
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Markup (additive)</th>
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Status</th>
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach($currencies as $currency)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 text-center font-mono text-xs font-bold bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 rounded px-1.5 py-0.5">{{ $currency->symbol }}</span>
                            <div>
                                <p class="font-semibold text-neutral-900 dark:text-white">{{ $currency->code }}</p>
                                <p class="text-xs text-neutral-400 dark:text-neutral-500">{{ $currency->name }}</p>
                            </div>
                            @if($currency->is_default)
                            <span class="text-[10px] font-semibold tracking-wide uppercase px-2 py-0.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 rounded">Default</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($editingCode === $currency->code)
                        <input type="number" wire:model="editingRate" step="0.00000001" min="0"
                               class="w-36 px-2 py-1 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded focus:outline-none focus:ring-2 focus:ring-brand" />
                        @else
                        <span class="font-mono text-xs text-neutral-700 dark:text-neutral-300">{{ number_format((float) $currency->latestRate?->rate, 8) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($editingCode === $currency->code)
                        <input type="number" wire:model="editingMarkup" step="0.0001" min="0"
                               class="w-24 px-2 py-1 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded focus:outline-none focus:ring-2 focus:ring-brand" />
                        @else
                        <span class="font-mono text-xs text-neutral-700 dark:text-neutral-300">{{ number_format((float) $currency->markup, 4) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($editingCode === $currency->code)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="editingActive" class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 text-brand focus:ring-brand" />
                            <span class="text-xs text-neutral-600 dark:text-neutral-400">Active</span>
                        </label>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
                                     {{ $currency->is_active
                                         ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                         : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currency->is_active ? 'bg-emerald-500' : 'bg-neutral-400' }}"></span>
                            {{ $currency->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($editingCode === $currency->code)
                            <button wire:click="saveRate({{ $currency->id }})"
                                    class="px-3 py-1.5 text-xs font-semibold bg-brand text-white rounded hover:bg-brand-600 transition-colors">
                                Save
                            </button>
                            <button wire:click="$set('editingCode', '')"
                                    class="px-3 py-1.5 text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                                Cancel
                            </button>
                            @else
                            <button wire:click="editCurrency({{ $currency->id }})"
                                    class="px-3 py-1.5 text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                                Edit
                            </button>
                            @if(! $currency->is_default)
                            <button wire:click="setDefault({{ $currency->id }})"
                                    wire:confirm="Set {{ $currency->code }} as the default display currency?"
                                    class="px-3 py-1.5 text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                                Set Default
                            </button>
                            @endif
                            @if(! $currency->is_default && $currency->code !== 'NGN')
                            <button wire:click="deleteCurrency({{ $currency->id }})"
                                    wire:confirm="Permanently delete {{ $currency->code }}? This cannot be undone."
                                    class="px-3 py-1.5 text-xs font-semibold bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                Delete
                            </button>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-xs text-neutral-400 dark:text-neutral-500">
        Rates are stored as: how many of the foreign currency equal 1 NGN. For example, if 1 NGN = 0.00065 USD, enter <code class="font-mono">0.00065000</code> for USD.<br>
        The <strong>additive markup</strong> is a flat amount in the foreign currency added to every price after conversion (e.g. markup 4 for USD adds $4). Use <code class="font-mono">0</code> for no markup.
    </p>

</div>
