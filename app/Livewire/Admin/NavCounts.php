<?php

namespace App\Livewire\Admin;

use App\Models\BlogComment;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class NavCounts extends Component
{
    /**
     * Called on initial mount and every poll interval.
     * Dispatches a browser event that the Alpine navCounts store listens to.
     *
     * To add a new badge:
     *  1. Add a key to getCounts() below.
     *  2. Reference $store.navCounts.counts.yourKey in the sidebar.
     */
    public function refreshCounts(): void
    {
        $this->dispatch('nav-counts-updated', counts: $this->getCounts());
    }

    /**
     * Records the current maximum order ID so that subsequent polls can
     * detect orders that arrived after this point (shown in red + blinking).
     */
    public function acknowledgeOrders(): void
    {
        $maxId = Order::max('id') ?? 0;
        session(['admin_orders_last_seen' => $maxId]);
        $this->refreshCounts();
    }

    public function render(): View
    {
        return view('livewire.admin.nav-counts');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function getCounts(): array
    {
        $lastSeen = session('admin_orders_last_seen', 0);

        // Orders pending processing (paid, not yet shipped/cancelled)
        $ordersPending = Order::where('payment_status', 'paid')
            ->where('status', 'pending')
            ->count();

        // Orders that arrived since admin last acknowledged
        $ordersNew = Order::where('payment_status', 'paid')
            ->where('status', 'pending')
            ->where('id', '>', $lastSeen)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ADD NEW COUNTS HERE
        | Each key becomes available as $store.navCounts.counts.yourKey in the
        | sidebar blade. No other files need to change.
        |--------------------------------------------------------------------------
        */
        return [
            'orders' => $ordersPending,
            'orders_new' => $ordersNew,
            'products' => Product::count(),
            'pending_comments' => BlogComment::where('is_approved', false)->count(),
            'unread_contacts' => Contact::whereNull('read_at')->count(),
            'newsletter_new' => NewsletterSubscriber::whereNull('unsubscribed_at')
                ->where('subscribed_at', '>=', now()->startOfDay())
                ->count(),
        ];
    }
}
