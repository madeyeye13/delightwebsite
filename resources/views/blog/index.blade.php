@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'Blog — 1st Delightsome Fabrics, Ikeja Lagos')

@push('head')
    <meta name="description" content="Fabric buying guides, aso-ebi coordination tips, Lagos fashion industry insights, and store updates from 1st Delightsome Fabrics in Ikeja, Lagos.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/blog') }}">
    <meta property="og:title" content="Blog — 1st Delightsome Fabrics">
    <meta property="og:description" content="Fabric guides, aso-ebi tips, and Lagos fashion insights.">
    <meta property="og:url" content="{{ url('/blog') }}">
    <meta property="og:type" content="website">

    <style>
        @@keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .blog-card { animation: fadeUp 0.45s ease-out both; }
        .blog-card:nth-child(1) { animation-delay: 0s; }
        .blog-card:nth-child(2) { animation-delay: 0.07s; }
        .blog-card:nth-child(3) { animation-delay: 0.14s; }
        .blog-card:nth-child(4) { animation-delay: 0.21s; }
        .blog-card:nth-child(5) { animation-delay: 0.28s; }
        .blog-card:nth-child(6) { animation-delay: 0.35s; }
        .blog-card:nth-child(7) { animation-delay: 0.42s; }
    </style>
@endpush

@section('content')

    <div class="bg-neutral-50 dark:bg-brand-900 min-h-screen border-t border-neutral-200 dark:border-brand-700">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 lg:px-16 pt-24">

            {{-- Page header --}}
            <header class="pt-14 pb-10 border-b border-neutral-200 dark:border-brand-700 mb-12">
                <div class="flex items-center gap-2.5 mb-3">
                    <span class="block w-6 h-px bg-accent"></span>
                    <span class="text-2xs font-semibold tracking-[0.15em] uppercase text-accent">Insights & Guides</span>
                </div>
                <h1 class="font-display text-[clamp(22px,3vw,32px)] font-extrabold text-ink dark:text-neutral-50 tracking-tight mb-2">All Blog Posts</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-[460px] leading-relaxed">Fabric guides, aso-ebi coordination tips, and Lagos fashion industry insights from the team at 1st Delightsome Fabrics.</p>
            </header>

            {{-- Livewire blog index (filters + grid + pagination) --}}
            <livewire:frontend.blog-index />

        </div>{{-- /max-w container --}}
    </div>

@endsection
