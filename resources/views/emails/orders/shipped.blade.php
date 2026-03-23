<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Order Is On Its Way</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Header --}}
        <tr>
          <td style="background:#111827;border-radius:12px 12px 0 0;padding:32px 40px;text-align:center;">
            <p style="margin:0;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.3px;">
              {{ config('app.name') }}
            </p>
            <p style="margin:8px 0 0;font-size:13px;color:#9ca3af;">Order Dispatch Notification</p>
          </td>
        </tr>

        {{-- Hero --}}
        <tr>
          <td style="background:#ffffff;padding:40px 40px 24px;text-align:center;">
            <div style="width:64px;height:64px;background:#ecfdf5;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
              <span style="font-size:30px;">🚚</span>
            </div>
            <h1 style="margin:0 0 8px;font-size:24px;font-weight:700;color:#111827;">Your order is on its way!</h1>
            <p style="margin:0;font-size:15px;color:#6b7280;">
              Hi {{ $order->contact_name }}, your order has been shipped and is heading to you.
            </p>
          </td>
        </tr>

        {{-- Order Info --}}
        <tr>
          <td style="background:#ffffff;padding:0 40px 24px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:8px;overflow:hidden;">
              <tr>
                <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                  <span style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Order Number</span><br>
                  <span style="font-size:15px;font-weight:700;color:#111827;font-family:monospace;">{{ $order->order_number }}</span>
                </td>
                @if($order->dhlShipment?->dhl_tracking_number)
                <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                  <span style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">DHL Tracking</span><br>
                  <span style="font-size:15px;font-weight:700;color:#111827;font-family:monospace;">{{ $order->dhlShipment->dhl_tracking_number }}</span>
                </td>
                @endif
              </tr>
              <tr>
                <td colspan="2" style="padding:16px 20px;">
                  <span style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Delivering To</span><br>
                  <span style="font-size:14px;color:#374151;">
                    {{ $order->shipping_street }},
                    {{ $order->shipping_city }},
                    {{ $order->shipping_state }},
                    {{ $order->shipping_country }}
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Tracking CTA --}}
        @if($order->dhlShipment?->dhl_tracking_number)
        <tr>
          <td style="background:#ffffff;padding:0 40px 32px;text-align:center;">
            <a href="{{ $order->dhlShipment->trackingUrl() }}"
               style="display:inline-block;background:#111827;color:#ffffff;font-size:14px;font-weight:600;padding:14px 32px;border-radius:8px;text-decoration:none;">
              Track My Shipment →
            </a>
            <p style="margin:12px 0 0;font-size:12px;color:#9ca3af;">
              Powered by DHL Express
            </p>
          </td>
        </tr>
        @endif

        {{-- Items --}}
        <tr>
          <td style="background:#ffffff;padding:0 40px 32px;">
            <p style="margin:0 0 12px;font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Items Shipped</p>
            @foreach($order->items as $item)
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
              <tr>
                <td style="font-size:14px;color:#374151;">
                  {{ $item->product_name }}
                  @if($item->variant_name)
                    <span style="color:#9ca3af;">— {{ $item->variant_name }}</span>
                  @endif
                </td>
                <td style="font-size:14px;color:#374151;text-align:right;white-space:nowrap;">
                  ×{{ $item->quantity }}
                </td>
              </tr>
            </table>
            @endforeach
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f9fafb;border-top:1px solid #f3f4f6;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
            <p style="margin:0 0 4px;font-size:13px;color:#6b7280;">
              Questions? Reply to this email or contact us.
            </p>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>