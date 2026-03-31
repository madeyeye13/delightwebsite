@extends('layouts.custom', ['alwaysShowHeaderBg' => true])

@section('content')
{{-- ── Alpine store init: theme (dark mode) ───────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('theme') === 'dark'
                || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            }
        });
    });
    // Apply dark class immediately to avoid flash of unstyled content
    (function() {
        var saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<div class="bg-neutral-50 dark:bg-neutral-900 min-h-screen pt-40 pb-16">
    <div class="max-w-xl mx-auto px-4 py-10 text-center">

        <div class="w-16 h-16 bg-brand-50 border border-brand-200 rounded-full flex items-center justify-center mx-auto mb-5"
             style="animation: successPulse 0.4s ease forwards">
            <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="font-display font-bold text-neutral-900 dark:text-neutral-50 mb-2" style="font-size:26px">
            Payment Confirmed!
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400 mb-4">
            Your order has been successfully placed and paid.
        </p>

        <div class="inline-flex items-center gap-3 px-5 py-3 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 mb-6">
            <span class="text-xs text-neutral-500 dark:text-neutral-400">Order</span>
            <span class="font-mono font-bold text-neutral-900 dark:text-neutral-50">{{ $orderNumber }}</span>
        </div>

        @if(!empty($giftCodes))
        <div class="mt-2 mb-6 text-left w-full">
            <p class="text-xs font-semibold text-neutral-700 dark:text-neutral-200 mb-3 text-center">
                Your Gift Card Code{{ count($giftCodes) > 1 ? 's' : '' }}
            </p>
            <div class="space-y-2">
                @foreach($giftCodes as $gc)
                <div class="flex items-center justify-between px-4 py-3 bg-brand-50 dark:bg-brand-950/30 border border-brand-200 dark:border-brand-800">
                    <div>
                        <p class="font-mono font-bold text-neutral-900 dark:text-neutral-50 text-sm tracking-wider">{{ $gc['code'] }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Value: ₦{{ number_format($gc['initial_balance']) }}</p>
                    </div>
                    <svg class="w-5 h-5 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                @endforeach
            </div>
            <p class="text-[11px] text-neutral-400 dark:text-neutral-500 mt-3 text-center">
                These codes have also been sent to your email. Use them at checkout to redeem their value.
            </p>
        </div>
        @endif

        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-7">
            @if(!empty($giftCodes))
                Your gift card codes are shown above and have been sent to your email.
            @else
                A confirmation email with your order details has been sent. We'll process and dispatch your order within 1–2 business days.
            @endif
        </p>

        <a href="{{ route('shop.index') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand text-white font-semibold hover:bg-brand-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Continue Shopping
        </a>

    </div>
</div>

<style>
@keyframes successPulse {
    0%   { transform: scale(0.8); opacity: 0; }
    70%  { transform: scale(1.05); }
    100% { transform: scale(1);  opacity: 1; }
}
</style>
@endsection
