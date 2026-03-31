{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      :class="{ 'dark': $store.theme?.dark }"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1F6F67">
    <meta name="author" content="Bezalel Koncept">
    <title>@yield('title', 'My Account') — {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="/images/logo1.png">
    <link rel="apple-touch-icon" href="/images/logo1.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        .dash-scroll::-webkit-scrollbar { width: 3px; }
        .dash-scroll::-webkit-scrollbar-track { background: transparent; }
        .dash-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 9999px; }
        .dash-scroll { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.06) transparent; }
    </style>
</head>
<body class="font-sans antialiased bg-[#0f1117] text-white h-full">

{{--
    Alpine store for user sidebar collapse state.
    Uses "userSidebar" (separate from admin's "sidebar" store to avoid conflicts).
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('userSidebar', {
            open: window.innerWidth >= 1024,
            toggle() { this.open = !this.open; },
        });
    });
</script>

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ── --}}
    @include('partials.user.sidebar')

    {{-- ── Mobile sidebar backdrop ── --}}
    <div x-data
         x-show="$store.userSidebar.open && window.innerWidth < 1024"
         x-cloak
         @click="$store.userSidebar.open = false"
         class="fixed inset-0 bg-black/60 z-20 lg:hidden">
    </div>

    {{-- ── Main area ── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- ── Top bar ── --}}
        <header class="h-14 border-b border-white/[0.06] bg-[#0f1117] flex items-center px-4 gap-3 shrink-0">

            {{-- Mobile hamburger --}}
            <button x-data @click="$store.userSidebar.toggle()"
                    class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg
                           text-white/40 hover:text-white hover:bg-white/[0.07] transition-all">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-4 h-4">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
            </button>

            <div class="flex-1"></div>

            {{-- User avatar --}}
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-brand-500/20 flex items-center justify-center
                            text-brand-400 text-xs font-semibold ring-1 ring-brand-500/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="hidden sm:block text-xs font-medium text-white/60 truncate max-w-[140px]">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </header>

        {{-- ── Page content ── --}}
        <main class="flex-1 overflow-y-auto dash-scroll bg-[#0f1117]">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>