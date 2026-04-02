<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed — {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#f0efed;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efed;padding:48px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">

  {{-- Top accent bar --}}
  <tr><td style="background:#1F6F67;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

  {{-- Header --}}
  <tr>
    <td style="padding:28px 40px 26px;border-bottom:1px solid #e8e8e6;">
      <span style="font-size:11px;font-weight:700;color:#1F6F67;letter-spacing:0.2em;text-transform:uppercase;">1st Delightsome</span>
    </td>
  </tr>

  {{-- Hero --}}
  <tr>
    <td style="padding:40px 40px 0;">
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#D9A21B;letter-spacing:0.15em;text-transform:uppercase;">Order Confirmed</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Thank you, {{ $order->contact_name }}.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Your order has been received and is being processed. We'll send you another email when it ships.</p>
    </td>
  </tr>

  {{-- Order number tag --}}
  <tr>
    <td style="padding:24px 40px 0;">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td style="background:#f7f7f5;padding:10px 16px;border-left:3px solid #1F6F67;">
            <span style="font-size:11px;color:#9ca3af;letter-spacing:0.08em;text-transform:uppercase;font-weight:600;">Order&nbsp;&nbsp;</span>
            <span style="font-size:13px;color:#111315;font-weight:700;font-family:monospace;letter-spacing:0.05em;">{{ $order->order_number }}</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- Items table --}}
  <tr>
    <td style="padding:32px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;width:72px;"></td>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Item</span>
          </td>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;text-align:center;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Qty</span>
          </td>
          <td style="padding-bottom:10px;border-bottom:1px solid #111315;text-align:right;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Total</span>
          </td>
        </tr>
        @foreach ($order->items as $item)
        @php $emailImg = $item->getEmailImageUrl(); @endphp
        <tr>
          <td style="padding:12px 8px 12px 0;border-bottom:1px solid #e8e8e6;width:72px;vertical-align:middle;">
            @if($emailImg)
            <img src="{{ $emailImg }}" width="60" height="60" alt="" style="display:block;border-radius:4px;object-fit:cover;">
            @else
            <div style="width:60px;height:60px;background:#f0efed;border-radius:4px;"></div>
            @endif
          </td>
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;line-height:1.5;">
            {{ $item->product_name }}
            @if($item->variant_name)
            <br><span style="font-size:12px;color:#9ca3af;">{{ $item->variant_name }}</span>
            @endif
          </td>
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#525252;text-align:center;vertical-align:middle;">{{ $item->quantity }}</td>
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;font-weight:600;text-align:right;white-space:nowrap;vertical-align:middle;">{{ $order->formatPrice($item->total_price) }}</td>
        </tr>
        @endforeach
      </table>
    </td>
  </tr>

  {{-- Totals --}}
  <tr>
    <td style="padding:0 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" style="padding-top:12px;font-size:13px;color:#525252;text-align:right;">Subtotal &nbsp;&nbsp; {{ $order->formatPrice($order->subtotal) }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
          <td colspan="2" style="padding-top:6px;font-size:13px;color:#D9A21B;text-align:right;">Discount &nbsp;&nbsp; −{{ $order->formatPrice($order->discount_amount) }}</td>
        </tr>
        @endif
        @if($order->shipping_cost > 0 || !$hasGiftCardProducts)
        <tr>
          <td colspan="2" style="padding-top:6px;font-size:13px;color:#525252;text-align:right;">
            Shipping{{ $order->shipping_carrier ? ' · '.$order->shipping_carrier : '' }} &nbsp;&nbsp;
            {{ $order->shipping_cost > 0 ? $order->formatPrice($order->shipping_cost) : 'Free' }}
          </td>
        </tr>
        @endif
        <tr>
          <td colspan="2" style="padding-top:14px;border-top:1px solid #e8e8e6;margin-top:14px;">
            <table width="100%" cellpadding="0" cellspacing="0"><tr>
              <td style="font-size:14px;font-weight:700;color:#111315;padding-top:12px;">Total Paid</td>
              <td style="font-size:14px;font-weight:700;color:#111315;text-align:right;padding-top:12px;">{{ $order->formatPrice($order->total) }}</td>
            </tr></table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- Shipping address --}}
  @if($order->shipping_street)
  <tr>
    <td style="padding:28px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f5;border-left:3px solid #e8e8e6;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 4px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Shipping To</p>
            <p style="margin:0;font-size:13px;color:#374151;line-height:1.6;">{{ $order->shipping_street }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  @endif

  @if($hasGiftCardProducts)
  <tr>
    <td style="padding:24px 40px 0;">
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Your gift card code(s) will be sent in a separate email once payment is confirmed.</p>
    </td>
  </tr>
  @endif

  {{-- CTA --}}
  <tr>
    <td style="padding:36px 40px 40px;">
      <a href="{{ $shopUrl }}" style="display:inline-block;background:#1F6F67;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">Continue Shopping</a>
    </td>
  </tr>

  {{-- Footer --}}
  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos<br>
        Questions? Contact us and we'll get back to you within 24 hours.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
