<x-mail::message>
# New Order Received

A new order has been placed on the store.

<x-mail::panel>
**Order:** {{ $order->order_number }}
**Buyer:** {{ $order->contact_name }} ({{ $order->contact_email }})
**Phone:** {{ $order->contact_phone }}
**Payment:** {{ ucfirst($order->payment_method) }} — {{ ucfirst($order->payment_status) }}
**Shipping:** {{ $order->shipping_carrier }} to {{ $order->shipping_city }}, {{ $order->shipping_state }}
**Total:** ₦{{ number_format($order->total, 0) }}
</x-mail::panel>

<x-mail::table>
| Item | Qty | Price |
|------|-----|-------|
@foreach ($order->items as $item)
| {{ $item->product_name }}{{ $item->variant_name ? ' (' . $item->variant_name . ')' : '' }} | {{ $item->quantity }} | ₦{{ number_format($item->total_price, 0) }} |
@endforeach
</x-mail::table>

<x-mail::button :url="$adminOrdersUrl">
View in Admin
</x-mail::button>

**1st Delightsome Admin**
</x-mail::message>
