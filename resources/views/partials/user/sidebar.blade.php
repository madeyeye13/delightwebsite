{{-- resources/views/partials/user/sidebar.blade.php --}}
<style>
    .usidebar-scroll::-webkit-scrollbar { width: 3px; }
    .usidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .usidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 9999px; }
    .usidebar-scroll { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.07) transparent; }
</style>

<aside
    x-data
    :class="$store.userSidebar.open ? 'w-60' : 'w-12'"
    class="relative flex flex-col h-full bg-[#0f1117] border-r border-white/[0.06]
           transition-all duration-300 ease-in-out overflow-hidden select-none z-30
           fixed lg:relative lg:translate-x-0
           lg:flex"
    :style="!$store.userSidebar.open && window.innerWidth < 1024 ? 'transform: translateX(-100%)' : ''"
>

    {{-- ── Header ── --}}
    <div class="relative flex items-center h-14 border-b border-white/[0.06] shrink-0">

        {{-- Expanded --}}
        <div x-show="$store.userSidebar.open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="flex items-center w-full px-3" style="display:none">

            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-brand-500/10 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-5 h-5 object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                {{-- Fallback icon --}}
                <svg style="display:none" viewBox="0 0 24 24" fill="none" class="w-4 h-4 text-brand-400"
                     stroke="currentColor" stroke-width="2">
                    <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>

            <div class="ml-2.5 flex flex-col leading-tight overflow-hidden whitespace-nowrap">
                <span class="text-white font-semibold text-[13px] tracking-wide font-display">My Account</span>
                <span class="text-brand-400 text-[9px] font-medium tracking-widest uppercase">1stDelightSome</span>
            </div>

            <button @click="$store.userSidebar.toggle()"
                    class="ml-auto hidden lg:flex items-center justify-center w-6 h-6 rounded-md
                           text-white/30 hover:text-white hover:bg-white/[0.07] transition-all shrink-0">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M3.5 3C3.22 3 3 3.22 3 3.5v13c0 .28.22.5.5.5.24 0 .44-.17.49-.4L4 16.5v-13C4 3.22 3.78 3 3.5 3zM11.87 5.17a.75.75 0 0 0-1.04 0L6.17 9.63A.75.75 0 0 0 6 10c0 .11.03.21.09.29l.08.08 4.66 4.5a.75.75 0 0 0 1.04-1.08L8.3 10.5H16.5a.5.5 0 0 0 0-1H8.3l3.57-3.29a.75.75 0 0 0 0-1.04z"/>
                </svg>
            </button>
        </div>

        {{-- Collapsed (desktop only) --}}
        <div x-show="!$store.userSidebar.open"
             x-transition:enter="transition ease-out duration-200 delay-100"
             x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="hidden lg:flex w-full items-center justify-center" style="display:none">
            <button @click="$store.userSidebar.toggle()"
                    class="flex items-center justify-center w-8 h-8 rounded-lg
                           text-white/40 hover:text-white hover:bg-white/[0.07] transition-all">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M16.5 3c.28 0 .5.22.5.5v13c0 .28-.22.5-.5.5a.5.5 0 0 1-.49-.4L16 16.5v-13c0-.28.22-.5.5-.5zM8.13 5.17a.75.75 0 0 1 1.04 0l4.66 4.46c.11.09.17.22.17.37 0 .11-.03.21-.09.29l-.08.08-4.66 4.5a.75.75 0 0 1-1.04-1.08L11.7 10.5H3.5a.5.5 0 0 1 0-1h8.2L8.13 6.21a.75.75 0 0 1 0-1.04z"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-2 space-y-0.5 usidebar-scroll">

        @php
            $navItems = [
                [
                    'route'  => 'account.orders',
                    'label'  => 'Orders',
                    'active' => request()->routeIs('account.orders*'),
                    'icon'   => '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>',
                ],
                [
                    'route'  => 'account.wishlist',
                    'label'  => 'Wishlist',
                    'active' => request()->routeIs('account.wishlist'),
                    'icon'   => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
                ],
                [
                    'route'  => 'account.profile',
                    'label'  => 'Profile',
                    'active' => request()->routeIs('account.profile'),
                    'icon'   => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
                ],
                [
                    'route'  => 'account.referral',
                    'label'  => 'Referral & Rewards',
                    'active' => request()->routeIs('account.referral'),
                    'icon'   => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
                ],
                [
                    'route'  => 'account.gift-cards',
                    'label'  => 'Gift Cards',
                    'active' => request()->routeIs('account.gift-cards'),
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>',
                ],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
               class="group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                      {{ $item['active']
                          ? 'bg-brand-500/10 text-brand-400'
                          : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}">

                <span class="flex items-center justify-center w-5 h-5 shrink-0">
                    <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8">
                        {!! $item['icon'] !!}
                    </svg>
                </span>

                <span x-show="$store.userSidebar.open"
                      x-transition:enter="transition ease-out duration-150 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="text-[13px] font-medium whitespace-nowrap" style="display:none">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        <div class="my-2 border-t border-white/[0.05]"></div>

        {{-- Back to shop --}}
        <a href="{{ route('shop.index') }}" title="Back to Shop"
           class="group flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-150
                  text-white/30 hover:text-white/60 hover:bg-white/[0.04]">
            <span class="flex items-center justify-center w-5 h-5 shrink-0">
                <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <path d="M3 6h18M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </span>
            <span x-show="$store.userSidebar.open"
                  x-transition:enter="transition ease-out duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="text-[13px] font-medium whitespace-nowrap" style="display:none">
                Back to Shop
            </span>
        </a>

    </nav>

    {{-- ── User + Logout ── --}}
    <div class="border-t border-white/[0.06] p-2 shrink-0 space-y-0.5">

        <div x-show="$store.userSidebar.open"
             x-transition:enter="transition ease-out duration-150 delay-75"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="flex items-center gap-2.5 px-2 py-2 rounded-lg" style="display:none">
            <div class="relative shrink-0">
                <div class="w-7 h-7 rounded-full bg-brand-500/20 flex items-center justify-center
                            text-brand-400 text-xs font-semibold ring-1 ring-brand-500/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-brand-400 border border-[#0f1117] rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white/80 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-white/30 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Log out"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded-lg
                           text-red-400/50 hover:text-red-400 hover:bg-red-500/[0.06]
                           transition-all duration-150">
                <span class="flex items-center justify-center w-5 h-5 shrink-0">
                    <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </span>
                <span x-show="$store.userSidebar.open"
                      x-transition:enter="transition ease-out duration-150 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="text-[13px] font-medium whitespace-nowrap" style="display:none">
                    Log out
                </span>
            </button>
        </form>
    </div>

</aside>