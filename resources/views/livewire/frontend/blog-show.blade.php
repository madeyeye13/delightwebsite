<div class="bg-neutral-50 dark:bg-brand-900 min-h-screen" itemscope itemtype="https://schema.org/BlogPosting">

    {{-- ── HEADER BAND ──────────────────────────────────────── --}}
    <div class="pt-24 border-b border-neutral-200 dark:border-brand-700">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 lg:px-16 py-10">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-2xs text-neutral-400 dark:text-neutral-600 mb-8" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-ink dark:hover:text-neutral-200 transition-colors duration-150">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-ink dark:hover:text-neutral-200 transition-colors duration-150">Blog</a>
                <span aria-hidden="true">/</span>
                <span class="text-neutral-500 dark:text-neutral-400 truncate max-w-[200px] sm:max-w-xs">{{ $post->title }}</span>
            </nav>

            {{-- Category tag --}}
            @if($post->category)
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[9px] font-bold tracking-[0.1em] uppercase text-accent border border-accent px-2 py-0.5">{{ $post->category->name }}</span>
                </div>
            @endif

            {{-- Title --}}
            <h1 class="font-display text-[clamp(22px,3.5vw,38px)] font-extrabold text-ink dark:text-neutral-50 tracking-tight leading-tight max-w-[800px] mb-4" itemprop="headline">
                {{ $post->title }}
            </h1>

            {{-- Excerpt --}}
            @if($post->excerpt)
                <p class="text-base text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-[680px]" itemprop="description">
                    {{ $post->excerpt }}
                </p>
            @endif

        </div>
    </div>

    {{-- ── MAIN IMAGE + META ────────────────────────────────── --}}
    <div class="max-w-[1200px] mx-auto px-5 sm:px-8 lg:px-16">

        {{-- Main image --}}
        @if($post->featured_image_url)
            <div class="mt-10 mb-0 overflow-hidden border border-neutral-200 dark:border-brand-700">
                <img class="w-full h-[320px] sm:h-[420px] lg:h-[480px] object-cover block"
                     src="{{ $post->featured_image_url }}"
                     alt="{{ $post->title }}"
                     itemprop="image"
                     width="1200" height="480">
            </div>
        @endif

        {{-- Meta bar: tags + author + date --}}
        <div class="flex flex-wrap items-center justify-between gap-3 py-4 mb-10 border-b border-neutral-200 dark:border-brand-700">
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($post->tags as $tag)
                    <span class="text-[9px] font-bold tracking-[0.08em] uppercase text-neutral-600 dark:text-neutral-400 bg-neutral-100 dark:bg-brand-800 border border-neutral-200 dark:border-brand-700 px-2 py-0.5">{{ $tag->name }}</span>
                @endforeach
            </div>
            <div class="flex items-center gap-2 text-2xs text-neutral-400 dark:text-neutral-600">
                @if($post->author)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                    <span itemprop="author">{{ $post->author }}</span>
                    <span class="text-[8px]" aria-hidden="true">●</span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5C3.9 4 3 4.9 3 6v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                <time itemprop="datePublished" datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->format('d M Y') }}
                </time>
                <span class="text-[8px]" aria-hidden="true">●</span>
                <span>{{ $post->view_count }} views</span>
            </div>
        </div>

        {{-- ── TWO-COLUMN CONTENT GRID ────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-10 xl:gap-16 pb-20">

            {{-- ─────────────────────────────────────────────
                 LEFT: Post content + prev/next + engage + comments
                 ───────────────────────────────────────────── --}}
            <div class="min-w-0">

                {{-- Post body --}}
                <div class="post-prose" itemprop="articleBody">
                    {!! $post->body_html !!}
                </div>

                {{-- ── Prev / Next ───────────────────────── --}}
                @if($prevPost || $nextPost)
                <div class="mt-14 pt-8 border-t border-neutral-200 dark:border-brand-700">
                    <p class="text-2xs font-semibold tracking-[0.12em] uppercase text-neutral-400 dark:text-neutral-600 mb-5">More articles</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        @if($prevPost)
                        <a href="{{ route('blog.show', $prevPost->slug) }}"
                           class="group flex flex-col gap-2 p-4 border border-neutral-200 dark:border-brand-700 bg-white dark:bg-brand-800 hover:border-neutral-400 dark:hover:border-brand-500 transition-colors duration-200 no-underline">
                            <span class="text-2xs text-neutral-400 dark:text-neutral-600 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                                Previous
                            </span>
                            <span class="font-display text-sm font-bold text-ink dark:text-neutral-100 leading-snug tracking-tight group-hover:text-brand dark:group-hover:text-brand-400 transition-colors duration-200 line-clamp-2">{{ $prevPost->title }}</span>
                        </a>
                        @else
                        <div></div>
                        @endif

                        @if($nextPost)
                        <a href="{{ route('blog.show', $nextPost->slug) }}"
                           class="group flex flex-col gap-2 p-4 border border-neutral-200 dark:border-brand-700 bg-white dark:bg-brand-800 hover:border-neutral-400 dark:hover:border-brand-500 transition-colors duration-200 no-underline sm:text-right">
                            <span class="text-2xs text-neutral-400 dark:text-neutral-600 flex items-center gap-1.5 sm:justify-end">
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                            </span>
                            <span class="font-display text-sm font-bold text-ink dark:text-neutral-100 leading-snug tracking-tight group-hover:text-brand dark:group-hover:text-brand-400 transition-colors duration-200 line-clamp-2">{{ $nextPost->title }}</span>
                        </a>
                        @endif

                    </div>
                </div>
                @endif

                {{-- ── Like & Share ─────────────────────── --}}
                <div class="mt-10 pt-8 border-t border-neutral-200 dark:border-brand-700"
                     x-data="{ liked: false, likeCount: {{ $post->like_count }}, copied: false }">

                    <div class="flex flex-wrap items-center gap-4">

                        {{-- Like --}}
                        <button
                            x-on:click="liked = !liked; liked ? likeCount++ : likeCount--"
                            :class="liked ? 'text-brand dark:text-brand-400 border-brand dark:border-brand-400' : 'text-neutral-400 dark:text-neutral-600 border-neutral-200 dark:border-brand-700 hover:border-neutral-400 dark:hover:border-brand-500'"
                            class="inline-flex items-center gap-2 px-4 py-2 border text-xs font-semibold transition-colors duration-200 font-sans cursor-pointer bg-transparent"
                            aria-label="Like this post">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                 :fill="liked ? 'currentColor' : 'none'"
                                 stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            <span x-text="likeCount + ' likes'"></span>
                        </button>

                        {{-- Copy link --}}
                        <button
                            x-on:click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-neutral-200 dark:border-brand-700 text-xs font-semibold text-neutral-400 dark:text-neutral-600 hover:border-neutral-400 dark:hover:border-brand-500 hover:text-ink dark:hover:text-neutral-200 transition-colors duration-200 font-sans cursor-pointer bg-transparent"
                            aria-label="Copy link to clipboard">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                            <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
                        </button>

                        {{-- Share on WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . url('/blog/' . $post->slug)) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-neutral-200 dark:border-brand-700 text-xs font-semibold text-neutral-400 dark:text-neutral-600 hover:border-neutral-400 dark:hover:border-brand-500 hover:text-ink dark:hover:text-neutral-200 transition-colors duration-200 no-underline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.555 4.121 1.527 5.853L.057 23.57a.75.75 0 0 0 .918.918l5.717-1.47A11.95 11.95 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.71 9.71 0 0 1-4.908-1.327l-.353-.21-3.656.94.961-3.536-.23-.365A9.712 9.712 0 0 1 2.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/>
                            </svg>
                            WhatsApp
                        </a>

                    </div>
                </div>

                {{-- ── Comments (Livewire component) ───── --}}
                <div class="mt-10 pt-8 border-t border-neutral-200 dark:border-brand-700">
                    <livewire:frontend.blog-comments :post-id="$post->id" />
                </div>

            </div>{{-- /left column --}}

            {{-- ─────────────────────────────────────────────
                 RIGHT: Sticky sidebar — related posts
                 ───────────────────────────────────────────── --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24 flex flex-col gap-8">

                    @if($relatedPosts->isNotEmpty())
                    <div>
                        <p class="text-2xs font-semibold tracking-[0.12em] uppercase text-neutral-400 dark:text-neutral-600 mb-5">Related posts</p>
                        <div class="flex flex-col gap-0 divide-y divide-neutral-200 dark:divide-brand-700">
                            @foreach($relatedPosts as $related)
                            <a href="{{ route('blog.show', $related->slug) }}"
                               class="group flex gap-3 py-3.5 no-underline hover:bg-neutral-100 dark:hover:bg-brand-800 -mx-3 px-3 transition-colors duration-200 first:pt-0">
                                @if($related->featured_image_url)
                                <div class="w-14 h-14 flex-shrink-0 overflow-hidden border border-neutral-200 dark:border-brand-700">
                                    <img src="{{ $related->featured_image_url }}"
                                         alt="{{ $related->title }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display text-xs font-bold text-ink dark:text-neutral-100 leading-snug line-clamp-2 group-hover:text-brand dark:group-hover:text-brand-400 transition-colors duration-200 tracking-tight">{{ $related->title }}</p>
                                    <p class="text-[10px] text-neutral-400 dark:text-neutral-600 mt-1">{{ $related->published_at?->format('d M Y') }}</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <a href="{{ route('blog.index') }}"
                           class="inline-flex items-center gap-1.5 mt-4 text-2xs font-semibold text-brand dark:text-brand-400 no-underline hover:gap-3 transition-[gap] duration-200">
                            View all posts →
                        </a>
                    </div>
                    @endif

                    {{-- Newsletter / CTA --}}
                    <div class="border border-neutral-200 dark:border-brand-700 p-5 bg-white dark:bg-brand-800">
                        <p class="font-display text-sm font-bold text-ink dark:text-neutral-50 tracking-tight mb-1">New collection alerts</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed mb-4">Get notified when new fabrics arrive — no spam, ever.</p>
                        <a href="{{ route('shop.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-ink dark:bg-neutral-50 text-neutral-50 dark:text-brand-900 text-xs font-bold tracking-[0.06em] uppercase font-sans no-underline hover:bg-brand dark:hover:bg-brand-100 transition-colors duration-200">
                            Browse Fabrics
                        </a>
                    </div>

                </div>
            </aside>{{-- /right sidebar --}}

        </div>{{-- /two-column grid --}}

    </div>{{-- /max-w container --}}

</div>{{-- /page wrapper --}}
