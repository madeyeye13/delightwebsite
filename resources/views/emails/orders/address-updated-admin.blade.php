@component('mail::message')
# Delivery Address Changed
 
Order **#{{ $order->order_number }}** — address updated by customer.
 
- **Customer:** {{ $order->contact_name }} ({{ $order->contact_email }})
- **New Address:** {{ $order->shipping_street }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}
@if($order->shipping_notes)
- **Notes:** {{ $order->shipping_notes }}
@endif
 
@component('mail::button', ['url' => config('app.url').'/admin/orders'])
View Order
@endcomponent
 
{{ config('app.name') }}
@endcomponent