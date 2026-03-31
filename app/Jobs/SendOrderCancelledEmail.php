<?php

namespace App\Jobs;

use App\Mail\OrderCancelledAdmin;
use App\Mail\OrderCancelledUser;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderCancelledEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        // Email to the customer
        Mail::to($this->order->contact_email)
            ->send(new OrderCancelledUser($this->order));

        // Email to the admin
        Mail::to(config('mail.admin_address', config('mail.from.address')))
            ->send(new OrderCancelledAdmin($this->order));
    }
}
