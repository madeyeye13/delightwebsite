{{--
╔══════════════════════════════════════════════════════════════════╗
║  FRONTEND HEADER PARTIAL                                          ║
║  resources/views/partials/frontend/header.blade.php               ║
║                                                                   ║
║  Inspired by: sliding announcement bar, CSS hover dropdowns,      ║
║  search modal overlay, side drawer mobile nav                     ║
║                                                                   ║
║  Structure:                                                       ║
║  1. Announcement bar  — sliding messages, prev/next              ║
║  2. Main header       — transparent → white on hover/scroll      ║
║     └─ Logo (swaps white ↔ color)                                ║
║     └─ Nav (CSS :hover dropdowns, underline left→right)          ║
║     └─ Actions (currency, search, user, cart)                    ║
║  3. Search modal      — full-screen overlay                       ║
║  4. Mobile side drawer — slides from right                        ║
╚══════════════════════════════════════════════════════════════════╝
--}}

{{-- ════════════════════════════════════════════════════════════════
     ROOT ALPINE COMPONENT
     Owns: scrolled, hover, search modal, mobile drawer, profile drawer
════════════════════════════════════════════════════════════════ --}}
<div
    x-cloak
    x-data="{
        scrolled: false,
        hovered: false,
        searchOpen: false,
        searchQuery: '',
        mobileOpen: false,
        profileOpen: false,
        shopOpen: false,
        collectionOpen: false,
        selectedCurrency: 'NGN',
        windowWidth: window.innerWidth,
        get bg() { return this.scrolled || this.hovered; },
        get gridColumns() {
            return this.windowWidth < 1024 ? 'auto 1fr auto' : '180px 1fr 220px';
        },

        changeCurrency(code) {
            this.selectedCurrency = code;
        },

        init() {
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 50;
            });
            window.addEventListener('resize', () => {
                this.windowWidth = window.innerWidth;
            });
            /* Lock body scroll when drawers are open */
            this.$watch('mobileOpen',  v => document.body.style.overflow = v ? 'hidden' : '');
            this.$watch('profileOpen', v => document.body.style.overflow = v ? 'hidden' : '');
            this.$watch('searchOpen',  v => document.body.style.overflow = v ? 'hidden' : '');
        }
    }"
    x-init="init()"
>

