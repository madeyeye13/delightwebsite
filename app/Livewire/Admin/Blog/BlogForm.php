<?php

namespace App\Livewire\Admin\Blog;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class BlogForm extends Component
{
    public ?BlogPost $post = null;

    public function mount(?BlogPost $post = null): void
    {
        $this->post = $post;
    }

    /**
     * Persist a new blog category from the inline modal.
     *
     * @param  array<string, mixed>  $data
     */
    public function storeCategory(array $data): array
    {
        $category = BlogCategory::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? '',
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ];
    }

    /**
     * Save or update a blog post from the Alpine form payload.
     *
     * @param  array<string, mixed>  $payload
     */
    public function savePost(array $payload, string $status = 'draft'): array
    {
        try {
            $isNew = empty($payload['id']);
            $slug = ! empty($payload['slug'])
                ? Str::slug($payload['slug'])
                : Str::slug($payload['title'] ?? 'untitled');

            if ($isNew) {
                $base = $slug;
                $n = 1;
                while (BlogPost::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$n++;
                }
            }

            $data = [
                'blog_category_id' => ! empty($payload['categoryId']) ? (int) $payload['categoryId'] : null,
                'title' => $payload['title'] ?? '',
                'slug' => $slug,
                'excerpt' => $payload['excerpt'] ?? null,
                'body' => $payload['body'] ?? null,
                'body_html' => $payload['bodyHtml'] ?? null,
                'featured_image_url' => $payload['featuredImageUrl'] ?? null,
                'author' => $payload['author'] ?? null,
                'meta_title' => $payload['metaTitle'] ?? null,
                'meta_description' => $payload['metaDescription'] ?? null,
                'og_image' => $payload['ogImage'] ?? null,
                'featured' => (bool) ($payload['featured'] ?? false),
                'status' => $status,
                'published_at' => $status === 'published' ? now() : null,
                'scheduled_at' => $status === 'scheduled' && ! empty($payload['scheduledAt'])
                    ? $payload['scheduledAt']
                    : null,
            ];

            if ($isNew) {
                $post = BlogPost::create($data);
            } else {
                $post = BlogPost::findOrFail((int) $payload['id']);
                if ($post->status !== 'published' && $status === 'published') {
                    $data['published_at'] = now();
                }
                $post->update($data);
            }

            // Sync tags
            $tagIds = [];
            foreach ($payload['tags'] ?? [] as $tagName) {
                $tagSlug = Str::slug($tagName);
                $tag = BlogTag::firstOrCreate(['slug' => $tagSlug], ['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);

            $post->load('tags', 'category');
            $this->dispatch('toast', type: 'success', message: $isNew ? 'Blog post created.' : 'Blog post updated.');

            return [
                'success' => true,
                'id' => $post->id,
                'slug' => $post->slug,
                'status' => $post->status,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Approve a comment.
     */
    public function approveComment(int $id): void
    {
        BlogComment::findOrFail($id)->update(['is_approved' => true]);
        $this->dispatch('toast', type: 'success', message: 'Comment approved.');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(int $id): void
    {
        BlogComment::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Comment deleted.');
    }

    public function render(): View
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get()
            ->map(fn (BlogCategory $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
            ])->values();

        $tags = BlogTag::orderBy('name')->pluck('name')->toArray();

        $postData = null;
        if ($this->post) {
            $this->post->load('tags', 'category');
            $postData = [
                'id' => $this->post->id,
                'title' => $this->post->title,
                'slug' => $this->post->slug,
                'excerpt' => $this->post->excerpt ?? '',
                'body' => $this->post->body ?? '',
                'bodyHtml' => $this->post->body_html ?? '',
                'featuredImageUrl' => $this->post->featured_image_url ?? '',
                'author' => $this->post->author ?? '',
                'categoryId' => $this->post->blog_category_id,
                'tags' => $this->post->tags->pluck('name')->toArray(),
                'metaTitle' => $this->post->meta_title ?? '',
                'metaDescription' => $this->post->meta_description ?? '',
                'ogImage' => $this->post->og_image ?? '',
                'featured' => (bool) $this->post->featured,
                'status' => $this->post->status,
                'scheduledAt' => $this->post->scheduled_at?->toIso8601String(),
                'publishedAt' => $this->post->published_at?->toDateTimeString(),
            ];
        }

        return view('livewire.admin.blog.blog-form', [
            'categoriesJson' => $categories->toJson(),
            'tagsJson' => json_encode($tags),
            'postJson' => json_encode($postData),
        ]);
    }
}
