<div
    x-data="{ open: false }"
    x-on:close-notifications.window="open = false"
    class="relative"
    wire:poll.60s
>
    {{-- Bell button --}}
    <button
        @click="open = !open"
        class="relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-150
               text-gray-400 hover:text-gray-700 hover:bg-gray-100
               dark:text-white/40 dark:hover:text-white/80 dark:hover:bg-white/[0.06]"
    >
        <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        @if($this->unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full
                         ring-[1.5px] ring-white dark:ring-[#0d0f14]"></span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-full mt-2 w-72
               bg-white border border-gray-100 rounded-xl shadow-xl
               dark:bg-[#161920] dark:border-white/[0.08] dark:shadow-black/40
               z-50 overflow-hidden"
        style="display:none"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 dark:border-white/[0.06]">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-gray-900 dark:text-white">Notifications</span>
                @if($this->unreadCount > 0)
                    <span class="inline-flex items-center justify-center w-4 h-4 text-[9px] font-bold bg-red-500 text-white rounded-full">
                        {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                    </span>
                @endif
            </div>
            @if(count($this->notifications) > 0)
                <button wire:click="markAllRead" @click="open = false"
                        class="text-[11px] text-emerald-500 font-medium cursor-pointer hover:text-emerald-400 transition-colors">
                    Mark all read
                </button>
            @endif
        </div>

        {{-- Items --}}
        <div class="divide-y divide-gray-50 dark:divide-white/[0.04] max-h-64 overflow-y-auto">
            @forelse($this->notifications as $notif)
                <a href="{{ $notif['url'] }}"
                   class="flex items-start gap-3 px-4 py-3
                          hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5
                                @if($notif['type'] === 'order') bg-emerald-500/10
                                @elseif($notif['type'] === 'product') bg-amber-500/10
                                @elseif($notif['type'] === 'contact') bg-red-500/10
                                @elseif($notif['type'] === 'newsletter') bg-purple-500/10
                                @else bg-blue-500/10 @endif">
                        @if($notif['type'] === 'order')
                            <svg class="w-3 h-3 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                            </svg>
                        @elseif($notif['type'] === 'product')
                            <svg class="w-3 h-3 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                        @elseif($notif['type'] === 'contact')
                            <svg class="w-3 h-3 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        @elseif($notif['type'] === 'newsletter')
                            <svg class="w-3 h-3 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        @else
                            <svg class="w-3 h-3 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] text-gray-700 dark:text-white/70 leading-snug">{{ $notif['message'] }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-white/30 mt-0.5">{{ $notif['time'] }}</p>
                    </div>
                    @if($this->unreadCount > 0)
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0 mt-1.5"></span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-6 text-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-white/20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p class="text-xs text-gray-400 dark:text-white/30">All caught up!</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2 border-t border-gray-100 dark:border-white/[0.06] text-center">
            <a href="{{ route('admin.orders.index') }}" class="text-[11px] text-gray-400 dark:text-white/30 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
                View all orders
            </a>
        </div>
    </div>
</div>
