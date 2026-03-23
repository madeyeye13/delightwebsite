<div>
    {{-- Filter bar --}}
    <div class="flex items-center gap-2 mb-10 flex-wrap" role="navigation" aria-label="Filter by tag">
        <span class="text-2xs font-semibold tracking-[0.08em] uppercase text-neutral-400 dark:text-neutral-600 mr-1">Filter:</span>

        <button
            wire:click="$set('activeTag', '')"
            class="text-2xs font-semibold tracking-[0.05em] uppercase px-3 py-[5px] border cursor-pointer transition-all font-sans {{ $activeTag === '' ? 'bg-ink text-neutral-50 border-ink dark:bg-neutral-50 dark:text-brand-900 dark:border-neutral-50' : 'bg-transparent text-neutral-500 dark:text-neutral-400 border-neutral-300 dark:border-brand-600 hover:text-ink hover:border-ink dark:hover:text-neutral-50 dark:hover:border-neutral-200' }}"
        >All</button>

        @foreach($tags as $tag)
            <button
                wire:click="$set('activeTag', '{{ $tag->slug }}')"
                class="text-2xs font-semibold tracking-[0.05em] uppercase px-3 py-[5px] border cursor-pointer transition-all font-sans {{ $activeTag === $tag->slug ? 'bg-ink text-neutral-50 border-ink dark:bg-neutral-50 dark:text-brand-900 dark:border-neutral-50' : 'bg-transparent text-neutral-500 dark:text-neutral-400 border-neutral-300 dark:border-brand-600 hover:text-ink hover:border-ink dark:hover:text-neutral-50 dark:hover:border-neutral-200' }}"
            >{{ $tag->name }}</button>
        @endforeach
    </div>

    {{-- Search bar --}}
    <div class="mb-8">
        <div class="relative max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input
                wire:model.live.debounce.400ms="search"
                type="search"
                placeholder="Search posts..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-neutral-300 dark:border-brand-600 bg-transparent text-neutral-900 dark:text-neutral-50 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400"
            >
        </div>
    </div>

    {{-- Blog grid --}}
    <section aria-label="Blog posts">
        @if($posts->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-5" role="list">
            @foreach($posts as $post)
            <article
                class="group bg-neutral-50 dark:bg-brand-900 flex flex-col transition-colors duration-200 hover:bg-neutral-100 dark:hover:bg-brand-800 border border-neutral-200 dark:border-brand-700"
                role="listitem"
                itemscope
                itemtype="https://schema.org/BlogPosting"
            >
                <a href="{{ route('blog.show', $post->slug) }}" tabindex="-1" aria-label="{{ $post->title }}">
                    <div class="overflow-hidden aspect-[16/10] bg-neutral-200 dark:bg-brand-800">
                        @if($post->featured_image_url)
                        <img
                            class="w-full h-full object-cover block transition-transform duration-500 group-hover:scale-[1.04]"
                            src="{{ $post->featured_image_url }}"
                            alt="{{ $post->title }}"
                            loading="lazy"
                            width="400" height="250"
                            itemprop="image"
                        >
                        @else
                        <div class="w-full h-full flex items-center justify-center text-neutral-400 dark:text-neutral-600">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                        @endif
                    </div>
                </a>

                <div class="p-5 flex flex-col flex-1 border-t border-neutral-200 dark:border-brand-700">
                    <div class="text-2xs text-neutral-400 dark:text-neutral-600 mb-2 flex items-center gap-1.5">
                        @if($post->author)
                        <span itemprop="author">{{ $post->author }}</span>
                        <span class="text-[8px]" aria-hidden="true">●</span>
                        @endif
                        @if($post->published_at)
                        <time itemprop="datePublished" datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('d M Y') }}</time>
                        @endif
                    </div>

                    <a
                        class="font-display text-base font-bold text-ink dark:text-neutral-50 tracking-tight leading-snug no-underline block mb-2 transition-colors duration-200 hover:text-brand dark:hover:text-brand-400"
                        href="{{ route('blog.show', $post->slug) }}"
                        itemprop="headline"
                    >{{ $post->title }}</a>

                    @if($post->excerpt)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed flex-1 mb-4 line-clamp-3" itemprop="description">{{ $post->excerpt }}</p>
                    @endif

                    <div class="flex items-center justify-between border-t border-neutral-200 dark:border-brand-700 pt-3 mt-auto">
                        <div class="flex gap-1.5 flex-wrap" aria-label="Tags">
                            @foreach($post->tags->take(3) as $tag)
                            <span class="text-[9px] font-bold tracking-[0.08em] uppercase text-neutral-600 dark:text-neutral-400 bg-neutral-100 dark:bg-brand-800 border border-neutral-200 dark:border-brand-700 px-1.5 py-0.5">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        <a
                            class="text-2xs font-semibold text-brand dark:text-brand-400 no-underline inline-flex items-center gap-1 hover:gap-2 whitespace-nowrap transition-[gap] duration-200"
                            href="{{ route('blog.show', $post->slug) }}"
                            aria-label="Read {{ $post->title }}"
                        >Read →</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <nav class="border-t border-neutral-200 dark:border-brand-700 py-8" aria-label="Blog pagination">
            {{ $posts->links() }}
        </nav>

        @else
        <div class="text-center py-24 text-neutral-400 dark:text-neutral-600">
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm">No posts found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
            @if($search || $activeTag)
            <button wire:click="$set('search', ''); $set('activeTag', '')" class="mt-3 text-xs text-brand dark:text-brand-400 hover:underline">Clear filters</button>
            @endif
        </div>
        @endif
    </section>
</div>
