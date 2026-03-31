@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'Unsubscribed — 1st Delightsome Fabrics')

@push('head')
    <meta name="robots" content="noindex, nofollow">
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
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-16 pt-20 pb-32 flex flex-col items-center text-center">

        <div class="w-14 h-14 bg-neutral-100 dark:bg-neutral-800 rounded-full flex items-center justify-center mb-6">
            <svg class="w-6 h-6 text-neutral-500 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink dark:text-neutral-50 tracking-tight mb-3">
            You've been unsubscribed
        </h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-sm leading-relaxed mb-8">
            You've successfully unsubscribed from the 1st Delightsome newsletter. We're sorry to see you go!
        </p>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-sm leading-relaxed mb-8">
            Changed your mind? You can always re-subscribe from our website.
        </p>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <a
                href="{{ route('shop.index') }}"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand text-white text-sm font-semibold tracking-wide hover:bg-brand-600 transition-colors duration-150"
            >
                Browse Our Fabrics
            </a>
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center gap-2 px-6 py-2.5 border border-neutral-300 dark:border-neutral-700 text-sm font-medium text-ink dark:text-neutral-200 hover:border-brand dark:hover:border-brand-400 hover:text-brand dark:hover:text-brand-400 transition-colors duration-150"
            >
                Go to Homepage
            </a>
        </div>

    </div>
</div>

@endsection
