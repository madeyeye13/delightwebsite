<div class="p-6 md:p-8 max-w-3xl mx-auto">

    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-white tracking-tight">Referral & Rewards</h1>
        <p class="text-sm text-white/40 mt-1">Earn points by sharing your referral link</p>
    </div>

    {{-- ── Stats row ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
        {{-- Points balance --}}
        <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-medium tracking-widests uppercase text-white/25 mb-2">Points Balance</p>
            <p class="font-display text-3xl font-bold text-white">{{ number_format($pointBalance) }}</p>
            <p class="text-xs text-white/30 mt-1">≈ ₦{{ number_format($pointsValue) }} value</p>
        </div>

        {{-- Value per point --}}
        <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-medium tracking-widests uppercase text-white/25 mb-2">Value Per Point</p>
            <p class="font-display text-3xl font-bold text-white">₦{{ number_format($nairaPerPoint) }}</p>
            <p class="text-xs text-white/30 mt-1">Set by admin</p>
        </div>

        {{-- Max per order --}}
        <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-medium tracking-widests uppercase text-white/25 mb-2">Max Per Order</p>
            <p class="font-display text-3xl font-bold text-white">{{ number_format($maxPerOrder) }} pts</p>
            <p class="text-xs text-white/30 mt-1">≈ ₦{{ number_format($maxPerOrder * $nairaPerPoint) }}</p>
        </div>
    </div>

    {{-- ── Referral Link ── --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5 mb-4"
         x-data="{ copied: false }">

        <h2 class="text-xs font-semibold text-white/40 tracking-widests uppercase mb-4">Your Referral Link</h2>

        <p class="text-sm text-white/50 leading-relaxed mb-4">
            Share this link. When someone uses it to shop and checkout,
            they receive a discount and you earn <span class="text-white/80 font-medium">{{ number_format(\App\Models\RewardSetting::pointsPerReferral()) }} points</span>
            (₦{{ number_format(\App\Models\RewardSetting::pointsPerReferral() * $nairaPerPoint) }}).
        </p>

        <div class="flex items-center gap-2">
            <div class="flex-1 bg-white/[0.05] border border-white/[0.08] rounded-xl px-4 py-2.5 overflow-hidden">
                <p class="text-sm text-white/60 font-mono truncate">{{ $referral->url }}</p>
            </div>
            <button
                @click="
                    navigator.clipboard.writeText('{{ $referral->url }}');
                    copied = true;
                    setTimeout(() => copied = false, 2500);
                "
                class="shrink-0 flex items-center gap-1.5 px-4 py-2.5 rounded-xl
                       bg-brand-500/10 hover:bg-brand-500/20 border border-brand-500/30
                       text-brand-400 hover:text-brand-300 text-xs font-medium
                       transition-all duration-150">
                <svg x-show="!copied" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
                <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
            </button>
        </div>

        <p class="text-[11px] text-white/25 mt-3">
            Your referral code: <span class="font-mono text-white/50">{{ $referral->code }}</span>
        </p>
    </div>

    {{-- ── Referral uses ── --}}
    @if($referralUses->isNotEmpty())
        <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl overflow-hidden mb-4">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <p class="text-xs font-semibold text-white/40 tracking-wide">Recent Referral Uses</p>
            </div>
            <div class="divide-y divide-white/[0.04]">
                @foreach($referralUses as $use)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-white/70 font-medium">
                                Order #{{ $use->order?->order_number ?? '—' }}
                            </p>
                            <p class="text-xs text-white/30 mt-0.5">
                                {{ $use->created_at->format('d M Y') }}
                                · Gave {{ number_format($use->discount_amount) }} NGN discount to buyer
                            </p>
                        </div>
                        <span class="text-sm font-semibold text-emerald-400 shrink-0">
                            +{{ number_format($use->points_awarded) }} pts
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Points history ── --}}
    @if($history->isNotEmpty())
        <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <p class="text-xs font-semibold text-white/40 tracking-wide">Points History</p>
            </div>
            <div class="divide-y divide-white/[0.04]">
                @foreach($history as $entry)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0
                                        {{ $entry->type === 'earned' ? 'bg-emerald-500/10' : 'bg-amber-500/10' }}">
                                <svg class="w-3.5 h-3.5 {{ $entry->type === 'earned' ? 'text-emerald-400' : 'text-amber-400' }}"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    @if($entry->type === 'earned')
                                        <path d="M12 5v14M5 12l7-7 7 7"/>
                                    @else
                                        <path d="M12 19V5M5 12l7 7 7-7"/>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-white/70">{{ $entry->description }}</p>
                                <p class="text-xs text-white/30 mt-0.5">{{ $entry->created_at->format('d M Y, g:ia') }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold shrink-0
                                     {{ $entry->points > 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $entry->points > 0 ? '+' : '' }}{{ number_format($entry->points) }} pts
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>