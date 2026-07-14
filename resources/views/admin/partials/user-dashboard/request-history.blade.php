{{-- Filter bar --}}
<div style="padding:10px 14px;background:#f8f9fa;border-bottom:1px solid #e9ecef;display:flex;align-items:center;gap:18px;">
    <div>
        <label style="font-size:12px;color:#6b7280;margin-right:6px;">Status:</label>
        <select id="rhStatusFilter" onchange="rhFilter()" style="padding:4px 8px;border:1.5px solid #d1d5db;border-radius:5px;font-size:12px;outline:none;">
            <option value="Pending" selected>Pending</option>
            <option value="">All</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>
    </div>
    <div>
        <label style="font-size:12px;color:#6b7280;margin-right:6px;">Type:</label>
        <select id="rhTypeFilter" onchange="rhFilter()" style="padding:4px 8px;border:1.5px solid #d1d5db;border-radius:5px;font-size:12px;outline:none;">
            <option value="">All</option>
            <option value="Cash">Cash</option>
            <option value="Shares">Shares</option>
        </select>
    </div>
    <span id="rhCount" style="font-size:11px;color:#9ca3af;margin-left:auto;"></span>
</div>

@if($requests->isEmpty())
<div style="text-align:center;padding:40px;color:#9ca3af;">
    <i class="fa-solid fa-clock-rotate-left" style="font-size:28px;margin-bottom:10px;display:block;"></i>
    No withdrawal requests found.
</div>
@else
<div style="overflow-x:auto;">
<table id="rhTable" style="width:100%;border-collapse:collapse;font-size:12px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">Request ID</th>
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">Date</th>
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">User</th>
            <th style="padding:8px 12px;text-align:center;font-weight:600;color:#6b7280;">Type</th>
            <th style="padding:8px 12px;text-align:left;font-weight:600;color:#6b7280;">Company</th>
            <th style="padding:8px 12px;text-align:right;font-weight:600;color:#6b7280;">Qty</th>
            <th style="padding:8px 12px;text-align:right;font-weight:600;color:#6b7280;">Amount</th>
            <th style="padding:8px 12px;text-align:center;font-weight:600;color:#6b7280;">Status</th>
            <th style="padding:8px 12px;"></th>
        </tr>
    </thead>
    <tbody>
    @foreach($requests as $r)
    @php
        $statusColor = match($r->REQUEST_STATUS) {
            'Completed' => ['bg'=>'#d1fae5','color'=>'#065f46','border'=>'#a7f3d0'],
            'Cancelled' => ['bg'=>'#f3f4f6','color'=>'#6b7280','border'=>'#e5e7eb'],
            default     => ['bg'=>'#fef9c3','color'=>'#854d0e','border'=>'#fde68a'],
        };
    @endphp
    <tr data-status="{{ $r->REQUEST_STATUS }}" data-type="{{ $r->REQUEST_TYPE }}"
        style="border-bottom:1px solid #f0f0f0;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
        <td style="padding:8px 12px;font-weight:600;color:#3b82f6;font-family:monospace;">{{ $r->REQUEST_ID }}</td>
        <td style="padding:8px 12px;white-space:nowrap;color:#374151;">
            {{ $r->REQUEST_DATE ? \Carbon\Carbon::parse($r->REQUEST_DATE)->format('d-M-Y') : '—' }}
        </td>
        <td style="padding:8px 12px;color:#374151;">{{ $user->name ?? '—' }}</td>
        <td style="padding:8px 12px;text-align:center;">
            <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;
                background:{{ $r->REQUEST_TYPE === 'Cash' ? '#eff6ff' : '#f0fdf4' }};
                color:{{ $r->REQUEST_TYPE === 'Cash' ? '#2563eb' : '#16a34a' }};
                border:1px solid {{ $r->REQUEST_TYPE === 'Cash' ? '#bfdbfe' : '#bbf7d0' }};">
                {{ $r->REQUEST_TYPE ?: '—' }}
            </span>
        </td>
        <td style="padding:8px 12px;color:#374151;">
            @if($r->REQUEST_TYPE === 'Shares')
                {{ $r->UL_STOCKS_S_NAME ?: 'Fincode: '.($r->REQUEST_FINCODE ?? '—') }}
            @else
                —
            @endif
        </td>
        <td style="padding:8px 12px;text-align:right;color:#374151;">
            @if($r->REQUEST_TYPE === 'Shares') {{ number_format($r->REQUEST_QTY ?? 0) }} @else — @endif
        </td>
        <td style="padding:8px 12px;text-align:right;font-weight:600;color:#111;">
            @if($r->REQUEST_TYPE === 'Cash') ₹{{ number_format($r->REQUEST_AMOUNT ?? 0, 2) }} @else — @endif
        </td>
        <td style="padding:8px 12px;text-align:center;">
            <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;
                background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};border:1px solid {{ $statusColor['border'] }};">
                {{ $r->REQUEST_STATUS ?: 'Pending' }}
            </span>
        </td>
        <td style="padding:8px 12px;text-align:center;">
            @if(!in_array($r->REQUEST_STATUS ?? '', ['Completed', 'Cancelled']))
            <button onclick="udmCancelRequest({{ $r->REQUEST_ID }})"
                style="padding:3px 12px;border-radius:5px;border:1.5px solid #0891b2;background:#fff;color:#0891b2;font-size:11px;font-weight:600;cursor:pointer;white-space:nowrap;">
                Cancel Request
            </button>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>

<script>
function rhFilter() {
    var status  = document.getElementById('rhStatusFilter').value;
    var type    = document.getElementById('rhTypeFilter').value;
    var rows    = document.querySelectorAll('#rhTable tbody tr');
    var visible = 0;
    rows.forEach(function(row) {
        var matchStatus = !status || row.dataset.status === status;
        var matchType   = !type   || row.dataset.type   === type;
        var show = matchStatus && matchType;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('rhCount').textContent = visible + ' record' + (visible === 1 ? '' : 's');
}
// Apply default filter (Pending) on load
rhFilter();
</script>
@endif
