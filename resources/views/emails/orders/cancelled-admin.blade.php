<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Cancelled by Customer — {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#f0efed;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efed;padding:48px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">

  <tr><td style="background:#525252;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

  <tr>
    <td style="padding:28px 40px 26px;border-bottom:1px solid #e8e8e6;">
      <span style="font-size:11px;font-weight:700;color:#1F6F67;letter-spacing:0.2em;text-transform:uppercase;">1st Delightsome</span>
      <span style="float:right;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:0.1em;text-transform:uppercase;">Admin</span>
    </td>
  </tr>

  <tr>
    <td style="padding:40px 40px 0;">
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:0.15em;text-transform:uppercase;">Admin Notification</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Order cancelled by customer.</h1>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;width:40%;">Order</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;font-weight:600;text-align:right;font-family:monospace;">{{ $order->order_number }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Customer</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ $order->contact_name }} &nbsp;·&nbsp; {{ $order->contact_email }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Order Total</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;font-weight:600;text-align:right;">{{ $order->formatPrice($order->total) }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Payment</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Reference</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;font-family:monospace;text-align:right;">{{ $order->payment_reference ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Refund Status</td>
          <td style="padding:10px 0;font-size:13px;color:#111315;font-weight:600;text-align:right;">{{ ucfirst($order->payment_status) }}</td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      <a href="{{ config('app.url') }}/admin/orders" style="display:inline-block;background:#111315;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">View in Admin</a>
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
