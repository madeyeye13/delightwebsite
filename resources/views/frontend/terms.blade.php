@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'Terms & Conditions — 1st Delightsome Fabrics')

@push('head')
    <meta name="description" content="Terms and conditions for purchasing fabrics and using the 1st Delightsome Fabrics website. Based in Ikeja, Lagos, Nigeria.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/terms') }}">
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
            <span class="text-neutral-600 dark:text-neutral-300">Terms &amp; Conditions</span>
        </nav>
        <div class="flex items-center gap-2.5 mb-3">
            <span class="block w-5 h-px bg-accent"></span>
            <span class="text-2xs font-semibold tracking-[0.15em] uppercase text-accent">Legal</span>
        </div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink dark:text-neutral-50 tracking-tight mb-2">
            Terms &amp; Conditions
        </h1>
        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Last updated: March 2026</p>
    </div>

    {{-- ── CONTENT ── --}}
    <div class="max-w-3xl mx-auto px-5 sm:px-8 lg:px-0 py-14 space-y-10">

        <div class="space-y-3">
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                These terms and conditions govern your use of the 1st Delightsome Fabrics website and the purchase of products through it. By browsing this website or placing an order, you agree to be bound by these terms. Please read them carefully. If you do not accept these terms, you should not use this website or purchase from us.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                This website is operated by 1st Delightsome Fabrics, located at 30b Opebi Rd, Opebi, Ikeja, Lagos 100281, Nigeria.
            </p>
        </div>

        {{-- Section 1 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Use of the Website</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                You may use this website for lawful purposes only. You must not use it in any way that causes, or could cause, damage to the website or impair the availability or accessibility of the website. You must not use this site to transmit or procure the sending of any unsolicited or unauthorised advertising, promotional material, or spam.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We reserve the right to restrict or terminate access to any part of the website at our discretion and without notice.
            </p>
        </div>

        {{-- Section 2 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Products and Pricing</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                All fabrics and products listed on this website are subject to availability. We make every reasonable effort to display product information accurately, including fabric descriptions, dimensions, and photography. However, we do not guarantee that product descriptions, colours, or images are entirely free from error. Slight colour variations between screen display and the physical fabric are inherent to how digital images work and do not constitute fault or misrepresentation.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                All prices shown on the website are quoted in Nigerian Naira (NGN) unless otherwise specified. Prices displayed in other currencies are indicative only and are subject to live exchange rate conversions. We reserve the right to change prices without notice. The price applicable to your order is the price shown at the time you complete payment.
            </p>
        </div>

        {{-- Section 3 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Orders and Payment</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Placing an order does not constitute a binding contract until payment is confirmed and we send you an order confirmation. We reserve the right to cancel any order at our discretion, in which case a full refund will be issued.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                All payments must be made in full at the time of ordering. We do not hold stock against unpaid orders. Once an order is confirmed and payment received, the fabric is prepared for dispatch. You are responsible for ensuring that the delivery address and order details you provide are accurate.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Once fabric has been cut to your specified length, the order cannot be amended or cancelled. If production has not yet begun, please contact us immediately and we will do our best to accommodate changes, though this cannot be guaranteed.
            </p>
        </div>

        {{-- Section 4 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Shipping and Delivery</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Delivery timelines provided on the website are estimates only and not guaranteed. We are not liable for delays caused by third-party couriers, national holidays, adverse weather, or circumstances beyond our control. Risk of loss and title for items pass to you upon dispatch.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                For international shipments, any customs duties, taxes, or import charges levied by the destination country are entirely the responsibility of the recipient. We cannot predict or advise on international customs fees.
            </p>
        </div>

        {{-- Section 5 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Returns and Refunds</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Returns and refunds are governed by our <a href="{{ route('return-policy') }}" class="text-brand hover:text-brand-600 underline underline-offset-2 transition-colors duration-150">Return Policy</a>, which forms part of these terms and conditions. By placing an order, you acknowledge that you have read and understood the return policy as it applies specifically to fabric goods.
            </p>
        </div>

        {{-- Section 6 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Intellectual Property</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                All content on this website, including text, photography, graphics, logos, and product descriptions, is the property of 1st Delightsome Fabrics or its content suppliers and is protected by Nigerian and international intellectual property laws. You may not reproduce, redistribute, sell, or commercially exploit any content from this website without our express written consent.
            </p>
        </div>

        {{-- Section 7 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Limitation of Liability</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                To the fullest extent permitted by law, 1st Delightsome Fabrics shall not be liable for any indirect, incidental, or consequential loss arising from your use of this website or from any products purchased through it. Our total liability in connection with any order shall not exceed the amount paid for that order. Nothing in these terms excludes or limits liability for fraud, death, or personal injury caused by our negligence.
            </p>
        </div>

        {{-- Section 8 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Governing Law</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                These terms and conditions shall be governed by and interpreted in accordance with the laws of the Federal Republic of Nigeria. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts of Lagos State.
            </p>
        </div>

        {{-- Section 9 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Changes to These Terms</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We reserve the right to update these terms at any time. Changes will be posted on this page with an updated date. Your continued use of the website after any changes constitutes acceptance of the new terms.
            </p>
        </div>

        {{-- Section 10 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Contact</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                For any questions about these terms, please contact us at
                <a href="mailto:hello@delightsome.com" class="text-brand hover:text-brand-600 transition-colors duration-150">hello@delightsome.com</a>
                or visit us at 30b Opebi Rd, Opebi, Ikeja, Lagos 100281.
            </p>
        </div>

    </div>{{-- /content --}}

</div>

@endsection
