<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPendingReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete Your Order '.$this->order->order_number.' - 1st Delightsome',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-pending-reminder',
            with: [
                'order' => $this->order,
                'checkoutUrl' => route('checkout.index'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