{{-- ════════════════════════════════════════════════════════════════
     1. ANNOUNCEMENT BAR
     • Fixed, always on top (z-[1001])
     • Slides between messages every 4 s
     • Prev / Next arrows
════════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        current: 0,
        messages: [
            '🎉 Welcome to <strong>1st Delightsome Fabrics</strong> — Your Global African Lace &amp; Aso-oke Supplier',
            '🚚 Free shipping on orders over ₦50,000 within Lagos',
            '✨ New arrivals every week — Shop the latest fabrics now'
        ],
        timer: null,
        init() { this.timer = setInterval(() => this.next(), 4000); },
        next() { this.current = (this.current + 1) % this.messages.length; clearInterval(this.timer); this.timer = setInterval(() => this.next(), 4000); },
        prev() { this.current = (this.current - 1 + this.messages.length) % this.messages.length; clearInterval(this.timer); this.timer = setInterval(() => this.next(), 4000); }
    }"
    x-init="init()"
    class="fixed top-0 left-0 right-0 z-[1001] bg-black text-white flex items-center justify-center gap-3 h-9 overflow-hidden"
>
    {{-- Prev arrow --}}
    <button @click="prev()" aria-label="Previous"
            class="flex-shrink-0 text-white/60 hover:text-white transition-colors duration-200 p-1">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
            <path d="M10.828 12 15.778 16.95 14.364 18.364 8 12l6.364-6.364 1.414 1.414L10.828 12Z"/>
        </svg>
    </button>

    {{-- Message slot — fixed width, clips overflow --}}
    <div class="relative w-[320px] sm:w-[480px] h-5 overflow-hidden flex-shrink-0">
        <template x-for="(msg, i) in messages" :key="i">
            <div
                x-show="current === i"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-6"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-6"
                class="absolute inset-0 flex items-center justify-center text-center font-sans text-[11px] sm:text-xs tracking-wide whitespace-nowrap"
                x-html="msg"
                style="display:none"
            ></div>
        </template>
    </div>

    {{-- Next arrow --}}
    <button @click="next()" aria-label="Next"
            class="flex-shrink-0 text-white/60 hover:text-white transition-colors duration-200 p-1">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
            <path d="M13.172 12 8.222 7.05 9.636 5.636 16 12l-6.364 6.364-1.414-1.414L13.172 12Z"/>
        </svg>
    </button>
</div>


{{-- ════════════════════════════════════════════════════════════════
     2. MAIN HEADER
     • Sits immediately below announcement bar (top-9)
     • Transparent by default; white bg fades in on hover OR scroll
     • 3-column grid: Logo | Nav | Actions
════════════════════════════════════════════════════════════════ --}}
<header
    @mouseenter="hovered = true"
    @mouseleave="hovered = false"
    :class="bg
        ? 'bg-white shadow-[0_2px_12px_rgba(0,0,0,0.07)]'
        : 'bg-transparent'"
    class="fixed top-9 left-0 right-0 z-[1000] transition-all duration-300 font-sans"
>
    <div class="max-w-[1400px] mx-auto px-0 sm:px-3 lg:px-16">

        {{-- ── 3-column grid ── --}}
        <div class="grid items-center py-4 lg:py-5 gap-6"
             :style="{ gridTemplateColumns: gridColumns }">

            {{-- ────────────────────────────────────────────
                 LOGO
                 White version on transparent, color on white
            ──────────────────────────────────────────── --}}
            <a href="{{ url('/') }}" class="relative block h-10 w-[120px]">
                {{-- Color logo (visible on white bg) --}}
                <img src="{{ asset('images/logo1.png') }}"
                     alt="1st Delightsome Fabrics"
                     :class="bg ? 'opacity-100 visible' : 'opacity-0 invisible'"
                     class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300">
                {{-- White logo (visible on transparent bg) --}}
                <img src="{{ asset('images/logowhite.png') }}"
                     alt="1st Delightsome Fabrics"
                     :class="bg ? 'opacity-0 invisible' : 'opacity-100 visible'"
                     class="absolute inset-0 w-full h-full object-contain transition-opacity duration-300">
            </a>

            {{-- ────────────────────────────────────────────
                 DESKTOP NAV  (hidden below lg)
                 • CSS :hover group drives dropdowns (no JS needed)
                 • Underline animates left → right via scale-x
            ──────────────────────────────────────────── --}}
            <nav class="hidden lg:flex items-center justify-center gap-1 flex-nowrap">

                {{-- Reusable link colour class via Alpine --}}
                {{-- :class binding handles transparent ↔ white flip --}}

                {{-- Home --}}
                <a href="{{ url('/') }}"
                   :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 group whitespace-nowrap">
                    Home
                    <span :class="bg ? 'bg-black' : 'bg-white'"
                          class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                </a>

                {{-- About --}}
                <a href="{{ url('/about') }}"
                   :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 group whitespace-nowrap">
                    About
                    <span :class="bg ? 'bg-black' : 'bg-white'"
                          class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                </a>

                {{-- Category — CSS hover dropdown --}}
                <div class="relative group">
                    <button
                        :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                        class="relative flex items-center gap-1 px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 whitespace-nowrap bg-transparent border-none cursor-pointer"
                    >
                        Category
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 transition-transform duration-200 group-hover:rotate-180">
                            <path d="M1.875 7.438 12 17.563 22.125 7.438" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        {{-- Underline --}}
                        <span :class="bg ? 'bg-black' : 'bg-white'"
                              class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                    </button>
                    {{-- Dropdown panel --}}
                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-[10px] w-52 bg-white shadow-[0_5px_20px_rgba(0,0,0,0.1)]
                                opacity-0 invisible pointer-events-none
                                group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto group-hover:mt-0
                                transition-all duration-300">
                        <ul class="py-2 list-none">
                            @foreach([
                                ['Lace Fabrics',    '/category/lace-fabrics'],
                                ['Aso-oke',          '/category/aso-oke'],
                                ['Ankara & Prints',  '/category/ankara'],
                                ['Bridal Fabrics',   '/category/bridal'],
                                ["Men's Fabrics",    '/category/mens'],
                                ['Plain & Solid',    '/category/plain'],
                            ] as [$label, $href])
                            <li>
                                <a href="{{ $href }}"
                                   class="block px-5 py-2.5 text-[13px] text-gray-600 hover:text-black hover:bg-gray-50 transition-colors duration-150 whitespace-nowrap">
                                    {{ $label }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Shop --}}
                <a href="{{ url('/shop') }}"
                   :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 group whitespace-nowrap">
                    Shop
                    <span :class="bg ? 'bg-black' : 'bg-white'"
                          class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                </a>

                {{-- Collection — CSS hover dropdown --}}
                <div class="relative group">
                    <button
                        :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                        class="relative flex items-center gap-1 px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 whitespace-nowrap bg-transparent border-none cursor-pointer"
                    >
                        Collection
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 transition-transform duration-200 group-hover:rotate-180">
                            <path d="M1.875 7.438 12 17.563 22.125 7.438" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span :class="bg ? 'bg-black' : 'bg-white'"
                              class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                    </button>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 mt-[10px] w-52 bg-white shadow-[0_5px_20px_rgba(0,0,0,0.1)]
                                opacity-0 invisible pointer-events-none
                                group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto group-hover:mt-0
                                transition-all duration-300">
                        <ul class="py-2 list-none">
                            @foreach([
                                ['New Arrivals',       '/collection/new-arrivals'],
                                ['Best Sellers',       '/collection/best-sellers'],
                                ['Wedding Season',     '/collection/wedding'],
                                ["Men's Collection",   '/collection/mens'],
                                ["Women's Collection", '/collection/womens'],
                                ['Sale & Clearance',   '/collection/sale'],
                            ] as [$label, $href])
                            <li>
                                <a href="{{ $href }}"
                                   class="block px-5 py-2.5 text-[13px] text-gray-600 hover:text-black hover:bg-gray-50 transition-colors duration-150 whitespace-nowrap">
                                    {{ $label }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Blog --}}
                <a href="{{ url('/blog') }}"
                   :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 group whitespace-nowrap">
                    Blog
                    <span :class="bg ? 'bg-black' : 'bg-white'"
                          class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                </a>

                {{-- FAQ --}}
                <a href="{{ url('/faq') }}"
                   :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 group whitespace-nowrap">
                    FAQ
                    <span :class="bg ? 'bg-black' : 'bg-white'"
                          class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                </a>

                {{-- Contact --}}
                <a href="{{ url('/contact') }}"
                   :class="bg ? 'text-gray-800 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative px-3 py-2 text-[12px] tracking-[0.4px] font-normal transition-colors duration-200 group whitespace-nowrap">
                    Contact
                    <span :class="bg ? 'bg-black' : 'bg-white'"
                          class="absolute bottom-0.5 left-3 right-3 h-[1.5px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out rounded-full"></span>
                </a>

            </nav>

            {{-- ────────────────────────────────────────────
                 HEADER ACTIONS (right side)
                 Desktop: currency + dark mode + search + user + cart
                 Mobile:  search + cart + hamburger
            ──────────────────────────────────────────── --}}
            <div class="flex items-center justify-end gap-4">

                {{-- ── DARK MODE TOGGLE (desktop) ── --}}
                <button
                    @click="
                        $store.theme.toggle();
                        document.documentElement.classList.toggle('dark', $store.theme.dark);
                        localStorage.setItem('theme', $store.theme.dark ? 'dark' : 'light');
                    "
                    :class="bg ? 'text-gray-600 hover:text-black' : 'text-white/80 hover:text-white'"
                    class="hidden lg:flex items-center justify-center transition-all duration-200 hover:opacity-70"
                    title="Toggle dark mode"
                >
                    <svg x-show="$store.theme.dark" class="w-[17px] h-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg x-show="!$store.theme.dark" class="w-[17px] h-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>

                {{-- ── CURRENCY SELECTOR (desktop) — CSS :hover --}}
                <div class="relative hidden lg:block group">
                    <button
                        :class="bg ? 'text-gray-700 hover:text-black' : 'text-white hover:text-white/80'"
                        class="flex items-center gap-1.5 text-[12px] tracking-[0.4px] font-medium transition-colors duration-200 bg-transparent border-none cursor-pointer"
                    >
                        <span x-text="selectedCurrency"></span>
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 transition-transform duration-200 group-hover:rotate-180">
                            <path d="M1.875 7.438 12 17.563 22.125 7.438" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                    {{-- Currency dropdown --}}
                    <div class="absolute top-full right-0 mt-[10px] w-[110px] bg-white shadow-[0_5px_20px_rgba(0,0,0,0.1)]
                                opacity-0 invisible pointer-events-none
                                group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto group-hover:mt-[5px]
                                transition-all duration-300 z-50">
                        @foreach([
                            ['NGN','🇳🇬'],['USD','🇺🇸'],['GBP','🇬🇧'],
                            ['CAD','🇨🇦'],['EUR','🇪🇺'],['GHS','🇬🇭'],['CFA','🌍'],
                        ] as [$code, $flag])
                        <button
                            @click="changeCurrency('{{ $code }}')"
                            :class="selectedCurrency === '{{ $code }}' ? 'text-black font-semibold bg-gray-50' : 'text-gray-600'"
                            class="w-full flex items-center gap-2 px-4 py-2 text-[13px] text-left hover:text-black hover:bg-gray-50 transition-colors duration-150 bg-transparent border-none cursor-pointer"
                        >
                            <span>{{ $flag }}</span>
                            <span>{{ $code }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- ── SEARCH ── --}}
                <button
                    @click="searchOpen = true"
                    :class="bg ? 'text-gray-700 hover:text-black' : 'text-white hover:text-white/80'"
                    class="transition-colors duration-200 hover:opacity-70 bg-transparent border-none cursor-pointer"
                    aria-label="Search"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                        <path d="M11.048 17.89a6.923 6.923 0 1 0 0-13.847 6.923 6.923 0 0 0 0 13.847z" stroke="currentColor" stroke-width="1.2"/>
                        <path d="m16 16 4.308 4.308" stroke="currentColor" stroke-width="1.2"/>
                    </svg>
                </button>

                {{-- ── USER / ACCOUNT (desktop: CSS hover, mobile: click drawer) ── --}}
                {{-- Desktop --}}
                <div class="relative hidden lg:block group">
                    <button
                        :class="bg ? 'text-gray-700 hover:text-black' : 'text-white hover:text-white/80'"
                        class="transition-colors duration-200 hover:opacity-70 bg-transparent border-none cursor-pointer"
                        aria-label="Account"
                    >
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                            <path d="M12 12.413a4.358 4.358 0 1 0 0-8.715 4.358 4.358 0 0 0 0 8.715zM3.488 20.857c0-3.085 1.594-5.61 5.26-5.61h6.503c3.667 0 5.261 2.525 5.261 5.61" stroke="currentColor" stroke-width="1.2"/>
                        </svg>
                    </button>
                    <div class="absolute top-full right-0 mt-[10px] min-w-[180px] bg-white shadow-[0_5px_20px_rgba(0,0,0,0.1)]
                                opacity-0 invisible pointer-events-none
                                group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto group-hover:mt-[5px]
                                transition-all duration-300 z-50">
                        @auth
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Welcome back,</p>
                                <p class="text-sm font-medium text-black">{{ auth()->user()->name }}</p>
                            </div>
                            <div class="py-1">
                                <a href="/my-profile" class="block px-4 py-2.5 text-[13px] text-gray-600 hover:bg-gray-50 hover:text-black transition-colors duration-150">Profile</a>
                                <a href="/my-profile?tab=orders" class="block px-4 py-2.5 text-[13px] text-gray-600 hover:bg-gray-50 hover:text-black transition-colors duration-150">My Orders</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-[13px] text-gray-600 hover:bg-gray-50 hover:text-black transition-colors duration-150 bg-transparent border-none cursor-pointer">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Browsing as guest</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('login') }}" class="block px-4 py-2.5 text-[13px] text-gray-600 hover:bg-gray-50 hover:text-black transition-colors duration-150">Login</a>
                                <a href="{{ route('register') }}" class="block px-4 py-2.5 text-[13px] text-gray-600 hover:bg-gray-50 hover:text-black transition-colors duration-150">Register</a>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- Mobile: click to open profile drawer --}}
                <button
                    @click="profileOpen = true"
                    :class="bg ? 'text-gray-700 hover:text-black' : 'text-white hover:text-white/80'"
                    class="lg:hidden transition-colors duration-200 hover:opacity-70 bg-transparent border-none cursor-pointer"
                    aria-label="Account"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                        <path d="M12 12.413a4.358 4.358 0 1 0 0-8.715 4.358 4.358 0 0 0 0 8.715zM3.488 20.857c0-3.085 1.594-5.61 5.26-5.61h6.503c3.667 0 5.261 2.525 5.261 5.61" stroke="currentColor" stroke-width="1.2"/>
                    </svg>
                </button>

                {{-- ── CART ── --}}
                <a href="{{ url('/cart') }}"
                   :class="bg ? 'text-gray-700 hover:text-black' : 'text-white hover:text-white/80'"
                   class="relative transition-colors duration-200 hover:opacity-70"
                   aria-label="Cart"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                        <path stroke="currentColor" stroke-width="1.2" d="M5 6.5h16l-2.024 10H7.024L5 6.5Zm0 0L4.364 3H1"/>
                        <path stroke="currentColor" stroke-width="1.2" d="M7.889 19.71a.65.65 0 1 1 .722 1.08.65.65 0 0 1-.722-1.08ZM16.889 19.71a.65.65 0 1 1 .722 1.08.65.65 0 0 1-.722-1.08Z"/>
                    </svg>
                    {{-- Cart count badge --}}
                    <span class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 flex items-center justify-center
                                 rounded-full bg-black text-white text-[9px] font-bold leading-none
                                 ring-[1.5px]"
                          :class="bg ? 'ring-white' : 'ring-transparent'">
                        0
                    </span>
                </a>

                {{-- ── HAMBURGER (mobile only) ── --}}
                <button
                    @click="mobileOpen = true"
                    :class="bg ? 'text-gray-700 hover:text-black' : 'text-white hover:text-white/80'"
                    class="lg:hidden transition-colors duration-200 hover:opacity-70 bg-transparent border-none cursor-pointer"
                    aria-label="Open menu"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                        <path d="M3.692 12.646h16.616M3.692 5.762h16.616M3.692 19.608h16.616" stroke="currentColor" stroke-width="1.2"/>
                    </svg>
                </button>

            </div>
        </div>{{-- /grid --}}
    </div>{{-- /container --}}
