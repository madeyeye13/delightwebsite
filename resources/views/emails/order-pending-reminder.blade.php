<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete Your Order — 1st Delightsome</title>
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
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#D9A21B;letter-spacing:0.15em;text-transform:uppercase;">Pending Order</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Your items are still waiting.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Hi {{ $order->contact_name }}, you started an order with us but payment wasn't completed. Your items are still reserved for you.</p>
    </td>
  </tr>

  {{-- Order details --}}
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f5;border-left:3px solid #D9A21B;">
        <tr>
          <td style="padding:16px 20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;padding-bottom:4px;">Order Number</td>
                <td style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;padding-bottom:4px;text-align:right;">Amount Due</td>
              </tr>
              <tr>
                <td style="font-size:14px;font-weight:700;color:#111315;font-family:monospace;">{{ $order->order_number }}</td>
                <td style="font-size:14px;font-weight:700;color:#111315;text-align:right;">₦{{ number_format($order->total, 0) }}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:16px 40px 0;">
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Visit checkout using the same email address and your cart will be waiting for you.</p>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      <a href="{{ $checkoutUrl }}" style="display:inline-block;background:#1F6F67;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">Complete My Order</a>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        If you no longer wish to proceed, no action is needed.<br>
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
