@php
$recentPosts = \App\Models\BlogPost::query()
    ->published()
    ->with('tags')
    ->latest('published_at')
    ->limit(4)
    ->get();
@endphp

@if($recentPosts->isNotEmpty())

<style>
    .rbp-section {
        --rbp-bg:        #FCFCF9;
        --rbp-surface:   #F3F3F3;
        --rbp-border:    #E5E5E5;
        --rbp-text:      #111315;
        --rbp-muted:     #737373;
        --rbp-faint:     #A3A3A3;
        --rbp-accent:    #1F6F67;
        --rbp-gold:      #D9A21B;
        --rbp-tag-bg:    #F3F3F3;
        --rbp-tag-text:  #525252;
    }

    .dark .rbp-section,
    [data-theme="dark"] .rbp-section {
        --rbp-bg:        #071E1E;
        --rbp-surface:   #0D3230;
        --rbp-border:    #134643;
        --rbp-text:      #F9F9F9;
        --rbp-muted:     #A3A3A3;
        --rbp-faint:     #525252;
        --rbp-accent:    #33A89F;
        --rbp-gold:      #D9A21B;
        --rbp-tag-bg:    #0D3230;
        --rbp-tag-text:  #A3A3A3;
    }

    .rbp-section {
        background: var(--rbp-bg);
        padding: 72px 64px 96px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        border-top: 1px solid var(--rbp-border);
    }

    .rbp-inner { max-width: 1200px; margin: 0 auto; }

    .rbp-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--rbp-border);
    }

    .rbp-section-title {
        font-family: 'Manrope', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--rbp-text);
        letter-spacing: -0.02em;
    }

    .rbp-view-all {
        font-size: 12px;
        font-weight: 600;
        color: var(--rbp-accent);
        text-decoration: none;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: gap 0.2s;
    }
    .rbp-view-all:hover { gap: 10px; }
    .rbp-view-all-arrow { transition: transform 0.2s; }
    .rbp-view-all:hover .rbp-view-all-arrow { transform: translateX(3px); }

    .rbp-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto;
        gap: 20px;
    }

    .rbp-featured {
        grid-row: 1 / 3;
        background: var(--rbp-bg);
        display: flex;
        flex-direction: column;
        border: 1px solid var(--rbp-border);
    }

    .rbp-secondary {
        background: var(--rbp-bg);
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 0;
        border: 1px solid var(--rbp-border);
    }

    .rbp-featured-img-wrap {
        overflow: hidden;
        aspect-ratio: 4/3;
        flex-shrink: 0;
    }

    .rbp-secondary-img-wrap {
        overflow: hidden;
        width: 180px;
        flex-shrink: 0;
    }

    .rbp-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .rbp-featured:hover .rbp-img,
    .rbp-secondary:hover .rbp-img { transform: scale(1.03); }

    .rbp-featured-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .rbp-secondary-body {
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .rbp-meta {
        font-size: 11px;
        color: var(--rbp-faint);
        margin-bottom: 8px;
        letter-spacing: 0.02em;
    }

    .rbp-meta span { margin: 0 4px; }

    .rbp-title {
        font-family: 'Manrope', sans-serif;
        font-weight: 700;
        color: var(--rbp-text);
        letter-spacing: -0.02em;
        line-height: 1.3;
        text-decoration: none;
        display: block;
        transition: color 0.2s;
    }

    .rbp-title:hover { color: var(--rbp-accent); }

    .rbp-featured-body .rbp-title { font-size: 20px; margin-bottom: 10px; }
    .rbp-secondary-body .rbp-title { font-size: 14px; margin-bottom: 6px; }

    .rbp-excerpt {
        font-size: 13px;
        color: var(--rbp-muted);
        line-height: 1.7;
        margin-bottom: 16px;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .rbp-secondary-body .rbp-excerpt {
        font-size: 12px;
        -webkit-line-clamp: 2;
        margin-bottom: 12px;
    }

    .rbp-tags { display: flex; flex-wrap: wrap; gap: 6px; }

    .rbp-tag {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--rbp-tag-text);
        background: var(--rbp-tag-bg);
        border: 1px solid var(--rbp-border);
        padding: 3px 8px;
    }

    .rbp-featured-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--rbp-accent);
        text-decoration: none;
        margin-top: 16px;
        transition: gap 0.2s;
    }
    .rbp-featured-link:hover { gap: 10px; }

    @media (max-width: 900px) {
        .rbp-section { padding: 48px 24px; }
        .rbp-grid { grid-template-columns: 1fr; }
        .rbp-featured { grid-row: auto; }
        .rbp-secondary { grid-template-columns: 140px 1fr; }
    }

    @media (max-width: 560px) {
        .rbp-secondary { grid-template-columns: 1fr; }
        .rbp-secondary-img-wrap { width: 100%; aspect-ratio: 16/9; }
    }
