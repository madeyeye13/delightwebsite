{{--
    Admin Header Partial
    Usage: @include('partials.admin.header')
    Requires Alpine.js & Tailwind CSS
--}}

<header class="h-14 flex items-center gap-3 px-4 lg:px-5
               bg-white border-b border-gray-100
               dark:bg-[#0d0f14] dark:border-white/[0.06]
               shrink-0 transition-colors duration-200">

    {{-- ── MOBILE: Hamburger only (no sidebar toggle on mobile) ── --}}
    <button
        @click="$store.sidebar.mobileToggle()"
        class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg
               text-gray-500 hover:bg-gray-100
               dark:text-white/50 dark:hover:bg-white/[0.06]
               transition-all"
    >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    {{-- ── PAGE TITLE + BREADCRUMB ── --}}
    <div class="flex flex-col justify-center min-w-0">
        <h1 class="text-[13px] font-semibold leading-none
                   text-gray-900 dark:text-white truncate">
            @yield('page-title', 'Dashboard')
        </h1>
        @hasSection('breadcrumb')
            <div class="flex items-center gap-1 mt-1">
                @yield('breadcrumb')
            </div>
        @endif
    </div>

    {{-- Spacer --}}
    <div class="flex-1"></div>

    {{-- ── ACTION ICONS ── --}}
    <div class="flex items-center gap-0.5">

        {{-- Dark mode toggle --}}
        <button
            @click="
                $store.theme.toggle();
                document.documentElement.classList.toggle('dark', $store.theme.dark);
                localStorage.setItem('theme', $store.theme.dark ? 'dark' : 'light');
            "
            class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-150
                   text-gray-400 hover:text-gray-700 hover:bg-gray-100
                   dark:text-white/40 dark:hover:text-white/80 dark:hover:bg-white/[0.06]"
            title="Toggle theme"
        >
            {{-- Sun — shown in dark mode --}}
            <svg x-show="$store.theme.dark" class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            {{-- Moon — shown in light mode --}}
            <svg x-show="!$store.theme.dark" class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>

        {{-- Notifications --}}
        <livewire:admin.notifications />

        {{-- Divider --}}
        <div class="w-px h-4 bg-gray-200 dark:bg-white/[0.08] mx-1.5"></div>

        {{-- User menu --}}
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg transition-all duration-150
                       hover:bg-gray-100 dark:hover:bg-white/[0.06] group"
            >
                <div class="relative">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600
                                flex items-center justify-center text-white text-[11px] font-bold shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-emerald-400 rounded-full
                                 border-[1.5px] border-white dark:border-[#0d0f14]"></span>
                </div>
                <div class="hidden sm:flex flex-col items-start leading-none">
                    <span class="text-[12px] font-semibold text-gray-800 dark:text-white/80">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </span>
                    <span class="text-[10px] text-gray-400 dark:text-white/30 capitalize">
                        {{ auth()->user()->role ?? 'admin' }}
                    </span>
                </div>
                <svg class="hidden sm:block w-3 h-3 text-gray-400 dark:text-white/30 transition-colors
                            group-hover:text-gray-600 dark:group-hover:text-white/50"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            {{-- User dropdown --}}
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-full mt-2 w-52
                       bg-white border border-gray-100 rounded-xl shadow-xl
                       dark:bg-[#161920] dark:border-white/[0.08] dark:shadow-black/40
                       z-50 overflow-hidden"
                style="display:none"
            >
                <div class="px-4 py-3 border-b border-gray-100 dark:border-white/[0.06]">
                    <p class="text-[13px] font-semibold text-gray-900 dark:text-white">
                        {{ auth()->user()->name ?? 'Admin User' }}
                    </p>
                    <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5 truncate">
                        {{ auth()->user()->email ?? 'admin@example.com' }}
                    </p>
                </div>
                <div class="py-1">
                    <a href="{{ route('account.profile') }}"
                       class="flex items-center gap-2.5 px-4 py-2 text-[13px]
                              text-gray-600 hover:text-gray-900 hover:bg-gray-50
                              dark:text-white/60 dark:hover:text-white dark:hover:bg-white/[0.04]
                              transition-colors">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center gap-2.5 px-4 py-2 text-[13px]
                              text-gray-600 hover:text-gray-900 hover:bg-gray-50
                              dark:text-white/60 dark:hover:text-white dark:hover:bg-white/[0.04]
                              transition-colors">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        Settings
                    </a>
                </div>
                <div class="border-t border-gray-100 dark:border-white/[0.06] py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-2 text-[13px]
                                       text-red-500/70 hover:text-red-500 hover:bg-red-50
                                       dark:hover:bg-red-500/[0.06] transition-colors">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>