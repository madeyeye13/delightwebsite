@component('mail::message')
# Delivery Address Updated
 
Hi {{ $order->contact_name }},
 
The delivery address for your order **#{{ $order->order_number }}** has been updated successfully.
 
**New Address:**
{{ $order->shipping_street }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}, {{ $order->shipping_country }}
@if($order->shipping_notes)
*Notes: {{ $order->shipping_notes }}*
@endif
 
If you did not make this change, please contact support immediately.
 
Thanks,
{{ config('app.name') }}
@endcomponent