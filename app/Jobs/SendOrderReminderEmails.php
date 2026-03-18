<?php

namespace App\Jobs;

use App\Mail\OrderPendingReminder;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderReminderEmails implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    /**
     * Send reminder emails to buyers who have pending orders older than 2 hours.
     * Schedule this job to run hourly via console/routes/console.php.
     */
    public function handle(): void
    {
        Order::where('payment_status', 'pending')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(2))
            ->where('created_at', '>=', now()->subDays(2))
            ->whereNull('reminder_sent_at')
            ->chunk(50, function ($orders) {
                foreach ($orders as $order) {
                    Mail::to($order->buyer_email)->queue(new OrderPendingReminder($order));
                    $order->update(['reminder_sent_at' => now()]);
                }
            });
    }
}
