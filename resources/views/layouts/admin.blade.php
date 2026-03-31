<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data
    :class="{ 'dark': $store.theme.dark }"
    class="h-full"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#1F6F67" />
    <meta name="author" content="Bezalel Koncept" />

    <link rel="icon" type="image/png" href="/images/logo1.png" />
    <link rel="apple-touch-icon" href="/images/logo1.png" />

    <title>@yield('title', 'Admin') — 1stDelightSome Fabrics</title>

    

    {{-- Tailwind + App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    
    <style>
        /* Fonts loaded via Google Fonts — Plus Jakarta Sans & Manrope */

        /* Scrollbar styling */
        .scrollbar-none { scrollbar-width: none; }
        .scrollbar-none::-webkit-scrollbar { display: none; }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* Sidebar transition */
        [x-cloak] { display: none !important; }

        /* Active nav item left border accent */
        .nav-item.active-nav {
            position: relative;
        }
        .nav-item.active-nav::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: #34d399;
            border-radius: 0 4px 4px 0;
        }

        /* Page fade-in — NOTE: no transform here, because transform creates a new
           stacking context that breaks position:fixed modals inside Livewire
           components (makes them position relative to the animated div, not
           the viewport, so they can't cover the fixed admin header). */
        @keyframes pageFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Nav badge blink — used for new orders alert */
        @keyframes navBadgeBlink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.35; }
        }
        .nav-badge-blink {
            animation: navBadgeBlink 1.1s ease-in-out infinite;
        }
        .page-enter {
            animation: pageFadeIn 0.25s ease-out forwards;
        }

        /* Dark mode content area */
        .dark .admin-content {
            background-color: #0a0c10;
        }
        .admin-content {
            background-color: #f8f9fc;
        }
    </style>

    {{-- Init Alpine stores before Alpine loads --}}
    <script>
        // Theme store
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: localStorage.getItem('theme') === 'dark'
                    || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                toggle() { this.dark = !this.dark; }
            });

            // Apply dark class to <html> immediately
            if (Alpine.store('theme').dark) {
                document.documentElement.classList.add('dark');
            }

            // Sidebar store
            Alpine.store('sidebar', {
                open: localStorage.getItem('sidebar') !== 'closed',
                mobileOpen: false,

                toggle() {
                    this.open = !this.open;
                    localStorage.setItem('sidebar', this.open ? 'open' : 'closed');
                },
                mobileToggle() { this.mobileOpen = !this.mobileOpen; },
                mobileClose() { this.mobileOpen = false; },
            });

            // Toast notification manager
            Alpine.data('adminToastManager', () => ({
                toasts: [],
                addToast({ type = 'success', message = '' }) {
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, type, message, visible: true });
                    setTimeout(() => this.dismiss(id), 4000);
                },
                dismiss(id) {
                    const t = this.toasts.find(x => x.id === id);
                    if (t) {
                        t.visible = false;
                        setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 300);
                    }
                },
            }));

            // Nav badge count store — populated by livewire:admin.nav-counts via
            // the 'nav-counts-updated' browser event (dispatched every 30 s).
            // counts.orders      → pending paid orders
            // counts.orders_new  → pending paid orders since last acknowledged
            // counts.products    → total product count
            // To add a new badge: add a key to NavCounts::getCounts() in PHP,
            // then read $store.navCounts.counts.yourKey in the sidebar.
            Alpine.store('navCounts', {
                counts: { orders: 0, orders_new: 0, products: 0 },
            });
        });

        // Listen for count updates dispatched by the NavCounts Livewire component
        window.addEventListener('nav-counts-updated', (e) => {
            if (e.detail && e.detail.counts) {
                Object.assign(Alpine.store('navCounts').counts, e.detail.counts);
            }
        });

        // Prevent flash of wrong theme
        (function() {
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @stack('styles')

    @livewireStyles
</head>

<body class="h-full overflow-hidden bg-gray-50 dark:bg-[#0a0c10] text-gray-900 dark:text-white antialiased">

    {{-- ═══════════════════════════════════════════════════
         ROOT LAYOUT: sidebar + main column
    ═══════════════════════════════════════════════════ --}}
    <div class="flex h-full w-full overflow-hidden">

        {{-- ── DESKTOP SIDEBAR ─────────────────────────── --}}
        <div class="hidden lg:flex h-full shrink-0 transition-all duration-300">
            @include('partials.admin.sidebar')
        </div>

        {{-- ── MOBILE SIDEBAR ──────────────────────────── --}}
        {{-- Backdrop --}}
        <div
            x-show="$store.sidebar.mobileOpen"
            @click="$store.sidebar.mobileClose()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"
            style="display:none"
        ></div>
        {{-- Drawer --}}
        <div
            x-show="$store.sidebar.mobileOpen"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed top-0 left-0 h-full w-64 z-40 lg:hidden"
            style="display:none"
        >
            @include('partials.admin.sidebar')
        </div>

        {{-- ── MAIN COLUMN ─────────────────────────────── --}}
        <div class="flex flex-col flex-1 min-w-0 h-full overflow-hidden">

            {{-- Header --}}
            @include('partials.admin.header')

            <livewire:admin.media.media-picker />
            <livewire:admin.nav-counts />

            {{-- Page content --}}
            <main class="admin-content flex-1 overflow-y-auto transition-colors duration-200">
                <div class="page-enter p-5 lg:p-7 max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         TOAST NOTIFICATIONS (listens for Livewire dispatch: toast)
    ════════════════════════════════════════════════════ --}}
    <div
        x-data="adminToastManager()"
        @toast.window="addToast($event.detail)"
        class="fixed top-4 right-4 z-[10000] flex flex-col gap-2 pointer-events-none"
        style="max-width:20rem;">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-4"
                class="pointer-events-auto flex items-start gap-3 px-4 py-3 shadow-xl min-w-[260px]"
                :class="{
                    'bg-neutral-900 dark:bg-neutral-50 text-neutral-50 dark:text-neutral-900': toast.type === 'success',
                    'bg-red-600 text-white': toast.type === 'error',
                    'bg-yellow-500 text-white': toast.type === 'warning',
                    'bg-blue-600 text-white': toast.type === 'info',
                }">
                <div class="flex-shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                </div>
                <p class="text-sm font-medium leading-5 flex-1" x-text="toast.message"></p>
                <button @click="dismiss(toast.id)" class="flex-shrink-0 ml-1 opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @stack('scripts')

    @livewireScripts
</body>
</html>