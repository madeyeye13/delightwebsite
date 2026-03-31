<?php

namespace App\Livewire\Dashboard;

use App\Models\GiftCard;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('My Gift Cards')]
class GiftCards extends Component
{
    /** @var array<int, bool> */
    public array $revealedCodes = [];

    public function toggleCode(int $id): void
    {
        $this->revealedCodes[$id] = ! ($this->revealedCodes[$id] ?? false);
    }

    public function render(): View
    {
        /** @var Collection<int, GiftCard> $cards */
        $cards = GiftCard::query()
            ->where(function ($query) {
                $query->where('purchased_by_user_id', auth()->id())
                    ->orWhere('recipient_email', auth()->user()->email);
            })
            ->with(['purchasedOrder'])
            ->latest()
            ->get();

        return view('livewire.dashboard.gift-cards', [
            'cards' => $cards,
        ]);
    }
}
