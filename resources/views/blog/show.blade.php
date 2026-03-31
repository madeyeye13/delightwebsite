@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@php
    $isoDate   = $post->published_at?->toIso8601String() ?? now()->toIso8601String();
    $keywords  = $post->tags->pluck('name')->join(', ');
    $wordCount = str_word_count(strip_tags((string) $post->body_html));
    $postUrl   = url('/blog/' . $post->slug);
    $ogImage   = $post->og_image ?: $post->featured_image_url;
@endphp

@section('title', $post->meta_title ?: ($post->title . ' — 1st Delightsome Fabrics'))

@push('head')
    {{-- ── Core SEO ── --}}
    <meta name="description" content="{{ $post->meta_description ?: $post->excerpt }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $postUrl }}">

    {{-- ── Open Graph (article type) ── --}}
    <meta property="og:site_name" content="1st Delightsome Fabrics">
    <meta property="og:locale" content="en_NG">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
    <meta property="og:url" content="{{ $postUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $post->title }}">
    <meta property="article:published_time" content="{{ $isoDate }}">
    <meta property="article:modified_time" content="{{ $isoDate }}">
    <meta property="article:author" content="{{ $post->author }}">
    @foreach($post->tags as $tag)
    <meta property="article:tag" content="{{ $tag->name }}">
    @endforeach

    {{-- ── Twitter / X Card ── --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@1stDelightsome">
    <meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta name="twitter:description" content="{{ $post->meta_description ?: $post->excerpt }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $post->title }}">

    {{-- ── JSON-LD: BlogPosting (Google Rich Results) ── --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BlogPosting",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ $postUrl }}"
        },
        "url": "{{ $postUrl }}",
        "headline": "{{ $post->title }}",
        "description": "{{ $post->excerpt }}",
        "keywords": "{{ $keywords }}",
        "wordCount": {{ $wordCount }},
        "image": {
            "@type": "ImageObject",
            "url": "{{ $ogImage }}",
            "width": 1200,
            "height": 630
        },
        "author": {
            "@type": "Person",
            "name": "{{ $post->author }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "1st Delightsome Fabrics",
            "url": "{{ url('/') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo.png') }}"
            }
        },
        "datePublished": "{{ $isoDate }}",
        "dateModified": "{{ $isoDate }}",
        "inLanguage": "en-NG"
    }
    </script>

    {{-- ── JSON-LD: BreadcrumbList (shows in Google search results) ── --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Blog",
                "item": "{{ route('blog.index') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ $post->title }}",
                "item": "{{ $postUrl }}"
            }
        ]
    }
    </script>

    {{-- ── Alpine store init: theme (dark mode) ───────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('theme') === 'dark'
                || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            }
        });
    });
    // Apply dark class immediately to avoid flash of unstyled content
    (function() {
        var saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

    <style>
        /* ── Rich post body prose styles ── */
        .post-prose { font-size: 15px; line-height: 1.65; color: #525252; }
        .dark .post-prose { color: #A3A3A3; }

        .post-prose p  { margin-bottom: 0.65em; }
        .post-prose p:has(> br:only-child) { margin-bottom: 0; line-height: 0.4; }
        .post-prose h2 { font-family: 'Manrope', sans-serif; font-size: 1.25rem; font-weight: 800;
                         color: #111315; letter-spacing: -0.02em; margin: 1.4em 0 0.5em; line-height: 1.3; }
        .post-prose h3 { font-family: 'Manrope', sans-serif; font-size: 1.05rem; font-weight: 700;
                         color: #111315; letter-spacing: -0.01em; margin: 1.2em 0 0.4em; line-height: 1.35; }
        .dark .post-prose h2,
        .dark .post-prose h3 { color: #F9F9F9; }

        .post-prose ul { list-style: disc; padding-left: 1.4em; margin-bottom: 1.5em; }
        .post-prose ol { list-style: decimal; padding-left: 1.4em; margin-bottom: 1.5em; }
        .post-prose li { margin-bottom: 0.45em; }

        .post-prose strong { color: #111315; font-weight: 700; }
        .dark .post-prose strong { color: #F9F9F9; }

        .post-prose a { color: #1F6F67; text-decoration: underline; transition: color 0.2s; }
        .post-prose a:hover { color: #134643; }
        .dark .post-prose a { color: #33A89F; }

        .post-prose img { width: 100%; height: auto; display: block; margin: 2.25em 0; }

        .post-prose blockquote {
            border-left: 3px solid #D9A21B;
            padding: 14px 20px;
            margin: 1.75em 0;
            font-style: italic;
            color: #737373;
            background: #F3F3F3;
        }
        .dark .post-prose blockquote { background: #0D3230; color: #A3A3A3; border-color: #D9A21B; }

        /* Note callout (use <div class="note">...</div> in body) */
        .post-prose .note {
            background: #E6F3F2;
            border-left: 3px solid #1F6F67;
            padding: 14px 20px;
            margin: 1.75em 0;
            border-radius: 1px;
            color: #195A55;
            font-style: normal;
        }
        .dark .post-prose .note { background: #0D3230; color: #33A89F; border-color: #33A89F; }

        /* Highlight / tip callout */
        .post-prose .highlight {
            background: #FBF4E1;
            border-left: 3px solid #D9A21B;
            padding: 14px 20px;
            margin: 1.75em 0;
            border-radius: 1px;
            font-style: normal;
        }
        .dark .post-prose .highlight { background: #3B2B07; color: #E7BA4B; border-color: #D9A21B; }

        /* CTA button inside content */
        .post-prose .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 11px 24px;
            background: #1F6F67;
            color: #FCFCF9;
            border: none;
            cursor: pointer;
            transition: background 0.2s, gap 0.2s;
            margin: 0.5em 0;
        }
        .post-prose .cta-btn:hover { background: #134643; text-decoration: none; }
        .dark .post-prose .cta-btn { background: #33A89F; color: #071E1E; }
        .dark .post-prose .cta-btn:hover { background: #1F6F67; }

        /* Horizontal rule */
        .post-prose hr { border: none; border-top: 1px solid #E5E5E5; margin: 2.5em 0; }
        .dark .post-prose hr { border-color: #134643; }
    </style>
@endpush

@section('content')

<livewire:frontend.blog-show :slug="$slug" />

@endsection