<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('My Orders')]
class Orders extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::query()
            ->where('user_id', auth()->id())
            ->with(['items'])
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.dashboard.orders', [
            'orders' => $query->paginate(10),
        ]);
    }
}
