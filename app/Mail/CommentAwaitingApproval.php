<?php

namespace App\Mail;

use App\Models\BlogComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommentAwaitingApproval extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly BlogComment $comment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Blog Comment Awaiting Approval — 1st Delightsome',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.comment-awaiting-approval',
            with: [
                'comment' => $this->comment->load('post:id,title,slug'),
                'moderationUrl' => route('admin.blog.comments'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
