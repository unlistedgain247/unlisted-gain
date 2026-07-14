@if($transactions->isEmpty())
<div style="text-align:center;padding:40px;color:#9ca3af;">
    <i class="fa-solid fa-arrow-right-arrow-left" style="font-size:28px;margin-bottom:10px;display:block;"></i>
    No transactions found for this user.
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;font-size:12px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;">TID</th>
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;white-space:nowrap;">Date</th>
            <th style="padding:8px 10px;text-align:center;font-weight:600;color:#6b7280;">Type</th>
            <th style="padding:8px 10px;text-align:right;font-weight:600;color:#6b7280;">Amount</th>
            <th style="padding:8px 10px;text-align:right;font-weight:600;color:#6b7280;">Balance</th>
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;">Account</th>
            <th style="padding:8px 10px;text-align:left;font-weight:600;color:#6b7280;">Ref No.</th>
        </tr>
    </thead>
    <tbody>
    @foreach($transactions as $t)
    <tr style="border-bottom:1px solid #f0f0f0;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
        <td style="padding:8px 10px;font-weight:600;color:#3b82f6;font-family:monospace;">{{ $t->pgt_tid }}</td>
        <td style="padding:8px 10px;white-space:nowrap;color:#374151;">
            @if($t->pgt_transaction_date)
                {{ \Carbon\Carbon::parse($t->pgt_transaction_date)->format('d M Y') }}
            @else —
            @endif
        </td>
        <td style="padding:8px 10px;text-align:center;">
            <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;
                background:{{ $t->pgt_transaction_type === 'Flow In' ? '#d1fae5' : '#fee2e2' }};
                color:{{ $t->pgt_transaction_type === 'Flow In' ? '#065f46' : '#b91c1c' }};
                border:1px solid {{ $t->pgt_transaction_type === 'Flow In' ? '#a7f3d0' : '#fecaca' }};">
                {{ $t->pgt_transaction_type === 'Flow In' ? '↓ In' : '↑ Out' }}
            </span>
        </td>
        <td style="padding:8px 10px;text-align:right;font-weight:600;
            color:{{ $t->pgt_transaction_type === 'Flow In' ? '#065f46' : '#b91c1c' }};">
            {{ $t->pgt_transaction_type === 'Flow In' ? '+' : '-' }}₹{{ number_format($t->pgt_in_out_amount ?? 0, 2) }}
        </td>
        <td style="padding:8px 10px;text-align:right;color:#374151;">
            ₹{{ number_format($t->pgt_balance ?? 0, 2) }}
        </td>
        <td style="padding:8px 10px;color:#6b7280;">{{ $t->pgt_bank_account ?: '—' }}</td>
        <td style="padding:8px 10px;color:#374151;font-family:monospace;">{{ $t->pgt_ref_no ?: '—' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
<div style="padding:10px 12px;font-size:11px;color:#9ca3af;border-top:1px solid #f0f0f0;">Showing last {{ $transactions->count() }} transactions.</div>
@endif
