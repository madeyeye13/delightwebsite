<?php

namespace App\Livewire\Frontend;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class WishlistToggle extends Component
{
    public int $productId;

    public bool $wishlisted = false;

    public function mount(int $productId): void
    {
        $this->productId = $productId;

        if (Auth::check()) {
            $this->wishlisted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->exists();
        }
    }

    public function toggle(): void
    {
        if (! Auth::check()) {
            $this->dispatch('open-auth-modal');

            return;
        }

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->wishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $this->productId,
            ]);
            $this->wishlisted = true;
        }
    }

    public function render(): View
    {
        return view('livewire.frontend.wishlist-toggle');
    }
}
