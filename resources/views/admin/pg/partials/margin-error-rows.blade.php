@if(empty($rows))
<div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">No data found.</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;min-width:1000px;">
    <thead>
        <tr>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;">Fincode</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;">Company</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Buy Comm.</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Sell Comm.</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Buy Amt</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Sell Amt</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Buy Qty</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Sell Qty</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Margin LP</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Margin MLP</th>
            <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;background:#f8f9fa;border-bottom:2px solid #e9ecef;text-align:right;">Difference</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        @php
            $diff      = $row->DIFFERENCE;
            $diffColor = $diff < 0 ? '#b91c1c' : ($diff > 0 ? '#065f46' : '#374151');
        @endphp
        <tr class="pg-merr-row"
            data-fincode="{{ $row->UL_ORD_FINCODE }}"
            style="cursor:pointer;transition:background 0.1s;"
            onmouseover="this.style.background='#fefce8'"
            onmouseout="this.style.background=''">
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;">{{ $row->UL_ORD_FINCODE }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;">{{ $row->UL_STOCKS_COMPNAME ?? '—' }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->BUY_COMMISSION) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->SELL_COMMISSION) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->BUY_ORDER_AMOUNT) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->SELL_ORDER_AMOUNT) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->BUY_ORDER_QTY) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->SELL_ORDER_QTY) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->MARGIN_LP) }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;text-align:right;">{{ number_format($row->MARGIN_MLP) }}</td>
            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f3f5;text-align:right;font-weight:700;color:{{ $diffColor }};">{{ number_format($diff) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif
