@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('content')

<section class="pt-24 pb-4 bg-white dark:bg-[#0a0c10]">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-16">
        <div>
            <p class="font-sans text-[10px] tracking-[0.2em] uppercase text-gray-400 dark:text-white/30 mb-3">
                All Products
            </p>
            <h1 class="font-display text-3xl md:text-4xl font-semibold text-gray-900 dark:text-white leading-tight tracking-tight">
                Shop
                <span class="block font-sans text-sm font-normal text-gray-400 dark:text-white/40 mt-1 tracking-normal">
                    Quality Materials &amp; Fabrics
                </span>
            </h1>
        </div>
    </div>
</section>

<div class="bg-white dark:bg-[#0a0c10] min-h-screen">
    <livewire:frontend.shop-index />
</div>

@endsection
