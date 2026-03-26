<?php

namespace App\Mail;

use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminTestimonialNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Testimonial $testimonial) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Testimonial Awaiting Approval — 1st Delightsome',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-testimonial-notification',
            with: [
                'testimonial' => $this->testimonial,
                'adminUrl' => route('admin.testimonials.index'),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
