<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to 1st Delightsome</title>
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
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#1F6F67;letter-spacing:0.15em;text-transform:uppercase;">Newsletter</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">Welcome to the community.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">You're now part of a community that loves beautiful fabrics and inspired style. Here's what to look forward to:</p>
    </td>
  </tr>

  {{-- Benefits list --}}
  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        @foreach(['New fabric arrivals and restocks', 'Exclusive subscriber-only offers', 'Styling tips and fabric inspiration', 'Behind-the-scenes from our store'] as $benefit)
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #f0f0ee;vertical-align:top;width:20px;">
            <span style="font-size:12px;color:#1F6F67;font-weight:700;">—</span>
          </td>
          <td style="padding:10px 0 10px 12px;border-bottom:1px solid #f0f0ee;font-size:13px;color:#374151;line-height:1.5;">{{ $benefit }}</td>
        </tr>
        @endforeach
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      <a href="{{ route('shop.index') }}" style="display:inline-block;background:#1F6F67;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">Explore Our Collections</a>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 40px;border-top:1px solid #e8e8e6;background:#fafaf8;">
      <p style="margin:0;font-size:11px;color:#9ca3af;line-height:1.8;">
        Warm regards, The 1st Delightsome Team &nbsp;·&nbsp; 30b Opebi Rd, Opebi, Ikeja, Lagos<br>
        <a href="{{ $unsubscribeUrl }}" style="color:#9ca3af;text-decoration:underline;">Unsubscribe</a> at any time.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
