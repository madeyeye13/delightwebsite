<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order '.$this->order->order_number.' - 1st Delightsome',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-order-notification',
            with: [
                'order' => $this->order->load('items'),
                'adminOrdersUrl' => route('admin.orders.index'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
