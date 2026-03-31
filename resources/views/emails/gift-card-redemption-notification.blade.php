<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gift Card Used — 1st Delightsome</title>
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
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:0.15em;text-transform:uppercase;">Gift Card Activity</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Your gift card was used.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Hello <strong style="color:#111315;">{{ $giftCard->getNotificationName() ?? 'Valued Customer' }}</strong>, here's a summary of recent activity on your gift card.</p>
    </td>
  </tr>

  {{-- Activity summary --}}
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Gift Card Code</span><br>
            <span style="font-size:14px;font-weight:700;color:#111315;font-family:monospace;letter-spacing:0.08em;">{{ $giftCard->code }}</span>
          </td>
        </tr>
        <tr>
          <td style="padding:12px 0;border-bottom:1px solid #e8e8e6;">
            <table width="100%" cellpadding="0" cellspacing="0"><tr>
              <td>
                <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Amount Used</span><br>
                <span style="font-size:15px;font-weight:700;color:#111315;">₦{{ number_format($amountUsed, 0) }}</span>
              </td>
              <td style="text-align:right;">
                <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Date</span><br>
                <span style="font-size:13px;color:#374151;">{{ now()->format('d M Y, g:i A') }}</span>
              </td>
            </tr></table>
          </td>
        </tr>
        <tr>
          <td style="padding:12px 0;">
            <span style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Remaining Balance</span><br>
            <span style="font-size:18px;font-weight:700;color:{{ $giftCard->current_balance > 0 ? '#1F6F67' : '#9ca3af' }};">₦{{ number_format($giftCard->current_balance, 0) }}</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:8px 40px 0;">
      @if($giftCard->current_balance > 0)
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">You still have <strong style="color:#1F6F67;">₦{{ number_format($giftCard->current_balance, 0) }}</strong> remaining. Use it on your next purchase!</p>
      @else
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Your gift card has been fully redeemed. Thank you for shopping with us!</p>
      @endif
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      <a href="{{ $shopUrl }}" style="display:inline-block;background:#1F6F67;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">{{ $giftCard->current_balance > 0 ? 'Shop Now' : 'Continue Shopping' }}</a>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        If you did not authorise this redemption, please contact us immediately.<br>
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
