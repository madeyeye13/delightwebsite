<div class="max-w-2xl space-y-8">

    <div class="space-y-6">
        {{-- Summary bar --}}
        <div class="flex items-center gap-6 pb-6 border-b border-neutral-100 dark:border-neutral-800">
            <div class="text-center flex-shrink-0">
                <p class="font-display text-5xl font-extrabold text-neutral-900 dark:text-white leading-none">{{ $reviewCount ? number_format($avgRating, 1) : '—' }}</p>
                <div class="flex items-center justify-center gap-0.5 mt-1.5">
                    @for($s = 1; $s <= 5; $s++)
                    <svg class="w-3.5 h-3.5 {{ $s <= round($avgRating) ? 'text-brand' : 'text-neutral-200 dark:text-neutral-700' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
                <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 mt-1">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</p>
            </div>
            <div class="flex-1 space-y-1.5">
                @foreach([5 => $ratingBreakdown[5], 4 => $ratingBreakdown[4], 3 => $ratingBreakdown[3], 2 => $ratingBreakdown[2], 1 => $ratingBreakdown[1]] as $star => $pct)
                <div class="flex items-center gap-2">
                    <span class="font-sans text-2xs text-neutral-500 dark:text-neutral-400 w-2">{{ $star }}</span>
                    <div class="flex-1 h-1.5 bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full bg-brand" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="font-sans text-2xs text-neutral-400 dark:text-neutral-500 w-6 text-right">{{ $pct }}%</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Individual reviews --}}
        @forelse($reviews as $review)
        <div class="pb-6 border-b border-neutral-100 dark:border-neutral-800 last:border-0">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div>
                    <p class="font-sans text-sm font-semibold text-neutral-900 dark:text-white">{{ $review->user->name }}</p>
                    <p class="font-sans text-2xs text-neutral-400 dark:text-neutral-500">{{ $review->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-0.5 flex-shrink-0">
                    @for($s = 1; $s <= 5; $s++)
                    <svg class="w-3 h-3 {{ $s <= $review->rating ? 'text-brand' : 'text-neutral-200 dark:text-neutral-700' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
            </div>
            @if($review->title)
            <p class="font-sans text-sm font-medium text-neutral-800 dark:text-neutral-200 mb-1">{{ $review->title }}</p>
            @endif
            <p class="font-sans text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ $review->body }}</p>
        </div>
        @empty
        <p class="font-sans text-sm text-neutral-400 dark:text-neutral-500">No reviews yet. Be the first to review this product.</p>
        @endforelse

        {{-- Success notice after submission --}}
        @if($submitted)
        <div class="rounded-sm bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3">
            <p class="font-sans text-sm text-green-700 dark:text-green-300">Thanks for your review! It will appear here once approved.</p>
        </div>
        @endif

        {{-- Write a review --}}
        @if(! $userHasReviewed && ! $submitted)
        <button wire:click="toggleForm" class="inline-flex items-center gap-2 font-sans text-sm font-semibold text-brand dark:text-brand-300 hover:underline">
            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            {{ $showForm ? 'Cancel' : 'Write a Review' }}
        </button>
        @endif

        {{-- Review form (inline toggle) --}}
        @if($showForm && ! $userHasReviewed)
        <form wire:submit="submitReview" class="space-y-5 pt-2">

            {{-- Star picker --}}
            <div>
                <label class="font-sans text-xs font-semibold text-neutral-700 dark:text-neutral-300 block mb-2">Your Rating <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-1">
                    @for($s = 1; $s <= 5; $s++)
                    <button type="button" wire:click="setRating({{ $s }})" class="group focus:outline-none">
                        <svg class="w-6 h-6 transition-colors {{ $s <= $rating ? 'text-brand' : 'text-neutral-200 dark:text-neutral-700 group-hover:text-brand/50' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </button>
                    @endfor
                </div>
                @error('rating') <p class="font-sans text-2xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label for="review-title" class="font-sans text-xs font-semibold text-neutral-700 dark:text-neutral-300 block mb-1.5">Title <span class="font-normal text-neutral-400">(optional)</span></label>
                <input id="review-title" type="text" wire:model="title" placeholder="Summarise your experience" class="w-full font-sans text-sm bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-sm px-3 py-2.5 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand" maxlength="100">
                @error('title') <p class="font-sans text-2xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Body --}}
            <div>
                <label for="review-body" class="font-sans text-xs font-semibold text-neutral-700 dark:text-neutral-300 block mb-1.5">Review <span class="text-red-500">*</span></label>
                <textarea id="review-body" wire:model="body" rows="4" placeholder="Tell others what you think about this product…" class="w-full font-sans text-sm bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-sm px-3 py-2.5 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand resize-none"></textarea>
                @error('body') <p class="font-sans text-2xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 font-sans text-sm font-semibold text-white bg-brand hover:bg-brand/90 disabled:opacity-60 px-5 py-2.5 rounded-sm transition-colors">
                <span wire:loading.remove>Submit Review</span>
                <span wire:loading>Submitting…</span>
            </button>
        </form>
        @endif

    </div>
</div>
