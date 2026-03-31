<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Order Is On Its Way — {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#f0efed;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efed;padding:48px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">

  <tr><td style="background:#1F6F67;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

  <tr>
    <td style="padding:28px 40px 26px;border-bottom:1px solid #e8e8e6;">
      <span style="font-size:11px;font-weight:700;color:#1F6F67;letter-spacing:0.2em;text-transform:uppercase;">1st Delightsome</span>
    </td>
  </tr>

  <tr>
    <td style="padding:40px 40px 0;">
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#1F6F67;letter-spacing:0.15em;text-transform:uppercase;">Order Dispatched</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Your order is on its way.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Hi {{ $order->contact_name }}, your order has been shipped and is heading to you.</p>
    </td>
  </tr>

  {{-- Order & Tracking Info --}}
  <tr>
    <td style="padding:28px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Order Number</span><br>
            <span style="font-size:14px;font-weight:700;color:#111315;font-family:monospace;letter-spacing:0.04em;">{{ $order->order_number }}</span>
          </td>
          @if($order->dhlShipment?->dhl_tracking_number)
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;text-align:right;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">DHL Tracking</span><br>
            <span style="font-size:14px;font-weight:700;color:#1F6F67;font-family:monospace;letter-spacing:0.04em;">{{ $order->dhlShipment->dhl_tracking_number }}</span>
          </td>
          @endif
        </tr>
        <tr>
          <td colspan="2" style="padding:12px 0;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Delivering To</span><br>
            <span style="font-size:13px;color:#374151;line-height:1.7;">{{ $order->shipping_street }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}, {{ $order->shipping_country }}</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- Items --}}
  <tr>
    <td style="padding:0 40px 0;">
      <p style="margin:0 0 10px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Items Shipped</p>
      @foreach($order->items as $item)
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:8px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#374151;">
            {{ $item->product_name }}
            @if($item->variant_name)
            <span style="color:#9ca3af;"> — {{ $item->variant_name }}</span>
            @endif
          </td>
          <td style="padding:8px 0;border-bottom:1px solid #f0f0ee;font-size:13px;color:#9ca3af;text-align:right;">×{{ $item->quantity }}</td>
        </tr>
      </table>
      @endforeach
    </td>
  </tr>

  {{-- Track CTA --}}
  <tr>
    <td style="padding:36px 40px 40px;">
      @if($order->dhlShipment?->dhl_tracking_number)
      <a href="{{ $order->dhlShipment->trackingUrl() }}" style="display:inline-block;background:#1F6F67;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;margin-right:12px;">Track Shipment →</a>
      <span style="font-size:11px;color:#9ca3af;">Powered by DHL Express</span>
      @else
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">No tracking number yet — we'll update you as soon as it's available.</p>
      @endif
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos<br>
        Questions? Reply to this email and we'll be happy to help.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
