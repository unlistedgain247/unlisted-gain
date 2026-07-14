@extends('layout.admin')

@section('title', 'Unlisted Reports')

@push('styles')
<style>
    /* ── Overlay modals ──────────────────────────────────────────────────── */
    .pgd-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,.5); z-index: 9000;
        align-items: flex-start; justify-content: center;
        padding: 30px 16px; overflow-y: auto;
    }
    .pgd-modal {
        background: #fff; border-radius: 12px; width: 100%; max-width: 950px;
        box-shadow: 0 12px 48px rgba(0,0,0,.22); flex-shrink: 0; margin: auto;
        overflow: hidden;
    }
    .pgd-modal-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: 13px 18px; background: #1e293b;
    }
    .pgd-modal-title { font-size: 13px; font-weight: 700; color: #fff; margin: 0; }
    .pgd-modal-close {
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25);
        border-radius: 7px; width: 28px; height: 28px; display: flex; align-items: center;
        justify-content: center; font-size: 17px; color: #fff; cursor: pointer; line-height: 1; padding: 0;
    }
    .pgd-modal-close:hover { background: rgba(255,255,255,.28); }
    .pgd-modal-body { padding: 18px 20px; max-height: 72vh; overflow-y: auto; overflow-x: auto; }
    .pgd-modal-body .table { font-size: 12px; }
    @media (max-width: 575.98px) {
        .pgd-overlay { padding: 8px; }
        .pgd-modal { border-radius: 8px; }
        .pgd-modal-hdr { padding: 10px 12px; }
        .pgd-modal-body { padding: 12px 10px; max-height: 85vh; }
    }

    /* ── Report card grid ────────────────────────────────────────────────── */
    .rpt-section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b8c7a;
        margin: 24px 0 10px;
    }
    .rpt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 8px;
    }
    .rpt-card {
        background: #fff;
        border: 1.5px solid #c8ecd4;
        border-radius: 12px;
        padding: 20px 18px;
        cursor: pointer;
        transition: all .15s;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        box-shadow: 0 1px 4px rgba(26,157,143,.07);
        text-decoration: none;
    }
    .rpt-card:hover {
        border-color: #87b942;
        box-shadow: 0 4px 14px rgba(135,185,66,.18);
        transform: translateY(-2px);
    }
    .rpt-card-icon { font-size: 22px; line-height: 1; }
    .rpt-card-title { font-size: 13px; font-weight: 700; color: #1a4d3a; line-height: 1.3; }
    .rpt-card-desc  { font-size: 11px; color: #6b8c7a; line-height: 1.4; }

    .rpt-sub-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b8c7a;
        background: #eafff0;
        padding: 5px 10px;
        border-radius: 4px;
        margin: 14px 0 6px;
    }

    /* Combined report wide table */
    .combined-report-wrapper {
        overflow-x: auto;
        max-width: 100%;
        border-radius: 8px;
    }
    .combined-report-table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
        table-layout: fixed;
        width: max-content;
        min-width: 1200px;
    }
    .combined-report-table th,
    .combined-report-table td {
        padding: 8px 10px;
        white-space: nowrap;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
    }
    .combined-report-table thead th {
        position: sticky;
        top: 0;
        background: #f1f3f5;
        font-weight: 600;
        z-index: 5;
    }
    .combined-report-table tbody tr:nth-child(even) { background: #fafafa; }
    .combined-report-table tbody tr:hover { background: #eef6ff; }
    .combined-report-table th:nth-child(1),
    .combined-report-table td:nth-child(1) {
        position: sticky; left: 0; background: #fff; z-index: 6;
        width: 200px; min-width: 200px; text-align: left;
        box-shadow: 2px 0 6px rgba(0,0,0,.08);
    }
    .combined-report-table th:nth-child(2),
    .combined-report-table td:nth-child(2) {
        position: sticky; left: 200px; background: #fff; z-index: 6;
        width: 100px; min-width: 100px;
        box-shadow: 2px 0 6px rgba(0,0,0,.08);
    }
    .combined-report-table thead th:nth-child(1),
    .combined-report-table thead th:nth-child(2) { z-index: 7; background: #e9ecef; }
    .combined-report-table th:not(:nth-child(1)):not(:nth-child(2)),
    .combined-report-table td:not(:nth-child(1)):not(:nth-child(2)) { width: 60px; min-width: 60px; }

    /* Pagination */
    .rpt-pagination { display: flex; gap: 4px; justify-content: center; flex-wrap: wrap; margin-top: 12px; }
    .rpt-page-btn {
        min-width: 32px; height: 32px; padding: 0 8px;
        border: 1px solid #dee2e6; background: #fff; border-radius: 6px;
        font-size: 12px; cursor: pointer; transition: all .12s;
    }
    .rpt-page-btn:hover, .rpt-page-btn.active { background: #87b942; color: #fff; border-color: #87b942; }
    .rpt-page-btn:disabled { opacity: .4; cursor: default; }

    .font-13-data-table { font-size: 13px; }
</style>
@endpush

@section('content')
@include('partials.admin-unlisted-subnav')

<div class="container-fluid py-3" style="max-width:1400px;">

    <div class="rpt-section-label">&#128196; Financial Reports</div>
    <div class="rpt-grid">
        <div class="rpt-card" onclick="rptOpen('combined')">
            <div class="rpt-card-icon">&#128200;</div>
            <div class="rpt-card-title">Company Financial Update Summary</div>
            <div class="rpt-card-desc">Orders with P&amp;L / B/S / CF availability matrix</div>
        </div>
        <div class="rpt-card" onclick="rptOpen('lastInsert')">
            <div class="rpt-card-icon">&#128337;</div>
            <div class="rpt-card-title">Company Financial Update Details</div>
            <div class="rpt-card-desc">Most recently inserted financial records</div>
        </div>
    </div>

    <div class="rpt-section-label">&#128203; Order Reports</div>
    <div class="rpt-grid">
        <div class="rpt-card" onclick="rptOpen('company')">
            <div class="rpt-card-icon">&#127970;</div>
            <div class="rpt-card-title">Company Orders</div>
            <div class="rpt-card-desc">Orders grouped by company (completed)</div>
        </div>
        <div class="rpt-card" onclick="rptOpen('customer')">
            <div class="rpt-card-icon">&#128100;</div>
            <div class="rpt-card-title">Customer Orders</div>
            <div class="rpt-card-desc">Orders grouped by customer (completed)</div>
        </div>
        <div class="rpt-card" onclick="rptOpen('orders')">
            <div class="rpt-card-icon">&#128203;</div>
            <div class="rpt-card-title">Orders Report</div>
            <div class="rpt-card-desc">Month-wise &amp; employee-wise performance</div>
        </div>
    </div>

</div>

{{-- ── Modal: Orders Report ────────────────────────────────────────────── --}}
<div class="pgd-overlay" id="rptOrdersOverlay">
    <div class="pgd-modal" style="max-width:1100px;">
        <div class="pgd-modal-hdr">
            <h5 class="pgd-modal-title">Orders Report</h5>
            <button class="pgd-modal-close" onclick="rptClose('Orders')">&#10005;</button>
        </div>
        <div style="padding:14px 20px;border-bottom:1px solid #f0f0f0;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label class="form-label mb-1" style="font-size:11px;">From</label>
                <input type="date" id="rptOrdersFrom" class="form-control form-control-sm" style="width:150px;">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:11px;">To</label>
                <input type="date" id="rptOrdersTo" class="form-control form-control-sm" style="width:150px;">
            </div>
            <button class="btn btn-sm btn-primary" onclick="rptOrdersLoad()">Search</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('rptOrdersFrom').value='';document.getElementById('rptOrdersTo').value='';rptOrdersLoad()">Reset</button>
        </div>
        <div class="pgd-modal-body" id="rptOrdersBody" style="min-height:200px;"></div>
    </div>
</div>

{{-- ── Modal: Customer Orders ──────────────────────────────────────────── --}}
<div class="pgd-overlay" id="rptCustomerOverlay">
    <div class="pgd-modal" style="max-width:960px;">
        <div class="pgd-modal-hdr">
            <h5 class="pgd-modal-title">Customer Orders</h5>
            <button class="pgd-modal-close" onclick="rptClose('Customer')">&#10005;</button>
        </div>
        <div style="padding:14px 20px;border-bottom:1px solid #f0f0f0;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label class="form-label mb-1" style="font-size:11px;">From</label>
                <input type="date" id="rptCustFrom" class="form-control form-control-sm" style="width:145px;">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:11px;">To</label>
                <input type="date" id="rptCustTo" class="form-control form-control-sm" style="width:145px;">
            </div>
            <div>
                <input type="text" id="rptCustSearch" class="form-control form-control-sm" placeholder="Name, Phone, Email, UID" style="width:220px;">
            </div>
            <button class="btn btn-sm btn-primary" onclick="rptCustomerLoad(0)">Search</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="rptCustReset()">Reset</button>
        </div>
        <div class="pgd-modal-body" id="rptCustomerBody" style="min-height:200px;"></div>
        <div id="rptCustomerPagination" class="rpt-pagination pb-3"></div>
    </div>
</div>

{{-- ── Modal: Company Orders ───────────────────────────────────────────── --}}
<div class="pgd-overlay" id="rptCompanyOverlay">
    <div class="pgd-modal" style="max-width:860px;">
        <div class="pgd-modal-hdr">
            <h5 class="pgd-modal-title">Company Orders</h5>
            <button class="pgd-modal-close" onclick="rptClose('Company')">&#10005;</button>
        </div>
        <div style="padding:14px 20px;border-bottom:1px solid #f0f0f0;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label class="form-label mb-1" style="font-size:11px;">From</label>
                <input type="date" id="rptCoFrom" class="form-control form-control-sm" style="width:145px;">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:11px;">To</label>
                <input type="date" id="rptCoTo" class="form-control form-control-sm" style="width:145px;">
            </div>
            <div>
                <input type="text" id="rptCoSearch" class="form-control form-control-sm" placeholder="Company name or Fincode" style="width:210px;">
            </div>
            <button class="btn btn-sm btn-primary" onclick="rptCompanyLoad(0)">Search</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="rptCoReset()">Reset</button>
        </div>
        <div class="pgd-modal-body" id="rptCompanyBody" style="min-height:200px;"></div>
        <div id="rptCompanyPagination" class="rpt-pagination pb-3"></div>
    </div>
</div>

{{-- ── Modal: Combined Financial ───────────────────────────────────────── --}}
<div class="pgd-overlay" id="rptCombinedOverlay">
    <div class="pgd-modal" style="max-width:1400px;">
        <div class="pgd-modal-hdr">
            <h5 class="pgd-modal-title">Company Financial Update Summary</h5>
            <button class="pgd-modal-close" onclick="rptClose('Combined')">&#10005;</button>
        </div>
        <div style="padding:14px 20px;border-bottom:1px solid #f0f0f0;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <input type="text" id="rptCombinedSearch" class="form-control form-control-sm" placeholder="Company name or Fincode" style="width:250px;">
            </div>
            <button class="btn btn-sm btn-primary" onclick="rptCombinedLoad(0)">Search</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('rptCombinedSearch').value='';rptCombinedLoad(0)">Reset</button>
        </div>
        <div class="pgd-modal-body" id="rptCombinedBody" style="min-height:200px;overflow-x:auto;"></div>
        <div id="rptCombinedPagination" class="rpt-pagination pb-3"></div>
    </div>
</div>

{{-- ── Modal: Last Insert ──────────────────────────────────────────────── --}}
<div class="pgd-overlay" id="rptLastInsertOverlay">
    <div class="pgd-modal" style="max-width:860px;">
        <div class="pgd-modal-hdr">
            <h5 class="pgd-modal-title">Company Financial Update Details</h5>
            <button class="pgd-modal-close" onclick="rptClose('LastInsert')">&#10005;</button>
        </div>
        <div style="padding:14px 20px;border-bottom:1px solid #f0f0f0;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <input type="text" id="rptLiSearch" class="form-control form-control-sm" placeholder="Company name or Fincode" style="width:210px;">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:11px;">From</label>
                <input type="date" id="rptLiFrom" class="form-control form-control-sm" style="width:145px;">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:11px;">To</label>
                <input type="date" id="rptLiTo" class="form-control form-control-sm" style="width:145px;">
            </div>
            <button class="btn btn-sm btn-primary" onclick="rptLastInsertLoad(0)">Search</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="rptLiReset()">Reset</button>
        </div>
        <div class="pgd-modal-body" id="rptLastInsertBody" style="min-height:200px;"></div>
        <div id="rptLastInsertPagination" class="rpt-pagination pb-3"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const RPT_BASE = '{{ url("/admin/unlisted/reports") }}';
const RPT_CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Open / Close ──────────────────────────────────────────────────────────────
function rptOpen(type) {
    if (type === 'orders')     { document.getElementById('rptOrdersOverlay').style.display = 'flex'; rptOrdersLoad(); }
    if (type === 'customer')   { document.getElementById('rptCustomerOverlay').style.display = 'flex'; rptCustomerLoad(0); }
    if (type === 'company')    { document.getElementById('rptCompanyOverlay').style.display = 'flex'; rptCompanyLoad(0); }
    if (type === 'combined')   { document.getElementById('rptCombinedOverlay').style.display = 'flex'; rptCombinedLoad(0); }
    if (type === 'lastInsert') {
        var today = new Date().toISOString().split('T')[0];
        var from  = new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0];
        document.getElementById('rptLiFrom').value = from;
        document.getElementById('rptLiTo').value   = today;
        document.getElementById('rptLastInsertOverlay').style.display = 'flex';
        rptLastInsertLoad(0);
    }
}

function rptClose(name) {
    document.getElementById('rpt' + name + 'Overlay').style.display = 'none';
}

// Close on overlay backdrop click
['Orders','Customer','Company','Combined','LastInsert'].forEach(function(n) {
    var el = document.getElementById('rpt' + n + 'Overlay');
    if (el) el.addEventListener('click', function(e) { if (e.target === el) rptClose(n); });
});

// ── Spinner ───────────────────────────────────────────────────────────────────
function rptSpinner(id) {
    document.getElementById(id).innerHTML =
        '<div class="text-center py-5"><div class="spinner-border text-success" role="status"></div></div>';
}

// ── Pagination renderer ───────────────────────────────────────────────────────
function rptPagination(containerId, total, page, limit, loadFn) {
    var pages = Math.ceil(total / limit) || 1;
    var el = document.getElementById(containerId);
    if (!el) return;
    var html = '<div class="rpt-pagination">';
    html += '<button class="rpt-page-btn" ' + (page <= 0 ? 'disabled' : '') + ' onclick="' + loadFn + '(' + (page-1) + ')">&laquo;</button>';
    var start = Math.max(0, page - 2), end = Math.min(pages - 1, page + 2);
    for (var i = start; i <= end; i++) {
        html += '<button class="rpt-page-btn' + (i === page ? ' active' : '') + '" onclick="' + loadFn + '(' + i + ')">' + (i+1) + '</button>';
    }
    html += '<button class="rpt-page-btn" ' + (page >= pages-1 ? 'disabled' : '') + ' onclick="' + loadFn + '(' + (page+1) + ')">&raquo;</button>';
    html += '<span style="font-size:12px;color:#888;line-height:32px;margin-left:6px;">' + total + ' records</span>';
    html += '</div>';
    el.innerHTML = html;
}

// ── Orders Report ─────────────────────────────────────────────────────────────
function rptOrdersLoad() {
    rptSpinner('rptOrdersBody');
    fetch(RPT_BASE + '/orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': RPT_CSRF },
        body: JSON.stringify({
            from_date: document.getElementById('rptOrdersFrom').value,
            to_date:   document.getElementById('rptOrdersTo').value,
        })
    }).then(r => r.json()).then(d => {
        document.getElementById('rptOrdersBody').innerHTML = d.table || '<p class="text-center text-muted py-4">No data</p>';
    });
}

// ── Customer Orders ───────────────────────────────────────────────────────────
function rptCustomerLoad(page) {
    rptSpinner('rptCustomerBody');
    fetch(RPT_BASE + '/customers', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': RPT_CSRF },
        body: JSON.stringify({
            from_date:  document.getElementById('rptCustFrom').value,
            to_date:    document.getElementById('rptCustTo').value,
            searchtext: document.getElementById('rptCustSearch').value,
            page: page
        })
    }).then(r => r.json()).then(d => {
        document.getElementById('rptCustomerBody').innerHTML = d.table;
        rptPagination('rptCustomerPagination', d.total, d.page, d.limit, 'rptCustomerLoad');
    });
}
function rptCustReset() {
    document.getElementById('rptCustFrom').value   = '';
    document.getElementById('rptCustTo').value     = '';
    document.getElementById('rptCustSearch').value = '';
    rptCustomerLoad(0);
}

