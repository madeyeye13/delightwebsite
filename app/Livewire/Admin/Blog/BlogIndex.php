<?php

namespace App\Livewire\Admin\Blog;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\View\View;
use Livewire\Component;

class BlogIndex extends Component
{
    public function deletePost(int $id): void
    {
        BlogPost::findOrFail($id)->delete();
        $this->dispatch('post-deleted', id: $id);
        $this->dispatch('toast', type: 'success', message: 'Blog post deleted.');
    }

    public function bulkDelete(array $ids): void
    {
        BlogPost::whereIn('id', $ids)->delete();
        $this->dispatch('posts-bulk-deleted', ids: $ids);
        $this->dispatch('toast', type: 'success', message: count($ids).' posts deleted.');
    }

    public function updateStatus(int $id, string $status): void
    {
        BlogPost::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('toast', type: 'success', message: 'Post status updated.');
    }

    public function render(): View
    {
        $posts = BlogPost::with(['category', 'tags'])
            ->latest()
            ->get()
            ->map(fn (BlogPost $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'excerpt' => $p->excerpt ?? '',
                'author' => $p->author ?? '—',
                'status' => $p->status,
                'featured' => (bool) $p->featured,
                'featured_image_url' => $p->featured_image_url,
                'categoryLabel' => $p->category?->name ?? '—',
                'categoryKey' => $p->category?->slug ?? '',
                'tags' => $p->tags->pluck('name')->toArray(),
                'published_at' => $p->published_at?->format('d M Y') ?? '—',
                'updated' => $p->updated_at->diffForHumans(),
            ])->values();

        $stats = [
            'total' => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'drafts' => BlogPost::where('status', 'draft')->count(),
            'scheduled' => BlogPost::where('status', 'scheduled')->count(),
            'featured' => BlogPost::where('featured', true)->count(),
        ];

        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.blog.blog-index', [
            'postsJson' => $posts->toJson(),
            'stats' => $stats,
            'categories' => $categories,
        ]);
    }
}
