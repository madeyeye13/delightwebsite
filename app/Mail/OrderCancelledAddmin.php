<?php

// app/Mail/OrderCancelledAdmin.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelledAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[Admin] Order #{$this->order->order_number} cancelled by customer");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.orders.cancelled-admin', with: ['order' => $this->order]);
    }
}
