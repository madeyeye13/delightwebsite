<?php

namespace App\Livewire\Admin\Orders;

use App\Jobs\SendOrderCancelledEmail;
use App\Jobs\SendOrderDeliveredEmail;
use App\Jobs\SendOrderShippedEmail;
use App\Models\DhlShipment;
use App\Models\Order;
use App\Services\DHLService;
use App\Services\FlutterwaveService;
use App\Services\PaystackService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $paymentFilter = '';

    public ?Order $viewing = null;

    public array $selectedIds = [];

    public bool $selectAll = false;

    // ─── Create DHL Shipment modal ────────────────────────────────────────────
    public bool $showShipmentModal = false;

    public ?int $creatingShipmentForId = null;

    public string $shipmentPhone = '';

    public string $shipmentError = '';

    // ─── Cancel order modal ───────────────────────────────────────────────────
    public bool $showCancelModal = false;

    public ?int $cancellingOrderId = null;

    public string $cancellationReason = '';

    public string $cancelError = '';

    // ─── Pagination resets ────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedPaymentFilter(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value
            ? $this->orders->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    private function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    // ─── Drawer ───────────────────────────────────────────────────────────────

    public function viewOrder(int $id): void
    {
        $this->viewing = Order::with(['items', 'user', 'dhlShipment'])->findOrFail($id);
    }

    public function closeOrder(): void
    {
        $this->viewing = null;
    }

    // ─── Status update (triggers emails) ─────────────────────────────────────

    public function updateStatus(int $id, string $status): void
    {
        $order = Order::findOrFail($id);
        $previousStatus = $order->status;

        $order->update(['status' => $status]);

        // Fire email only when genuinely transitioning into these statuses
        if ($status === 'shipped' && $previousStatus !== 'shipped') {
            SendOrderShippedEmail::dispatch($order->fresh())->onQueue('emails');
        } elseif ($status === 'delivered' && $previousStatus !== 'delivered') {
            SendOrderDeliveredEmail::dispatch($order->fresh())->onQueue('emails');
        }

        // Refresh open drawer
        if ($this->viewing && $this->viewing->id === $id) {
            $this->viewing = $order->fresh(['items', 'user', 'dhlShipment']);
        }
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function deleteOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->dhlShipment?->delete();
        $order->delete();

        if ($this->viewing && $this->viewing->id === $id) {
            $this->viewing = null;
        }

        $this->selectedIds = array_values(array_filter($this->selectedIds, fn ($sid) => (int) $sid !== $id));
        session()->flash('success', 'Order deleted.');
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = count($this->selectedIds);
        $orders = Order::whereIn('id', $this->selectedIds)->get();

        foreach ($orders as $order) {
            $order->items()->delete();
            $order->dhlShipment?->delete();
            $order->delete();
        }

        if ($this->viewing && in_array((string) $this->viewing->id, $this->selectedIds)) {
            $this->viewing = null;
        }

        $this->clearSelection();
        session()->flash('success', "{$count} order(s) deleted.");
    }

    // ─── Create DHL Shipment ──────────────────────────────────────────────────

    public function openCreateShipmentModal(int $id): void
    {
        $order = Order::findOrFail($id);

        $this->creatingShipmentForId = $id;
        $this->shipmentPhone = $order->contact_phone ?? '';
        $this->shipmentError = '';
        $this->showShipmentModal = true;
    }

    public function createDhlShipment(): void
    {
        $this->shipmentError = '';

        $this->validate([
            'shipmentPhone' => 'required|string|min:7|max:25',
        ], [
            'shipmentPhone.required' => 'Receiver phone number is required for DHL.',
        ]);

        $order = Order::with('items')->findOrFail($this->creatingShipmentForId);

        /** @var DHLService $dhl */
        $dhl = app(DHLService::class);
        $result = $dhl->createShipment([
            'product_code' => 'P',
            'receiver_name' => $order->contact_name,
            'receiver_email' => $order->contact_email,
            'receiver_phone' => $this->shipmentPhone,
            'receiver_address' => $order->shipping_street ?? 'N/A',
            'receiver_city' => $order->shipping_city,
            'receiver_country_code' => $order->shipping_country, // stored as 2-letter ISO code
            'receiver_postal' => $order->shipping_postal,
            'total_weight' => $order->getTotalWeight(),
            'declared_value' => (float) ($order->total / 100), // convert kobo → NGN if needed; adjust if already in NGN
            'currency' => $order->currency ?? 'NGN',
            'invoice_number' => $order->order_number,
            'invoice_date' => $order->created_at->format('Y-m-d'),
            'line_items' => $order->items->map(fn ($item) => [
                'description' => $item->product_name,
                'price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'weight' => round(($item->weight_kg ?? 1.5) * $item->quantity, 3),
            ])->toArray(),
        ]);

        if (! $result['success']) {
            $this->shipmentError = $result['error'];

            return; // keep modal open so admin sees the error
        }

        // Persist DHL shipment record
        DhlShipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'dhl_tracking_number' => $result['tracking_number'],
                'tracking_url' => $result['tracking_url'],
                'shipment_id' => $result['shipment_id'],
                'product_code' => 'P',
                'total_weight' => round($order->getTotalWeight(), 3),
                'weight_unit' => 'kg',
                'label_data' => $result['documents'][0]['content'] ?? null,
                'label_format' => $result['documents'][0]['imageFormat'] ?? 'PDF',
                'shipment_response' => $result['raw_response'] ?? null,
                'status' => 'created',
                'shipped_at' => now(),
            ]
        );

        // Mark order shipped + email
        $order->update(['status' => 'shipped']);
        SendOrderShippedEmail::dispatch($order->fresh())->onQueue('emails');

        // Close modal, reset state
        $this->showShipmentModal = false;
        $this->creatingShipmentForId = null;
        $this->shipmentPhone = '';

        // Refresh drawer if open
        if ($this->viewing && $this->viewing->id === $order->id) {
            $this->viewing = $order->fresh(['items', 'user', 'dhlShipment']);
        }

        session()->flash('success', 'DHL shipment created. Tracking #: '.$result['tracking_number']);
    }

    // ─── Cancel Order (with auto-refund) ─────────────────────────────────────

    public function openCancelModal(int $id): void
    {
        $this->cancellingOrderId = $id;
        $this->cancellationReason = '';
        $this->cancelError = '';
        $this->showCancelModal = true;
    }

    public function confirmCancelOrder(): void
    {
        $this->cancelError = '';

        $this->validate([
            'cancellationReason' => 'required|string|min:5|max:500',
        ], [
            'cancellationReason.required' => 'Please provide a cancellation reason.',
            'cancellationReason.min' => 'Reason must be at least 5 characters.',
        ]);

        $order = Order::findOrFail($this->cancellingOrderId);

        if (! $order->canBeCancelled()) {
            $this->cancelError = 'This order cannot be cancelled (it may already be shipped or cancelled).';

            return;
        }

        // Capture paid status BEFORE any updates
        $wasPaid = $order->isPaid() && ! empty($order->payment_reference);

        // Process refund if order was paid
        if ($wasPaid) {
            $refunded = $this->processRefund($order);

            if (! $refunded) {
                $this->cancelError = 'Refund via payment gateway failed. Please process it manually, then retry.';

                return;
            }
        }

        $order->update([
            'status' => 'cancelled',
            'payment_status' => $wasPaid ? 'refunded' : $order->payment_status,
            'admin_notes' => trim(
                ($order->admin_notes ?? '').
                "\n[".now()->format('Y-m-d H:i').'] Cancelled by admin: '.$this->cancellationReason
            ),
        ]);

        // Dispatch cancellation email if the job exists
        if (class_exists(SendOrderCancelledEmail::class)) {
            SendOrderCancelledEmail::dispatch($order->fresh())->onQueue('emails');
        }

        $this->showCancelModal = false;
        $this->cancellingOrderId = null;
        $this->cancellationReason = '';

        if ($this->viewing && $this->viewing->id === $order->id) {
            $this->viewing = $order->fresh(['items', 'user', 'dhlShipment']);
        }

        $message = $wasPaid
            ? 'Order cancelled and refund has been initiated.'
            : 'Order cancelled successfully.';

        session()->flash('success', $message);
    }

    /**
     * Attempt a refund via the appropriate payment gateway.
     * Returns true on success, false on failure.
     */
    private function processRefund(Order $order): bool
    {
        try {
            if ($order->payment_method === 'paystack') {
                $result = app(PaystackService::class)->refundTransaction([
                    'transaction' => $order->payment_reference,
                ]);

                // Paystack returns ['status' => true] on success
                return $result['status'] === true || $result['status'] === 'true';
            }

            if ($order->payment_method === 'flutterwave') {
                $result = app(FlutterwaveService::class)->refundTransaction(
                    $order->payment_reference
                );

                return $result['status'] === 'success';
            }

            Log::warning("No refund handler for payment method: {$order->payment_method}", [
                'order' => $order->order_number,
            ]);

            return false;

        } catch (\Throwable $e) {
            Log::error("Refund failed for order {$order->order_number}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    #[Computed]
    public function orders()
    {
        return Order::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('order_number', 'like', '%'.$this->search.'%')
                        ->orWhere('contact_name', 'like', '%'.$this->search.'%')
                        ->orWhere('contact_email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->paymentFilter, fn ($q) => $q->where('payment_status', $this->paymentFilter))
            ->latest()
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.admin.orders.order-index');
    }
}
