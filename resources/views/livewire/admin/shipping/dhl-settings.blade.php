<div class="max-w-2xl">
    @if(session('success'))
    <div class="mb-5 flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl divide-y divide-gray-100 dark:divide-white/[0.06]">

        {{-- Mode --}}
        <div class="px-5 py-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider mb-3">Mode</h3>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model="testMode" value="1" class="accent-emerald-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-white/70">Test Mode</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model="testMode" value="0" class="accent-emerald-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-white/70">Live Mode</span>
                </label>
            </div>
            <p class="text-xs text-gray-400 dark:text-white/30 mt-2">
                Test mode uses sandbox endpoints and returns dummy rates. Switch to Live when your DHL account is approved.
            </p>
        </div>

        {{-- Rates --}}
        <div class="px-5 py-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider mb-3">Rate Settings</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Markup % <span class="text-gray-400">(added on top of DHL rate)</span></label>
                    <input wire:model="markupPercentage" type="number" step="0.1" min="0" max="200"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('markupPercentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Max Weight (kg)</label>
                    <input wire:model="maxWeightKg" type="number" step="0.1" min="0.1"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('maxWeightKg') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Origin Address --}}
        <div class="px-5 py-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider mb-1">Shipper Origin Address</h3>
            <p class="text-xs text-gray-400 dark:text-white/30 mb-3">
                This is the registered DHL pick-up address. It defaults to your config/services.php values.
            </p>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Contact / Business Name</label>
                    <input wire:model="originName" type="text"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('originName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Street Address</label>
                    <input wire:model="originStreet" type="text"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('originStreet') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">City</label>
                        <input wire:model="originCity" type="text"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Postal Code</label>
                        <input wire:model="originPostal" type="text"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-white/50 mb-1">Country (2-letter)</label>
                        <input wire:model="originCountry" type="text" maxlength="2"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 rounded-lg text-gray-900 dark:text-white uppercase focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Save --}}
        <div class="px-5 py-4 flex justify-end">
            <button wire:click="save()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save DHL Settings
            </button>
        </div>

    </div>
</div>
