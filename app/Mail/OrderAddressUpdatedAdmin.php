<?php

// app/Mail/OrderAddressUpdatedAdmin.php
namespace App\Mail;
 
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAddressUpdatedAdmin extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public readonly Order $order) {}
    public function envelope(): Envelope {
        return new Envelope(subject: "[Admin] Address updated for order #{$this->order->order_number}");
    }
    public function content(): Content {
        return new Content(markdown: 'emails.orders.address-updated-admin', with: ['order' => $this->order]);
    }
}