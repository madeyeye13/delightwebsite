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
        <meta property="og:title" content="@yield('title', $seoTitle)">
        <meta property="og:site_name" content="{{ config('app.name') }}">


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

        @livewireScripts
    </body>
</html>
