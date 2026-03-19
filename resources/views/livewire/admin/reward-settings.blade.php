<div class="max-w-2xl">

    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">
            Reward & Referral Settings
        </h1>
        <p class="text-sm text-gray-500 dark:text-white/40 mt-1">
            Configure point values and referral discounts
        </h1>
    </div>

    <div class="bg-white dark:bg-white/[0.03]
                border border-gray-200 dark:border-white/[0.06]
                rounded-2xl p-6 space-y-6 shadow-sm dark:shadow-none">

        {{-- Points per referral --}}
        <div>
            <label class="block text-[11px] font-medium tracking-widest uppercase
                           text-gray-500 dark:text-white/35 mb-1.5">
                Points Awarded per Referral
            </label>
            <p class="text-xs text-gray-400 dark:text-white/25 mb-2">
                Points the referrer earns each time someone uses their code at checkout.
            </p>
            <input wire:model="points_per_referral" type="number" min="1" max="10000"
                   class="w-full bg-gray-50 dark:bg-white/[0.04]
                          border border-gray-200 dark:border-white/[0.08]
                          rounded-xl text-sm text-gray-900 dark:text-white
                          px-4 py-2.5
                          focus:outline-none focus:border-emerald-500
                          dark:focus:border-emerald-500/50
                          transition-colors" />
            @error('points_per_referral')
                <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Naira per point --}}
        <div>
            <label class="block text-[11px] font-medium tracking-widest uppercase
                           text-gray-500 dark:text-white/35 mb-1.5">
                Naira Value Per Point (₦)
            </label>
            <p class="text-xs text-gray-400 dark:text-white/25 mb-2">
                How much each reward point is worth when redeemed at checkout.
            </p>
            <input wire:model="naira_per_point" type="number" min="1" max="10000"
                   class="w-full bg-gray-50 dark:bg-white/[0.04]
                          border border-gray-200 dark:border-white/[0.08]
                          rounded-xl text-sm text-gray-900 dark:text-white
                          px-4 py-2.5
                          focus:outline-none focus:border-emerald-500
                          dark:focus:border-emerald-500/50
                          transition-colors" />
            @error('naira_per_point')
                <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Max points per order --}}
        <div>
            <label class="block text-[11px] font-medium tracking-widest uppercase
                           text-gray-500 dark:text-white/35 mb-1.5">
                Max Points Redeemable Per Order
            </label>
            <p class="text-xs text-gray-400 dark:text-white/25 mb-2">
                Caps how many points a user can spend on a single order.
            </p>
            <input wire:model="max_points_per_order" type="number" min="1" max="100000"
                   class="w-full bg-gray-50 dark:bg-white/[0.04]
                          border border-gray-200 dark:border-white/[0.08]
                          rounded-xl text-sm text-gray-900 dark:text-white
                          px-4 py-2.5
                          focus:outline-none focus:border-emerald-500
                          dark:focus:border-emerald-500/50
                          transition-colors" />
            @error('max_points_per_order')
                <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Referral discount percent --}}
        <div>
            <label class="block text-[11px] font-medium tracking-widest uppercase
                           text-gray-500 dark:text-white/35 mb-1.5">
                Referral Discount (%)
            </label>
            <p class="text-xs text-gray-400 dark:text-white/25 mb-2">
                Percentage discount applied to the referred customer's checkout order.
            </p>
            <div class="relative">
                <input wire:model="referral_discount_percent" type="number" min="1" max="100"
                       class="w-full bg-gray-50 dark:bg-white/[0.04]
                              border border-gray-200 dark:border-white/[0.08]
                              rounded-xl text-sm text-gray-900 dark:text-white
                              px-4 py-2.5 pr-10
                              focus:outline-none focus:border-emerald-500
                              dark:focus:border-emerald-500/50
                              transition-colors" />
                <span class="absolute right-4 top-1/2 -translate-y-1/2
                             text-sm text-gray-400 dark:text-white/30">%</span>
            </div>
            @error('referral_discount_percent')
                <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Live preview --}}
        <div class="bg-gray-50 dark:bg-white/[0.03]
                    border border-gray-200 dark:border-white/[0.05]
                    rounded-xl p-4">
            <p class="text-[10px] font-semibold tracking-widest uppercase mb-3
                       text-gray-400 dark:text-white/25">
                Live Preview
            </p>
            <div class="space-y-1.5 text-xs text-gray-500 dark:text-white/50">
                <p>
                    Referrer earns
                    <span class="text-gray-800 dark:text-white/80 font-medium">
                        {{ number_format($points_per_referral) }} points
                    </span>
                    per referral
                </p>
                <p>
                    {{ number_format($points_per_referral) }} points =
                    <span class="text-gray-800 dark:text-white/80 font-medium">
                        ₦{{ number_format($points_per_referral * $naira_per_point) }}
                    </span>
                </p>
                <p>
                    Max per order:
                    <span class="text-gray-800 dark:text-white/80 font-medium">
                        {{ number_format($max_points_per_order) }} pts
                        = ₦{{ number_format($max_points_per_order * $naira_per_point) }}
                    </span>
                </p>
                <p>
                    Referred customer gets:
                    <span class="text-gray-800 dark:text-white/80 font-medium">
                        {{ $referral_discount_percent }}% off
                    </span>
                </p>
            </div>
        </div>

        {{-- Save button --}}
        <div class="pt-2">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                           bg-emerald-50 hover:bg-emerald-100
                           dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20
                           border border-emerald-200 dark:border-emerald-500/30
                           text-emerald-700 dark:text-emerald-400
                           hover:text-emerald-800 dark:hover:text-emerald-300
                           text-sm font-medium transition-all duration-150
                           disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading wire:target="save"
                     class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>

    </div>
</div>