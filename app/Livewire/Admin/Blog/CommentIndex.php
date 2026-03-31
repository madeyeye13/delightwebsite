<?php

namespace App\Livewire\Admin\Blog;

use App\Models\BlogComment;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CommentIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = 'pending';

    #[Url]
    public string $search = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function approveComment(int $id): void
    {
        BlogComment::findOrFail($id)->update(['is_approved' => true]);
        $this->dispatch('toast', type: 'success', message: 'Comment approved.');
    }

    public function rejectComment(int $id): void
    {
        BlogComment::findOrFail($id)->update(['is_approved' => false]);
        $this->dispatch('toast', type: 'success', message: 'Comment rejected.');
    }

    public function deleteComment(int $id): void
    {
        BlogComment::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Comment deleted.');
    }

    public function render(): View
    {
        $comments = BlogComment::query()
            ->with('post:id,title,slug')
            ->when($this->statusFilter === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($this->statusFilter === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('body', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(20);

        $pendingCount = BlogComment::where('is_approved', false)->count();

        return view('livewire.admin.blog.comment-index', compact('comments', 'pendingCount'));
    }
}
