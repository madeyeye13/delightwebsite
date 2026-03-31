<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Gift Card from 1st Delightsome</title>
</head>
<body style="margin:0;padding:0;background:#f0efed;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0efed;padding:48px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">

  <tr><td style="background:#D9A21B;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

  <tr>
    <td style="padding:28px 40px 26px;border-bottom:1px solid #e8e8e6;">
      <span style="font-size:11px;font-weight:700;color:#1F6F67;letter-spacing:0.2em;text-transform:uppercase;">1st Delightsome</span>
    </td>
  </tr>

  <tr>
    <td style="padding:40px 40px 0;">
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#D9A21B;letter-spacing:0.15em;text-transform:uppercase;">Gift Card</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Your gift card is ready.</h1>

      @if($giftCard->recipient_email && $giftCard->recipient_email !== $order->contact_email)
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">
        Hello <strong style="color:#111315;">{{ $giftCard->getNotificationName() ?? $order->contact_name }}</strong>,
        someone has sent you a gift card for 1st Delightsome.
      </p>
      @if($giftCard->personal_message)
      <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;border-left:3px solid #D9A21B;background:#fffdf5;">
        <tr>
          <td style="padding:14px 18px;font-size:13px;color:#525252;font-style:italic;line-height:1.7;">"{{ $giftCard->personal_message }}"</td>
        </tr>
      </table>
      @endif
      @else
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">Thank you for your order, <strong style="color:#111315;">{{ $giftCard->getNotificationName() ?? $order->contact_name }}</strong>. Your gift card is ready to use.</p>
      @endif
    </td>
  </tr>

  {{-- Gift card code block --}}
  <tr>
    <td style="padding:28px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #111315;">
        <tr>
          <td style="padding:28px 32px;text-align:center;">
            <p style="margin:0 0 8px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.15em;text-transform:uppercase;">Gift Card Code</p>
            <p style="margin:0 0 16px;font-size:26px;font-weight:700;color:#111315;font-family:monospace;letter-spacing:0.18em;">{{ $giftCard->code }}</p>
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="padding:0 20px;border-right:1px solid #e8e8e6;text-align:center;">
                  <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Value</p>
                  <p style="margin:0;font-size:15px;font-weight:700;color:#1F6F67;">₦{{ number_format($giftCard->current_balance, 0) }}</p>
                </td>
                <td style="padding:0 20px;text-align:center;">
                  <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Expires</p>
                  <p style="margin:0;font-size:14px;font-weight:600;color:#111315;">
                    @if($giftCard->expires_at)
                    {{ $giftCard->expires_at->format('d M Y') }}
                    @else
                    Never
                    @endif
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- How to use --}}
  <tr>
    <td style="padding:28px 40px 0;">
      <p style="margin:0 0 14px;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">How to Redeem</p>
      <table width="100%" cellpadding="0" cellspacing="0">
        @foreach(['Add your items to cart', 'Proceed to checkout', 'Enter the code above in the "Have a gift card?" section', 'Your balance will be applied automatically'] as $i => $step)
        <tr>
          <td style="padding:8px 0;border-bottom:1px solid #f0f0ee;vertical-align:top;width:24px;">
            <span style="font-size:11px;font-weight:700;color:#1F6F67;">{{ $i + 1 }}.</span>
          </td>
          <td style="padding:8px 0 8px 10px;border-bottom:1px solid #f0f0ee;font-size:13px;color:#374151;line-height:1.5;">{{ $step }}</td>
        </tr>
        @endforeach
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:36px 40px 40px;">
      <a href="{{ $shopUrl }}" style="display:inline-block;background:#1F6F67;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">Shop Now</a>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        © {{ date('Y') }} 1st Delightsome &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos<br>
        Order reference: {{ $order->order_number }}
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
