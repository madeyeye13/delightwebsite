<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Cancelled — {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#f0efed;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efed;padding:48px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">

  <tr><td style="background:#525252;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

  <tr>
    <td style="padding:28px 40px 26px;border-bottom:1px solid #e8e8e6;">
      <span style="font-size:11px;font-weight:700;color:#1F6F67;letter-spacing:0.2em;text-transform:uppercase;">1st Delightsome</span>
    </td>
  </tr>

  <tr>
    <td style="padding:40px 40px 0;">
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:0.15em;text-transform:uppercase;">Order Cancelled</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Your order has been cancelled.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Hi {{ $order->contact_name }}, your order <strong style="color:#111315;">#{{ $order->order_number }}</strong> has been cancelled as requested.</p>
    </td>
  </tr>

  @if($order->payment_status === 'refunded')
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f5;border-left:3px solid #1F6F67;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 4px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Refund</p>
            <p style="margin:0;font-size:13px;color:#374151;line-height:1.7;">A full refund of <strong style="color:#1F6F67;">₦{{ number_format($order->total) }}</strong> has been initiated to your original payment method. Please allow 3–7 business days depending on your bank.</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  @endif

  {{-- Items table --}}
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Item</span>
          </td>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;text-align:center;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Qty</span>
          </td>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;text-align:right;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Amount</span>
          </td>
        </tr>
        @foreach($order->items as $item)
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#374151;line-height:1.5;">
            {{ $item->product_name }}
            @if($item->variant_name)
            <span style="color:#9ca3af;"> — {{ $item->variant_name }}</span>
            @endif
          </td>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#525252;text-align:center;">{{ $item->quantity }}</td>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#111315;font-weight:500;text-align:right;">₦{{ number_format($item->total_price) }}</td>
        </tr>
        @endforeach
        <tr>
          <td style="padding-top:12px;font-size:13px;font-weight:700;color:#111315;">Total</td>
          <td></td>
          <td style="padding-top:12px;font-size:13px;font-weight:700;color:#111315;text-align:right;">₦{{ number_format($order->total) }}</td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:16px 40px 0;">
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">If you have any questions, please contact our support team and we'll be happy to help.</p>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      {{-- No CTA for cancellation — nothing to push them to do --}}
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
