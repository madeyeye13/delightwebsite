<?php

namespace App\Jobs;

use App\Mail\AccountCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendAccountCreatedEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly User $user,
        public readonly string $plainPassword,
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new AccountCreated($this->user, $this->plainPassword));
    }
}
