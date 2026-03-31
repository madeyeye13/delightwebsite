<?php

namespace App\Livewire\Frontend;

use App\Jobs\SendCommentAwaitingApprovalEmail;
use App\Models\BlogComment;
use Illuminate\View\View;
use Livewire\Component;

class BlogComments extends Component
{
    public int $postId;

    public string $name = '';

    public string $email = '';

    public string $body = '';

    public ?int $replyToId = null;

    public string $replyToName = '';

    public bool $submitted = false;

    public function mount(int $postId): void
    {
        $this->postId = $postId;
    }

    public function setReplyTo(int $commentId, string $name): void
    {
        $this->replyToId = $commentId;
        $this->replyToName = $name;
        $this->submitted = false;
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
        $this->replyToName = '';
    }

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $comment = BlogComment::create([
            'blog_post_id' => $this->postId,
            'parent_id' => $this->replyToId,
            'name' => $this->name,
            'email' => $this->email,
            'body' => $this->body,
            'is_approved' => false,
            'is_author_reply' => false,
        ]);

        SendCommentAwaitingApprovalEmail::dispatch($comment);

        $this->reset('body', 'replyToId', 'replyToName');
        $this->submitted = true;
    }

    public function render(): View
    {
        $comments = BlogComment::query()
            ->where('blog_post_id', $this->postId)
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->with(['replies' => fn ($q) => $q->where('is_approved', true)->orderBy('created_at')])
            ->orderBy('created_at')
            ->get();

        return view('livewire.frontend.blog-comments', compact('comments'));
    }
}
