@php
    $th = 'padding:10px 14px;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;border-bottom:2px solid #e9ecef;';
    $td = 'padding:10px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f1f3f5;white-space:nowrap;';
@endphp

<div style="font-size:12px;color:#6c757d;padding:10px 14px 0;">{{ number_format($total) }} result{{ $total === 1 ? '' : 's' }}</div>

@if(empty($rows))
<div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">No data found for the selected filters.</div>
@elseif($isDemat)
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;min-width:800px;">
    <thead>
        <tr>
            <th style="{{ $th }}">ID</th>
            <th style="{{ $th }}">Company</th>
            <th style="{{ $th }}">User</th>
            <th style="{{ $th }}">Demat Date</th>
            <th style="{{ $th }} text-align:right;">Qty</th>
            <th style="{{ $th }}">Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td style="{{ $td }}">{{ $row->DEMAT_TRANS_ID }}</td>
            <td style="{{ $td }}">{{ $row->UL_STOCKS_COMPNAME ?? '—' }}</td>
            <td style="{{ $td }}">{{ $row->user_name ?? '—' }}</td>
            <td style="{{ $td }}">{{ $row->DEMAT_DATE }}</td>
            <td style="{{ $td }} text-align:right;">{{ number_format($row->DEMAT_QTY, 2) }}</td>
            <td style="{{ $td }}">
                {{ $row->DEMAT_IN_OUT_FLAG }}
                @if($row->DEMAT_IN_OUT_FLAG === 'Flow In')
                    <i class="fa fa-arrow-down" style="color:green;margin-left:4px;"></i>
                @else
                    <i class="fa fa-arrow-up" style="color:red;margin-left:4px;"></i>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;min-width:1200px;">
    <thead>
        <tr>
            <th style="{{ $th }}">TID</th>
            <th style="{{ $th }}">From / To</th>
            <th style="{{ $th }}">Account</th>
            <th style="{{ $th }}">Direct</th>
            <th style="{{ $th }}">Commission</th>
            <th style="{{ $th }}">TDS</th>
            <th style="{{ $th }}">Trans. Date</th>
            <th style="{{ $th }}">Ref No.</th>
            <th style="{{ $th }}">Type</th>
            <th style="{{ $th }} text-align:right;">Amount In</th>
            <th style="{{ $th }} text-align:right;">Amount Out</th>
            <th style="{{ $th }} text-align:right;">Balance</th>
            <th style="{{ $th }}">Remarks</th>
            <th style="{{ $th }}">Cust.</th>
            <th style="{{ $th }}">Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td style="{{ $td }}">
                <a href="javascript:void(0)"
                   style="color:#87b942;font-weight:600;text-decoration:none;"
                   onclick="openMappingModal({{ $row->pgt_tid }}, {{ $row->pgt_in_out_amount }}, '{{ addslashes($row->pgt_ref_no ?? '') }}', '{{ $row->pgt_transaction_type }}')">
                    {{ $row->pgt_tid }}
                </a>
            </td>
            <td style="{{ $td }}">{{ $row->pgt_from_to }}</td>
            <td style="{{ $td }}">{{ $row->pgt_bank_account }}</td>
            <td style="{{ $td }}">{{ $row->pgt_direct_flag ? 'Yes' : 'No' }}</td>
            <td style="{{ $td }}">{{ $row->pgt_commission_flag ? 'Yes' : 'No' }}</td>
            <td style="{{ $td }}">{{ $row->pgt_TDS_flag ? 'Yes' : 'No' }}</td>
            <td style="{{ $td }}">{{ $row->pgt_transaction_date }}</td>
            <td style="{{ $td }}">{{ $row->pgt_ref_no }}</td>
            <td style="{{ $td }}">
                {{ $row->pgt_transaction_type }}
                @if($row->pgt_transaction_type === 'Flow In')
                    <i class="fa fa-arrow-down" style="color:green;margin-left:4px;"></i>
                @else
                    <i class="fa fa-arrow-up" style="color:red;margin-left:4px;"></i>
                @endif
            </td>
            <td style="{{ $td }} text-align:right;">{{ $row->pgt_transaction_type === 'Flow In'  ? number_format($row->pgt_in_out_amount, 2) : '' }}</td>
            <td style="{{ $td }} text-align:right;">{{ $row->pgt_transaction_type === 'Flow Out' ? number_format($row->pgt_in_out_amount, 2) : '' }}</td>
            <td style="{{ $td }} text-align:right;">{{ number_format($row->pgt_balance, 2) }}</td>
            <td style="{{ $td }} white-space:normal;max-width:220px;">{{ $row->pgt_remarks }}</td>
            <td style="{{ $td }}">{{ $row->cust_name ?? '—' }}</td>
            <td style="{{ $td }}">{{ $row->pgt_created_on }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif

<div style="padding:14px;">{!! $pagination !!}</div>
