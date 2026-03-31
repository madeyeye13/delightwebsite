<div class="max-w-xl">

    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">
            Gift Card Redemption
        </h1>
        <p class="text-sm text-gray-500 dark:text-white/40 mt-1">
            Look up a customer's gift card and apply it to an in-store order.
        </p>
    </div>

    {{-- Step 1: Code Lookup --}}
    @if($step === 1)
    <div class="bg-white dark:bg-white/[0.03]
                border border-gray-200 dark:border-white/[0.06]
                rounded-2xl p-6 shadow-sm dark:shadow-none">

        <label class="block text-[11px] font-medium tracking-widest uppercase
                       text-gray-500 dark:text-white/35 mb-2">
            Gift Card Code
        </label>
        <p class="text-xs text-gray-400 dark:text-white/25 mb-3">
            Ask the customer for their gift card code (format: DLT-XXXX-XXXX-XXXX).
        </p>

        <div class="flex gap-3">
            <input
                wire:model="code"
                wire:keydown.enter="lookupCode"
                type="text"
                maxlength="19"
                placeholder="DLT-XXXX-XXXX-XXXX"
                autocomplete="off"
                spellcheck="false"
                class="flex-1 bg-gray-50 dark:bg-white/[0.04]
                       border border-gray-200 dark:border-white/[0.08]
                       rounded-xl text-sm text-gray-900 dark:text-white
                       px-4 py-2.5 font-mono tracking-widest uppercase
                       focus:outline-none focus:border-emerald-500 dark:focus:border-emerald-500/50
                       placeholder:normal-case placeholder:tracking-normal placeholder:font-sans
                       transition-colors"
            />
            <button
                wire:click="lookupCode"
                wire:loading.attr="disabled"
                class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50
                       text-white text-sm font-semibold rounded-xl transition-colors shrink-0"
            >
                <span wire:loading.remove wire:target="lookupCode">Look Up</span>
                <span wire:loading wire:target="lookupCode">Checking…</span>
            </button>
        </div>

        @if($errorMessage)
        <p class="mt-3 text-sm text-red-500 dark:text-red-400 flex items-center gap-1.5">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $errorMessage }}
        </p>
        @endif
    </div>
    @endif

    {{-- Step 2: Card Details + Order Amount --}}
    @if($step === 2 && $foundCard)
    <div class="space-y-4">

        {{-- Card details panel --}}
        <div class="bg-white dark:bg-white/[0.03]
                    border border-emerald-200 dark:border-emerald-500/20
                    rounded-2xl p-6 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold tracking-widest uppercase text-emerald-600 dark:text-emerald-400 mb-1">
                        Gift Card Found
                    </p>
                    <p class="font-mono text-lg font-bold text-gray-900 dark:text-white tracking-widest">
                        {{ $foundCard['code'] }}
                    </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                             {{ $foundCard['status'] === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-white/[0.06] dark:text-white/50' }}">
                    {{ strtoupper($foundCard['status']) }}
                </span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-medium tracking-widest uppercase text-gray-400 dark:text-white/30 mb-0.5">Available Balance</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                        ₦{{ number_format($foundCard['current_balance'], 0) }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-medium tracking-widest uppercase text-gray-400 dark:text-white/30 mb-0.5">Original Value</p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-white/70">
                        ₦{{ number_format($foundCard['initial_balance'], 0) }}
                    </p>
                </div>
            </div>

            @if($foundCard['expires_at'])
            <p class="mt-3 text-xs text-gray-400 dark:text-white/30">
                Expires: <span class="text-gray-600 dark:text-white/50 font-medium">{{ $foundCard['expires_at'] }}</span>
            </p>
            @endif

            @if($foundCard['recipient_name'])
            <p class="mt-1 text-xs text-gray-400 dark:text-white/30">
                Issued to: <span class="text-gray-600 dark:text-white/50 font-medium">{{ $foundCard['recipient_name'] }}</span>
            </p>
            @endif
        </div>

        {{-- Order amount input --}}
        <div class="bg-white dark:bg-white/[0.03]
                    border border-gray-200 dark:border-white/[0.06]
                    rounded-2xl p-6 shadow-sm dark:shadow-none space-y-4">

            <div>
                <label class="block text-[11px] font-medium tracking-widest uppercase
                               text-gray-500 dark:text-white/35 mb-2">
                    Customer's Order Amount (₦)
                </label>
                <p class="text-xs text-gray-400 dark:text-white/25 mb-3">
                    Enter the total value of the customer's in-store purchase. The system will calculate
                    how much the gift card covers.
                </p>
                <input
                    wire:model.live="orderAmount"
                    type="number"
                    min="1"
                    placeholder="e.g. 25000"
                    class="w-full bg-gray-50 dark:bg-white/[0.04]
                           border border-gray-200 dark:border-white/[0.08]
                           rounded-xl text-sm text-gray-900 dark:text-white
                           px-4 py-2.5
                           focus:outline-none focus:border-emerald-500 dark:focus:border-emerald-500/50
                           transition-colors"
                />
            </div>

            {{-- Live calculation preview --}}
            @php
                $orderAmtInt    = (int) $orderAmount;
                $balance        = (int) ($foundCard['current_balance'] ?? 0);
                $covered        = ($orderAmtInt > 0) ? min($orderAmtInt, $balance) : 0;
                $remaining      = ($orderAmtInt > 0) ? max(0, $orderAmtInt - $balance) : 0;
                $cardAfter      = ($orderAmtInt > 0) ? max(0, $balance - $orderAmtInt) : $balance;
            @endphp
            @if($orderAmtInt > 0)
            <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] p-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-white/40">Order amount</span>
                    <span class="font-semibold text-gray-800 dark:text-white">₦{{ number_format($orderAmtInt, 0) }}</span>
                </div>
                <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                    <span>Gift card covers</span>
                    <span class="font-semibold">−₦{{ number_format($covered, 0) }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-white/[0.06] flex justify-between">
                    <span class="text-gray-600 dark:text-white/50 font-medium">Customer still owes</span>
                    <span class="font-bold text-gray-900 dark:text-white text-base">₦{{ number_format($remaining, 0) }}</span>
                </div>
                <div class="flex justify-between text-xs text-gray-400 dark:text-white/25">
                    <span>Card remaining after</span>
                    <span>₦{{ number_format($cardAfter, 0) }}</span>
                </div>
            </div>
            @endif

            {{-- Notes --}}
            <div>
                <label class="block text-[11px] font-medium tracking-widest uppercase
                               text-gray-500 dark:text-white/35 mb-2">
                    Notes (optional)
                </label>
                <input
                    wire:model="notes"
                    type="text"
                    maxlength="255"
                    placeholder="e.g. In-store purchase — receipt #1234"
                    class="w-full bg-gray-50 dark:bg-white/[0.04]
                           border border-gray-200 dark:border-white/[0.08]
                           rounded-xl text-sm text-gray-900 dark:text-white
                           px-4 py-2.5
                           focus:outline-none focus:border-emerald-500 dark:focus:border-emerald-500/50
                           transition-colors"
                />
            </div>

            @if($errorMessage)
            <p class="text-sm text-red-500 dark:text-red-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ $errorMessage }}
            </p>
            @endif

            <div class="flex gap-3 pt-2">
                <button
                    wire:click="resetPanel"
                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.05] dark:hover:bg-white/[0.08]
                           text-gray-700 dark:text-white/70 text-sm font-medium rounded-xl transition-colors"
                >
                    ← Back
                </button>
                <button
                    wire:click="applyCard"
                    wire:loading.attr="disabled"
                    @disabled($orderAmtInt <= 0)
                    class="flex-1 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50
                           text-white text-sm font-semibold rounded-xl transition-colors"
                >
                    <span wire:loading.remove wire:target="applyCard">Apply Gift Card</span>
                    <span wire:loading wire:target="applyCard">Applying…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Step 3: Success --}}
    @if($step === 3 && $result)
    <div class="bg-white dark:bg-white/[0.03]
                border border-emerald-200 dark:border-emerald-500/20
                rounded-2xl p-6 shadow-sm dark:shadow-none">

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/15 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">Gift Card Applied</p>
                <p class="text-xs text-gray-400 dark:text-white/35">Transaction recorded successfully</p>
            </div>
        </div>

        <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] p-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-white/40">Amount applied from card</span>
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">₦{{ number_format($result['applied'], 0) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-white/40">Customer still owed</span>
                <span class="font-semibold text-gray-800 dark:text-white">₦{{ number_format($result['remaining'], 0) }}</span>
            </div>
            <div class="pt-2 border-t border-gray-200 dark:border-white/[0.06] flex justify-between">
                <span class="text-gray-500 dark:text-white/40">Remaining card balance</span>
                <span class="font-semibold text-gray-800 dark:text-white">₦{{ number_format($result['card_balance_after'], 0) }}</span>
            </div>
        </div>

        @if($result['card_balance_after'] <= 0)
        <p class="mt-3 text-xs text-center text-amber-600 dark:text-amber-400">
            This gift card has been fully redeemed.
        </p>
        @endif

        <button
            wire:click="resetPanel"
            class="mt-5 w-full px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600
                   text-white text-sm font-semibold rounded-xl transition-colors"
        >
            Redeem Another Card
        </button>
    </div>
    @endif

</div>
