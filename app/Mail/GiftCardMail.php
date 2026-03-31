<?php

namespace App\Mail;

use App\Models\GiftCard;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftCardMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly GiftCard $giftCard,
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Gift Card Code — 1st Delightsome',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.gift-card',
            with: [
                'giftCard' => $this->giftCard,
                'order' => $this->order,
                'shopUrl' => route('shop.index'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
