<?php

namespace App\Jobs;

use App\Mail\OrderAddressUpdatedAdmin;
use App\Mail\OrderAddressUpdatedUser;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderAddressUpdatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        Mail::to($this->order->contact_email)
            ->send(new OrderAddressUpdatedUser($this->order));

        Mail::to(config('mail.admin_address', config('mail.from.address')))
            ->send(new OrderAddressUpdatedAdmin($this->order));
    }
}