// ── Company Orders ────────────────────────────────────────────────────────────
function rptCompanyLoad(page) {
    rptSpinner('rptCompanyBody');
    fetch(RPT_BASE + '/companies', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': RPT_CSRF },
        body: JSON.stringify({
            from_date:  document.getElementById('rptCoFrom').value,
            to_date:    document.getElementById('rptCoTo').value,
            searchtext: document.getElementById('rptCoSearch').value,
            page: page
        })
    }).then(r => r.json()).then(d => {
        document.getElementById('rptCompanyBody').innerHTML = d.table;
        rptPagination('rptCompanyPagination', d.total, d.page, d.limit, 'rptCompanyLoad');
    });
}
function rptCoReset() {
    document.getElementById('rptCoFrom').value   = '';
    document.getElementById('rptCoTo').value     = '';
    document.getElementById('rptCoSearch').value = '';
    rptCompanyLoad(0);
}

// ── Combined Financial ────────────────────────────────────────────────────────
function rptCombinedLoad(page) {
    rptSpinner('rptCombinedBody');
    fetch(RPT_BASE + '/combined', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': RPT_CSRF },
        body: JSON.stringify({
            searchtext: document.getElementById('rptCombinedSearch').value,
            page: page
        })
    }).then(r => r.json()).then(d => {
        document.getElementById('rptCombinedBody').innerHTML = d.table;
        rptPagination('rptCombinedPagination', d.total, d.page, d.limit, 'rptCombinedLoad');
    });
}

// ── Last Insert ───────────────────────────────────────────────────────────────
function rptLastInsertLoad(page) {
    rptSpinner('rptLastInsertBody');
    fetch(RPT_BASE + '/last-insert', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': RPT_CSRF },
        body: JSON.stringify({
            searchtext: document.getElementById('rptLiSearch').value,
            from_date:  document.getElementById('rptLiFrom').value,
            to_date:    document.getElementById('rptLiTo').value,
            page: page
        })
    }).then(r => r.json()).then(d => {
        document.getElementById('rptLastInsertBody').innerHTML = d.table;
        rptPagination('rptLastInsertPagination', d.total, d.page, d.limit, 'rptLastInsertLoad');
    });
}
function rptLiReset() {
    var today = new Date().toISOString().split('T')[0];
    var from  = new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0];
    document.getElementById('rptLiSearch').value = '';
    document.getElementById('rptLiFrom').value   = from;
    document.getElementById('rptLiTo').value     = today;
    rptLastInsertLoad(0);
}
</script>
@endpush
