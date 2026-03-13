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

    <title>@yield('title', 'Admin') — 1stDelightSome Fabrics</title>

    

    {{-- Tailwind + App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

        /* Page fade-in */
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
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

            @include('admin.partials.media-picker')

            {{-- Page content --}}
            <main class="admin-content flex-1 overflow-y-auto transition-colors duration-200">
                <div class="page-enter p-5 lg:p-7 max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>