{{--
    marquee-collection.blade.php
    1. Marquee promotional banner (CSS-only infinite scroll)
    2. Two-column collection grid (Men + Women)
--}}

<style>
    @keyframes marqueeScroll {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }
    .marquee-track {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        animation: marqueeScroll 28s linear infinite;
        will-change: transform;
    }
    .marquee-wrapper:hover .marquee-track {
        animation-play-state: paused;
    }
    @media (prefers-reduced-motion: reduce) {
        .marquee-track { animation: none; }
    }

    /* Shop Now shimmer underline */
    .shop-link {
        position: relative;
        display: inline-block;
    }
    .shop-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 1px;
        background: currentColor;
    }
    .shop-link::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: -20%;
        width: 30%;
        height: 1px;
        background: rgba(255,255,255,0.55);
        transform: skewX(-20deg);
        transition: left 0.55s cubic-bezier(0.4,0,0.2,1);
        opacity: 0;
    }
    .shop-link:hover::before {
        left: 110%;
        opacity: 1;
    }
</style>

{{-- ══════════════════════════════════════════════
     MARQUEE BANNER
══════════════════════════════════════════════ --}}
<section
    class="relative flex items-center overflow-hidden bg-black h-[10vh] min-h-[56px] marquee-wrapper"
    aria-label="Promotional ticker"
    role="region"
>
    <div class="marquee-track" aria-hidden="true">
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>New Arrivals &mdash; African Lace Fabrics
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Aso-oke &amp; Ankara Collections
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Bridal &amp; Ready-to-Wear Fabrics
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Shop Men&#39;s &amp; Women&#39;s Materials
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Your Global Fabric Supplier
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Quality Fabrics from Ikeja
        </span>
        {{-- Duplicate set for seamless loop --}}
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>New Arrivals &mdash; African Lace Fabrics
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Aso-oke &amp; Ankara Collections
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Bridal &amp; Ready-to-Wear Fabrics
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Shop Men&#39;s &amp; Women&#39;s Materials
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Your Global Fabric Supplier
        </span>
        <span class="inline-flex items-center gap-5 mx-8 font-display text-sm font-light tracking-widest text-white/90 uppercase">
            <span class="text-white/30 text-[9px]">&#10022;</span>Quality Fabrics from Ikeja
        </span>
    </div>

    <div class="sr-only">
        <h2>Current Promotions at 1st Delightsome Fabrics</h2>
        <p>
            Shop new arrivals in African lace fabrics and aso-oke.
            Explore men and women collections of quality ready-to-wear materials from Ikeja, Lagos.
            Your trusted global African lace fabrics and aso-oke supplier.
        </p>
    </div>
</section>


{{-- ══════════════════════════════════════════════
     TWO-COLUMN COLLECTION GRID
     No gap, no margin from banner above
══════════════════════════════════════════════ --}}
<section
    class="grid grid-cols-1 md:grid-cols-2"
    aria-label="Shop by collection"
>

    {{-- LEFT: Men --}}
    <article class="relative overflow-hidden aspect-[4/5] md:aspect-auto md:h-[85vh]">

        <img
            src="{{ asset('images/kaftan.jpg') }}"
            alt="Classic African fabric materials for men including senator wear, agbada and lace"
            class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 ease-out hover:scale-[1.03]"
            loading="lazy"
            width="800"
            height="1000"
        />

        {{-- Flat black overlay, no gradient --}}
        <div class="absolute inset-0 bg-black/35"></div>

        <div class="absolute bottom-0 left-0 p-7 md:p-10 z-10">
            <h2 class="font-display text-2xl md:text-3xl font-semibold text-white leading-snug tracking-tight mb-2">
                Classic Men&#39;s Material
            </h2>
            <p class="font-sans text-sm text-white/75 leading-relaxed mb-5 max-w-[260px]">
                Senator wear, agbada and lace fabrics crafted for the modern Nigerian man.
            </p>
            <a
                href="{{ url('/collection/mens') }}"
                class="shop-link font-sans text-sm font-medium text-white tracking-wide"
                aria-label="Shop the Classic Men material collection"
            >
                Shop Now
            </a>
        </div>

    </article>

    {{-- RIGHT: Women --}}
    <article class="relative overflow-hidden aspect-[4/5] md:aspect-auto md:h-[85vh]">

        <img
            src="{{ asset('images/asooke.jpg') }}"
            alt="Modern African fabric materials for women including lace, aso-ebi and ready-to-wear"
            class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 ease-out hover:scale-[1.03]"
            loading="lazy"
            width="800"
            height="1000"
        />

        {{-- Flat black overlay, no gradient --}}
        <div class="absolute inset-0 bg-black/35"></div>

        <div class="absolute bottom-0 left-0 md:left-auto md:right-0 p-7 md:p-10 z-10 md:text-right">
            <h2 class="font-display text-2xl md:text-3xl font-semibold text-white leading-snug tracking-tight mb-2">
                Women&#39;s Modern Material
            </h2>
            <p class="font-sans text-sm text-white/75 leading-relaxed mb-5 max-w-[260px] md:ml-auto">
                Lace, aso-ebi and ready-to-wear fabrics for dresses, occasions and everyday elegance.
            </p>
            <a
                href="{{ url('/collection/womens') }}"
                class="shop-link font-sans text-sm font-medium text-white tracking-wide"
                aria-label="Shop the Women Modern Material collection"
            >
                Shop Now
            </a>
        </div>

    </article>

</section>