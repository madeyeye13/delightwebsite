<?php

namespace App\Livewire\Admin\Newsletter;

use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'active';

    public array $selectedIds = [];

    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value
            ? $this->subscribers->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function unsubscribe(int $id): void
    {
        NewsletterSubscriber::findOrFail($id)->update(['unsubscribed_at' => now()]);
        $this->dispatch('toast', type: 'success', message: 'Subscriber unsubscribed.');
    }

    public function resubscribe(int $id): void
    {
        NewsletterSubscriber::findOrFail($id)->update(['unsubscribed_at' => null, 'subscribed_at' => now()]);
        $this->dispatch('toast', type: 'success', message: 'Subscriber reactivated.');
    }

    public function delete(int $id): void
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Subscriber removed.');
    }

    public function deleteSelected(): void
    {
        NewsletterSubscriber::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('toast', type: 'success', message: 'Selected subscribers removed.');
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function getSubscribersProperty()
    {
        return NewsletterSubscriber::query()
            ->when($this->search, fn ($q) => $q->where('email', 'like', '%'.$this->search.'%'))
            ->when($this->statusFilter === 'active', fn ($q) => $q->whereNull('unsubscribed_at'))
            ->when($this->statusFilter === 'unsubscribed', fn ($q) => $q->whereNotNull('unsubscribed_at'))
            ->latest('subscribed_at')
            ->paginate(20);
    }

    public function getStatsProperty(): array
    {
        return [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::whereNull('unsubscribed_at')->count(),
            'unsubscribed' => NewsletterSubscriber::whereNotNull('unsubscribed_at')->count(),
            'this_week' => NewsletterSubscriber::whereNull('unsubscribed_at')
                ->where('subscribed_at', '>=', now()->startOfWeek())
                ->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.newsletter.newsletter-index', [
            'subscribers' => $this->subscribers,
            'stats' => $this->stats,
        ]);
    }
}
