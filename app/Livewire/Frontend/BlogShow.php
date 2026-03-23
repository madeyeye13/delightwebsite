<?php

namespace App\Livewire\Frontend;

use App\Models\BlogPost;
use Illuminate\View\View;
use Livewire\Component;

class BlogShow extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        $post = BlogPost::query()
            ->published()
            ->with(['category', 'tags', 'approvedComments.replies'])
            ->where('slug', $this->slug)
            ->firstOrFail();

        // Increment view count
        $post->increment('view_count');

        $prevPost = BlogPost::query()
            ->published()
            ->where('published_at', '<', $post->published_at)
            ->orderByDesc('published_at')
            ->select(['id', 'title', 'slug', 'featured_image_url'])
            ->first();

        $nextPost = BlogPost::query()
            ->published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at')
            ->select(['id', 'title', 'slug', 'featured_image_url'])
            ->first();

        $relatedPosts = BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('livewire.frontend.blog-show', compact('post', 'prevPost', 'nextPost', 'relatedPosts'));
    }
}
