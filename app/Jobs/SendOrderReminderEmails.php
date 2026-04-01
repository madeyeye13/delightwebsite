<?php

namespace App\Jobs;

use App\Mail\OrderPendingReminder;
use App\Mail\OrderPendingReminderSecond;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderReminderEmails implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    /**
     * Send reminder emails to buyers with pending orders.
     *
     * First reminder:  2–23 hours after order creation, reminder_sent_at is null.
     * Second reminder: 24+ hours after first reminder, order still pending, second_reminder_sent_at is null.
     *
     * Scheduled hourly via routes/console.php.
     */
    public function handle(): void
    {
        $baseQuery = Order::where('payment_status', 'pending')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(2));

        // ── First reminder: sent 2–23 hours after order creation ─────────────
        (clone $baseQuery)
            ->where('created_at', '<=', now()->subHours(2))
            ->whereNull('reminder_sent_at')
            ->chunk(50, function ($orders) {
                foreach ($orders as $order) {
                    Mail::to($order->contact_email)->queue(new OrderPendingReminder($order));
                    $order->update(['reminder_sent_at' => now()]);
                }
            });

        // ── Second reminder: sent 24+ hours after the first ──────────────────
        (clone $baseQuery)
            ->whereNotNull('reminder_sent_at')
            ->where('reminder_sent_at', '<=', now()->subHours(24))
            ->whereNull('second_reminder_sent_at')
            ->chunk(50, function ($orders) {
                foreach ($orders as $order) {
                    Mail::to($order->contact_email)->queue(new OrderPendingReminderSecond($order));
                    $order->update(['second_reminder_sent_at' => now()]);
                }
            });
    }
}
