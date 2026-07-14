@if($orders->isEmpty())
<div style="text-align:center;padding:40px;color:#9ca3af;">
    <i class="fa-solid fa-inbox" style="font-size:28px;margin-bottom:10px;display:block;"></i>
    No orders found for this user.
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;font-size:12px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap;">#</th>
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap;">Share</th>
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap;">Date</th>
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap;">Dealer</th>
            <th style="padding:8px 10px;text-align:center;font-weight:600;color:#6b7280;white-space:nowrap;">Type</th>
            <th style="padding:8px 10px;text-align:right;font-weight:600;color:#6b7280;white-space:nowrap;">Qty</th>
            <th style="padding:8px 10px;text-align:right;font-weight:600;color:#6b7280;white-space:nowrap;">Price</th>
            <th style="padding:8px 10px;text-align:right;font-weight:600;color:#6b7280;white-space:nowrap;">Amount</th>
            <th style="padding:8px 10px;text-align:center;font-weight:600;color:#6b7280;white-space:nowrap;">Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders as $o)
    @php
        $statusColor = match($o->UL_ORD_STATUS) {
            'Completed'  => ['bg'=>'#d1fae5','color'=>'#065f46','border'=>'#a7f3d0'],
            'Pending'    => ['bg'=>'#fef9c3','color'=>'#854d0e','border'=>'#fde68a'],
            'Processing' => ['bg'=>'#dbeafe','color'=>'#1d4ed8','border'=>'#bfdbfe'],
            'Cancelled'  => ['bg'=>'#fee2e2','color'=>'#b91c1c','border'=>'#fecaca'],
            default      => ['bg'=>'#f3f4f6','color'=>'#6b7280','border'=>'#e5e7eb'],
        };
    @endphp
    <tr style="border-bottom:1px solid #f0f0f0;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
        <td style="padding:8px 10px;font-weight:600;color:#3b82f6;font-family:monospace;">{{ $o->UL_ORD_ID }}</td>
        <td style="padding:8px 10px;max-width:160px;">
            <div style="font-weight:600;color:#111;line-height:1.2;">{{ $o->UL_STOCKS_S_NAME ?: $o->UL_STOCKS_COMPNAME ?: '—' }}</div>
        </td>
        <td style="padding:8px 10px;white-space:nowrap;color:#374151;">
            @if($o->UL_ORD_DATE)
                {{ \Carbon\Carbon::parse($o->UL_ORD_DATE)->format('d M Y') }}
            @elseif($o->UL_ORD_INSERT_TIME)
                {{ \Carbon\Carbon::parse($o->UL_ORD_INSERT_TIME)->format('d M Y') }}
            @else
                —
            @endif
        </td>
        <td style="padding:8px 10px;color:#6b7280;">{{ $o->dealer_name ?: '—' }}</td>
        <td style="padding:8px 10px;text-align:center;">
            <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;
                background:{{ $o->UL_ORD_TYPE === 'buy' ? '#d1fae5' : '#fee2e2' }};
                color:{{ $o->UL_ORD_TYPE === 'buy' ? '#065f46' : '#b91c1c' }};
                border:1px solid {{ $o->UL_ORD_TYPE === 'buy' ? '#a7f3d0' : '#fecaca' }};">
                {{ strtoupper($o->UL_ORD_TYPE ?: '—') }}
            </span>
        </td>
        <td style="padding:8px 10px;text-align:right;font-weight:500;">{{ number_format($o->UL_ORD_QUANTITY ?? 0) }}</td>
        <td style="padding:8px 10px;text-align:right;">₹{{ number_format($o->UL_ORD_PRICE_PER_SHARE ?? 0, 2) }}</td>
        <td style="padding:8px 10px;text-align:right;font-weight:600;">₹{{ number_format($o->UL_ORD_AMOUNT ?? 0, 2) }}</td>
        <td style="padding:8px 10px;text-align:center;">
            <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;
                background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};border:1px solid {{ $statusColor['border'] }};">
                {{ $o->UL_ORD_STATUS ?: 'Unknown' }}
            </span>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endif
