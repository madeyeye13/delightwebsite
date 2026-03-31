@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'Contact Us — 1st Delightsome Fabrics | Ikeja, Lagos')

@push('head')
    <meta name="description" content="Get in touch with 1st Delightsome Fabrics. Visit our store at 30b Opebi Rd, Ikeja, Lagos or send us a message and we'll respond within 24–48 hours.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/contact') }}">
    <meta property="og:title" content="Contact Us — 1st Delightsome Fabrics">
    <meta property="og:description" content="Reach out to 1st Delightsome Fabrics. We'd love to hear from you.">
    <meta property="og:url" content="{{ url('/contact') }}">
    <meta property="og:type" content="website">
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
            <span class="text-neutral-600 dark:text-neutral-300">Contact</span>
        </nav>
        <div class="flex items-center gap-2.5 mb-3">
            <span class="block w-5 h-px bg-accent"></span>
            <span class="text-2xs font-semibold tracking-[0.15em] uppercase text-accent">Let's Talk</span>
        </div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink dark:text-neutral-50 tracking-tight mb-2">
            Contact Us
        </h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-lg leading-relaxed">
            Have a question about fabric, an order, or anything else? Fill in the form and we'll get back to you as soon as possible.
        </p>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-16 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 xl:gap-16">

            {{-- ── LEFT: Contact Form ── --}}
            <div class="lg:col-span-3">
                <h2 class="font-display text-lg font-semibold text-ink dark:text-neutral-100 tracking-tight mb-6">
                    Send Us a Message
                </h2>
                <livewire:frontend.contact-form />
            </div>

            {{-- ── RIGHT: Store Info + Map ── --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Store Details --}}
                <div>
                    <h2 class="font-display text-lg font-semibold text-ink dark:text-neutral-100 tracking-tight mb-6">
                        Visit Our Store
                    </h2>

                    <ul class="space-y-5" role="list">
                        {{-- Address --}}
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 w-8 h-8 flex-shrink-0 bg-brand-50 dark:bg-brand-900 flex items-center justify-center rounded-sm">
                                <svg class="w-4 h-4 text-brand dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500 mb-0.5">Address</p>
                                <address class="not-italic text-sm text-ink dark:text-neutral-200 leading-relaxed">
                                    30b Opebi Rd, Opebi<br>
                                    Ikeja, Lagos 100281<br>
                                    Nigeria
                                </address>
                            </div>
                        </li>

                        {{-- Hours --}}
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 w-8 h-8 flex-shrink-0 bg-brand-50 dark:bg-brand-900 flex items-center justify-center rounded-sm">
                                <svg class="w-4 h-4 text-brand dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500 mb-0.5">Opening Hours</p>
                                <dl class="text-sm text-ink dark:text-neutral-200 space-y-0.5">
                                    <div class="flex gap-4">
                                        <dt class="text-neutral-500 dark:text-neutral-400 w-24">Mon – Sat</dt>
                                        <dd>8:00 am – 6:00 pm</dd>
                                    </div>
                                    <div class="flex gap-4">
                                        <dt class="text-neutral-500 dark:text-neutral-400 w-24">Sunday</dt>
                                        <dd class="text-neutral-400 dark:text-neutral-500">Closed</dd>
                                    </div>
                                </dl>
                            </div>
                        </li>

                        {{-- Email --}}
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 w-8 h-8 flex-shrink-0 bg-brand-50 dark:bg-brand-900 flex items-center justify-center rounded-sm">
                                <svg class="w-4 h-4 text-brand dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500 mb-0.5">Email</p>
                                <a href="mailto:hello@delightsome.com" class="text-sm text-brand dark:text-brand-400 hover:underline">
                                    hello@delightsome.com
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Google Map --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500 mb-3">Find Us on the Map</p>
                    <div class="relative w-full overflow-hidden rounded-sm border border-neutral-200 dark:border-neutral-700" style="padding-top: 66%;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.43538091825!2d3.355689973504708!3d6.592684122358585!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8d8581aa6e51%3A0x4eab6f2bfc5ef3f5!2s1stDelightsome%20Fabrics!5e0!3m2!1sen!2sng!4v1774897703440!5m2!1sen!2sng"
                            class="absolute inset-0 w-full h-full border-0"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="1st Delightsome Fabrics on Google Maps"
                        ></iframe>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
