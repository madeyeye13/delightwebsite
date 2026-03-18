<div>
    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Tab switcher --}}
    <div class="flex items-center gap-1 mb-5 border-b border-gray-100 dark:border-white/[0.06]">
        <button wire:click="$set('activeTab', 'states')"
            class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'states' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70' }}">
            States ({{ \App\Models\StateShipping::count() }})
        </button>
        <button wire:click="$set('activeTab', 'cities')"
            class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'cities' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70' }}">
            Cities ({{ \App\Models\NigerianCityShipping::count() }})
        </button>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('admin.shipping.dhl') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                DHL Settings
            </a>
        </div>
    </div>

    {{-- ── STATES TAB ──────────────────────────────────────────────── --}}
    @if($activeTab === 'states')
    <div class="space-y-4">
        <div class="relative max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="stateSearch" type="text" placeholder="Filter states…"
                class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 dark:border-white/[0.06]">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">State</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Shipping Fee (₦)</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Est. Days</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                    @forelse($this->states as $state)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $state->state_name }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-white/70">{{ number_format($state->shipping_fee, 0) }}</td>
                        <td class="px-4 py-3 text-center text-gray-500 dark:text-white/40">{{ $state->estimated_days }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="editState({{ $state->id }})" class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">Edit</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-white/30 text-sm">No states found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($this->states->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06]">{{ $this->states->links() }}</div>
            @endif
        </div>

        {{-- State Edit Form --}}
        @if($showStateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="resetStateForm()"></div>
            <div class="relative w-full max-w-md bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-2xl p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Edit State Shipping: {{ $stateName }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">State Name</label>
                        <input wire:model="stateName" type="text" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        @error('stateName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Shipping Fee (₦)</label>
                        <input wire:model="shippingFee" type="number" step="0.01" min="0" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Estimated Days</label>
                        <input wire:model="estimatedDays" type="number" min="1" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="flex gap-2 mt-5">
                    <button wire:click="saveState()" class="flex-1 py-2 text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors">Save Changes</button>
                    <button wire:click="resetStateForm()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white transition-colors">Cancel</button>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ── CITIES TAB ──────────────────────────────────────────────── --}}
    @if($activeTab === 'cities')
    <div class="space-y-4">
        <div class="relative max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="citySearch" type="text" placeholder="Filter cities or states…"
                class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 dark:border-white/[0.06]">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">City</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">State</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Shipping Fee (₦)</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Days</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                    @forelse($this->cities as $city)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $city->city_name }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-white/50 text-xs">{{ $city->state_name }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-white/70">{{ $city->shipping_fee ? number_format($city->shipping_fee, 0) : '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-500 dark:text-white/40">{{ $city->estimated_days ?? 'State' }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="editCity({{ $city->id }})" class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">Edit</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 dark:text-white/30 text-sm">No cities found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($this->cities->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06]">{{ $this->cities->links() }}</div>
            @endif
        </div>

        {{-- City Edit Form --}}
        @if($showCityForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="resetCityForm()"></div>
            <div class="relative w-full max-w-md bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl shadow-2xl p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Edit City: {{ $cityName }}</h3>
                <p class="text-xs text-gray-400 dark:text-white/30 mb-4">Leave Base Price / Per kg / Days blank to inherit from the state.</p>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">City Name</label>
                            <input wire:model="cityName" type="text" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">State Name</label>
                            <input wire:model="cityStateName" type="text" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Shipping Fee (₦) override</label>
                            <input wire:model="cityShippingFee" type="number" step="0.01" min="0" placeholder="0 = state" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Days override</label>
                            <input wire:model="cityDays" type="number" min="1" placeholder="0 = state" class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 mt-5">
                    <button wire:click="saveCity()" class="flex-1 py-2 text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors">Save Changes</button>
                    <button wire:click="resetCityForm()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white transition-colors">Cancel</button>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
