<?php

namespace App\Jobs;

use App\Mail\CommentAwaitingApproval;
use App\Models\BlogComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCommentAwaitingApprovalEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly BlogComment $comment) {}

    public function handle(): void
    {
        Mail::to(config('mail.admin_address', config('mail.from.address')))
            ->send(new CommentAwaitingApproval($this->comment));
    }
}
