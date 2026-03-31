<div class="space-y-5">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Blog Comments</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Moderate reader comments before they appear on the blog.
                @if($pendingCount > 0)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 dark:bg-amber-400/10 text-amber-700 dark:text-amber-400">
                    {{ $pendingCount }} awaiting review
                </span>
                @endif
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        {{-- Status tabs --}}
        <div class="flex rounded-xl overflow-hidden border border-neutral-200 dark:border-white/[0.08] text-sm">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', '' => 'All'] as $val => $label)
            <button wire:click="$set('statusFilter', '{{ $val }}')"
                class="px-4 py-2 font-medium transition-colors
                    {{ $statusFilter === $val
                        ? 'bg-brand text-white'
                        : 'bg-white dark:bg-[#1C1F27] text-neutral-600 dark:text-white/60 hover:bg-neutral-50 dark:hover:bg-white/[0.04]' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Search --}}
        <div class="relative flex-1 min-w-[220px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name, email, content…"
                class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Author</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Comment</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Post</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors" wire:key="comment-{{ $comment->id }}">
                        <td class="px-4 py-3 align-top">
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $comment->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-white/30">{{ $comment->email }}</p>
                            @if($comment->parent_id)
                            <span class="mt-0.5 inline-flex items-center gap-1 text-[10px] text-purple-500 dark:text-purple-400 font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Reply
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-top max-w-xs">
                            <p class="text-gray-700 dark:text-white/70 text-sm leading-relaxed line-clamp-3">{{ $comment->body }}</p>
                        </td>
                        <td class="px-4 py-3 align-top">
                            @if($comment->post)
                            <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank"
                                class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline line-clamp-2 leading-relaxed max-w-[160px] block">
                                {{ $comment->post->title }}
                            </a>
                            @else
                            <span class="text-xs text-gray-400 dark:text-white/30">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-top text-center">
                            @if($comment->is_approved)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Approved</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 dark:bg-amber-400/10 text-amber-700 dark:text-amber-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-top">
                            <span class="text-xs text-gray-400 dark:text-white/30">{{ $comment->created_at->format('d M Y') }}</span>
                            <p class="text-[10px] text-gray-300 dark:text-white/20">{{ $comment->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3 align-top whitespace-nowrap text-right">
                            @if(! $comment->is_approved)
                            <button wire:click="approveComment({{ $comment->id }})"
                                class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">
                                Approve
                            </button>
                            @else
                            <button wire:click="rejectComment({{ $comment->id }})"
                                class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 transition-colors">
                                Reject
                            </button>
                            @endif
                            <button wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Delete this comment? This cannot be undone."
                                class="ml-3 text-xs font-medium text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center text-gray-400 dark:text-white/30 text-sm">
                            @if($statusFilter === 'pending')
                                No comments awaiting moderation.
                            @elseif($statusFilter === 'approved')
                                No approved comments yet.
                            @else
                                No comments found.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06]">
            {{ $comments->links() }}
        </div>
        @endif
    </div>
</div>
