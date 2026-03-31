<div>
    {{-- Bulk action bar --}}
    @if(count($selectedIds) > 0)
    <div class="mb-3 flex items-center gap-3 px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
        <span class="text-sm font-medium text-red-700 dark:text-red-400">{{ count($selectedIds) }} message(s) selected</span>
        <button wire:click="deleteSelected()"
            wire:confirm="Delete {{ count($selectedIds) }} message(s)? This cannot be undone."
            class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/></svg>
            Delete Selected
        </button>
        <button wire:click="clearSelection" class="text-xs text-red-500 dark:text-red-400 hover:underline">Clear</button>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-5">
        {{-- Main panel --}}
        <div class="flex-1 min-w-0">
            {{-- Stat bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $contacts->total() }}</p>
                </div>
                <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">Unread</p>
                    <p class="text-2xl font-bold text-red-500">{{ $this->unreadCount }}</p>
                </div>
                <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1">Read</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ max(0, $contacts->total() - $this->unreadCount) }}</p>
                </div>
                <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl px-4 py-3 flex items-center gap-2">
                    @if($this->unreadCount > 0)
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse shrink-0"></span>
                        <p class="text-[13px] font-medium text-red-500">Needs attention</p>
                    @else
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <p class="text-[13px] font-medium text-emerald-500">All caught up</p>
                    @endif
                </div>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-3 mb-5">
                {{-- Status tabs --}}
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/[0.05] rounded-xl p-1">
                    @foreach(['unread' => 'Unread', 'read' => 'Read', 'all' => 'All'] as $val => $label)
                    <button wire:click="$set('statusFilter', '{{ $val }}')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-150
                               {{ $statusFilter === $val
                                  ? 'bg-white dark:bg-[#1C1F27] text-gray-900 dark:text-white shadow-sm'
                                  : 'text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/60' }}">
                        {{ $label }}
                        @if($val === 'unread' && $this->unreadCount > 0)
                            <span class="ml-1 inline-flex items-center justify-center w-4 h-4 text-[9px] font-bold bg-red-500 text-white rounded-full">
                                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                            </span>
                        @endif
                    </button>
                    @endforeach
                </div>

                {{-- Search --}}
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name, email, message…"
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
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Sender</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Message</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Date</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 w-24"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                            @forelse($contacts as $contact)
                            <tr wire:key="contact-{{ $contact->id }}"
                                class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors cursor-pointer {{ $contact->read_at === null ? 'bg-blue-50/30 dark:bg-blue-500/[0.03]' : '' }}"
                                wire:click="viewMessage({{ $contact->id }})">
                                <td class="px-4 py-3" wire:click.stop>
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $contact->id }}"
                                        class="rounded border-gray-300 dark:border-white/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 bg-white dark:bg-white/[0.05] cursor-pointer">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shrink-0">
                                            <span class="text-white text-xs font-bold">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm {{ $contact->read_at === null ? 'font-bold' : '' }}">{{ $contact->name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-white/30">{{ $contact->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-sm text-gray-600 dark:text-white/60 truncate">{{ Str::limit($contact->message, 80) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-gray-500 dark:text-white/40">{{ $contact->created_at->format('M d, Y') }}</p>
                                    <p class="text-[10px] text-gray-400 dark:text-white/25 mt-0.5">{{ $contact->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($contact->read_at === null)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            Unread
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-white/40">
                                            Read
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" wire:click.stop>
                                    <div class="flex items-center justify-end gap-1">
                                        @if($contact->read_at === null)
                                            <button wire:click="markAsRead({{ $contact->id }})"
                                                title="Mark as read"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all duration-150">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        @else
                                            <button wire:click="markAsUnread({{ $contact->id }})"
                                                title="Mark as unread"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-all duration-150">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </button>
                                        @endif
                                        <button wire:click="delete({{ $contact->id }})"
                                            wire:confirm="Delete this message from {{ $contact->name }}?"
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
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">No messages found</p>
                                            <p class="text-xs text-gray-400 dark:text-white/30 mt-0.5">
                                                @if($search) No results for "{{ $search }}"
                                                @elseif($statusFilter === 'unread') All messages have been read
                                                @else No contact messages yet
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
                @if($contacts->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06]">
                    {{ $contacts->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Message detail panel --}}
        @if($viewing)
        <div class="w-full lg:w-[380px] shrink-0"
             x-data
             x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">
            <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden sticky top-4">
                {{-- Panel header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Message Detail</p>
                    <button wire:click="closeMessage"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Sender info --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shrink-0">
                            <span class="text-white text-sm font-bold">{{ strtoupper(substr($viewing->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $viewing->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-white/40">{{ $viewing->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $viewing->created_at->format('M d, Y \a\t g:i A') }}
                    </div>
                </div>

                {{-- Message body --}}
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-2">Message</p>
                    <p class="text-sm text-gray-700 dark:text-white/70 leading-relaxed whitespace-pre-wrap">{{ $viewing->message }}</p>
                </div>

                {{-- Actions --}}
                <div class="px-5 py-4 border-t border-gray-100 dark:border-white/[0.06] flex flex-col gap-2">
                    <a href="mailto:{{ $viewing->email }}?subject=Re: Your message to 1st Delightsome"
                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Reply via Email
                    </a>
                    <div class="flex gap-2">
                        @if($viewing->read_at !== null)
                            <button wire:click="markAsUnread({{ $viewing->id }})"
                                class="flex-1 px-3 py-2 text-xs font-medium border border-gray-200 dark:border-white/[0.08] text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white rounded-xl transition-colors">
                                Mark Unread
                            </button>
                        @endif
                        <button wire:click="delete({{ $viewing->id }})"
                            wire:confirm="Delete this message?"
                            class="flex-1 px-3 py-2 text-xs font-medium border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
