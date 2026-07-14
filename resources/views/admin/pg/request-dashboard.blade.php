@extends('layout.admin')

@section('title', 'Request Dashboard | Admin')

@push('styles')
<style>
    .rd-badge {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .rd-badge-pending   { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
    .rd-badge-completed { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .rd-badge-cancelled { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
    .rd-badge-cash      { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .rd-badge-shares    { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

    .rd-tbl { font-size: 12px; width: 100%; border-collapse: collapse; }
    .rd-tbl thead th {
        background: #f8fafc;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
        padding: 9px 12px;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap;
    }
    .rd-tbl tbody td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .rd-tbl tbody tr:hover { background: #fafafa; }
    .rd-tbl tbody tr:last-child td { border-bottom: none; }

    .rd-edit-btn {
        padding: 3px 10px;
        border-radius: 5px;
        border: 1.5px solid #6b7280;
        background: #fff;
        color: #374151;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }
    .rd-edit-btn:hover { border-color: #87b942; color: #87b942; }

    .rd-pagination { display: flex; gap: 4px; justify-content: center; flex-wrap: wrap; }
    .rd-page-btn {
        padding: 4px 10px;
        border-radius: 5px;
        border: 1.5px solid #d1d5db;
        background: #fff;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .rd-page-btn:hover  { border-color: #87b942; color: #87b942; }
    .rd-page-btn.active { background: #87b942; color: #fff; border-color: #87b942; }
    .rd-page-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* pgd overlay reuse */
    .pgd-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
        z-index:9000; align-items:flex-start; justify-content:center;
        padding:30px 16px; overflow-y:auto; }
    .pgd-overlay.open { display:flex; }
    .pgd-modal { background:#fff; border-radius:12px; width:100%; max-width:500px;
        box-shadow:0 12px 48px rgba(0,0,0,.22); flex-shrink:0; margin:auto; overflow:hidden; }
    .pgd-modal-hdr {
        display:flex; align-items:center; justify-content:space-between;
        padding:13px 18px; background:#1e293b;
    }
    .pgd-modal-hdr h5 { font-size:13px; font-weight:700; color:#fff; margin:0; }
    .pgd-modal-close { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25);
        border-radius:7px; width:28px; height:28px; display:flex; align-items:center;
        justify-content:center; font-size:17px; color:#fff; cursor:pointer; line-height:1; padding:0; }
    .pgd-modal-close:hover { background:rgba(255,255,255,.28); }
    .pgd-modal-body { padding:20px; }
</style>
@endpush

@section('content')
@include('partials.admin-pg-subnav')

<div class="admin-main">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0">Request Dashboard</h5>
            <p class="text-muted mb-0" style="font-size:11px;margin-top:2px;">Manage user withdrawal requests</p>
        </div>
        <span id="rdTotalBadge" class="badge bg-secondary" style="font-size:12px;"></span>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-3 px-3">
            <div class="d-flex flex-wrap gap-2 align-items-end">

                <div style="flex:1 1 180px;min-width:160px;">
                    <label style="font-size:11px;color:#6b7280;font-weight:600;display:block;margin-bottom:3px;">User (Name / Phone / UID)</label>
                    <input type="text" id="rdSearch" placeholder="Search..." class="form-control form-control-sm">
                </div>

                <div style="flex:0 0 110px;">
                    <label style="font-size:11px;color:#6b7280;font-weight:600;display:block;margin-bottom:3px;">Type</label>
                    <select id="rdType" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="Cash">Cash</option>
                        <option value="Shares">Shares</option>
                    </select>
                </div>

                <div style="flex:0 0 120px;">
                    <label style="font-size:11px;color:#6b7280;font-weight:600;display:block;margin-bottom:3px;">Status</label>
                    <select id="rdStatus" class="form-select form-select-sm">
                        <option value="Pending" selected>Pending</option>
                        <option value="">All</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div style="flex:0 0 130px;">
                    <label style="font-size:11px;color:#6b7280;font-weight:600;display:block;margin-bottom:3px;">From Date</label>
                    <input type="date" id="rdFromDate" class="form-control form-control-sm">
                </div>

                <div style="flex:0 0 130px;">
                    <label style="font-size:11px;color:#6b7280;font-weight:600;display:block;margin-bottom:3px;">To Date</label>
                    <input type="date" id="rdToDate" class="form-control form-control-sm">
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" onclick="rdLoad(0)">Search</button>
                    <button class="btn btn-sm btn-light border" onclick="rdReset()">Reset</button>
                </div>

            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive" id="rdTableWrap">
                <div style="text-align:center;padding:60px;color:#9ca3af;">
                    <i class="fa fa-spinner fa-spin" style="font-size:20px;"></i>
                </div>
            </div>
            <div id="rdPagination" class="py-3 px-3"></div>
        </div>
    </div>

</div>{{-- /admin-main --}}

{{-- Edit Modal --}}
<div class="pgd-overlay" id="rdEditOverlay">
    <div class="pgd-modal">
        <div class="pgd-modal-hdr">
            <h5 id="rdEditTitle">Update Request</h5>
            <button class="pgd-modal-close" onclick="document.getElementById('rdEditOverlay').classList.remove('open')">&times;</button>
        </div>
        <div class="pgd-modal-body">
            <input type="hidden" id="rdEditId">
            <div class="mb-3">
                <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">Status</label>
                <select id="rdEditStatus" class="form-select form-select-sm">
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="mb-3">
                <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">Comment</label>
                <textarea id="rdEditComment" class="form-control form-control-sm" rows="3" placeholder="Optional note..."></textarea>
            </div>
            <div id="rdEditMsg" style="display:none;padding:8px 12px;border-radius:6px;font-size:12px;margin-bottom:10px;"></div>
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-sm btn-light border" onclick="document.getElementById('rdEditOverlay').classList.remove('open')">Cancel</button>
                <button class="btn btn-sm btn-success" id="rdEditSubmitBtn" onclick="rdSubmitUpdate()">Update</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var RD_BASE = '{{ url("/admin/pg/request-dashboard") }}';
var RD_CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var rdCurrentPage = 0;

function rdLoad(page) {
    rdCurrentPage = page || 0;
    var params = {
        searchtext:     $('#rdSearch').val(),
        REQUEST_TYPE:   $('#rdType').val(),
        REQUEST_STATUS: $('#rdStatus').val(),
        from_date:      $('#rdFromDate').val(),
        to_date:        $('#rdToDate').val(),
        page_no:        rdCurrentPage,
    };

    $('#rdTableWrap').html('<div style="text-align:center;padding:60px;color:#9ca3af;"><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i></div>');
    $('#rdPagination').empty();

    $.ajax({
        type: 'POST',
        url:  RD_BASE + '/data',
        headers: { 'X-CSRF-TOKEN': RD_CSRF },
        data: params,
        dataType: 'json',
        success: function(res) {
            rdRenderTable(res.rows);
            rdRenderPagination(res.current_page, res.pages, res.total);
        },
        error: function() {
            $('#rdTableWrap').html('<div style="color:#b91c1c;padding:24px;font-size:13px;">Failed to load. Please try again.</div>');
        }
    });
}

function rdRenderTable(rows) {
    if (!rows || rows.length === 0) {
        $('#rdTableWrap').html('<div style="text-align:center;padding:48px;color:#9ca3af;font-size:13px;"><i class="bx bx-inbox" style="font-size:28px;display:block;margin-bottom:8px;"></i>No records found.</div>');
        return;
    }

    var html = '<table class="rd-tbl"><thead><tr>'
        + '<th>ID</th><th>Date</th><th>User</th><th>Phone</th><th>Type</th>'
        + '<th>Company</th><th>Qty</th><th>Amount</th>'
        + '<th>Status</th><th>Comment</th><th>Updated By</th><th></th>'
        + '</tr></thead><tbody>';

    rows.forEach(function(r) {
        var statusClass = r.REQUEST_STATUS === 'Completed' ? 'rd-badge-completed'
                        : r.REQUEST_STATUS === 'Cancelled' ? 'rd-badge-cancelled'
                        : 'rd-badge-pending';
        var statusLabel = r.REQUEST_STATUS || 'Pending';

        var typeClass = r.REQUEST_TYPE === 'Shares' ? 'rd-badge-shares' : 'rd-badge-cash';

        var date = r.REQUEST_DATE
            ? new Date(r.REQUEST_DATE).toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'})
            : '—';

        var updatedBy = r.updated_by_name
            ? (r.updated_by_name + (r.REQUEST_UPDATED_DATE ? '<br><span style="color:#9ca3af;font-size:10px;">' + r.REQUEST_UPDATED_DATE.substring(0,16) + '</span>' : ''))
            : '—';

        var comment = r.REQUEST_STATUS_COMMENTS
            ? '<span title="' + rdEscape(r.REQUEST_STATUS_COMMENTS) + '" style="max-width:140px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:help;">' + rdEscape(r.REQUEST_STATUS_COMMENTS) + '</span>'
            : '<span style="color:#d1d5db;">—</span>';

        var company = r.UL_STOCKS_S_NAME || (r.REQUEST_FINCODE ? '#' + r.REQUEST_FINCODE : '—');
        var qty     = r.REQUEST_QTY     ? rdFmt(r.REQUEST_QTY)                        : '<span style="color:#d1d5db;">—</span>';
        var amount  = r.REQUEST_AMOUNT  ? '₹' + rdFmtAmt(r.REQUEST_AMOUNT)            : '<span style="color:#d1d5db;">—</span>';

        html += '<tr>'
            + '<td class="fw-semibold" style="color:#3b82f6;font-family:monospace;">' + r.REQUEST_ID + '</td>'
            + '<td style="white-space:nowrap;">' + date + '</td>'
            + '<td style="font-weight:600;">' + rdEscape(r.name || String(r.REQUEST_USER_ID)) + '</td>'
            + '<td style="color:#6b7280;">' + rdEscape(r.phone || '—') + '</td>'
            + '<td><span class="rd-badge ' + typeClass + '">' + (r.REQUEST_TYPE || '—') + '</span></td>'
            + '<td>' + rdEscape(company) + '</td>'
            + '<td style="text-align:right;">' + qty + '</td>'
            + '<td style="text-align:right;font-weight:600;">' + amount + '</td>'
            + '<td><span class="rd-badge ' + statusClass + '">' + statusLabel + '</span></td>'
            + '<td>' + comment + '</td>'
            + '<td style="font-size:11px;">' + updatedBy + '</td>'
            + '<td><button class="rd-edit-btn" onclick="rdOpenEdit(' + r.REQUEST_ID + ')"><i class="bx bx-edit-alt"></i> Edit</button></td>'
            + '</tr>';
    });

    html += '</tbody></table>';
    $('#rdTableWrap').html(html);
}

function rdRenderPagination(current, pages, total) {
    $('#rdTotalBadge').text(total + ' record' + (total === 1 ? '' : 's'));
    if (pages <= 1) { $('#rdPagination').empty(); return; }

    var html = '<div class="rd-pagination">';
    html += '<button class="rd-page-btn" onclick="rdLoad(' + (current - 1) + ')" ' + (current === 0 ? 'disabled' : '') + '>&laquo; Prev</button>';

    var start = Math.max(0, current - 2);
    var end   = Math.min(pages - 1, current + 2);
    if (start > 0)        html += '<button class="rd-page-btn" onclick="rdLoad(0)">1</button>' + (start > 1 ? '<span style="padding:4px 6px;font-size:12px;color:#9ca3af;">…</span>' : '');
    for (var i = start; i <= end; i++) {
        html += '<button class="rd-page-btn' + (i === current ? ' active' : '') + '" onclick="rdLoad(' + i + ')">' + (i + 1) + '</button>';
    }
    if (end < pages - 1) html += (end < pages - 2 ? '<span style="padding:4px 6px;font-size:12px;color:#9ca3af;">…</span>' : '') + '<button class="rd-page-btn" onclick="rdLoad(' + (pages - 1) + ')">' + pages + '</button>';
    html += '<button class="rd-page-btn" onclick="rdLoad(' + (current + 1) + ')" ' + (current >= pages - 1 ? 'disabled' : '') + '>Next &raquo;</button>';
    html += '</div>';
    $('#rdPagination').html(html);
}

function rdOpenEdit(requestId) {
    $('#rdEditMsg').hide();
    $('#rdEditStatus').val('');
    $('#rdEditComment').val('');
    $('#rdEditTitle').text('Update Request #' + requestId);
    $('#rdEditId').val(requestId);
    $('#rdEditSubmitBtn').prop('disabled', false).text('Update');

    $.get(RD_BASE + '/' + requestId, function(res) {
        if (res.success) {
            $('#rdEditStatus').val(res.data.REQUEST_STATUS || 'Pending');
            $('#rdEditComment').val(res.data.REQUEST_STATUS_COMMENTS || '');
        }
    });

    document.getElementById('rdEditOverlay').classList.add('open');
}

function rdSubmitUpdate() {
    var id       = $('#rdEditId').val();
    var status   = $('#rdEditStatus').val();
    var comments = $('#rdEditComment').val();

    $('#rdEditMsg').hide();
    $('#rdEditSubmitBtn').prop('disabled', true).text('Saving…');

    $.ajax({
        type: 'POST',
        url:  RD_BASE + '/' + id + '/update',
        headers: { 'X-CSRF-TOKEN': RD_CSRF },
        data: { REQUEST_STATUS: status, REQUEST_STATUS_COMMENTS: comments },
        dataType: 'json',
        success: function(res) {
            $('#rdEditSubmitBtn').prop('disabled', false).text('Update');
            $('#rdEditMsg')
                .css({ background: res.success ? '#d1fae5' : '#fee2e2', color: res.success ? '#065f46' : '#b91c1c' })
                .text(res.message).show();
            if (res.success) {
                setTimeout(function() {
                    document.getElementById('rdEditOverlay').classList.remove('open');
                    rdLoad(rdCurrentPage);
                }, 800);
            }
        },
        error: function() {
            $('#rdEditSubmitBtn').prop('disabled', false).text('Update');
            $('#rdEditMsg').css({ background: '#fee2e2', color: '#b91c1c' }).text('Server error. Try again.').show();
        }
    });
}

function rdReset() {
    $('#rdSearch').val('');
    $('#rdType').val('');
    $('#rdStatus').val('Pending');
    $('#rdFromDate').val('');
    $('#rdToDate').val('');
    rdLoad(0);
}

function rdEscape(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function rdFmt(n)    { return parseFloat(n).toLocaleString('en-IN'); }
function rdFmtAmt(n) { return parseFloat(n).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}); }

// Close overlay on backdrop click
$(document).ready(function() {
    $('#rdEditOverlay').on('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
    // Search on Enter key
    $('#rdSearch').on('keydown', function(e) { if (e.key === 'Enter') rdLoad(0); });

    rdLoad(0);
});
</script>
@endpush
