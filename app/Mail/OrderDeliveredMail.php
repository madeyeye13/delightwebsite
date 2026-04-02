<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order Has Been Delivered — '.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        $this->order->loadMissing([
            'items.product.media',
            'items.variant.media',
        ]);

        return new Content(view: 'emails.orders.delivered');
    }
}
