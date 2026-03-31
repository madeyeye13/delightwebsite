<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Comment Awaiting Moderation</title>
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
      <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:0.15em;text-transform:uppercase;">Moderation Required</p>
      <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#111315;line-height:1.25;letter-spacing:-0.02em;">New comment awaiting approval.</h1>
      <p style="margin:0;font-size:13px;color:#525252;line-height:1.7;">A reader has submitted a comment on your blog that is waiting for moderation.</p>
    </td>
  </tr>

  <tr>
    <td style="padding:24px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;width:30%;">Author</td>
          <td style="padding:10px 0;border-bottom:1px solid #e8e8e6;font-size:13px;color:#111315;text-align:right;">{{ $comment->name }} &nbsp;·&nbsp; {{ $comment->email }}</td>
        </tr>
        <tr>
          <td style="padding:10px 0;border-bottom:{{ $comment->parent_id ? '1px solid #e8e8e6' : 'none' }};font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Post</td>
          <td style="padding:10px 0;border-bottom:{{ $comment->parent_id ? '1px solid #e8e8e6' : 'none' }};font-size:13px;color:#111315;text-align:right;">{{ $comment->post?->title ?? '—' }}</td>
        </tr>
        @if($comment->parent_id)
        <tr>
          <td style="padding:10px 0;font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:0.12em;text-transform:uppercase;">Type</td>
          <td style="padding:10px 0;font-size:13px;color:#525252;text-align:right;">Reply to another comment</td>
        </tr>
        @endif
      </table>
    </td>
  </tr>

  {{-- Comment body --}}
  <tr>
    <td style="padding:20px 40px 0;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f5;border-left:3px solid #1F6F67;">
        <tr>
          <td style="padding:16px 20px;font-size:13px;color:#374151;line-height:1.7;">{{ $comment->body }}</td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:28px 40px 40px;">
      <a href="{{ $moderationUrl }}" style="display:inline-block;background:#111315;color:#ffffff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">Review Comment</a>
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
