<?php

namespace App\Jobs;

use App\Mail\GiftCardMail;
use App\Models\GiftCard;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendGiftCardEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly GiftCard $giftCard,
        public readonly Order $order,
    ) {}

    public function handle(): void
    {
        $email = $this->giftCard->getNotificationEmail() ?? $this->order->contact_email;

        Mail::to($email)->send(new GiftCardMail($this->giftCard, $this->order));
    }
}
