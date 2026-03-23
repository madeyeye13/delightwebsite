<?php

namespace App\Livewire\Dashboard; // for user dashboard order details page

use App\Jobs\SendOrderAddressUpdatedEmail;
use App\Jobs\SendOrderCancelledEmail;
use App\Models\Order;
use App\Services\FlutterwaveService;
use App\Services\PaystackService;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Order Details')]
class OrderDetail extends Component
{
    public Order $order;

    // Address form fields
    public string $street = '';

    public string $city = '';

    public string $state = '';

    public string $country = 'NG';

    public string $postal = '';

    public string $notes = '';

    public bool $addressSaved = false;

    public function mount(Order $order): void
    {
        // Ensure the order belongs to the authenticated user
        abort_if($order->user_id !== auth()->id(), 403);

        $this->order = $order->load('items.product');

        $this->street = $order->shipping_street ?? '';
        $this->city = $order->shipping_city ?? '';
        $this->state = $order->shipping_state ?? '';
        $this->country = $order->shipping_country ?? 'NG';
        $this->postal = $order->shipping_postal ?? '';
        $this->notes = $order->shipping_notes ?? '';
    }

    // ── Cancel Order ─────────────────────────────────────────────────────────

    public function cancelOrder(): void
    {
        $this->order->refresh();

        if (! $this->order->canBeCancelled()) {
            $this->addError('cancel', 'This order can no longer be cancelled.');

            return;
        }

        // Process refund if order was paid
        if ($this->order->payment_status === 'paid' && $this->order->payment_reference) {
            $this->processRefund();
        }

        $this->order->update([
            'status' => 'cancelled',
            'payment_status' => $this->order->payment_status === 'paid' ? 'refunded' : $this->order->payment_status,
        ]);

        // Reverse referral/reward points
        app(ReferralService::class)->reversePointsForOrder($this->order);

        // Queue emails to user + admin
        SendOrderCancelledEmail::dispatch($this->order);

        $this->order->refresh();
        $this->dispatch('order-cancelled');
    }

    private function processRefund(): void
    {
        try {
            if ($this->order->payment_method === 'paystack') {
                app(PaystackService::class)->refundTransaction([
                    'transaction' => $this->order->payment_reference,
                ]);
            } elseif ($this->order->payment_method === 'flutterwave') {
                app(FlutterwaveService::class)->refundTransaction(
                    $this->order->payment_reference
                );
            }
        } catch (\Throwable $e) {
            Log::error("Refund failed for order {$this->order->order_number}: {$e->getMessage()}");
            // Still mark as cancelled — admin can handle refund manually if gateway fails
        }
    }

    // ── Update Address ───────────────────────────────────────────────────────

    public function updateAddress(): void
    {
        $this->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|size:2',
            'postal' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->order->refresh();

        if (! $this->order->canChangeAddress()) {
            $this->addError('address', 'The delivery address can no longer be changed for this order.');

            return;
        }

        $this->order->update([
            'shipping_street' => $this->street,
            'shipping_city' => $this->city,
            'shipping_state' => $this->state,
            'shipping_country' => $this->country,
            'shipping_postal' => $this->postal,
            'shipping_notes' => $this->notes,
        ]);

        SendOrderAddressUpdatedEmail::dispatch($this->order);

        $this->order->refresh();
        $this->addressSaved = true;
        $this->dispatch('address-updated');
    }

    public function render()
    {
        return view('livewire.dashboard.order-detail');
    }
}
