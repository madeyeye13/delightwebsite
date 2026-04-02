<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Order — {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#f0efed;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efed;padding:48px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">

  <tr><td style="background:#1F6F67;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

  <tr>
    <td style="padding:28px 40px 26px;border-bottom:1px solid #e8e8e6;">
      <span style="font-size:11px;font-weight:700;color:#1F6F67;letter-spacing:0.2em;text-transform:uppercase;">1st Delightsome</span>
      <span style="float:right;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:0.1em;text-transform:uppercase;">Admin</span>
    </td>
  </tr>

  <tr>
    <td style="padding:40px 40px 0;">
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#1F6F67;letter-spacing:0.15em;text-transform:uppercase;">New Order</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">A new order has been placed.</h1>
    </td>
  </tr>

  {{-- Order meta --}}
  <tr>
    <td style="padding:20px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;width:35%;">Order</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;font-weight:700;text-align:right;font-family:monospace;">{{ $order->order_number }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Buyer</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ $order->contact_name }} &nbsp;·&nbsp; {{ $order->contact_email }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Phone</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ $order->contact_phone }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Payment</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ ucfirst($order->payment_method) }} &nbsp;·&nbsp; {{ ucfirst($order->payment_status) }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Shipping</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ $order->shipping_carrier }} — {{ $order->shipping_city }}, {{ $order->shipping_state }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Total</td>
          <td style="padding:10px 0;font-size:15px;font-weight:700;color:#1F6F67;text-align:right;">{{ $order->formatPrice($order->total) }}</td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- Items --}}
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding-bottom:8px;border-bottom:1px solid #111315;width:72px;"></td>
          <td style="padding-bottom:8px;border-bottom:1px solid #111315;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Item</span>
          </td>
          <td style="padding-bottom:8px;border-bottom:1px solid #111315;text-align:center;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Qty</span>
          </td>
          <td style="padding-bottom:8px;border-bottom:1px solid #111315;text-align:right;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Price</span>
          </td>
        </tr>
        @foreach ($order->items as $item)
        @php $emailImg = $item->getEmailImageUrl(); @endphp
        <tr>
          <td style="padding:8px 8px 8px 0;border-bottom:1px solid #f0f0ee;width:72px;vertical-align:middle;">
            @if($emailImg)
            <img src="{{ $emailImg }}" width="56" height="56" alt="" style="display:block;border-radius:4px;object-fit:cover;">
            @else
            <div style="width:56px;height:56px;background:#f0efed;border-radius:4px;"></div>
            @endif
          </td>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#374151;line-height:1.5;">
            {{ $item->product_name }}
            @if($item->variant_name)
            <br><span style="font-size:12px;color:#9ca3af;">{{ $item->variant_name }}</span>
            @endif
          </td>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#525252;text-align:center;vertical-align:middle;">{{ $item->quantity }}</td>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#111315;font-weight:500;text-align:right;vertical-align:middle;">{{ $order->formatPrice($item->total_price) }}</td>
        </tr>
        @endforeach
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      <a href="{{ $adminOrdersUrl }}" style="display:inline-block;background:#111315;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">View in Admin</a>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; Admin Notification
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
