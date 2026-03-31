<?php

namespace App\Jobs;

use App\Mail\GiftCardRedemptionMail;
use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendGiftCardRedemptionNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly GiftCard $giftCard,
        public readonly int $amountUsed,
    ) {}

    public function handle(): void
    {
        $email = $this->giftCard->getNotificationEmail();

        if (! $email) {
            return;
        }

        Mail::to($email)->send(new GiftCardRedemptionMail($this->giftCard, $this->amountUsed));
    }
}
