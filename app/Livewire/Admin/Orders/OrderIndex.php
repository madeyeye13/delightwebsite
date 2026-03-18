<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
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

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedPaymentFilter(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value
            ? $this->orders->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function viewOrder(int $id): void
    {
        $this->viewing = Order::with('items', 'user')->findOrFail($id);
    }

    public function closeOrder(): void
    {
        $this->viewing = null;
    }

    public function updateStatus(int $id, string $status): void
    {
        Order::findOrFail($id)->update(['status' => $status]);
    }

    public function deleteOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
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
            $order->delete();
        }

        if ($this->viewing && in_array((string) $this->viewing->id, $this->selectedIds)) {
            $this->viewing = null;
        }

        $this->selectedIds = [];
        $this->selectAll = false;
        session()->flash('success', "{$count} order(s) deleted.");
    }

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