</header>


{{-- ════════════════════════════════════════════════════════════════
     3. SEARCH MODAL (full-screen overlay)
     • Identical feel to reference: white near-opaque bg, big input
     • × button top-right closes it
════════════════════════════════════════════════════════════════ --}}
<div
    x-show="searchOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="searchOpen = false; searchQuery = ''"
    class="fixed inset-0 bg-white/[0.97] z-[3000] flex items-center justify-center"
    style="display:none"
>
    {{-- Close button --}}
    <button
        @click="searchOpen = false; searchQuery = ''"
        class="absolute top-7 right-7 text-gray-400 hover:text-black transition-colors duration-200 bg-transparent border-none cursor-pointer"
        aria-label="Close search"
    >
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
            <path d="M18.462 6.479 5.538 19.402M5.538 6.479l12.924 12.923" stroke="currentColor" stroke-width="1.2"/>
        </svg>
    </button>

    {{-- Search input --}}
    <div class="w-[90%] max-w-[580px]">
        <p class="font-sans text-xs tracking-widest uppercase text-gray-400 mb-4">Search</p>
        <div class="flex items-center border-b-2 border-black pb-2 gap-3">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 flex-shrink-0">
                <path d="M11.048 17.89a6.923 6.923 0 1 0 0-13.847 6.923 6.923 0 0 0 0 13.847z" stroke="currentColor" stroke-width="1.2"/>
                <path d="m16 16 4.308 4.308" stroke="currentColor" stroke-width="1.2"/>
            </svg>
            <input
                type="text"
                x-model="searchQuery"
                x-init="$watch('searchOpen', v => { if (v) $nextTick(() => $el.focus()); })"
                placeholder="Search fabrics, lace, aso-oke…"
                class="flex-1 border-none outline-none font-sans text-xl text-black placeholder-gray-300 bg-transparent"
            />
            <button
                x-show="searchQuery.length > 0"
                @click="searchQuery = ''"
                class="text-gray-400 hover:text-black transition-colors bg-transparent border-none cursor-pointer"
            >
                <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                    <path d="M18.462 6.479 5.538 19.402M5.538 6.479l12.924 12.923" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>
        </div>
        {{-- Helper text --}}
        <p x-show="searchQuery.length === 0" class="mt-4 font-sans text-xs text-gray-400">
            Try: "lace fabric", "ankara", "aso-oke"
        </p>
        <p x-show="searchQuery.length > 0 && searchQuery.length < 2" class="mt-4 font-sans text-xs text-gray-400">
            Please enter at least 2 characters…
        </p>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════════════
     4A. MOBILE MENU DRAWER (slides from right)
