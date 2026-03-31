<div>
    {{-- Bulk action bar --}}
    @if(count($selectedIds) > 0)
    <div class="mb-3 flex items-center gap-3 px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
        <span class="text-sm font-medium text-red-700 dark:text-red-400">{{ count($selectedIds) }} subscriber(s) selected</span>
        <button wire:click="deleteSelected()"
            wire:confirm="Delete {{ count($selectedIds) }} subscriber(s)? This cannot be undone."
            class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/></svg>
            Delete Selected
        </button>
        <button wire:click="clearSelection" class="text-xs text-red-500 dark:text-red-400 hover:underline">Clear</button>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">Active</p>
            <p class="text-2xl font-bold text-emerald-500">{{ $this->stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">Unsubscribed</p>
            <p class="text-2xl font-bold text-gray-500 dark:text-white/40">{{ $this->stats['unsubscribed'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">This Week</p>
            <p class="text-2xl font-bold text-blue-500">{{ $this->stats['this_week'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">
        {{-- Status tabs --}}
        <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/[0.05] rounded-xl p-1">
            @foreach(['active' => 'Active', 'unsubscribed' => 'Unsubscribed', 'all' => 'All'] as $val => $label)
            <button wire:click="$set('statusFilter', '{{ $val }}')"
                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-150
                       {{ $statusFilter === $val
                          ? 'bg-white dark:bg-[#1C1F27] text-gray-900 dark:text-white shadow-sm'
                          : 'text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/60' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search email address…"
                class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 dark:border-white/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 bg-white dark:bg-white/[0.05] cursor-pointer">
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Email</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Subscribed</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Unsubscribed</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                    @forelse($subscribers as $subscriber)
                    <tr wire:key="sub-{{ $subscriber->id }}" class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $subscriber->id }}"
                                class="rounded border-gray-300 dark:border-white/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 bg-white dark:bg-white/[0.05] cursor-pointer">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center shrink-0">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($subscriber->email, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $subscriber->email }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($subscriber->unsubscribed_at === null)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-white/40">
                                    Unsubscribed
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($subscriber->subscribed_at)
                                <p class="text-xs text-gray-700 dark:text-white/60">{{ $subscriber->subscribed_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-white/25 mt-0.5">{{ $subscriber->subscribed_at->diffForHumans() }}</p>
                            @else
                                <span class="text-xs text-gray-400 dark:text-white/30">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($subscriber->unsubscribed_at)
                                <p class="text-xs text-gray-500 dark:text-white/40">{{ $subscriber->unsubscribed_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-white/25 mt-0.5">{{ $subscriber->unsubscribed_at->diffForHumans() }}</p>
                            @else
                                <span class="text-xs text-gray-400 dark:text-white/30">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if($subscriber->unsubscribed_at !== null)
                                    <button wire:click="resubscribe({{ $subscriber->id }})"
                                        title="Resubscribe"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </button>
                                @else
                                    <button wire:click="unsubscribe({{ $subscriber->id }})"
                                        wire:confirm="Unsubscribe {{ $subscriber->email }}?"
                                        title="Unsubscribe"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                @endif
                                <button wire:click="delete({{ $subscriber->id }})"
                                    wire:confirm="Permanently delete {{ $subscriber->email }}?"
                                    title="Delete"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all duration-150">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-14 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/[0.05] flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400 dark:text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">No subscribers found</p>
                                    <p class="text-xs text-gray-400 dark:text-white/30 mt-0.5">
                                        @if($search) No results for "{{ $search }}"
                                        @else No newsletter subscribers yet
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($subscribers->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06]">
            {{ $subscribers->links() }}
        </div>
        @endif
    </div>
</div>
