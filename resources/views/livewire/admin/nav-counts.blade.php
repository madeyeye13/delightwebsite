{{--
    Admin Nav Counts — render-less Livewire component.
    Polls every 30 seconds and dispatches 'nav-counts-updated' browser event.
    The Alpine navCounts store (initialised in admin.blade.php) receives the data.
    Listen to 'acknowledge-orders' window event to reset the "new orders" alert.
--}}
<div
    wire:init="refreshCounts"
    wire:poll.30s="refreshCounts"
    @acknowledge-orders.window="$wire.acknowledgeOrders()"
    style="display:none"
></div>
