<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delivery Address Updated — {{ $order->order_number }}</title>
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
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#1F6F67;letter-spacing:0.15em;text-transform:uppercase;">Address Updated</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Your delivery address has been updated.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Hi {{ $order->contact_name }}, the delivery address for your order <strong style="color:#111315;">#{{ $order->order_number }}</strong> has been updated successfully.</p>
    </td>
  </tr>

  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f5;border-left:3px solid #1F6F67;">
        <tr>
          <td style="padding:16px 20px;">
            <p style="margin:0 0 4px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">New Delivery Address</p>
            <p style="margin:0;font-size:13px;color:#374151;line-height:1.7;">
              {{ $order->shipping_street }}, {{ $order->shipping_city }},<br>
              {{ $order->shipping_state }}, {{ $order->shipping_country }}
            </p>
            @if($order->shipping_notes)
            <p style="margin:8px 0 0;font-size:12px;color:#9ca3af;font-style:italic;">Note: {{ $order->shipping_notes }}</p>
            @endif
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:24px 40px 0;">
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">If you did not make this change, please contact our support team immediately.</p>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      {{-- Intentionally no CTA --}}
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
