{{-- livewire/frontend/cart-sync.blade.php --}}
{{-- Invisible component. Just bridges Alpine cart store ↔ DB persistence. --}}
<div
    x-data
    @cart:initialized.window="$store.cart.items = $event.detail.items ?? []"
    @cart:synced.window="$store.cart.items = $event.detail.items ?? []"
></div>
