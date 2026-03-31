<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ContactIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'unread';

    public ?Contact $viewing = null;

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
            ? $this->contacts->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function viewMessage(int $id): void
    {
        $this->viewing = Contact::findOrFail($id);

        if ($this->viewing->read_at === null) {
            $this->viewing->update(['read_at' => now()]);
            $this->dispatch('contact-read');
        }
    }

    public function closeMessage(): void
    {
        $this->viewing = null;
    }

    public function markAsRead(int $id): void
    {
        Contact::findOrFail($id)->update(['read_at' => now()]);
        $this->dispatch('contact-read');
    }

    public function markAsUnread(int $id): void
    {
        Contact::findOrFail($id)->update(['read_at' => null]);
        $this->dispatch('contact-read');
    }

    public function delete(int $id): void
    {
        Contact::findOrFail($id)->delete();

        if ($this->viewing?->id === $id) {
            $this->viewing = null;
        }

        $this->dispatch('toast', type: 'success', message: 'Message deleted.');
    }

    public function deleteSelected(): void
    {
        Contact::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('toast', type: 'success', message: 'Selected messages deleted.');
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function getContactsProperty()
    {
        return Contact::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('message', 'like', '%'.$this->search.'%');
            }))
            ->when($this->statusFilter === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($this->statusFilter === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->latest()
            ->paginate(15);
    }

    public function getUnreadCountProperty(): int
    {
        return Contact::whereNull('read_at')->count();
    }

    public function render(): View
    {
        return view('livewire.admin.contacts.contact-index', [
            'contacts' => $this->contacts,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
