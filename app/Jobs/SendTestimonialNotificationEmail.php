<?php

namespace App\Jobs;

use App\Mail\AdminTestimonialNotification;
use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTestimonialNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Testimonial $testimonial) {}

    public function handle(): void
    {
        $adminEmail = config('mail.admin_address', config('mail.from.address'));

        Mail::to($adminEmail)
            ->send(new AdminTestimonialNotification($this->testimonial));
    }
}
