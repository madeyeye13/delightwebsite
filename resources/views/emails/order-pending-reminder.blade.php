<x-mail::message>
# Your Order is Waiting

Hi {{ $order->contact_name }},

You started an order with us but payment wasn't completed. Your items are still reserved!

**Order Number:** {{ $order->order_number }}
**Amount Due:** ₦{{ number_format($order->total, 0) }}

If you'd like to complete your purchase, visit the checkout and use the same email address — your cart will be waiting.

<x-mail::button :url="$checkoutUrl">
Complete My Order
</x-mail::button>

If you no longer wish to proceed, no action is needed.

Thanks,<br>
**1st Delightsome**
</x-mail::message>
