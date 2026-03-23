<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Order Has Been Delivered</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Header --}}
        <tr>
          <td style="background:#111827;border-radius:12px 12px 0 0;padding:32px 40px;text-align:center;">
            <p style="margin:0;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.3px;">
              {{ config('app.name') }}
            </p>
            <p style="margin:8px 0 0;font-size:13px;color:#9ca3af;">Delivery Confirmation</p>
          </td>
        </tr>

        {{-- Hero --}}
        <tr>
          <td style="background:#ffffff;padding:40px 40px 24px;text-align:center;">
            <div style="width:64px;height:64px;background:#ecfdf5;border-radius:50%;margin:0 auto 20px;">
              <span style="font-size:30px;line-height:64px;">✅</span>
            </div>
            <h1 style="margin:0 0 8px;font-size:24px;font-weight:700;color:#111827;">Your order has arrived!</h1>
            <p style="margin:0;font-size:15px;color:#6b7280;">
              Hi {{ $order->contact_name }}, your order has been delivered. We hope you love it!
            </p>
          </td>
        </tr>

        {{-- Order Summary --}}
        <tr>
          <td style="background:#ffffff;padding:0 40px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:8px;padding:16px 20px;">
              <tr>
                <td style="padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                  <span style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Order</span>
                  <span style="float:right;font-size:14px;font-weight:700;color:#111827;font-family:monospace;">{{ $order->order_number }}</span>
                </td>
              </tr>
              @foreach($order->items as $item)
              <tr>
                <td style="padding-top:10px;">
                  <span style="font-size:14px;color:#374151;">
                    {{ $item->product_name }}
                    @if($item->variant_name) <span style="color:#9ca3af;">— {{ $item->variant_name }}</span> @endif
                  </span>
                  <span style="float:right;font-size:14px;color:#6b7280;">×{{ $item->quantity }}</span>
                </td>
              </tr>
              @endforeach
              <tr>
                <td style="padding-top:12px;border-top:1px solid #f3f4f6;margin-top:12px;">
                  <span style="font-size:14px;font-weight:700;color:#111827;">Total Paid</span>
                  <span style="float:right;font-size:14px;font-weight:700;color:#111827;">₦{{ number_format($order->total) }}</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="background:#ffffff;padding:0 40px 32px;text-align:center;">
            <a href="{{ route('shop.index') }}"
               style="display:inline-block;background:#111827;color:#ffffff;font-size:14px;font-weight:600;padding:14px 32px;border-radius:8px;text-decoration:none;">
              Shop Again
            </a>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f9fafb;border-top:1px solid #f3f4f6;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
            <p style="margin:0 0 4px;font-size:13px;color:#6b7280;">
              Enjoying your purchase? Tell a friend about us.
            </p>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>