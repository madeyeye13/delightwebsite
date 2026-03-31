@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'Privacy Policy — 1st Delightsome Fabrics')

@push('head')
    <meta name="description" content="Privacy policy for 1st Delightsome Fabrics. Learn how we collect, use, and protect your personal information when you shop for fabric with us online or in-store.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/privacy') }}">
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
            <span class="text-neutral-600 dark:text-neutral-300">Privacy Policy</span>
        </nav>
        <div class="flex items-center gap-2.5 mb-3">
            <span class="block w-5 h-px bg-accent"></span>
            <span class="text-2xs font-semibold tracking-[0.15em] uppercase text-accent">Legal</span>
        </div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink dark:text-neutral-50 tracking-tight mb-2">
            Privacy Policy
        </h1>
        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Last updated: March 2026</p>
    </div>

    {{-- ── CONTENT ── --}}
    <div class="max-w-3xl mx-auto px-5 sm:px-8 lg:px-0 py-14 space-y-10">

        <div class="space-y-3">
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                1st Delightsome Fabrics ("we", "us", "our") is committed to protecting your personal information. This privacy policy explains what data we collect, how we use it, how long we retain it, and what rights you have regarding your information. By using this website, creating an account, or placing an order, you agree to the practices described here.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We are based at 30b Opebi Rd, Opebi, Ikeja, Lagos 100281, Nigeria.
            </p>
        </div>

        {{-- Section 1 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">What Information We Collect</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                When you create an account or place an order, we collect your full name, email address, phone number, and delivery address. For payment, we collect only what is necessary to process your transaction. Full card details are handled directly by our payment processors (Paystack and Flutterwave) and are not stored on our servers.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                When you browse our website, we may automatically collect technical data including your IP address, browser type, operating system, pages visited, and time spent on the site. This is collected via cookies and similar technologies to help us understand how our website is used.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                If you subscribe to our newsletter or contact us directly, we collect your email address and any other details you choose to share in your message.
            </p>
        </div>

        {{-- Section 2 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">How We Use Your Information</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We use your personal information primarily to process and fulfil your orders, communicate with you about your purchase, and send your order confirmation and shipping notifications. We may also use your email address to notify you about updates to your account or changes to our policies.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Where you have opted in, we may send you newsletters, new arrival notifications, and promotional updates. You can unsubscribe from these at any time by clicking the unsubscribe link at the bottom of any marketing email or by contacting us directly.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We use browsing data in aggregate form to improve our website performance, understand which pages are most useful, and identify areas for improvement. This data does not identify you personally.
            </p>
        </div>

        {{-- Section 3 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Sharing Your Information</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We do not sell, rent, or trade your personal information to any third party. We share your data only where it is necessary to fulfil your order, specifically with our payment processors (Paystack and Flutterwave) to handle transactions, and with our logistics and courier partners to deliver your order.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We may also disclose your information if required to do so by law or in response to a valid legal request from Nigerian authorities.
            </p>
        </div>

        {{-- Section 4 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Cookies</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We use cookies to keep your shopping session active, remember your currency preference, and help us understand site usage through basic analytics. Cookies do not contain personally identifiable information on their own. You can disable cookies in your browser settings; however, doing so may affect some features of this website, including your ability to complete a purchase.
            </p>
        </div>

        {{-- Section 5 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Data Retention</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We retain your personal data only for as long as necessary to fulfil the purpose for which it was collected, or as required by law. Order records are typically retained for up to 7 years for accounting and legal compliance purposes. You may request deletion of your account and associated data at any time by contacting us.
            </p>
        </div>

        {{-- Section 6 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Your Rights</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                You have the right to access the personal information we hold about you, to request corrections to any inaccurate data, and to request deletion of your data where it is no longer needed for the purpose it was collected. You also have the right to withdraw consent to marketing communications at any time.
            </p>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                To exercise any of these rights, please email us at
                <a href="mailto:hello@delightsome.com" class="text-brand hover:text-brand-600 transition-colors duration-150">hello@delightsome.com</a>.
                We aim to respond to all requests within 5 working days.
            </p>
        </div>

        {{-- Section 7 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Security</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We take reasonable technical and organisational measures to protect your personal information from unauthorised access, loss, or misuse. All payment processing is handled by certified third-party providers and transmitted over encrypted connections. While we do our best to protect your data, no method of transmission over the internet is 100% secure and we cannot guarantee absolute security.
            </p>
        </div>

        {{-- Section 8 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Changes to This Policy</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                We may update this privacy policy from time to time. When we do, we will revise the date at the top of this page. Continued use of this website following any update constitutes your acceptance of the revised policy. We encourage you to review this page periodically.
            </p>
        </div>

        {{-- Section 9 --}}
        <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
            <h2 class="font-display text-md font-semibold text-ink dark:text-neutral-100 tracking-snug">Contact Us</h2>
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                If you have any questions or concerns about this privacy policy or about how your data is handled, please write to us at
                <a href="mailto:hello@delightsome.com" class="text-brand hover:text-brand-600 transition-colors duration-150">hello@delightsome.com</a>
                or visit us in person at 30b Opebi Rd, Opebi, Ikeja, Lagos 100281, Nigeria.
            </p>
        </div>

    </div>{{-- /content --}}

</div>

@endsection
