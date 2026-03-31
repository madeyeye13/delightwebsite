<?php

namespace App\Jobs;

use App\Mail\ContactAdminNotification;
use App\Mail\ContactUserConfirmation;
use App\Models\AppSetting;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Contact $contact) {}

    public function handle(): void
    {
        Mail::to($this->contact->email)
            ->send(new ContactUserConfirmation($this->contact));

        if ((bool) AppSetting::get('notify_new_contact', '1')) {
            $adminEmail = AppSetting::get('admin_notification_email', config('mail.from.address'));
            Mail::to($adminEmail)
                ->send(new ContactAdminNotification($this->contact));
        }
    }
}
