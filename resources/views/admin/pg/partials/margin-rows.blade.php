@php
    $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    $tBuyComm = $tBuyAmt = $tBuyQty = $tLp = $tMlp = $tNetLp = $tNetMlp = 0;
    foreach ($rows as $row) {
        $tBuyComm += $row->BUY_COMMISSION;
        $tBuyAmt  += $row->BUY_ORDER_AMOUNT;
        $tBuyQty  += $row->BUY_ORDER_QTY;
        $tLp      += $row->MARGIN_LP;
        $tMlp     += $row->MARGIN_MLP;
        $tNetLp   += $row->NET_MARGIN_LP;
        $tNetMlp  += $row->NET_MARGIN_MLP;
    }
    $totalAvgPct = $tBuyAmt > 0 ? round(($tNetLp / $tBuyAmt) * 100, 1) : 0;

    $fFrom   = $filters['fromDate']      ?? '';
    $fTo     = $filters['toDate']        ?? '';
    $fSearch = $filters['searchtext']    ?? '';
    $fAdv    = $filters['advisorSearch'] ?? '';
@endphp

@if(empty($rows))
<div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">No data found for the selected filters.</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;min-width:900px;">
    <thead>
        <tr>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;">Month</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Buy Commission</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Buy Order Amt</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Buy Qty</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Margin LP</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Margin MLP</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Net Margin LP</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Margin %</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Net Margin MLP</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        @php
            $marginPct = $row->BUY_ORDER_AMOUNT > 0
                ? round(($row->NET_MARGIN_LP / $row->BUY_ORDER_AMOUNT) * 100, 1)
                : 0;
            $label = ($months[$row->ord_month] ?? $row->ord_month) . ' ' . $row->ord_year;
        @endphp
        <tr class="pg-margin-row"
            data-month="{{ $row->ord_month }}"
            data-year="{{ $row->ord_year }}"
            data-from="{{ $fFrom }}"
            data-to="{{ $fTo }}"
            data-search="{{ $fSearch }}"
            data-advisor="{{ $fAdv }}"
            style="cursor:pointer;transition:background 0.1s;"
            onmouseover="this.style.background='#f0fdf4'"
            onmouseout="this.style.background=''">
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;font-weight:500;">{{ $label }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->BUY_COMMISSION) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->BUY_ORDER_AMOUNT) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->BUY_ORDER_QTY) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->MARGIN_LP) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->MARGIN_MLP) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f3f5;text-align:right;font-weight:600;color:#065f46;">{{ number_format($row->NET_MARGIN_LP) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ $marginPct }}%</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->NET_MARGIN_MLP) }}</td>
        </tr>
        @endforeach

        {{-- Total Row --}}
        <tr style="background:#eff6ff;cursor:pointer;"
            data-is-total="yes"
            data-from="{{ $fFrom }}"
            data-to="{{ $fTo }}"
            data-search="{{ $fSearch }}"
            data-advisor="{{ $fAdv }}"
            class="pg-margin-row"
            onmouseover="this.style.background='#dbeafe'"
            onmouseout="this.style.background='#eff6ff'">
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;font-weight:700;color:#1d4ed8;">Total</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ number_format($tBuyComm) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ number_format($tBuyAmt) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ number_format($tBuyQty) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ number_format($tLp) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ number_format($tMlp) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;color:#065f46;">{{ number_format($tNetLp) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ $totalAvgPct }}%</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:700;">{{ number_format($tNetMlp) }}</td>
        </tr>
    </tbody>
</table>
</div>
@endif
