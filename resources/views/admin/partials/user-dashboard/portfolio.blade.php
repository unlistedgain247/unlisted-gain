@if($portfolio->isEmpty())
<div style="text-align:center;padding:40px;color:#9ca3af;">
    <i class="fa-solid fa-briefcase" style="font-size:28px;margin-bottom:10px;display:block;"></i>
    No portfolio data found. (Computed from completed orders)
</div>
@else
@php
    $grandQty  = $portfolio->sum('net_qty');
    $grandCost = $portfolio->sum('net_cost');
@endphp
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;font-size:12px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">#</th>
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">Company</th>
            <th style="padding:8px 12px;text-align:right;font-weight:600;color:#6b7280;">Net Qty</th>
            <th style="padding:8px 12px;text-align:right;font-weight:600;color:#6b7280;">Avg Cost</th>
            <th style="padding:8px 12px;text-align:right;font-weight:600;color:#6b7280;">Total Cost</th>
        </tr>
    </thead>
    <tbody>
    @foreach($portfolio as $i => $p)
    @php
        $avgCost = ($p->buy_qty > 0) ? ($p->buy_cost / $p->buy_qty) : 0;
    @endphp
    <tr style="border-bottom:1px solid #f0f0f0;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
        <td style="padding:8px 12px;color:#9ca3af;">{{ $i + 1 }}</td>
        <td style="padding:8px 12px;font-weight:600;color:#111;">
            {{ $p->UL_STOCKS_S_NAME ?: $p->UL_STOCKS_COMPNAME ?: 'Fincode: '.$p->UL_ORD_FINCODE }}
        </td>
        <td style="padding:8px 12px;text-align:right;font-weight:700;color:#065f46;">{{ number_format($p->net_qty) }}</td>
        <td style="padding:8px 12px;text-align:right;">₹{{ number_format($avgCost, 2) }}</td>
        <td style="padding:8px 12px;text-align:right;font-weight:600;">₹{{ number_format($p->net_cost, 2) }}</td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f0f4f8;font-weight:700;border-top:2px solid #e9ecef;">
            <td colspan="2" style="padding:8px 12px;color:#374151;">Total</td>
            <td style="padding:8px 12px;text-align:right;color:#065f46;">{{ number_format($grandQty) }}</td>
            <td></td>
            <td style="padding:8px 12px;text-align:right;color:#1d4ed8;">₹{{ number_format($grandCost, 2) }}</td>
        </tr>
    </tfoot>
</table>
</div>
@endif
