@if($holdings->isEmpty())
<div style="text-align:center;padding:40px;color:#9ca3af;">
    <i class="fa-solid fa-chart-line" style="font-size:28px;margin-bottom:10px;display:block;"></i>
    No demat holdings found. (Computed from completed orders)
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;font-size:12px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">#</th>
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">Company</th>
            <th style="padding:8px 12px;text-align:right;font-weight:600;color:#6b7280;">Net Qty</th>
            <th style="padding:8px 12px;text-align:center;font-weight:600;color:#6b7280;">Request Transfer</th>
        </tr>
    </thead>
    <tbody>
    @foreach($holdings as $i => $h)
    <tr style="border-bottom:1px solid #f0f0f0;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
        <td style="padding:8px 12px;color:#9ca3af;">{{ $i + 1 }}</td>
        <td style="padding:8px 12px;font-weight:600;color:#111;">
            {{ $h->UL_STOCKS_S_NAME ?: $h->UL_STOCKS_COMPNAME ?: 'Fincode: '.$h->UL_ORD_FINCODE }}
        </td>
        <td style="padding:8px 12px;text-align:right;font-weight:700;color:#065f46;">{{ number_format($h->net_qty) }}</td>
        <td style="padding:8px 12px;text-align:center;">
            <button onclick="udmInitWithdraw('Shares', {{ $h->UL_ORD_FINCODE }}, {{ $h->net_qty }}, '{{ addslashes($h->UL_STOCKS_S_NAME ?? $h->UL_STOCKS_COMPNAME ?? '') }}')"
                style="padding:3px 12px;border-radius:5px;border:1.5px solid #2b80b9;background:#fff;color:#2b80b9;font-size:11px;font-weight:600;cursor:pointer;">
                Transfer
            </button>
        </td>
    </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f0f4f8;font-weight:700;border-top:2px solid #e9ecef;">
            <td colspan="2" style="padding:8px 12px;color:#374151;">Total Holdings</td>
            <td style="padding:8px 12px;text-align:right;color:#065f46;">{{ number_format($holdings->sum('net_qty')) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
</div>
@endif
