<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class Notifications extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function markAllRead(): void
    {
        session(['admin_notifs_last_read' => now()->toISOString()]);
        $this->dispatch('notify', message: 'Notifications marked as read.', type: 'success');
    }

    public function getNotificationsProperty(): array
    {
        $items = [];

        // Pending orders in last 48 hours
        $pendingOrders = Order::where('payment_status', 'pending')
            ->where('created_at', '>=', now()->subHours(48))
            ->latest()
            ->limit(5)
            ->get();

        foreach ($pendingOrders as $order) {
            $items[] = [
                'type' => 'order',
                'message' => 'Order #'.$order->order_number.' is pending payment',
                'time' => $order->created_at->diffForHumans(),
                'url' => route('admin.orders.show', $order->id),
                'at' => $order->created_at,
            ];
        }

        // Low stock products
        $lowStockProducts = Product::with(['variants'])
            ->whereHas('variants', function ($q): void {
                $q->whereColumn('stock', '<=', 'low_stock_threshold')->where('stock', '>', 0);
            })
            ->latest('updated_at')
            ->limit(5)
            ->get();

        foreach ($lowStockProducts as $product) {
            $items[] = [
                'type' => 'product',
                'message' => '"'.$product->name.'" is low in stock',
                'time' => $product->updated_at->diffForHumans(),
                'url' => route('admin.inventory.index'),
                'at' => $product->updated_at,
            ];
        }

        // Out of stock products
        $outOfStockProducts = Product::whereHas('variants', function ($q): void {
            $q->where('stock', 0);
        })
            ->latest('updated_at')
            ->limit(3)
            ->get();

        foreach ($outOfStockProducts as $product) {
            $items[] = [
                'type' => 'product',
                'message' => '"'.$product->name.'" is out of stock',
                'time' => $product->updated_at->diffForHumans(),
                'url' => route('admin.inventory.index'),
                'at' => $product->updated_at,
            ];
        }

        // New customers in last 24 hours
        $newUsers = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->limit(3)
            ->get();

        foreach ($newUsers as $user) {
            $items[] = [
                'type' => 'user',
                'message' => $user->name.' just registered',
                'time' => $user->created_at->diffForHumans(),
                'url' => route('admin.users.index'),
                'at' => $user->created_at,
            ];
        }

        // Unread contact messages (last 48 hours)
        $unreadContacts = Contact::whereNull('read_at')
            ->where('created_at', '>=', now()->subHours(48))
            ->latest()
            ->limit(5)
            ->get();

        foreach ($unreadContacts as $contact) {
            $items[] = [
                'type' => 'contact',
                'message' => 'New message from '.$contact->name,
                'time' => $contact->created_at->diffForHumans(),
                'url' => route('admin.contacts.index'),
                'at' => $contact->created_at,
            ];
        }

        // New newsletter subscribers (last 24 hours)
        $newSubscribers = NewsletterSubscriber::whereNull('unsubscribed_at')
            ->where('subscribed_at', '>=', now()->subHours(24))
            ->latest('subscribed_at')
            ->limit(5)
            ->get();

        foreach ($newSubscribers as $subscriber) {
            $items[] = [
                'type' => 'newsletter',
                'message' => $subscriber->email.' subscribed to the newsletter',
                'time' => $subscriber->subscribed_at->diffForHumans(),
                'url' => route('admin.newsletter.index'),
                'at' => $subscriber->subscribed_at,
            ];
        }

        // Sort by most recent first and limit to 8
        usort($items, fn ($a, $b) => $b['at']->timestamp <=> $a['at']->timestamp);

        return array_slice($items, 0, 8);
    }

    public function getUnreadCountProperty(): int
    {
        $lastRead = session('admin_notifs_last_read');

        if (! $lastRead) {
            return min(count($this->notifications), 9);
        }

        $lastReadAt = Carbon::parse($lastRead);

        return collect($this->notifications)
            ->filter(fn ($n) => $n['at']->isAfter($lastReadAt))
            ->count();
    }

    public function render(): View
    {
        return view('livewire.admin.notifications');
    }
}
