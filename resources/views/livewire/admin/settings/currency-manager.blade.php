{{-- livewire/admin/settings/currency-manager.blade.php --}}

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Currencies</h1>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Manage exchange rates and per-currency markup multipliers. All product prices are stored in NGN.</p>
    </div>

    {{-- Currency table --}}
    <div class="bg-white dark:bg-[#111827] border border-neutral-200 dark:border-neutral-800 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 dark:border-neutral-800">
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Currency</th>
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Rate (per 1 NGN)</th>
                    <th class="px-6 py-3 text-left font-semibold text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Markup</th>
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
                        <input type="number" wire:model="editingMarkup" step="0.0001" min="0.01"
                               class="w-24 px-2 py-1 text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white rounded focus:outline-none focus:ring-2 focus:ring-brand" />
                        @else
                        <span class="font-mono text-xs text-neutral-700 dark:text-neutral-300">×{{ number_format((float) $currency->markup, 4) }}</span>
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
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-xs text-neutral-400 dark:text-neutral-500">
        Rates are stored as: how many of the foreign currency equal 1 NGN. For example, if 1 NGN = 0.00065 USD, enter <code class="font-mono">0.00065000</code> for USD.
        The markup multiplier is applied on top of the conversion rate (1.0 = no markup, 1.5 = 50% added).
    </p>

</div>