════════════════════════════════════════════════════════════════ --}}

{{-- Backdrop --}}
<div
    x-show="mobileOpen"
    @click="mobileOpen = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 z-[1999] lg:hidden"
    style="display:none"
></div>

{{-- Drawer panel --}}
<div
    x-show="mobileOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-250"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 bottom-0 w-full max-w-[380px] bg-white z-[2000] overflow-y-auto lg:hidden"
    style="display:none"
>
    {{-- Drawer header --}}
    <div class="flex items-center justify-between px-5 py-5 border-b border-gray-100">
        <img src="{{ asset('images/logo1.png') }}" alt="1st Delightsome Fabrics" class="h-9 w-auto">
        <button @click="mobileOpen = false" class="text-gray-500 hover:text-black transition-colors bg-transparent border-none cursor-pointer" aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                <path d="M18.462 6.479 5.538 19.402M5.538 6.479l12.924 12.923" stroke="currentColor" stroke-width="1.2"/>
            </svg>
        </button>
    </div>

    {{-- Nav links --}}
    <nav class="px-5 pt-2 pb-4">
        @foreach([
            ['Home',    url('/')],
            ['About',   url('/about')],
            ['Shop',    url('/shop')],
            ['Blog',    url('/blog')],
            ['FAQ',     url('/faq')],
            ['Contact', url('/contact')],
        ] as [$label, $href])
        <div class="border-b border-gray-100">
            <a href="{{ $href }}" @click="mobileOpen = false"
               class="flex items-center justify-between py-4 font-sans text-[15px] text-black font-medium no-underline">
                {{ $label }}
            </a>
        </div>
        @endforeach

        {{-- Category accordion --}}
        <div x-data="{ open: false }" class="border-b border-gray-100">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between py-4 font-sans text-[15px] text-black font-medium bg-transparent border-none cursor-pointer text-left">
                Category
                <svg viewBox="0 0 24 24" fill="none" :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200">
                    <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="pb-2 pl-3">
                @foreach(['Lace Fabrics','Aso-oke','Ankara & Prints','Bridal Fabrics',"Men's Fabrics",'Plain & Solid'] as $cat)
                <a href="/category/{{ Str::slug($cat) }}" @click="mobileOpen = false"
                   class="block py-2.5 text-[13px] text-gray-600 hover:text-black border-b border-gray-50 last:border-0 transition-colors">
                    {{ $cat }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Collection accordion --}}
        <div x-data="{ open: false }" class="border-b border-gray-100">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between py-4 font-sans text-[15px] text-black font-medium bg-transparent border-none cursor-pointer text-left">
                Collection
                <svg viewBox="0 0 24 24" fill="none" :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200">
                    <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="pb-2 pl-3">
                @foreach(['New Arrivals','Best Sellers','Wedding Season',"Men's Collection","Women's Collection",'Sale & Clearance'] as $col)
                <a href="/collection/{{ Str::slug($col) }}" @click="mobileOpen = false"
                   class="block py-2.5 text-[13px] text-gray-600 hover:text-black border-b border-gray-50 last:border-0 transition-colors">
                    {{ $col }}
                </a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- Currency picker in drawer --}}
    <div class="px-5 pt-4 pb-6 border-t border-gray-100">
        <p class="font-sans text-xs tracking-widest uppercase text-gray-400 mb-3">Currency</p>
        <div class="grid grid-cols-2 gap-2">
            @foreach([
                ['NGN','🇳🇬'],['USD','🇺🇸'],['GBP','🇬🇧'],
                ['CAD','🇨🇦'],['EUR','🇪🇺'],['GHS','🇬🇭'],['CFA','🌍'],
            ] as [$code, $flag])
            <button
                @click="changeCurrency('{{ $code }}')"
                :class="selectedCurrency === '{{ $code }}' ? 'border-black text-black font-semibold' : 'border-gray-200 text-gray-600'"
                class="flex items-center gap-2 px-3 py-2 border rounded text-[13px] hover:border-black hover:text-black transition-colors bg-transparent cursor-pointer"
            >
                <span>{{ $flag }}</span>
                <span>{{ $code }}</span>
            </button>
            @endforeach
        </div>
    </div>

