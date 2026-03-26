{{--
╔══════════════════════════════════════════════════════════════╗
║  WELCOME PAGE                                                 ║
║  resources/views/welcome.blade.php                           ║
║                                                               ║
║  Sections:                                                    ║
║  • Hero slider (3 slides, 6s auto-advance, Ken Burns effect)  ║
╚══════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.custom')
@section('content')


    {{-- Tailwind + App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    

    <style>

        /* ── HERO: Ken Burns zoom on active slide ────────── */
        @keyframes kenBurns {
            0%   { transform: scale(1)    translate(0, 0); }
            50%  { transform: scale(1.06) translate(-1%, -0.5%); }
            100% { transform: scale(1.08) translate(1%, 0.5%); }
        }
        .slide-active .hero-bg {
            animation: kenBurns 8s ease-in-out forwards;
        }

        /* ── HERO: slide content entrance ───────────────── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-active .hero-content > * {
            animation: slideUp 0.7s ease-out forwards;
            opacity: 0;
        }
        .slide-active .hero-content > *:nth-child(1) { animation-delay: 0.2s; }
        .slide-active .hero-content > *:nth-child(2) { animation-delay: 0.4s; }
        .slide-active .hero-content > *:nth-child(3) { animation-delay: 0.6s; }

        /* ── PROGRESS BAR animation ─────────────────────── */
        @keyframes progressFill {
            from { width: 0%; }
            to   { width: 100%; }
        }
        .progress-active {
            animation: progressFill 6s linear forwards;
        }

        /* ── GRAIN OVERLAY for texture ──────────────────── */
        .grain::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 2;
        }

        /* ── SCROLL indicator bounce ────────────────────── */
        @keyframes scrollBounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(8px); }
        }
        .scroll-bounce { animation: scrollBounce 1.8s ease-in-out infinite; }
    </style>

    {{-- Alpine stores (same as admin layout) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: localStorage.getItem('theme') === 'dark'
                    || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                toggle() { this.dark = !this.dark; }
            });
        });
        (function() {
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();

        // ──────────────────────────────────────────────────
        // Product QuickView Modal - Alpine Data
        // ──────────────────────────────────────────────────
        function productQuickView() {
            return {
                isOpen: false,
                qty: 1,
                
                // Mock product data structure (replace with real data when backend ready)
                product: {
                    name: 'French Lace Fabric',
                    slug: 'french-lace-fabric',
                    category: 'Fabrics',
                    price: 28500,
                    old_price: 34000,
                    image: 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=500&h=600&fit=crop',
                    unit: 'Multiples of 5 yards',
                    stockQuantity: 8,
                    activeVariant: 0,
                    primaryImage: null,
                    
                    // Mock variants
                    variants: [
                        { color: 'Ivory White', hex: '#F5F0E8' },
                        { color: 'Gold', hex: '#D4AF37' },
                        { color: 'Champagne', hex: '#F7E7CE' },
                    ],
                    
                    // Mock images array
                    images: [
                        'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=500&h=600&fit=crop',
                        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500&h=600&fit=crop',
                        'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=500&h=600&fit=crop',
                    ],
                },
                
                // Helper: Determine text color for checkmark on colored background
                hexContrast(hex) {
                    if (!hex) return 'dark';
                    const r = parseInt(hex.slice(1, 3), 16);
                    const g = parseInt(hex.slice(3, 5), 16);
                    const b = parseInt(hex.slice(5, 7), 16);
                    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                    return luminance > 0.5 ? 'dark' : 'light';
                },
                
                // Mock function - replace with real cart logic
                addToCart() {
                    console.log('Added to cart:', {
                        product: this.product.name,
                        quantity: this.qty,
                        variant: this.product.variants[this.product.activeVariant],
                    });
                    // TODO: Implement actual cart functionality
                    alert(`Added ${this.qty}x ${this.product.name} to cart!`);
                },
                
                // Open quickview with product data
                openQuickView(productData) {
                    this.product = { ...this.product, ...productData };
                    this.qty = 1;
                    this.product.activeVariant = 0;
                    this.isOpen = true;
                },
            };
        }
    </script>




    {{-- ════════════════════════════════════════════════════════════
         HERO SLIDER
         • 3 slides, auto-advances every 6 seconds
         • Full-viewport height (100dvh)
         • Each slide: background image placeholder + content
         • Ken Burns zoom effect on active slide bg
         • Cross-fade transition between slides
         • Progress dots + bar indicators
         • Prev/Next arrows
    ═══════════════════════════════════════════════════════════════ --}}
    <section
        x-data="{
            current: 0,
            total: 3,
            timer: null,
            paused: false,

            /* Auto-advance every 6 seconds */
            init() {
                this.startTimer();
            },
            startTimer() {
                this.timer = setInterval(() => {
                    if (!this.paused) this.next();
                }, 6000);
            },
            resetTimer() {
                clearInterval(this.timer);
                this.startTimer();
            },
            next() {
                this.current = (this.current + 1) % this.total;
                this.resetTimer();
            },
            prev() {
                this.current = (this.current - 1 + this.total) % this.total;
                this.resetTimer();
            },
            goTo(i) {
                this.current = i;
                this.resetTimer();
            }
        }"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        class="relative w-full overflow-hidden grain"
        style="height: 100dvh; min-height: 600px;"
    >

        {{-- ── SLIDE DEFINITIONS ─────────────────────────────── --}}
        @php
        $slides = [
            [
                'tag'     => 'h1',
                'heading' => 'Ready-to-Wear Fabric Materials You\'ll Love',
                'body'    => 'Quality lace and African fabrics available in Ikeja for designers and ready-to-wear brands. Find materials that work well for everyday fashion and special outfits.',
                'cta'     => 'Shop Now',
                'cta_url' => '/shop',
                'align'   => 'left',
                'image'   => asset('images/hero1.jpg'),
                'gradient' => 'linear-gradient(135deg, rgba(26, 10, 0, 0.4) 0%, rgba(61, 26, 0, 0.4) 40%, rgba(107, 47, 0, 0.4) 70%, rgba(45, 26, 0, 0.4) 100%)',
                'overlay' => 'from-black/70 via-black/40 to-transparent',
                'accent'  => '#d97706', /* amber */
            ],
            [
                'tag'     => 'h2',
                'heading' => 'Materials for Modern Men\'s Styles',
                'body'    => 'From senator wear to agbada and casual outfits, explore fabrics that work perfectly for men\'s ready-to-wear collections.',
                'cta'     => 'Men\'s Collection',
                'cta_url' => '/collection/mens',
                'align'   => 'center',
                'image'   => asset('images/hero2.jpg'),
                'gradient' => 'linear-gradient(135deg, rgba(10, 10, 26, 0.4) 0%, rgba(26, 26, 61, 0.4) 40%, rgba(47, 47, 107, 0.4) 70%, rgba(26, 26, 61, 0.4) 100%)',
                'overlay' => 'from-black/75 via-black/40 to-black/20',
                'accent'  => '#6366f1', /* indigo */
            ],
            [
                'tag'     => 'h2',
                'heading' => 'Fabrics for Elegant Women\'s Fashion',
                'body'    => 'Discover lace and other African materials suitable for dresses, aso-ebi styles, and ready-to-wear women\'s outfits.',
                'cta'     => 'Women\'s Collection',
                'cta_url' => '/collection/womens',
                'align'   => 'right',
                'image'   => asset('images/hero3.jpg'),
                'gradient' => 'linear-gradient(135deg, rgba(26, 0, 26, 0.4) 0%, rgba(61, 0, 61, 0.4) 40%, rgba(107, 0, 80, 0.4) 70%, rgba(45, 0, 26, 0.4) 100%)',
                'overlay' => 'from-black/70 via-black/35 to-transparent',
                'accent'  => '#ec4899', /* pink */
            ],
        ];
        @endphp

        {{-- ── SLIDES LOOP ───────────────────────────────────── --}}
        @foreach($slides as $i => $slide)
        <div
            x-show="current === {{ $i }}"
            x-transition:enter="transition-opacity ease-in-out duration-1000"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in-out duration-1000"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="current === {{ $i }} ? 'slide-active' : ''"
            class="absolute inset-0 z-0"
            style="display:none"
        >
            {{-- Background image / gradient --}}
            <div
                class="hero-bg absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ $slide['image'] }}'), {{ $slide['gradient'] }}; background-size: cover; background-position: center;"
            ></div>

            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r {{ $slide['overlay'] }} z-10"></div>

            {{-- Decorative bottom fade --}}
            <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-black/50 to-transparent z-10"></div>

            {{-- ── SLIDE CONTENT ─────────────────────────────── --}}
            <div class="relative z-20 flex items-center h-full">
                <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16 w-full">
                    <div
                        class="hero-content max-w-2xl
                               {{ $slide['align'] === 'center' ? 'mx-auto text-center' : '' }}
                               {{ $slide['align'] === 'right'  ? 'ml-auto text-right'  : '' }}"
                    >
                        {{-- Eyebrow label --}}
                        <div class="inline-flex items-center gap-2 mb-5 px-3 py-1.5 rounded-full
                                    bg-white/10 backdrop-blur-sm border border-white/20">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse"
                                  style="background-color: {{ $slide['accent'] }}"></span>
                            <span class="font-sans text-xs font-semibold tracking-widest uppercase text-white/80">
                                {{ $i === 0 ? 'Premium Collection' : ($i === 1 ? 'Men\'s Fashion' : 'Women\'s Fashion') }}
                            </span>
                        </div>

                        {{-- Heading --}}
                        <{{ $slide['tag'] }} class="font-display font-bold text-white leading-tight tracking-tight
                                           text-3xl sm:text-4xl lg:text-5xl xl:text-[56px] mb-5">
                            {!! $slide['heading'] !!}
                        </{{ $slide['tag'] }}>

                        {{-- Body --}}
                        <p class="font-sans text-base sm:text-lg text-white/75 leading-relaxed mb-8 max-w-xl
                                  {{ $slide['align'] === 'center' ? 'mx-auto' : '' }}
                                  {{ $slide['align'] === 'right'  ? 'ml-auto'  : '' }}">
                            {{ $slide['body'] }}
                        </p>

                        {{-- CTA Button --}}
                        <div class="{{ $slide['align'] === 'center' ? 'flex justify-center' : ($slide['align'] === 'right' ? 'flex justify-end' : '') }}">
                            <a href="{{ $slide['cta_url'] }}"
                               class="group inline-flex items-center gap-3 px-5 py-2.5 rounded-none font-sans text-sm font-semibold
                                      text-white transition-all duration-300
                                      hover:gap-4"
                               style="background-color: {{ $slide['accent'] }};"
                            >
                                {{ $slide['cta'] }}
                                {{-- Arrow icon --}}
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>{{-- /slide --}}
        @endforeach

        {{-- ── PREV / NEXT ARROWS ─────────────────────────────── --}}
        <button
            @click="prev()"
            class="absolute left-4 lg:left-8 top-1/2 -translate-y-1/2 z-30
                   w-11 h-11 rounded-full flex items-center justify-center
                   bg-white/10 backdrop-blur-sm border border-white/20 text-white
                   hover:bg-white/25 transition-all duration-200 hover:scale-105"
            aria-label="Previous slide"
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button
            @click="next()"
            class="absolute right-4 lg:right-8 top-1/2 -translate-y-1/2 z-30
                   w-11 h-11 rounded-full flex items-center justify-center
                   bg-white/10 backdrop-blur-sm border border-white/20 text-white
                   hover:bg-white/25 transition-all duration-200 hover:scale-105"
            aria-label="Next slide"
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        {{-- ── SLIDE INDICATORS (bottom centre) ─────────────── --}}
        <div class="absolute bottom-8 left-0 right-0 z-30 flex flex-col items-center gap-3">

            {{-- Progress dots --}}
            <div class="flex items-center gap-2">
                @foreach($slides as $i => $slide)
                <button
                    @click="goTo({{ $i }})"
                    :class="current === {{ $i }}
                        ? 'w-8 bg-white'
                        : 'w-2 bg-white/40 hover:bg-white/70'"
                    class="h-2 rounded-full transition-all duration-400 ease-out"
                    aria-label="Go to slide {{ $i + 1 }}"
                ></button>
                @endforeach
            </div>

            {{-- Progress bar under active dot --}}
            <div class="w-24 h-[2px] bg-white/20 rounded-full overflow-hidden">
                <div
                    :key="current"
                    class="h-full bg-white rounded-full progress-active"
                    x-effect="
                        /* Re-trigger animation on slide change */
                        $el.style.animation = 'none';
                        $el.offsetHeight; /* reflow */
                        $el.style.animation = '';
                    "
                ></div>
            </div>

            {{-- Slide counter --}}
            <p class="font-sans text-xs text-white/50 font-medium tracking-widest">
                <span x-text="String(current + 1).padStart(2, '0')"></span>
                <span class="mx-1.5 opacity-40">/</span>
                <span>0{{ count($slides) }}</span>
            </p>

        </div>

        {{-- ── SCROLL DOWN INDICATOR ──────────────────────────── --}}
        <div class="absolute bottom-8 right-8 z-30 hidden lg:flex flex-col items-center gap-2">
            <span class="font-sans text-[10px] tracking-widest uppercase text-white/40 [writing-mode:vertical-rl] rotate-180">
                Scroll
            </span>
            <div class="scroll-bounce">
                <svg class="w-4 h-4 text-white/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 5v14M5 12l7 7 7-7"/>
                </svg>
            </div>
        </div>

    </section>
    
   
@include('partials.frontend.marquee-collection')
<livewire:frontend.featured-products />



    {{-- ════════════════════════════════════════════════════════════
         WHY CHOOSE US
    ═══════════════════════════════════════════════════════════════ --}}

    <style>
        @keyframes wcuUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .wcu-in { animation: wcuUp 0.6s ease-out both; }

        .wcu-feat-line {
            width: 0;
            transition: width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .wcu-feat:hover .wcu-feat-line { width: 100%; }

        .wcu-arr { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .wcu-btn:hover .wcu-arr { transform: translateX(5px); }

        .wcu-section { background-color: #FCFCF9; }
        .dark .wcu-section { background-color: #071E1E; }
    </style>

    <section class="wcu-section">

        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-16 py-24 lg:py-32">
            <div class="grid lg:grid-cols-[1fr_340px] xl:grid-cols-[1fr_380px] gap-12 xl:gap-20 items-stretch">

                {{-- ── LEFT ──────────────────────────────────────── --}}
                <div class="flex flex-col justify-between gap-14">

                    {{-- Header --}}
                    <div>
                        <div class="wcu-in flex items-center gap-3 mb-7" style="animation-delay:0s">
                            <span class="h-px w-8 bg-brand-500 block"></span>
                            <span class="font-sans text-2xs font-semibold tracking-widest uppercase text-brand-500 dark:text-brand-300">
                                Why Choose Us
                            </span>
                        </div>

                        <h2 class="wcu-in font-display font-extrabold text-ink dark:text-white leading-tight tracking-tighter
                                   text-3xl sm:text-4xl lg:text-[42px] mb-5"
                            style="animation-delay:0.08s">
                            Why Choose 1st Delightsome Fabrics
                            <span class="block text-brand-500 dark:text-brand-300">in Ikeja, Lagos</span>
                        </h2>

                        <p class="wcu-in font-sans text-base text-neutral-500 dark:text-white/55 leading-relaxed max-w-xl"
                           style="animation-delay:0.16s">
                            We've been on this street long enough to know exactly what fabric buyers actually need —
                            and what most shops get wrong. Our stock is hand-picked, prices are fixed, and the staff
                            know every roll on the shelf. No runaround, no shortage, no negotiating the obvious.
                        </p>
                    </div>

                    {{-- 2×2 Features --}}
                    @php
                        $wcuFeats = [
                            [
                                'title' => 'Hand-picked, every bolt',
                                'body'  => 'Every fabric is inspected for weight, finish, and colour before it hits the shelf. If it doesn\'t pass, it doesn\'t come in.',
                                'delay' => '0.24s',
                            ],
                            [
                                'title' => 'Fixed pricing, always',
                                'body'  => 'The price on the tag is the price at checkout — two yards or two hundred. No back-and-forth, no "special rate" politics.',
                                'delay' => '0.32s',
                            ],
                            [
                                'title' => 'Staff who know fabrics',
                                'body'  => 'Drape, care, what wears well in Lagos heat — our team can answer the practical questions that save you from bad buys.',
                                'delay' => '0.40s',
                            ],
                            [
                                'title' => 'Built for Lagos fashion',
                                'body'  => 'Deep stock kept deliberately — so when aso-ebi season hits or a deadline creeps up, you can walk in and walk out sorted.',
                                'delay' => '0.48s',
                            ],
                        ];
                    @endphp

                    <div class="grid sm:grid-cols-2 gap-x-10 gap-y-0 divide-y divide-neutral-200 dark:divide-white/10 sm:divide-y-0">
                        @foreach($wcuFeats as $i => $f)
                        <div class="wcu-feat wcu-in group py-6 sm:py-0 sm:border-t sm:border-neutral-200 dark:sm:border-white/10 cursor-default"
                             style="animation-delay:{{ $f['delay'] }}; {{ $i >= 2 ? 'padding-top:1.5rem; margin-top:1.5rem;' : 'padding-top:1.5rem;' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-sans text-2xs font-bold tracking-widest text-accent">0{{ $i + 1 }}</span>
                                <div class="relative h-px flex-1 bg-neutral-200 dark:bg-white/10 overflow-hidden">
                                    <div class="wcu-feat-line absolute top-0 left-0 h-full bg-brand-400 dark:bg-brand-300"></div>
                                </div>
                            </div>
                            <h3 class="font-display text-md font-semibold text-ink dark:text-white leading-snug mb-1.5
                                       transition-colors duration-300 group-hover:text-brand-500 dark:group-hover:text-brand-300">
                                {{ $f['title'] }}
                            </h3>
                            <p class="font-sans text-sm text-neutral-500 dark:text-white/45 leading-relaxed">{{ $f['body'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- CTAs --}}
                    <div class="wcu-in flex flex-wrap gap-3 pt-2" style="animation-delay:0.56s">
                        <a href="{{ route('shop.index') }}"
                           class="wcu-btn inline-flex items-center gap-3 px-7 py-3.5
                                  font-sans text-sm font-semibold text-white
                                  bg-brand-500 transition-colors duration-300 hover:bg-brand-600">
                            View Collections
                            <svg class="wcu-arr w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                        <a href="/about"
                           class="wcu-btn inline-flex items-center gap-3 px-7 py-3.5
                                  font-sans text-sm font-semibold text-ink dark:text-white
                                  border border-neutral-300 dark:border-white/20
                                  transition-all duration-300 hover:border-ink dark:hover:border-white/50">
                            About Us
                            <svg class="wcu-arr w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                </div>{{-- /left --}}


                {{-- ── RIGHT: dark brand panel ─────────────────── --}}
                <div class="wcu-in bg-brand-800 dark:bg-brand-800/60 flex flex-col justify-between p-10 lg:p-12"
                     style="animation-delay:0.2s">

                    {{-- Tagline --}}
                    <div class="mb-10">
                        <p class="font-display text-xl font-semibold text-white leading-snug mb-4">
                            "The fabric shop that actually knows what it's selling."
                        </p>
                        <span class="block h-px w-12 bg-accent"></span>
                    </div>

                    {{-- Stats --}}
                    @php
                        $wcuStats = [
                            ['val' => '15+',  'lbl' => 'Experience'],
                            ['val' => '500+', 'lbl' => 'Fabric Varieties'],
                            ['val' => '1K+',  'lbl' => 'Happy Designers'],
                        ];
                    @endphp
                    <div class="space-y-6">
                        @foreach($wcuStats as $s)
                        <div class="flex items-end justify-between border-b border-white/10 pb-5 last:border-0 last:pb-0">
                            <span class="font-sans text-sm text-white/50 tracking-wide">{{ $s['lbl'] }}</span>
                            <span class="font-display text-4xl font-extrabold text-accent leading-none tracking-tighter">{{ $s['val'] }}</span>
                        </div>
                        @endforeach
                    </div>

                </div>{{-- /right panel --}}

            </div>{{-- /grid --}}
        </div>{{-- /container --}}

    </section>{{-- /why-choose-us --}}


    <livewire:frontend.our-products />

    @include('partials.frontend.video-banner')

    @include('partials.frontend.testimonial')

    @include('components.recent-blog-posts')

@endsection