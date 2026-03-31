@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'Return Policy — 1st Delightsome Fabrics')

@push('head')
    <meta name="description" content="Read the fabric return and exchange policy for 1st Delightsome Fabrics, Ikeja Lagos. Understand what can be returned, how to raise a claim, and how refunds are processed.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/return-policy') }}">
@endpush

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

<div class="bg-white dark:bg-ink min-h-screen border-t border-neutral-200 dark:border-neutral-800 pt-40">

    {{-- ── PAGE HERO ── --}}
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-16 pt-20 pb-10 border-b border-neutral-200 dark:border-neutral-800">
        <nav class="flex items-center gap-1.5 mb-5 text-xs text-neutral-400" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-brand transition-colors duration-150">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-neutral-600 dark:text-neutral-300">Return Policy</span>
        </nav>
        <div class="flex items-center gap-2.5 mb-3">
            <span class="block w-5 h-px bg-accent"></span>
            <span class="text-2xs font-semibold tracking-[0.15em] uppercase text-accent">Legal</span>
        </div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink dark:text-neutral-50 tracking-tight mb-2">
            Return Policy
        </h1>
        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Last updated: March 2026</p>
    </div>

    {{-- ── CONTENT ── --}}
    <div class="max-w-3xl mx-auto px-5 sm:px-8 lg:px-0 py-14 space-y-10">

        <div class="space-y-3">
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                At 1st Delightsome Fabrics, we take great care in selecting and dispatching every order. Because fabric is a physical material that changes once it is handled, cut, or washed, our return policy reflects the nature of the products we sell. Please read this policy carefully before placing your order.
            </p>
        </div>

        {{-- Section 1 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Cut Fabric</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Once fabric has been cut to your specified length, the sale is considered final. Cut fabric cannot be returned, exchanged, or refunded under any circumstances. This applies to all fabric types including Lace, Ankara, Aso-oke, George, chiffon, bridal fabric, and every other material in our range. We encourage customers to confirm the exact yardage they need with their tailor before placing an order.
            </p>
        </div>

        {{-- Section 2 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Uncut Fabric Returns</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                If you receive fabric that is entirely uncut and in its original undisturbed condition, you may request a return within 7 days of delivery. The fabric must be unwashed, unfolded from its original state, free from any marks, perfume, or odours, and returned in the same packaging it arrived in. Returns outside this window will not be accepted.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                The cost of return shipping is the responsibility of the customer unless the return is the result of an error on our part. We recommend using a tracked delivery service as we cannot accept liability for items lost in return transit.
            </p>
        </div>

        {{-- Section 3 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Faulty or Incorrect Items</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                If you receive fabric that is visibly faulty, defective in weave or print, or clearly different from what you ordered, you must notify us within 24 hours of delivery. Claims raised after this period may not be considered. To raise a claim, contact us at
                <a href="mailto:hello@delightsome.com" class="text-brand hover:text-brand-600 transition-colors duration-150">hello@delightsome.com</a>
                with your order number and clear photographs showing the issue.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                On receipt of your claim, we will review and respond within 2 working days. If the claim is accepted, we will arrange a replacement or issue a refund at our discretion. Minor colour variations that fall within normal fabric production tolerances do not qualify as faults.
            </p>
        </div>

        {{-- Section 4 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">How to Initiate a Return</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                To begin the return process, email us at
                <a href="mailto:hello@delightsome.com" class="text-brand hover:text-brand-600 transition-colors duration-150">hello@delightsome.com</a>
                with the subject line "Return Request" and include your full name, order number, the reason for the return, and photographs of the item where applicable. Do not return any item without first receiving written confirmation from us. Items returned without prior authorisation will not be processed.
            </p>
        </div>

        {{-- Section 5 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Refunds</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Approved refunds are processed via bank transfer to the account details you provide. Refunds are typically completed within 5 to 7 working days of approval. We do not issue refunds to cards that have expired or accounts that cannot be verified. Original shipping fees are non-refundable unless the return is due to our error.
            </p>
        </div>

        {{-- Section 6 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Gift Cards</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Digital gift cards purchased on our website are non-refundable and non-exchangeable once issued. They cannot be converted to cash.
            </p>
        </div>

        {{-- Section 7 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Questions</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                If you have any questions about this policy, please contact us at
                <a href="mailto:hello@delightsome.com" class="text-brand hover:text-brand-600 transition-colors duration-150">hello@delightsome.com</a>
                or visit our store at 30b Opebi Rd, Opebi, Ikeja, Lagos 100281.
                You can also view our <a href="{{ route('faq') }}" class="text-brand hover:text-brand-600 transition-colors duration-150 underline underline-offset-2">FAQs</a> for common questions about orders and delivery.
            </p>
        </div>

    </div>{{-- /content --}}

</div>

@endsection
