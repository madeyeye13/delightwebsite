<?php

namespace App\Mail;

use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftCardRedemptionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly GiftCard $giftCard,
        public readonly int $amountUsed,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Gift Card Has Been Used — 1st Delightsome',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.gift-card-redemption-notification',
            with: [
                'giftCard' => $this->giftCard,
                'amountUsed' => $this->amountUsed,
                'shopUrl' => route('shop.index'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
