<?php

// app/Livewire/Dashboard/Wishlist.php

namespace App\Livewire\Dashboard;

use App\Models\Wishlist as WishlistModel; // ← add alias
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('My Wishlist')]
class Wishlist extends Component
{
    public function removeFromWishlist(int $productId): void
    {
        WishlistModel::where('user_id', auth()->id()) // ← use alias
            ->where('product_id', $productId)
            ->delete();

        $this->dispatch('wishlist-updated');
    }

    public function render()
    {
        $items = WishlistModel::where('user_id', auth()->id()) // ← use alias
            ->with(['product.category'])
            ->latest()
            ->get();

        return view('livewire.dashboard.wishlist', compact('items'));
    }
}