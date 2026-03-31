<!-- resources/views/layouts/custom.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data @if($alwaysShowHeaderBg ?? false)data-header-bg="1"@endif :class="{ 'dark': $store.theme.dark }" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $seoTitle       = \App\Models\AppSetting::get('seo_title', config('app.name'));
            $seoDescription = \App\Models\AppSetting::get('seo_description');
            $seoKeywords    = \App\Models\AppSetting::get('seo_keywords');
            $seoNoindex     = (bool) \App\Models\AppSetting::get('seo_noindex', '0');
        @endphp

        <title>@yield('title', $seoTitle)</title>
        @if($seoDescription)
        <meta name="description" content="{{ $seoDescription }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        @endif
        @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
        @endif
        <meta name="robots" content="{{ $seoNoindex ? 'noindex, nofollow' : 'index, follow' }}">
        <meta name="theme-color" content="#1F6F67">
        <meta name="author" content="Bezalel Koncept">
        <meta property="og:title" content="@yield('title', $seoTitle)">
        <meta property="og:site_name" content="{{ config('app.name') }}">

        <link rel="icon" type="image/png" href="/images/logo1.png">
        <link rel="apple-touch-icon" href="/images/logo1.png">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            [x-cloak] { display: none !important; }
        </style>
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('partials.frontend.cart-panel')
            @include('partials.frontend.header', ['alwaysShowHeaderBg' => $alwaysShowHeaderBg ?? false])
            <livewire:frontend.cart-sync />

            <!-- Page Content -->
            <main>
                @yield('content')
                
            </main>
        </div>

        @include('partials.frontend.footer')

        {{-- ── FRONTEND TOAST NOTIFICATIONS ── --}}
        <div
            x-data="{
                toasts: [],
                addToast({ type, message }) {
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, type, message, visible: true });
                    setTimeout(() => this.dismiss(id), 5000);
                },
                dismiss(id) {
                    const t = this.toasts.find(x => x.id === id);
                    if (t) { t.visible = false; setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 300); }
                },
            }"
            @toast.window="addToast($event.detail)"
            class="fixed top-4 right-4 z-[10000] flex flex-col gap-2 pointer-events-none"
            style="max-width:22rem;"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="toast.visible"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border"
                    :class="{
                        'bg-white border-emerald-200 text-emerald-800': toast.type === 'success',
                        'bg-white border-red-200 text-red-800': toast.type === 'error',
                        'bg-white border-blue-200 text-blue-800': toast.type === 'info',
                    }"
                    style="display:none"
                >
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
                    </template>
                    <span class="text-xs font-medium leading-relaxed" x-text="toast.message"></span>
                    <button @click="dismiss(toast.id)" class="ml-auto shrink-0 text-neutral-400 hover:text-neutral-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        {{-- ── AUTH MODAL — shown when guests click wishlist / review ── --}}
        <div
            x-data="{ open: false }"
            @open-auth-modal.window="open = true"
            x-show="open"
            x-cloak
            style="display:none"
        >
            {{-- Backdrop --}}
            <div
                class="fixed inset-0 z-[9000] bg-black/60 backdrop-blur-sm"
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="open = false"
            ></div>

            {{-- Panel --}}
            <div
                class="fixed inset-0 z-[9001] flex items-center justify-center px-4"
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @keydown.escape.window="open = false"
            >
                <div class="relative w-full max-w-sm bg-white rounded-xl shadow-2xl overflow-hidden">
                    {{-- Brand bar --}}
                    <div class="h-1 w-full bg-brand-500"></div>

                    {{-- Close --}}
                    <button
                        @click="open = false"
                        class="absolute top-4 right-4 p-1 rounded-md text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors"
                        aria-label="Close"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="px-8 pt-8 pb-8">
                        {{-- Icon --}}
                        <div class="flex justify-center mb-4">
                            <div class="w-14 h-14 rounded-full bg-brand-50 flex items-center justify-center">
                                <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-center text-lg font-semibold text-neutral-900 font-display mb-1">
                            Sign in to continue
                        </h3>
                        <p class="text-center text-sm text-neutral-500 mb-7">
                            You need an account to save wishlists and leave reviews.
                        </p>

                        <div class="flex flex-col gap-3">
                            <a
                                href="{{ route('login') }}"
                                class="flex items-center justify-center gap-2 w-full rounded-lg bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium py-2.5 transition-colors duration-200"
                            >
                                Sign in
                            </a>
                            <a
                                href="{{ route('register') }}"
                                class="flex items-center justify-center gap-2 w-full rounded-lg border border-neutral-200 hover:border-brand-300 hover:bg-brand-50 text-neutral-700 hover:text-brand-700 text-sm font-medium py-2.5 transition-colors duration-200"
                            >
                                Create an account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── /AUTH MODAL ── --}}

        @livewireScripts
    </body>
</html>