</style>

<section class="rbp-section" aria-label="Recent blog posts">
    <div class="rbp-inner">

        <div class="rbp-header">
            <h2 class="rbp-section-title">Recent Blog Posts</h2>
            <a href="{{ route('blog.index') }}" class="rbp-view-all" aria-label="View all blog posts">
                All posts <span class="rbp-view-all-arrow" aria-hidden="true">→</span>
            </a>
        </div>

        <div class="rbp-grid" role="list">

            @php $featured = $recentPosts->firstWhere('featured', true) ?? $recentPosts->first(); @endphp

            <article class="rbp-featured" role="listitem" itemscope itemtype="https://schema.org/BlogPosting">
                <a href="{{ route('blog.show', $featured->slug) }}" aria-label="{{ $featured->title }}" tabindex="-1">
                    <div class="rbp-featured-img-wrap">
                        <img class="rbp-img"
                             src="{{ $featured->featured_image_url }}"
                             alt="{{ $featured->title }}"
                             loading="lazy"
                             width="800" height="600"
                             itemprop="image">
                    </div>
                </a>
                <div class="rbp-featured-body">
                    <p class="rbp-meta">
                        <span itemprop="author">{{ $featured->author }}</span>
                        <span aria-hidden="true">•</span>
                        <time itemprop="datePublished" datetime="{{ $featured->published_at?->toIso8601String() }}">{{ $featured->published_at?->format('d M Y') }}</time>
                    </p>
                    <a class="rbp-title" href="{{ route('blog.show', $featured->slug) }}" itemprop="headline">
                        {{ $featured->title }}
                    </a>
                    <p class="rbp-excerpt" itemprop="description">{{ $featured->excerpt }}</p>
                    <div class="rbp-tags" aria-label="Tags">
                        @foreach($featured->tags as $tag)
                            <span class="rbp-tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </article>

            @foreach($recentPosts->where('id', '!=', $featured->id)->take(3) as $post)
            <article class="rbp-secondary" role="listitem" itemscope itemtype="https://schema.org/BlogPosting">
                <a href="{{ route('blog.show', $post->slug) }}" aria-label="{{ $post->title }}" tabindex="-1">
                    <div class="rbp-secondary-img-wrap">
                        <img class="rbp-img"
                             src="{{ $post->featured_image_url }}"
                             alt="{{ $post->title }}"
                             loading="lazy"
                             width="360" height="270"
                             itemprop="image">
                    </div>
                </a>
                <div class="rbp-secondary-body">
                    <div>
                        <p class="rbp-meta">
                            <span itemprop="author">{{ $post->author }}</span>
                            <span aria-hidden="true">•</span>
                            <time itemprop="datePublished" datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('d M Y') }}</time>
                        </p>
                        <a class="rbp-title" href="{{ route('blog.show', $post->slug) }}" itemprop="headline">
                            {{ $post->title }}
                        </a>
                        <p class="rbp-excerpt" itemprop="description">{{ $post->excerpt }}</p>
                    </div>
                    <div class="rbp-tags" aria-label="Tags">
                        @foreach($post->tags as $tag)
                            <span class="rbp-tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </article>
            @endforeach

        </div>
    </div>
</section>

@endif