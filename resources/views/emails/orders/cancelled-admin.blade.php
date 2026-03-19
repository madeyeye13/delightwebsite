@component('mail::message')
# Order Cancelled by Customer
 
Order **#{{ $order->order_number }}** was cancelled by the customer.
 
- **Customer:** {{ $order->contact_name }} ({{ $order->contact_email }})
- **Order Total:** ₦{{ number_format($order->total) }}
- **Payment Method:** {{ ucfirst($order->payment_method ?? 'N/A') }}
- **Payment Ref:** {{ $order->payment_reference ?? 'N/A' }}
- **Refund Status:** {{ $order->payment_status }}
 
@component('mail::button', ['url' => config('app.url').'/admin/orders'])
View in Admin
@endcomponent
 
{{ config('app.name') }}
@endcomponent