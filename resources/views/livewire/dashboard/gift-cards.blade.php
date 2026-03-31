<div class="p-6 md:p-8 max-w-5xl mx-auto">
    <!---resources/views/livewire/dashboard/gift-cards.blade.php--->

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-white tracking-tight">My Gift Cards</h1>
        <p class="text-sm text-white/40 mt-1">View your gift card codes and available balances</p>
    </div>

    @if($cards->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-14 h-14 rounded-full bg-white/[0.04] flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-white/30">You don't have any gift cards yet</p>
            <p class="text-xs text-white/20 mt-1">Gift cards you purchase or receive will appear here</p>
            <a href="{{ route('shop.index') }}"
               class="mt-5 text-xs text-brand-400 hover:text-brand-300 transition-colors underline underline-offset-4">
                Browse our shop
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($cards as $card)
                @php
                    $percent = $card->initial_balance > 0
                        ? round(($card->current_balance / $card->initial_balance) * 100)
                        : 0;

                    $statusConfig = match($card->status) {
                        'active'   => ['label' => 'Active',   'classes' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
                        'redeemed' => ['label' => 'Redeemed', 'classes' => 'bg-neutral-500/10 text-neutral-400 border-neutral-500/20'],
                        'expired'  => ['label' => 'Expired',  'classes' => 'bg-red-500/10    text-red-400    border-red-500/20'],
                        'void'     => ['label' => 'Void',     'classes' => 'bg-orange-500/10 text-orange-400 border-orange-500/20'],
                        default    => ['label' => ucfirst($card->status), 'classes' => 'bg-neutral-500/10 text-neutral-400 border-neutral-500/20'],
                    };

                    $isRevealed = $revealedCodes[$card->id] ?? false;
                @endphp

                <div class="bg-white/[0.03] border border-white/[0.07] rounded-xl p-5 space-y-4">

                    {{-- Top row: status badge + expiry --}}
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border {{ $statusConfig['classes'] }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $statusConfig['label'] }}
                        </span>

                        <div class="flex items-center gap-3 text-xs text-white/30">
                            @if($card->expires_at)
                                <span>
                                    {{ $card->expires_at->isPast() ? 'Expired' : 'Expires' }}
                                    {{ $card->expires_at->format('M j, Y') }}
                                </span>
                            @else
                                <span>No expiry</span>
                            @endif

                            @if($card->purchasedOrder)
                                <a href="{{ route('account.orders.show', $card->purchasedOrder) }}"
                                   class="text-brand-400/70 hover:text-brand-400 transition-colors">
                                    Order #{{ $card->purchasedOrder->order_number ?? $card->purchasedOrder->id }}
                                </a>
                            @elseif($card->is_pos_issued)
                                <span>Issued in-store</span>
                            @endif
                        </div>
                    </div>

                    {{-- Code row --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-white/[0.04] border border-white/[0.08] rounded-lg px-4 py-2.5">
                            <p class="text-[11px] text-white/30 mb-0.5">Gift Card Code</p>
                            <p class="font-mono text-sm font-semibold tracking-widest text-white/90 select-all">
                                {{ $isRevealed ? $card->code : preg_replace('/[A-Z0-9](?=[A-Z0-9]{4,})/', '•', $card->code) }}
                            </p>
                        </div>
                        <button
                            wire:click="toggleCode({{ $card->id }})"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all
                                   {{ $isRevealed
                                       ? 'bg-white/[0.07] text-white/60 hover:bg-white/[0.1]'
                                       : 'bg-brand-500/10 text-brand-400 border border-brand-500/20 hover:bg-brand-500/20' }}">
                            @if($isRevealed)
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                                Hide
                            @else
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Reveal
                            @endif
                        </button>
                    </div>

                    {{-- Balance bar --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-white/40">Balance</span>
                            <span class="text-xs font-semibold text-white/80">
                                ₦{{ number_format($card->current_balance) }}
                                <span class="font-normal text-white/30">/ ₦{{ number_format($card->initial_balance) }}</span>
                            </span>
                        </div>
                        <div class="w-full bg-white/[0.06] rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                        {{ $percent > 20 ? 'bg-brand-400' : 'bg-orange-400' }}"
                                 style="width: {{ $percent }}%">
                            </div>
                        </div>
                        <p class="text-[10px] text-white/25 mt-1">{{ $percent }}% remaining</p>
                    </div>

                    {{-- Personal message if present --}}
                    @if($card->personal_message)
                        <div class="bg-white/[0.02] border border-white/[0.05] rounded-lg px-4 py-3">
                            <p class="text-[11px] text-white/30 mb-1">Message</p>
                            <p class="text-xs text-white/60 italic">"{{ $card->personal_message }}"</p>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    @endif
</div>
