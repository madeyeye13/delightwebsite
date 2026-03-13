{{--
    Admin Sidebar Partial
    Usage: @include('partials.admin.sidebar')
    Requires Alpine.js & Tailwind CSS
--}}

<aside
    x-data
    :class="$store.sidebar.open ? 'w-64' : 'w-12'"
    class="relative flex flex-col h-full bg-[#0f1117] border-r border-white/[0.06] transition-all duration-300 ease-in-out overflow-hidden select-none z-30"
>

    {{-- ═══════════════════════════════════════
         HEADER ROW
         • Expanded  → logo + brand text + collapse arrow (right)
         • Collapsed → ONLY the expand arrow, centred (desktop only)
    ═══════════════════════════════════════ --}}
    <div class="relative font-display flex items-center h-16 border-b border-white/[0.06] shrink-0">

        {{-- Expanded state --}}
        <div
            x-show="$store.sidebar.open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center w-full px-3"
            style="display:none"
        >
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-5 h-5 object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                <svg style="display:none" viewBox="0 0 24 24" fill="none" class="w-4 h-4 text-emerald-400" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="ml-2.5 flex flex-col leading-tight overflow-hidden whitespace-nowrap">
                <span class="text-white font-semibold text-[13px] tracking-wide font-display">1stDelightSome</span>
                <span class="text-emerald-400 text-[9px] font-medium tracking-widest uppercase">Fabrics</span>
            </div>
            {{-- Collapse button — desktop only --}}
            <button
                @click="$store.sidebar.toggle()"
                title="Collapse sidebar"
                class="ml-auto hidden lg:flex items-center justify-center w-6 h-6 rounded-md text-white/30 hover:text-white hover:bg-white/[0.07] transition-all duration-150 shrink-0"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M3.5 3C3.22386 3 3 3.22386 3 3.5V16.5C3 16.7761 3.22386 17 3.5 17C3.74168 17 3.94372 16.8286 3.99023 16.6006L4 16.5V3.5C4 3.22386 3.77614 3 3.5 3ZM11.8721 5.16504C11.7104 4.98547 11.4476 4.95058 11.2471 5.06836L11.165 5.12793L6.165 9.62793C6.05973 9.72275 6 9.85828 6 10C6 10.1063 6.03326 10.2093 6.09473 10.2939L6.165 10.3721L11.165 14.8721C11.3703 15.0568 11.6873 15.0402 11.8721 14.835C12.0568 14.6297 12.0402 14.3127 11.835 14.1279L7.80371 10.5H16.5C16.7761 10.5 17 10.2761 17 10C17 9.72386 16.7761 9.5 16.5 9.5H7.80371L11.835 5.87207L11.9023 5.79688C12.0407 5.60979 12.0338 5.34471 11.8721 5.16504Z"/>
                </svg>
            </button>
        </div>

        {{-- Collapsed state: just the expand arrow, centred — desktop only --}}
        <div
            x-show="!$store.sidebar.open"
            x-transition:enter="transition ease-out duration-200 delay-100"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="hidden lg:flex w-full items-center justify-center"
            style="display:none"
        >
            <button
                @click="$store.sidebar.toggle()"
                title="Expand sidebar"
                class="flex items-center justify-center w-8 h-8 rounded-lg text-white/40 hover:text-white hover:bg-white/[0.07] transition-all duration-150"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M16.5 3C16.7761 3 17 3.22386 17 3.5V16.5L16.9902 16.6006C16.9437 16.8286 16.7417 17 16.5 17C16.2583 17 16.0563 16.8286 16.0098 16.6006L16 16.5V3.5C16 3.22386 16.2239 3 16.5 3ZM8.12793 5.16504C8.28958 4.98547 8.5524 4.95058 8.75293 5.06836L8.83496 5.12793L13.835 9.62793C13.9403 9.72275 14 9.85828 14 10C14 10.1063 13.9667 10.2093 13.9053 10.2939L13.835 10.3721L8.83496 14.8721C8.62972 15.0568 8.31267 15.0402 8.12793 14.835C7.94322 14.6297 7.95984 14.3127 8.16504 14.1279L12.1963 10.5H3.5C3.22386 10.5 3 10.2761 3 10C3 9.72386 3.22386 9.5 3.5 9.5H12.1963L8.16504 5.87207L8.09766 5.79688C7.95931 5.60979 7.96622 5.34471 8.12793 5.16504Z"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         MAIN NAV
    ═══════════════════════════════════════ --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-2 space-y-0.5 scrollbar-none">

        <p x-show="$store.sidebar.open"
           x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
           class="px-2 pt-1 pb-2 text-[10px] font-semibold tracking-widest uppercase text-white/20" style="display:none">Main</p>

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" title="Dashboard"
           class="nav-item group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </span>
            <span x-show="$store.sidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap flex-1" style="display:none">Dashboard</span>
            @if(request()->routeIs('admin.dashboard'))
                <span x-show="$store.sidebar.open" class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0" style="display:none"></span>
            @endif
        </a>

        {{-- Orders --}}
        <a href="{{ route('admin.orders.index') }}" title="Orders"
           class="nav-item group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path d="M9 12h6M9 16h4"/>
                </svg>
            </span>
            <span x-show="$store.sidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap flex-1" style="display:none">Orders</span>
            <span x-show="$store.sidebar.open"
                  class="bg-emerald-500/15 text-emerald-400 text-[10px] font-semibold px-1.5 py-0.5 rounded-md shrink-0" style="display:none">12</span>
        </a>

        {{-- Products --}}
        <a href="{{ route('admin.products.index') }}" title="Products"
           class="nav-item group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  {{ request()->routeIs('admin.products.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
            </span>
            <span x-show="$store.sidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap" style="display:none">Products</span>
        </a>

        {{-- Media Library --}}
        <a href="{{ route('admin.media.index') }}" title="Media"
           class="nav-item group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  {{ request()->routeIs('admin.media.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </span>
            <span x-show="$store.sidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap" style="display:none">Media</span>
        </a>

        {{-- Users --}}
        <a href="{{ route('admin.users.index') }}" title="Users"
           class="nav-item group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  {{ request()->routeIs('admin.users.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span x-show="$store.sidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap" style="display:none">Users</span>
        </a>

        <div class="my-2 border-t border-white/[0.05]"></div>

        <p x-show="$store.sidebar.open"
           x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
           class="px-2 pt-1 pb-2 text-[10px] font-semibold tracking-widest uppercase text-white/20" style="display:none">System</p>

        {{-- Settings --}}
        <a href="{{ route('admin.settings') }}" title="Settings"
           class="nav-item group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  {{ request()->routeIs('admin.settings') ? 'bg-emerald-500/10 text-emerald-400' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
            </span>
            <span x-show="$store.sidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap" style="display:none">Settings</span>
        </a>

    </nav>

    {{-- ═══════════════════════════════════════
         USER + LOGOUT
    ═══════════════════════════════════════ --}}
    <div class="border-t border-white/[0.06] p-2 shrink-0 space-y-0.5">

        {{-- User info row — only when expanded --}}
        <div x-show="$store.sidebar.open"
             x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="flex items-center gap-2.5 px-2 py-2 rounded-lg" style="display:none">
            <div class="relative shrink-0">
                <div class="w-7 h-7 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-semibold ring-1 ring-emerald-500/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-emerald-400 border border-[#0f1117] rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white/80 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[10px] text-white/30 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Log out"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-red-400/50 hover:text-red-400 hover:bg-red-500/[0.06] transition-all duration-150">
                <span class="flex items-center justify-center w-5 h-5 shrink-0">
                    <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </span>
                <span x-show="$store.sidebar.open"
                      x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="text-[13px] font-medium whitespace-nowrap" style="display:none">Log out</span>
            </button>
        </form>
    </div>

</aside>