{{--
╔══════════════════════════════════════════════════════════════╗
║  ABOUT PAGE                                                   ║
║  resources/views/frontend/about.blade.php                    ║
╚══════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.custom')

@section('title', 'About Us — 1st Delightsome Fabrics | Premium Textiles in Ikeja, Lagos')

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

    {{-- ══════════════════════════════════
         HERO
    ══════════════════════════════════ --}}
    <section class="relative w-full overflow-hidden" style="height:60vh; min-height:380px;">

        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ asset('images/hero1.jpg') }}');"></div>

        {{-- Black overlay --}}
        <div class="absolute inset-0 bg-black/60"></div>

        {{-- Content --}}
        <div class="relative z-10 flex h-full items-center">
            <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16 w-full pt-28">

                {{-- Breadcrumb --}}
                <nav class="mb-4 text-xs" aria-label="Breadcrumb">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="{{ url('/') }}" class="text-white/60 hover:text-white transition-colors">Home</a>
                        </li>
                        <li class="text-white/30">/</li>
                        <li class="text-white/80 font-medium">About Us</li>
                    </ol>
                </nav>

                {{-- Eyebrow --}}
                <div class="inline-flex items-center gap-2 mb-4 px-3 py-1.5 rounded-full
                            bg-white/10 backdrop-blur-sm border border-white/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                    <span class="font-sans text-xs font-semibold tracking-widest uppercase text-white/80">
                        Our Story
                    </span>
                </div>

                <h1 class="font-display font-bold text-white leading-tight tracking-tight
                           text-2xl sm:text-3xl lg:text-4xl mb-3">
                    Premium Fabrics,<br>
                    <span class="text-brand-300">Trusted in Lagos</span>
                </h1>

                <p class="font-sans text-sm text-white/70 leading-relaxed max-w-lg">
                    Quality lace, Ankara, Aso-oke and African textiles for designers, brands and everyday fashion — all in Ikeja.
                </p>

            </div>
        </div>

    </section>


    {{-- ══════════════════════════════════
         OUR STORY
    ══════════════════════════════════ --}}
    <section class="py-16 bg-white dark:bg-[#0f1117]">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16">

            <div class="max-w-3xl">
                <div class="flex items-center gap-3 mb-4">
                    <span class="h-px w-6 bg-brand-500 block"></span>
                    <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand-500 dark:text-brand-300">
                        Story
                    </span>
                </div>

                <h2 class="font-display font-bold text-ink dark:text-white leading-tight tracking-tight
                           text-2xl sm:text-3xl mb-5">
                    A fabric store built for the real needs of
                    <span class="text-brand-500 dark:text-brand-300">Lagos designers</span>
                </h2>

                {{-- Read more / less toggle --}}
                <div x-data="{ expanded: false }">

                    {{-- Collapsed --}}
                    <div x-show="! expanded" x-transition>
                        <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed">
                            At
                            <a href="{{ url('/') }}" class="font-semibold text-brand-500 dark:text-brand-300 hover:underline">
                                1st Delightsome Fabrics
                            </a>,
                            we specialise in premium African textiles — from French and Swiss lace to Ankara, Aso-oke,
                            and suiting materials. Based on the streets of Ikeja, we serve individual buyers, fashion designers,
                            and ready-to-wear brands who need consistent quality without the runaround.
                        </p>
                        <button @click="expanded = true"
                                class="mt-4 font-sans text-sm font-semibold text-brand-500 dark:text-brand-300 hover:underline transition-colors">
                            Read More
                        </button>
                    </div>

                    {{-- Expanded --}}
                    <div x-show="expanded" x-transition>
                        <div class="space-y-4">
                            <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed">
                                At
                                <a href="{{ url('/') }}" class="font-semibold text-brand-500 dark:text-brand-300 hover:underline">
                                    1st Delightsome Fabrics
                                </a>,
                                we believe that the right material is the foundation of every great outfit. Founded with a deep love for
                                African textile craftsmanship, we curate fabrics that blend contemporary fashion demands with the timeless
                                beauty of traditional weaves and lace. Whether you're sourcing for a bridal aso-ebi, a ready-to-wear
                                production run, or a one-of-a-kind design, you'll find exactly what you need in our
                                <a href="{{ route('shop.index') }}" class="font-semibold text-brand-500 dark:text-brand-300 hover:underline">
                                    full collection
                                </a>.
                            </p>

                            <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed">
                                Every bolt of fabric on our shelves has been hand-selected for weight, drape, colourfastness, and
                                finish. We work directly with trusted mills and importers to bring you materials that photograph
                                beautifully, wear comfortably in the Lagos climate, and hold up after repeated washing. No compromise
                                on quality — that's a standard we've kept from day one.
                            </p>

                            <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed">
                                Our commitment goes beyond selling fabric. We're invested in supporting local fashion — by keeping
                                deep stock, offering honest pricing, and employing staff who can guide you to the right material
                                for your specific design. When you choose 1st Delightsome, you're choosing a supplier that treats
                                your craft with the same seriousness you do.
                            </p>
                        </div>
                        <button @click="expanded = false"
                                class="mt-4 font-sans text-sm font-semibold text-brand-500 dark:text-brand-300 hover:underline transition-colors">
                            Read Less
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </section>


    {{-- ══════════════════════════════════
         3-COLUMN VALUES STRIP
    ══════════════════════════════════ --}}
    <section class="bg-neutral-50 dark:bg-[#161920] border-y border-neutral-100 dark:border-white/[0.06]">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">

                @php
                $values = [
                    [
                        'icon'  => '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Hand-Inspected Quality',
                        'body'  => 'Every fabric is checked for weight, finish, and colour consistency before it reaches our shelves.',
                    ],
                    [
                        'icon'  => '<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Transparent Pricing',
                        'body'  => 'Fixed prices across the board — the tag price is the checkout price, whether you buy 2 yards or 200.',
                    ],
                    [
                        'icon'  => '<path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'title' => 'Deep Local Stock',
                        'body'  => 'Curated for the Lagos market — so when aso-ebi season or a production deadline hits, we have what you need.',
                    ],
                ];
                @endphp

                @foreach($values as $v)
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-500/10 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-brand-500 dark:text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            {!! $v['icon'] !!}
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-semibold text-md text-ink dark:text-white mb-1">{{ $v['title'] }}</h3>
                        <p class="font-sans text-sm text-neutral-500 dark:text-white/50 leading-relaxed">{{ $v['body'] }}</p>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════
         QUALITY & SOURCING
    ══════════════════════════════════ --}}
    <section class="py-16 bg-white dark:bg-[#0f1117]">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16">

            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- Image side --}}
                <div class="relative overflow-hidden rounded-2xl">
                    <img src="{{ asset('images/asooke.jpg') }}"
                         alt="Premium African fabric at 1st Delightsome"
                         class="w-full h-72 sm:h-96 object-cover"
                         loading="lazy">
                    {{-- Accent badge --}}
                    <div class="absolute bottom-4 left-4 bg-black/70 backdrop-blur-sm text-white
                                px-4 py-2 rounded-xl flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-accent block"></span>
                        <span class="font-sans text-xs font-semibold tracking-wide">500+ Fabric Varieties</span>
                    </div>
                </div>

                {{-- Text side --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="h-px w-6 bg-brand-500 block"></span>
                        <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand-500 dark:text-brand-300">
                            Quality & Sourcing
                        </span>
                    </div>

                    <h2 class="font-display font-bold text-ink dark:text-white leading-tight tracking-tight
                               text-2xl sm:text-3xl mb-4">
                        Fabrics sourced with purpose, stocked for the craft
                    </h2>

                    <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed mb-4">
                        We work directly with reliable mills and importers to bring you materials that meet the practical demands
                        of fashion production in Nigeria. Every piece — from French lace to hand-woven Aso-oke — is evaluated
                        for drape, durability, and how well it photographs and wears in real life.
                    </p>

                    <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed mb-6">
                        We also believe in responsible sourcing: favouring suppliers with ethical labour practices, minimising
                        overstock waste through thoughtful buying, and packaging orders in a way that reduces unnecessary material.
                        Quality and conscience don't have to be in conflict.
                    </p>

                    <a href="{{ route('shop.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 font-sans text-sm font-semibold text-white
                              bg-brand-500 hover:bg-brand-600 transition-colors duration-200">
                        Browse Our Fabrics
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════
         SIGNATURE / FEATURED PRODUCTS
    ══════════════════════════════════ --}}
    <section class="bg-neutral-50 dark:bg-[#161920] border-t border-neutral-100 dark:border-white/[0.06] py-16">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16 mb-8">
            <div class="flex items-center gap-3 mb-3">
                <span class="h-px w-6 bg-brand-500 block"></span>
                <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand-500 dark:text-brand-300">
                    Signature Materials
                </span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <h2 class="font-display font-bold text-ink dark:text-white leading-tight tracking-tight text-2xl sm:text-3xl">
                    Fabrics our customers keep coming back for
                </h2>
                <a href="{{ route('shop.index') }}"
                   class="shrink-0 inline-flex items-center gap-2 font-sans text-sm font-semibold text-brand-500 dark:text-brand-300の hover:underline">
                    View all
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <livewire:frontend.featured-products />
    </section>


    {{-- ══════════════════════════════════
         OUR COMMITMENTS (ACCORDION)
    ══════════════════════════════════ --}}
    <section class="py-16 bg-white dark:bg-[#0f1117]">
        <div class="max-w-3xl mx-auto px-6 sm:px-10">

            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-3 mb-3">
                    <span class="h-px w-6 bg-brand-500 block"></span>
                    <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand-500 dark:text-brand-300">
                        Commitments
                    </span>
                    <span class="h-px w-6 bg-brand-500 block"></span>
                </div>
                <h2 class="font-display font-bold text-ink dark:text-white leading-tight tracking-tight text-2xl sm:text-3xl">
                    What we stand for
                </h2>
            </div>

            <div class="border-t border-neutral-200 dark:border-white/[0.08]">

                @php
                $commitments = [
                    [
                        'title' => 'Consistent Quality',
                        'icon'  => '<path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
                        'body'  => 'We inspect every bolt before it hits our shelves. If the weight, colour, or finish doesn\'t meet our standard, it doesn\'t come in — simple as that.',
                    ],
                    [
                        'title' => 'Fair & Fixed Pricing',
                        'icon'  => '<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'body'  => 'No hidden markups, no negotiation theatre. Our prices are fixed and transparent — the same whether you\'re buying 2 yards or a full roll.',
                    ],
                    [
                        'title' => 'Responsible Sourcing',
                        'icon'  => '<path d="M4.877 4.446c2.216 4.81 1.413 8.76 2.858 11.452 1.325 2.468 4.353 3.362 6.36 2.173 2.065-1.223 2.392-4.273.678-7.143-1.476-2.469-4.813-5.083-9.896-6.482z"/><path d="M11.577 7.687c.922-1.593 3.843-3.037 6.914-3.46-1.47 2.996-.417 6.981-2.528 9.931M5.355 4.741c4.267 2.951 8.135 9.181 8.135 17.406"/>',
                        'body'  => 'We source from suppliers who value ethical labour and fair trade. Mindful buying also means we avoid overstocking to reduce waste across our entire operation.',
                    ],
                    [
                        'title' => 'Community & Craft',
                        'icon'  => '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'body'  => 'We actively support Lagos fashion by keeping reliable stock for designers and brands, training staff who genuinely understand fabric, and showing up for the community we serve.',
                    ],
                ];
                @endphp

                @foreach($commitments as $c)
                <div x-data="{ open: false }"
                     class="border-b border-neutral-200 dark:border-white/[0.08]">
                    <button @click="open = ! open"
                            class="flex items-center justify-between w-full py-4 text-left gap-4
                                   text-ink dark:text-white hover:text-brand-500 dark:hover:text-brand-300 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 text-brand-500 dark:text-brand-300"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                {!! $c['icon'] !!}
                            </svg>
                            <span class="font-display font-semibold text-md">{{ $c['title'] }}</span>
                        </div>
                        <div class="w-5 h-5 shrink-0 transition-transform duration-200"
                             :class="open ? 'rotate-45' : ''">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="pb-4 pl-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-white/50 leading-relaxed">
                            {{ $c['body'] }}
                        </p>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════
         BASED IN IKEJA
    ══════════════════════════════════ --}}
    <section class="bg-neutral-50 dark:bg-[#161920] py-16">
        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16">

            <div class="max-w-2xl mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <span class="h-px w-6 bg-brand-500 block"></span>
                    <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand-500 dark:text-brand-300">
                        Location
                    </span>
                </div>
                <h2 class="font-display font-bold text-ink dark:text-white leading-tight tracking-tight
                           text-2xl sm:text-3xl mb-3">
                    Based in Ikeja, Lagos
                </h2>
                <p class="font-sans text-base text-neutral-600 dark:text-white/60 leading-relaxed">
                    Our store is located at 30b Opebi Rd, Opebi, Ikeja Lagos. Every fabric we carry is hand-picked for the
                    demands of Nigerian fashion — from aso-ebi events to everyday ready-to-wear production. Walk in and
                    walk out with exactly what you need, or order online and we'll deliver nationwide.
                </p>
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 font-sans text-sm font-semibold text-white
                              bg-brand-500 hover:bg-brand-600 transition-colors duration-200">
                        Get in Touch
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('shop.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 font-sans text-sm font-semibold
                              text-ink dark:text-white border border-neutral-300 dark:border-white/20
                              hover:border-ink dark:hover:border-white/50 transition-colors duration-200">
                        Shop Now
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Full-width image --}}
            <div class="w-full overflow-hidden rounded-2xl">
                <img src="{{ asset('images/hero2.jpg') }}"
                     alt="1st Delightsome Fabrics store in Ikeja, Lagos"
                     class="w-full object-cover"
                     style="height: 55vh; min-height: 300px;"
                     loading="lazy">
            </div>

        </div>
    </section>

@endsection
