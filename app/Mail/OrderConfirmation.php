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

    protected ?Order $order = null;

    public function __construct(
        public readonly int $orderId,
    ) {}

    protected function getOrder(): ?Order
    {
        return $this->order ??= Order::with([
            'items.product.media',
            'items.variant.media',
        ])->find($this->orderId);
    }

    public function envelope(): Envelope
    {
        $order = $this->getOrder();

        return new Envelope(
            subject: $order
                ? 'Order Confirmed: '.$order->order_number.' - 1st Delightsome'
                : 'Order Confirmation - 1st Delightsome',
        );
    }

    public function content(): Content
    {
        $order = $this->getOrder();

        if (! $order) {
            return new Content(
                markdown: 'emails.fallback',
                with: [
                    'message' => 'Order not found.',
                ],
            );
        }

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
