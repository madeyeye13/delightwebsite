<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected ?User $userModel = null;

    public function __construct(
        public readonly int $userId,
        public readonly ?string $temporaryPassword = null,
    ) {}

    protected function getUser(): ?User
    {
        return $this->userModel ??= User::find($this->userId);
    }

    public function envelope(): Envelope
    {
        $user = $this->getUser();

        return new Envelope(
            subject: $user
                ? 'Welcome to 1st Delightsome - Your Account is Ready'
                : 'Welcome to 1st Delightsome',
        );
    }

    public function content(): Content
    {
        $user = $this->getUser();

        if (! $user) {
            return new Content(
                markdown: 'emails.fallback',
                with: [
                    'message' => 'User account not found.',
                ],
            );
        }

        return new Content(
            markdown: 'emails.account-created',
            with: [
                'userName' => $user->name,
                'userEmail' => $user->email,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => route('login'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
