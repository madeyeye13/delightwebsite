<div>

    <h2 class="font-display text-lg font-bold text-ink dark:text-neutral-50 tracking-tight mb-8">
        Comments
        @if($comments->isNotEmpty())
            <span class="text-neutral-400 dark:text-neutral-600 font-normal text-sm">({{ $comments->count() }})</span>
        @endif
    </h2>

    {{-- ── Existing approved comments ─────────────────────── --}}
    @if($comments->isNotEmpty())
        <div class="flex flex-col gap-0 divide-y divide-neutral-200 dark:divide-brand-700 mb-10">

            @foreach($comments as $comment)
            <div class="py-6 first:pt-0">
                {{-- Parent comment --}}
                <div class="flex gap-4">
                    <div class="w-9 h-9 flex-shrink-0 bg-brand-100 dark:bg-brand-800 flex items-center justify-center">
                        <span class="text-2xs font-bold text-brand dark:text-brand-400">{{ strtoupper(substr($comment->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xs font-bold text-ink dark:text-neutral-100">{{ $comment->name }}</span>
                            @if($comment->is_author_reply)
                                <span class="text-[9px] font-bold tracking-[0.08em] uppercase text-brand dark:text-brand-400 border border-brand dark:border-brand-400 px-1.5 py-0.5">Author</span>
                            @endif
                            <span class="text-2xs text-neutral-400 dark:text-neutral-600">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed mb-2">{{ $comment->body }}</p>
                        <button
                            wire:click="setReplyTo({{ $comment->id }}, '{{ e($comment->name) }}')"
                            class="text-2xs font-semibold text-neutral-400 dark:text-neutral-600 hover:text-brand dark:hover:text-brand-400 transition-colors duration-150 bg-transparent border-none cursor-pointer p-0 font-sans">
                            ↩ Reply
                        </button>
                    </div>
                </div>

                {{-- Replies --}}
                @if($comment->replies->isNotEmpty())
                    <div class="mt-4 ml-[52px] flex flex-col gap-4 border-l-2 border-neutral-200 dark:border-brand-700 pl-4">
                        @foreach($comment->replies as $reply)
                        <div class="flex gap-3">
                            <div class="w-7 h-7 flex-shrink-0 bg-neutral-100 dark:bg-brand-700 flex items-center justify-center">
                                <span class="text-[9px] font-bold text-neutral-500 dark:text-neutral-400">{{ strtoupper(substr($reply->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1.5">
                                    <span class="text-xs font-bold text-ink dark:text-neutral-100">{{ $reply->name }}</span>
                                    @if($reply->is_author_reply)
                                        <span class="text-[9px] font-bold tracking-[0.08em] uppercase text-brand dark:text-brand-400 border border-brand dark:border-brand-400 px-1.5 py-0.5">Author</span>
                                    @endif
                                    <span class="text-2xs text-neutral-400 dark:text-neutral-600">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">{{ $reply->body }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endforeach

        </div>
    @else
        <p class="text-sm text-neutral-400 dark:text-neutral-600 mb-10">No comments yet. Be the first to share your thoughts!</p>
    @endif

    {{-- ── Comment form ─────────────────────────────────────── --}}
    <div class="border border-neutral-200 dark:border-brand-700 p-6 bg-white dark:bg-brand-800">

        @if($submitted)
            <div class="flex items-start gap-3 p-4 bg-brand-50 dark:bg-brand-900 border border-brand-200 dark:border-brand-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand dark:text-brand-400 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-ink dark:text-neutral-100 mb-0.5">Comment submitted!</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Your comment is awaiting moderation and will appear once approved.</p>
                </div>
            </div>
        @else
            <h3 class="font-display text-base font-bold text-ink dark:text-neutral-50 tracking-tight mb-5">
                @if($replyToId)
                    Replying to <span class="text-brand dark:text-brand-400">{{ $replyToName }}</span>
                    <button wire:click="cancelReply" class="ml-2 text-xs font-normal text-neutral-400 dark:text-neutral-600 hover:text-ink dark:hover:text-neutral-200 transition-colors duration-150 cursor-pointer bg-transparent border-none p-0 font-sans">
                        (Cancel)
                    </button>
                @else
                    Leave a comment
                @endif
            </h3>

            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-2xs font-semibold tracking-[0.08em] uppercase text-neutral-500 dark:text-neutral-400" for="comment-name">Name</label>
                        <input
                            id="comment-name"
                            type="text"
                            wire:model="name"
                            placeholder="Your name"
                            class="w-full px-3 py-2.5 text-sm border border-neutral-200 dark:border-brand-700 bg-neutral-50 dark:bg-brand-900 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150 font-sans @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-2xs font-semibold tracking-[0.08em] uppercase text-neutral-500 dark:text-neutral-400" for="comment-email">Email <span class="normal-case font-normal text-neutral-400">(not published)</span></label>
                        <input
                            id="comment-email"
                            type="email"
                            wire:model="email"
                            placeholder="your@email.com"
                            class="w-full px-3 py-2.5 text-sm border border-neutral-200 dark:border-brand-700 bg-neutral-50 dark:bg-brand-900 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150 font-sans @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-2xs font-semibold tracking-[0.08em] uppercase text-neutral-500 dark:text-neutral-400" for="comment-body">Comment</label>
                    <textarea
                        id="comment-body"
                        wire:model="body"
                        rows="4"
                        placeholder="Share your thoughts..."
                        class="w-full px-3 py-2.5 text-sm border border-neutral-200 dark:border-brand-700 bg-neutral-50 dark:bg-brand-900 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150 resize-none font-sans @error('body') border-red-400 @enderror"></textarea>
                    @error('body')
                        <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink dark:bg-neutral-50 text-neutral-50 dark:text-brand-900 text-xs font-bold tracking-[0.06em] uppercase font-sans cursor-pointer border-none hover:bg-brand dark:hover:bg-brand-100 transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="submit">Post Comment</span>
                        <span wire:loading wire:target="submit">Posting...</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" wire:loading.remove wire:target="submit">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    </div>

</div>
