{{-- ============================================================
     resources/views/emails/orders/cancelled-user.blade.php
     ============================================================ --}}
@component('mail::message')
# Your order has been cancelled
 
Hi {{ $order->contact_name }},
 
Your order **#{{ $order->order_number }}** has been cancelled as requested.
 
@if($order->payment_status === 'refunded')
**Refund:** A full refund of **₦{{ number_format($order->total) }}** has been initiated back to your original payment method. Please allow 3–7 business days depending on your bank.
@endif
 
@component('mail::table')
| Item | Qty | Amount |
|:-----|:---:|-------:|
@foreach($order->items as $item)
| {{ $item->product_name }}{{ $item->variant_name ? ' — '.$item->variant_name : '' }} | {{ $item->quantity }} | ₦{{ number_format($item->total_price) }} |
@endforeach
| **Total** | | **₦{{ number_format($order->total) }}** |
@endcomponent
 
If you have any questions, please contact our support team.
 
Thanks,
{{ config('app.name') }}
@endcomponent