</div>


{{-- ════════════════════════════════════════════════════════════════
     4B. MOBILE PROFILE DRAWER (slides from right)
════════════════════════════════════════════════════════════════ --}}

{{-- Backdrop --}}
<div
    x-show="profileOpen"
    @click="profileOpen = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 z-[2001] lg:hidden"
    style="display:none"
></div>

{{-- Drawer panel --}}
<div
    x-show="profileOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-250"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 bottom-0 w-full max-w-[360px] bg-white z-[2002] overflow-y-auto lg:hidden"
    style="display:none"
>
    <div class="flex items-center justify-between px-5 py-5 border-b border-gray-100">
        <h3 class="font-sans text-base font-semibold text-black">My Account</h3>
        <button @click="profileOpen = false" class="text-gray-500 hover:text-black transition-colors bg-transparent border-none cursor-pointer">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                <path d="M18.462 6.479 5.538 19.402M5.538 6.479l12.924 12.923" stroke="currentColor" stroke-width="1.2"/>
            </svg>
        </button>
    </div>

    <div class="p-5">
        @auth
            <div class="mb-5 pb-5 border-b border-gray-100">
                <p class="font-sans text-xs text-gray-400">Welcome back,</p>
                <p class="font-sans text-base font-semibold text-black mt-0.5">{{ auth()->user()->name }}</p>
            </div>
            <nav class="space-y-0">
                <a href="/my-profile" @click="profileOpen = false"
                   class="flex items-center justify-between py-4 font-sans text-[15px] text-black border-b border-gray-100 hover:text-gray-600 transition-colors">
                    Profile
                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
                <a href="/my-profile?tab=orders" @click="profileOpen = false"
                   class="flex items-center justify-between py-4 font-sans text-[15px] text-black border-b border-gray-100 hover:text-gray-600 transition-colors">
                    My Orders
                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-between py-4 font-sans text-[15px] text-black border-b border-gray-100 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer text-left">
                        Logout
                        <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/></svg>
                    </button>
                </form>
            </nav>
        @else
            <div class="mb-5 pb-5 border-b border-gray-100">
                <p class="font-sans text-sm text-gray-500">Browsing as guest</p>
            </div>
            <nav>
                <a href="{{ route('login') }}" @click="profileOpen = false"
                   class="flex items-center justify-between py-4 font-sans text-[15px] text-black border-b border-gray-100">
                    Login
                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
                <a href="{{ route('register') }}" @click="profileOpen = false"
                   class="flex items-center justify-between py-4 font-sans text-[15px] text-black border-b border-gray-100">
                    Register
                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
            </nav>
        @endauth
    </div>
</div>

</div>{{-- /root Alpine x-data --}}