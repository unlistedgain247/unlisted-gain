<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f5f6fa; padding:24px; margin:0;">
    <div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #eaeaea;">
        @include('emails.partials.header')

        @php
            $isSell       = strtolower($type) === 'sell';
            $verb         = $isSell ? 'Sell' : 'Buy';
            $accent       = $isSell ? '#2563eb' : '#1e8e3e';
            $accentBg     = $isSell ? '#eff6ff' : '#f0fbf3';
            $accentBorder = $isSell ? '#bfdbfe' : '#cdeed7';
        @endphp

        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; background:{{ $accentBg }}; border-bottom:1px solid {{ $accentBorder }};">
            <tr>
                <td style="padding:16px 32px;">
                    <table cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                        <tr>
                            <td style="width:26px; height:26px; border-radius:50%; background:{{ $accent }}; color:#fff; font-size:13px; font-weight:700; text-align:center; vertical-align:middle;">{!! $isSell ? '&#8595;' : '&#8593;' !!}</td>
                            <td style="padding-left:10px; color:{{ $accent }}; font-weight:700; font-size:14px; letter-spacing:.3px; text-transform:uppercase;">{{ $verb }} Order Received</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="padding:32px;">
            <p style="color:#7c8698; font-size:12px; letter-spacing:1px; text-transform:uppercase; margin:0 0 4px;">UnlistedGain</p>
            <h2 style="margin:0 0 16px; color:#222;">{{ $verb }} Order Received</h2>
            <p style="color:#444; font-size:15px; margin:0 0 20px;">
                We've received your request to {{ strtolower($verb) }} shares of <strong>{{ $stockName }}</strong>. Our team will review it and get in touch shortly to complete the process.
            </p>

            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:20px;">
                <tr>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#7c8698; font-size:13px;">Order ID</td>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#222; font-size:13px; text-align:right; font-weight:600;">#{{ $orderId }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#7c8698; font-size:13px;">Company</td>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#222; font-size:13px; text-align:right; font-weight:600;">{{ $stockName }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#7c8698; font-size:13px;">Order Type</td>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:{{ $accent }}; font-size:13px; text-align:right; font-weight:700; text-transform:uppercase;">{{ $verb }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#7c8698; font-size:13px;">Quantity</td>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#222; font-size:13px; text-align:right; font-weight:600;">{{ number_format($quantity) }} shares</td>
                </tr>
                <tr>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#7c8698; font-size:13px;">Price / Share</td>
                    <td style="padding:10px 0; border-bottom:1px solid #eee; color:#222; font-size:13px; text-align:right; font-weight:600;">&#8377;{{ number_format($pricePerShare, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:14px 0 0; color:#222; font-size:14px; font-weight:700;">Total Amount</td>
                    <td style="padding:14px 0 0; color:{{ $accent }}; font-size:16px; text-align:right; font-weight:700;">&#8377;{{ number_format($amount, 2) }}</td>
                </tr>
            </table>

            <p style="color:#888; font-size:13px; margin:0;">This is a request confirmation, not a final trade confirmation. Our team will reach out to verify details and proceed with settlement.</p>
        </div>
        @include('emails.partials.footer')
    </div>
</body>
</html>
