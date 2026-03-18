<x-mail::message>
# Order Confirmed!

Thank you, **{{ $order->contact_name }}**. Your order has been received and is being processed.

**Order Number:** {{ $order->order_number }}

<x-mail::table>
| Item | Qty | Total |
|------|-----|-------|
@foreach ($order->items as $item)
| {{ $item->product_name }}{{ $item->variant_name ? ' (' . $item->variant_name . ')' : '' }} | {{ $item->quantity }} | ₦{{ number_format($item->total_price, 0) }} |
@endforeach
</x-mail::table>

<x-mail::panel>
**Subtotal:** ₦{{ number_format($order->subtotal, 0) }}
@if ($order->discount_amount > 0)
**Discount:** −₦{{ number_format($order->discount_amount, 0) }}
@endif
**Shipping ({{ $order->shipping_carrier }}):** {{ $order->shipping_cost > 0 ? '₦' . number_format($order->shipping_cost, 0) : 'Free' }}
**Total Paid:** ₦{{ number_format($order->total, 0) }}
</x-mail::panel>

**Shipping to:** {{ $order->shipping_street }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}

We'll notify you when your order ships.

<x-mail::button :url="$shopUrl">
Continue Shopping
</x-mail::button>

Thanks for your order,<br>
**1st Delightsome**
</x-mail::message>
