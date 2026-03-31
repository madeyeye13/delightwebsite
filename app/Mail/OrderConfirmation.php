<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed: '.$this->order->order_number.' - 1st Delightsome',
        );
    }

    public function content(): Content
    {
        $order = $this->order->load('items.product');
        $hasGiftCardProducts = $order->items->some(
            fn ($item) => (bool) $item->product?->is_gift_card
        );

        return new Content(
            markdown: 'emails.order-confirmation',
            with: [
                'order' => $order,
                'shopUrl' => route('shop.index'),
                'hasGiftCardProducts' => $hasGiftCardProducts,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
