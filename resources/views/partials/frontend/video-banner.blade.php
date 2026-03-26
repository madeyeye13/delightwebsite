<section class="relative w-full overflow-hidden" style="height: clamp(480px, 70vh, 720px);" aria-label="1st Delightsome Fabrics — Hero">

    {{-- Video background --}}
    <video
        class="absolute inset-0 w-full h-full object-cover"
        src="{{ asset('images/fabvideo1.mp4') }}"
        autoplay
        loop
        muted
        playsinline
        preload="none"
        aria-hidden="true"
        poster="{{ asset('images/fabvideo1-poster.jpg') }}"
    ></video>

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/60" aria-hidden="true"></div>

    {{-- Grain texture overlay for depth --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
         style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');
                background-size: 160px;"
         aria-hidden="true"></div>

    {{-- Content --}}
    <div class="relative z-10 h-full flex items-end">
        <div class="w-full max-w-6xl mx-auto px-6 md:px-16 pb-14 md:pb-20">

            {{-- Eyebrow --}}
            <p class="font-sans text-2xs font-semibold tracking-widest uppercase text-accent-400 mb-4"
               style="letter-spacing: 0.12em;">
                Lagos · Nigeria
            </p>

            {{-- Heading --}}
            <h2 class="font-display font-bold text-neutral-50 mb-4 max-w-lg"
                style="font-size: clamp(18px, 2.4vw, 26px); line-height: 1.25; letter-spacing: -0.025em;"
                itemprop="name">
                Fabric That Actually Holds Up, in Heat, in Wear, and at Every Function
            </h2>

            {{-- Paragraph --}}
            <p class="font-sans text-neutral-300 mb-8 max-w-md"
               style="font-size: 13px; line-height: 1.75;">
                We stock what tailors reach for, not what fills shelves.
                Every piece — from senator to Swiss lace — is hand-checked
                before it reaches you. No guesswork, fixed prices, shipped across Nigeria.
            </p>

            {{-- CTA --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('shop.index') }}"
                   class="inline-flex items-center gap-2 font-sans font-semibold text-ink-DEFAULT bg-accent-DEFAULT hover:bg-accent-600 transition-colors duration-200 px-5 py-2.5"
                   style="font-size: 12px; letter-spacing: 0.02em;">
                    Shop Fabrics
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M2 6.5h9M7.5 3l3.5 3.5L7.5 10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="{{ route('blog.index') }}"
                   class="font-sans text-neutral-300 hover:text-neutral-50 transition-colors duration-200"
                   style="font-size: 12px; letter-spacing: 0.02em;">
                    Read the blog →
                </a>
            </div>

        </div>
    </div>

</section>