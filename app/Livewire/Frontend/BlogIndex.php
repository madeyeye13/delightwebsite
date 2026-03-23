<?php

namespace App\Livewire\Frontend;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $activeTag = '';

    public string $activeCategory = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveTag(): void
    {
        $this->resetPage();
    }

    public function updatingActiveCategory(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = BlogPost::query()
            ->published()
            ->with(['category', 'tags'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('excerpt', 'like', '%'.$this->search.'%');
            }))
            ->when($this->activeTag, fn ($q) => $q->whereHas('tags', fn ($q) => $q->where('slug', $this->activeTag)))
            ->when($this->activeCategory, fn ($q) => $q->whereHas('category', fn ($q) => $q->where('slug', $this->activeCategory)))
            ->latest('published_at');

        $posts = $query->paginate(12);
        $tags = BlogTag::has('posts')->orderBy('name')->get();
        $categories = BlogCategory::has('posts')->where('is_active', true)->orderBy('name')->get();

        return view('livewire.frontend.blog-index', compact('posts', 'tags', 'categories'));
    }
}
