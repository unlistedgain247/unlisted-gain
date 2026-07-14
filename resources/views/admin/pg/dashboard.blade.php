@extends('layout.admin')

@section('title', 'PG Dashboard | Admin')

@push('styles')
<style>
    /* ── PG Dashboard — Modern UI ─────────────────────────────── */

    /* Section panel header strip */
    .pgd-section-hdr {
        background: #f8fafc;
        border-bottom: 1px solid #eef0f3;
        padding: 9px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border-radius: 10px 10px 0 0;
    }
    .pgd-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
    }
    .pgd-accent-bar {
        display: inline-block;
        width: 3px;
        height: 14px;
        border-radius: 2px;
        background: #87b942;
        flex-shrink: 0;
    }

    /* Overlay modals */
    .pgd-overlay {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,.5); z-index:9000;
        align-items:flex-start; justify-content:center;
        padding:30px 16px; overflow-y:auto;
    }
    .pgd-overlay.open { display:flex; }
    .pgd-modal {
        background:#fff; border-radius:12px; width:100%; max-width:950px;
        box-shadow:0 12px 48px rgba(0,0,0,.22); flex-shrink:0; margin:auto;
        overflow:hidden;
    }
    .pgd-modal-hdr {
        display:flex; align-items:center; justify-content:space-between;
        padding:13px 18px; border-bottom:none;
        background:#1e293b;
    }
    .pgd-modal-hdr h5 { font-size:13px; font-weight:700; color:#fff; margin:0; }
    .pgd-modal-close {
        background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25);
        border-radius:7px; width:28px; height:28px; display:flex; align-items:center;
        justify-content:center; font-size:17px; color:#fff; cursor:pointer; line-height:1; padding:0;
    }
    .pgd-modal-close:hover { background:rgba(255,255,255,.28); }
    .pgd-modal-body { padding:18px 20px; max-height:72vh; overflow-y:auto; }
    .pgd-modal-body .table { font-size:12px; }

    /* Clean data tables */
    .pgd-tbl { font-size:11px; }
    .pgd-tbl thead th {
        background: transparent !important;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
        padding: 8px 10px;
        border-bottom: 2px solid #e9ecef;
    }
    .pgd-tbl tbody td { padding: 7px 10px; vertical-align: middle; }
    .pgd-tbl tbody tr:last-child td { border-bottom: none; }
    .pgd-tbl tfoot tr td { padding: 7px 10px; background: #f8fafc; font-weight: 700; border-top: 2px solid #e9ecef; }

    /* Icon in section title */
    .pgd-section-title i.bx { font-size: 13px; color: #87b942; margin-right: 1px; }

    /* Clickable user links */
    .pgd-user-link { color: #374151; text-decoration: none; cursor: pointer; font-weight: 600; }
    .pgd-user-link:hover { color: #87b942; text-decoration: underline; }

    /* ── Responsive fixes ──────────────────────────────────────── */
    /* Allow section header title + buttons to wrap on narrow cards */
    .pgd-section-hdr { flex-wrap: wrap; row-gap: 6px; }

    /* Allow horizontal scrolling in modal bodies (for wide tables) */
    .pgd-modal-body { overflow-x: auto; }

    /* Tighter modals on phones */
    @media (max-width: 575.98px) {
        .pgd-overlay { padding: 8px 8px; }
        .pgd-modal { border-radius: 8px; }
        .pgd-modal-hdr { padding: 10px 12px; }
        .pgd-modal-body { padding: 12px 10px; max-height: 85vh; }
        .pgd-modal-hdr h5 { font-size: 12px; }
        .pgd-section-hdr { padding: 8px 10px; }
    }
</style>
@endpush

@section('content')

@include('partials.admin-pg-subnav')

<div class="admin-main">

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0">PG Dashboard</h5>
            <p class="text-muted mb-0" style="font-size:11px;margin-top:2px;">Payment Gateway · Operations Overview</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-success" onclick="openAddTransactionModal()">
                <i class="fa-solid fa-plus me-1"></i>Add Transaction
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="openAddDematTransactionModal()">
                <i class="fa-solid fa-plus me-1"></i>Add Demat Transaction
            </button>
        </div>
    </div>

    {{-- ── Stat Summary ──────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card rounded-4 bg-gradient-cosmic bubble position-relative overflow-hidden border-0">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-white fw-bold" style="font-size:24px;line-height:1.1;">{{ count($openTransRows) }}</div>
                            <div class="text-white" style="font-size:11px;opacity:.85;margin-top:4px;">Open Transactions</div>
                        </div>
                        <i class='bx bx-transfer text-white' style="font-size:30px;opacity:.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card rounded-4 bg-gradient-burning bubble position-relative overflow-hidden border-0">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-white fw-bold" style="font-size:24px;line-height:1.1;">{{ count($pendingWdrawRows) }}</div>
                            <div class="text-white" style="font-size:11px;opacity:.85;margin-top:4px;">Pending Withdrawals</div>
                        </div>
                        <i class='bx bx-time-five text-white' style="font-size:30px;opacity:.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card rounded-4 bg-gradient-lush bubble position-relative overflow-hidden border-0">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-white fw-bold" style="font-size:18px;line-height:1.2;">₹{{ number_format($iciciBankBalance, 0) }}</div>
                            <div class="text-white" style="font-size:11px;opacity:.85;margin-top:4px;">ICICI Balance</div>
                        </div>
                        <i class='bx bx-wallet text-white' style="font-size:30px;opacity:.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card rounded-4 bg-gradient-moonlit bubble position-relative overflow-hidden border-0">
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-white fw-bold" style="font-size:18px;line-height:1.2;">₹{{ number_format($bandhanBankBalance, 0) }}</div>
                            <div class="text-white" style="font-size:11px;opacity:.85;margin-top:4px;">Bandhan Balance</div>
                        </div>
                        <i class='bx bx-bank text-white' style="font-size:30px;opacity:.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Date Filter ─────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ url('/admin/pg/dashboard') }}">
                <div class="d-flex align-items-end flex-wrap gap-2">
                    <div style="min-width:150px;flex:1 1 150px;">
                        <label class="form-label fw-semibold text-uppercase text-muted mb-1" style="font-size:10px;letter-spacing:.05em;">From Date</label>
                        <input type="date" class="form-control form-control-sm w-100" name="from_date" value="{{ $fromDate }}">
                    </div>
                    <div style="min-width:150px;flex:1 1 150px;">
                        <label class="form-label fw-semibold text-uppercase text-muted mb-1" style="font-size:10px;letter-spacing:.05em;">To Date</label>
                        <input type="date" class="form-control form-control-sm w-100" name="to_date" value="{{ $toDate }}">
                    </div>
                    <div class="d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>Search
                        </button>
                        <a href="{{ url('/admin/pg/dashboard') }}" class="btn btn-sm btn-light border">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Main Grid ────────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">

        {{-- COL 1: Open Transactions + User Balance --}}
        <div class="col-xl-4 col-lg-6 col-md-6 d-flex flex-column gap-3">

            {{-- Open Transactions --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="pgd-section-hdr">
                    <div class="pgd-section-title">
                        <span class="pgd-accent-bar"></span>
                        <i class='bx bx-transfer'></i>Open Transactions
                    </div>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="openCommissionModal()">Commission Report</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pgd-tbl text-center mb-0">
                            <thead>
                                <tr>
                                    <th>TID</th>
                                    <th>Ageing</th>
                                    <th>In <i class="fa fa-arrow-down text-success"></i></th>
                                    <th>Out <i class="fa fa-arrow-up text-danger"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($openTransRows as $r)
                            <tr>
                                <td><a href="javascript:void(0)"
                                    onclick="openMappingModal({{ $r->pgt_tid }}, {{ $r->pgt_in_out_amount }}, '{{ addslashes($r->pgt_ref_no ?? '') }}', '{{ $r->pgt_transaction_type }}')"
                                    class="fw-semibold text-primary text-decoration-none">{{ $r->pgt_tid }}</a></td>
                                <td>{{ $r->ageing }} days</td>
                                <td class="text-end">{{ round($r->flow_in_amount ?? 0, 2) }}</td>
                                <td class="text-end">{{ round($r->flow_out_amount ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-muted fst-italic py-3">No open transactions</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- User Balance --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="pgd-section-hdr">
                    <div class="pgd-section-title">
                        <span class="pgd-accent-bar"></span>
                        <i class='bx bx-wallet'></i>User Balance
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="openUserBalModal()">Zoom</button>
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="openUserBalPendingModal()">Pending</button>
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="openTdsTcsModal()">TDS/TCS</button>
                        <a href="{{ route('admin.pg.dashboard.export-user-balance', array_filter(['from_date' => $fromDate, 'to_date' => $toDate])) }}"
                           class="btn btn-sm btn-outline-success py-0 px-2" style="font-size:11px;" target="_blank">
                            <i class='bx bx-download' style="font-size:11px;"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pgd-tbl text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">User</th>
                                    <th>Debits</th><th>Credits</th><th>Balance</th><th>Days</th><th>RM</th><th>Req Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $dTotal=0; $cTotal=0; $bTotal=0; $aTotal=0; @endphp
                            @foreach($userBalRows as $r)
                                @if(($r->balance <= -1 || $r->balance >= 1) && $r->user_id > 0)
                                @php $dTotal+=$r->debits; $cTotal+=$r->credits; $bTotal+=$r->balance; $aTotal+=($r->REQUEST_AMOUNT??0); @endphp
                                <tr>
                                    <td class="text-start" style="font-size:10px;"><a href="javascript:void(0)" class="pgd-user-link" onclick="openUserDashboard({{ $r->user_id }}, '{{ addslashes($r->name) }}')">{{ $r->user_id }} – {{ $r->name }}</a></td>
                                    <td>{{ round($r->debits, 2) }}</td>
                                    <td>{{ round($r->credits, 2) }}</td>
                                    <td class="{{ $r->balance < 0 ? 'text-danger fw-semibold' : '' }}">{{ round($r->balance, 2) }}</td>
                                    <td>{{ $r->number_of_days_order }}</td>
                                    <td>{{ $r->order_added_by_name ?? '' }}</td>
                                    <td>{{ round($r->REQUEST_AMOUNT ?? 0, 2) }}</td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-start">Total</td>
                                    <td>{{ round($dTotal, 2) }}</td>
                                    <td>{{ round($cTotal, 2) }}</td>
                                    <td>{{ round($bTotal, 2) }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- /col1 --}}

        {{-- COL 2: Order Report --}}
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="pgd-section-hdr">
                    <div class="pgd-section-title">
                        <span class="pgd-accent-bar"></span>
                        <i class='bx bx-bar-chart-alt-2'></i>Order Report
                    </div>
                    <a href="{{ route('admin.pg.dashboard.export-order-report', array_filter(['from_date' => $fromDate, 'to_date' => $toDate])) }}"
                       title="Export to CSV" style="color:#87b942;font-size:13px;">
                        <i class='bx bx-download'></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pgd-tbl text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">Order Type</th>
                                    <th>Amount</th>
                                    <th>Commission</th>
                                    <th>Net Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $netSale=0; $netPurchase=0; @endphp
                            @foreach($orderReportRows as $r)
                            @php
                                $label = $r->UL_ORD_TYPE === 'Buy' ? 'Sale' : 'Purchase';
                                $net   = $r->UL_ORD_TYPE === 'Buy'
                                       ? ($r->total_amount - $r->total_commission)
                                       : ($r->total_amount + $r->total_commission);
                                if ($label === 'Sale') $netSale = $net;
                                else $netPurchase = $net;
                            @endphp
                            <tr>
                                <td class="text-start">{{ $label }}</td>
                                <td class="text-end">{{ number_format(round($r->total_amount)) }}</td>
                                <td class="text-end">{{ number_format(round($r->total_commission)) }}</td>
                                <td class="text-end fw-semibold">{{ number_format(round($net)) }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                            @if($orderReportRows)
                            <tfoot>
                                <tr>
                                    <td class="text-start">Margin</td>
                                    <td colspan="2"></td>
                                    <td class="text-end">{{ number_format(round($netSale - $netPurchase)) }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>{{-- /col2 --}}

        {{-- COL 3: Demat Holdings + User Wise Balance --}}
        <div class="col-xl-4 col-lg-12 d-flex flex-column gap-3">

            {{-- Demat Company Holdings --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="pgd-section-hdr">
                    <div class="pgd-section-title">
                        <span class="pgd-accent-bar"></span>
                        <i class='bx bx-building'></i>Demat – Company Holdings
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pgd-tbl text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">Company</th>
                                    <th>Purchase Price</th>
                                    <th>Balance</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($holdingsRows as $r)
                            <tr>
                                <td class="text-start">{{ $r->UL_STOCKS_S_NAME ?? $r->company_id }}</td>
                                <td>{{ isset($r->last_traded_price) ? round($r->last_traded_price, 2) : '0' }}</td>
                                <td>{{ ($r->balance * -1) }}</td>
                                <td>{{ round($r->balance * ($r->last_traded_price ?? 0) * -1, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-muted fst-italic py-3">No holdings</td></tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-start">Total</td><td colspan="2"></td>
                                    <td>{{ round($holdingsTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Demat User Wise Balance --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="pgd-section-hdr">
                    <div class="pgd-section-title">
                        <span class="pgd-accent-bar"></span>
                        <i class='bx bx-user'></i>Demat – User Wise Balance
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pgd-tbl text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">User</th>
                                    <th>Company</th>
                                    <th>Debits</th>
                                    <th>Credits</th>
                                    <th>Balance</th>
                                    <th>Qty Req</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($userWiseRows as $r)
                            <tr>
                                <td class="text-start" style="font-size:10px;"><a href="javascript:void(0)" class="pgd-user-link" onclick="openUserDashboard({{ $r->user_id }}, '{{ addslashes($r->name) }}')">{{ $r->user_id }} – {{ $r->name }}</a></td>
                                <td style="font-size:10px;">{{ $r->UL_STOCKS_S_NAME ?? $r->company_id }}</td>
                                <td>{{ $r->debits }}</td>
                                <td>{{ $r->credits }}</td>
                                <td class="{{ ($r->balance < 0) ? 'text-danger fw-semibold' : '' }}">{{ $r->balance }}</td>
                                <td>{{ $r->REQUEST_QTY ?? '' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-muted fst-italic py-3">No demat balance</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- /col3 --}}

    </div>{{-- /row --}}

    {{-- ── Pending Withdrawal Requests ─────────────────────────── --}}
    @if(count($pendingWdrawRows) > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="pgd-section-hdr">
            <div class="pgd-section-title">
                <span class="pgd-accent-bar"></span>
                <i class='bx bx-time-five'></i>Pending Withdrawal Requests
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark">{{ count($pendingWdrawRows) }}</span>
                <a href="{{ route('admin.pg.request-dashboard') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;">Manage →</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table pgd-tbl text-center mb-0">
                    <thead>
                        <tr>
                            <th>ID</th><th>User</th><th>Type</th><th>Company</th>
                            <th>Amount / Qty</th><th>Date</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($pendingWdrawRows as $r)
                    <tr>
                        <td class="fw-semibold text-primary">{{ $r->REQUEST_ID }}</td>
                        <td><a href="javascript:void(0)" class="pgd-user-link" onclick="openUserDashboard({{ $r->REQUEST_USER_ID }}, '{{ addslashes($r->name ?? '') }}')">{{ $r->name ?? $r->REQUEST_USER_ID }}</a></td>
                        <td>
                            <span class="badge {{ $r->REQUEST_TYPE === 'Cash' ? 'bg-primary' : 'bg-success' }}">{{ $r->REQUEST_TYPE }}</span>
                        </td>
                        <td>{{ $r->UL_STOCKS_S_NAME ?? ($r->REQUEST_FINCODE ?? '—') }}</td>
                        <td class="fw-semibold">
                            @if($r->REQUEST_TYPE === 'Cash')
                                ₹{{ number_format($r->REQUEST_AMOUNT, 2) }}
                            @else
                                {{ $r->REQUEST_QTY }} shares
                            @endif
                        </td>
                        <td>{{ $r->REQUEST_DATE ?? '—' }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $r->REQUEST_STATUS ?: 'Pending' }}</span></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Gateway Expectations ─────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-0">
        <div class="card-body py-3">
            <div class="pgd-section-title mb-2" style="text-transform:none;font-size:12px;font-weight:600;color:#374151;">
                <span class="pgd-accent-bar"></span>Gateway Expectations
            </div>
            <ol class="mb-0 ps-3 text-muted" style="font-size:12px;">
                <li>Open transaction ageing should not exceed 2 days.</li>
                <li>Outflow can only happen if a matching order or refund entry exists.</li>
                <li>Customer refunds should be processed within a max TAT of 5 days.</li>
            </ol>
        </div>
    </div>

</div>{{-- /admin-main --}}


{{-- ══════════════════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════════════════ --}}

{{-- User Balance Zoom View --}}
<div class="pgd-overlay" id="pgdUserBalOverlay">
    <div class="pgd-modal">
        <div class="pgd-modal-hdr">
            <h5>User Balance</h5>
            <button class="pgd-modal-close" onclick="closeOverlay('pgdUserBalOverlay')">&times;</button>
        </div>
        <div class="pgd-modal-body">
            <div class="table-responsive">
            <table class="table table-sm table-bordered" style="font-size:11px;">
                <thead><tr style="background:#fdebeb;">
                    <th>UID – Name</th><th class="text-end">Debits</th><th class="text-end">Credits</th>
                    <th class="text-end">Balance</th><th class="text-end">Days</th><th>RM</th><th class="text-end">Req Amt</th>
                </tr></thead>
                <tbody>
                @php $dT=0; $cT=0; $bT=0; $aT=0; @endphp
                @foreach($userBalRows as $r)
                    @if(($r->balance <= -1 || $r->balance >= 1) && $r->user_id > 0)
                    @php $dT+=$r->debits; $cT+=$r->credits; $bT+=$r->balance; $aT+=($r->REQUEST_AMOUNT??0); @endphp
                    <tr>
                        <td><a href="javascript:void(0)" class="pgd-user-link" onclick="openUserDashboard({{ $r->user_id }}, '{{ addslashes($r->name) }}')">{{ $r->user_id }} – {{ $r->name }}</a></td>
                        <td class="text-end">{{ round($r->debits) }}</td>
                        <td class="text-end">{{ round($r->credits) }}</td>
                        <td class="text-end" style="{{ $r->balance < 0 ? 'color:#b91c1c;' : '' }}">{{ round($r->balance) }}</td>
                        <td class="text-end">{{ $r->number_of_days_order }}</td>
                        <td>{{ $r->order_added_by_name ?? '' }}</td>
                        <td class="text-end">{{ round($r->REQUEST_AMOUNT ?? 0) }}</td>
                    </tr>
                    @endif
                @endforeach
                <tr style="background:#e9ecef;font-weight:700;">
                    <td>Total</td>
                    <td class="text-end">{{ round($dT) }}</td><td class="text-end">{{ round($cT) }}</td>
                    <td class="text-end">{{ round($bT) }}</td><td colspan="2"></td>
                    <td class="text-end">{{ round($aT) }}</td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

{{-- User Pending Balance --}}
<div class="pgd-overlay" id="pgdUserBalPendingOverlay">
    <div class="pgd-modal">
        <div class="pgd-modal-hdr">
            <h5>User Pending Balance</h5>
            <button class="pgd-modal-close" onclick="closeOverlay('pgdUserBalPendingOverlay')">&times;</button>
        </div>
        <div class="pgd-modal-body">
            <div class="table-responsive">
            <table class="table table-sm table-bordered" style="font-size:11px;">
                <thead><tr style="background:#fdebeb;">
                    <th>#</th><th>UID – Name</th><th class="text-end">Debits</th><th class="text-end">Credits</th>
                    <th class="text-end">Balance</th><th class="text-end">Days</th><th>RM</th><th class="text-end">Req Amt</th>
                </tr></thead>
                <tbody>
                @php $dT=0; $cT=0; $bT=0; $aT=0; $ik=0; @endphp
                @foreach($userBalPending as $r)
                    @if(($r->balance <= -1 || $r->balance >= 1) && $r->user_id > 0)
                    @php $ik++; $dT+=$r->debits; $cT+=$r->credits; $bT+=$r->balance; $aT+=($r->REQUEST_AMOUNT??0); @endphp
                    <tr>
                        <td>{{ $ik }}</td>
                        <td><a href="javascript:void(0)" class="pgd-user-link" onclick="openUserDashboard({{ $r->user_id }}, '{{ addslashes($r->name) }}')">{{ $r->user_id }} – {{ $r->name }}</a></td>
                        <td class="text-end">{{ round($r->debits) }}</td>
                        <td class="text-end">{{ round($r->credits) }}</td>
                        <td class="text-end" style="color:#b91c1c;">{{ round($r->balance) }}</td>
                        <td class="text-end">{{ $r->number_of_days_order }}</td>
                        <td>{{ $r->order_added_by_name ?? '' }}</td>
                        <td class="text-end">{{ round($r->REQUEST_AMOUNT ?? 0) }}</td>
                    </tr>
                    @endif
                @endforeach
                <tr style="background:#e9ecef;font-weight:700;">
                    <td colspan="2">Total</td>
                    <td class="text-end">{{ round($dT) }}</td><td class="text-end">{{ round($cT) }}</td>
                    <td class="text-end">{{ round($bT) }}</td><td colspan="2"></td>
                    <td class="text-end">{{ round($aT) }}</td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

{{-- TDS / TCS Modal --}}
<div class="pgd-overlay" id="pgdTdsTcsOverlay">
    <div class="pgd-modal">
        <div class="pgd-modal-hdr">
            <h5>User TDS / TCS</h5>
            <button class="pgd-modal-close" onclick="closeOverlay('pgdTdsTcsOverlay')">&times;</button>
        </div>
        <div class="pgd-modal-body">
            <div class="row g-2 mb-3" style="font-size:12px;">
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="pgdFY">
                        <option value="">Select Financial Year</option>
                        @php
                            $today = new DateTime();
                            $curFY = ($today->format('m') >= 4) ? (int)$today->format('Y') : (int)$today->format('Y') - 1;
                            for ($yr = 2022; $yr <= $curFY; $yr++):
                                $fFrom = $yr.'-04-01'; $fTo = ($yr+1).'-03-31';
                                $val   = $fFrom.'&'.$fTo;
                                $label = 'FY '.substr($yr,-2).'-'.substr($yr+1,-2);
                                $sel   = ($yr === $curFY) ? 'selected' : '';
                        @endphp
                        <option value="{{ $val }}" {{ $sel }}>{{ $label }}</option>
                        @php endfor; @endphp
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="pgdMonth">
                        <option value="">All Months</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" id="pgdTaxType">
                        <option value="TDS" selected>TDS</option>
                        <option value="TCS">TCS</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" id="pgdTaxSearch" placeholder="Search Name, Phone, Email & UID">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm" onclick="loadTdsTcsData()">Search</button>
                </div>
            </div>
            <div id="pgdTdsTcsRows"><p class="text-center text-muted py-4">Click Search to load data.</p></div>
            <div id="pgdTdsTcsPagination" class="mt-2"></div>
        </div>
    </div>
</div>

{{-- Commission Modal --}}
<div class="pgd-overlay" id="pgdCommissionOverlay">
    <div class="pgd-modal">
        <div class="pgd-modal-hdr">
            <h5>User Commission Report</h5>
            <button class="pgd-modal-close" onclick="closeOverlay('pgdCommissionOverlay')">&times;</button>
        </div>
        <div class="pgd-modal-body">
            <div class="row g-2 mb-3" style="font-size:12px;">
                <div class="col-md-3">
                    <input type="date" class="form-control form-control-sm" id="pgdCommFrom">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control form-control-sm" id="pgdCommTo">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" id="pgdCommSearch" placeholder="Search ID, Name, Commission">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-sm" onclick="loadCommissionData()">Search</button>
                </div>
            </div>
            <div id="pgdCommissionRows"><p class="text-center text-muted py-4">Click Search to load data.</p></div>
            <div id="pgdCommissionPagination" class="mt-2"></div>
        </div>
    </div>
</div>

{{-- ══ Transaction Mapping Modal ══════════════════════════════════════════ --}}
<div class="pgd-overlay" id="pgdMappingOverlay" style="align-items:center;">
<div class="pgd-modal" style="max-width:480px;width:100%;">
    <div class="pgd-modal-hdr">
        <h5>Add Transaction Mapping</h5>
        <button class="pgd-modal-close" onclick="closeOverlay('pgdMappingOverlay')">&times;</button>
    </div>
    <div class="pgd-modal-body">
        {{-- Transaction summary row --}}
        <table class="table table-sm table-bordered mb-3" style="font-size:12px;">
            <thead><tr style="background:#fdebeb;">
                <th>TID</th><th>Amount</th><th>Ref ID</th>
            </tr></thead>
            <tbody>
            <tr>
                <td id="pgdMapTidCell" style="font-weight:600;"></td>
                <td id="pgdMapAmtCell"></td>
                <td id="pgdMapRefCell" style="word-break:break-all;"></td>
            </tr>
            </tbody>
        </table>
        {{-- User search --}}
        <div class="mb-3">
            <label class="form-label" style="font-size:12px;font-weight:600;">Customer Name / UID <span class="text-danger">*</span></label>
            <div style="position:relative;">
                <input type="text" class="form-control form-control-sm" id="pgdMapUserSearch" placeholder="Type name or UID…" autocomplete="off">
                <input type="hidden" id="pgdMapUserId">
                <div id="pgdMapUserDropdown" style="position:absolute;z-index:9999;width:100%;background:#fff;border:1px solid #ddd;border-radius:4px;display:none;max-height:160px;overflow-y:auto;font-size:12px;"></div>
            </div>
        </div>
        <input type="hidden" id="pgdMapTid">
        <div id="pgdMapMsg" class="mb-2" style="font-size:12px;display:none;"></div>
        <div class="text-end">
            <button type="button" class="btn btn-primary btn-sm" onclick="saveMappingTransaction()">
                <span id="pgdMapSpinner" class="spinner-border spinner-border-sm d-none"></span> Save
            </button>
        </div>
    </div>
</div>
</div>

{{-- ══ Add Transaction Modal ══════════════════════════════════════════════ --}}
<div class="pgd-overlay" id="pgdAddTxnOverlay" style="align-items:center;">
<div class="pgd-modal" style="max-width:560px;width:100%;">
    <div class="pgd-modal-hdr">
        <h5>Add Transaction</h5>
        <button class="pgd-modal-close" onclick="closeOverlay('pgdAddTxnOverlay')">&times;</button>
    </div>
    <div class="pgd-modal-body">
        <form id="pgdAddTxnForm" autocomplete="off">
            {{-- Account --}}
            <div class="mb-3">
                <label class="form-label d-block" style="font-size:12px;font-weight:600;">Unlisted Gain Account *</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pgt_bank_account" id="pgdAccICICI" value="ICICI Bank" onchange="pgdTxnDynamic()">
                        <label class="form-check-label" for="pgdAccICICI" style="font-size:12px;">
                            ICICI Bank <span style="color:#6c757d;">(₹{{ number_format($iciciBankBalance, 2) }})</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pgt_bank_account" id="pgdAccBandhan" value="Bandhan Bank" onchange="pgdTxnDynamic()">
                        <label class="form-check-label" for="pgdAccBandhan" style="font-size:12px;">
                            Bandhan Bank <span style="color:#6c757d;">(₹{{ number_format($bandhanBankBalance, 2) }})</span>
                        </label>
                    </div>
                </div>
            </div>
            {{-- Flow --}}
            <div class="mb-3">
                <div class="fw-semibold mb-1" style="font-size:12px;">Transaction Type</div>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pgt_transaction_type" id="pgdTxnIn" value="Flow In" onchange="pgdTxnDynamic()">
                        <label class="form-check-label" for="pgdTxnIn"><span style="color:green;">&#8595;</span> Flow In</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pgt_transaction_type" id="pgdTxnOut" value="Flow Out" onchange="pgdTxnDynamic()">
                        <label class="form-check-label" for="pgdTxnOut"><span style="color:red;">&#8593;</span> Flow Out</label>
                    </div>
                </div>
            </div>
            {{-- Dynamic fields --}}
            <div id="pgdTxnFields" style="display:none;">
                {{-- From / To --}}
                <div class="mb-2">
                    <label class="form-label" style="font-size:12px;font-weight:600;" id="pgdFromToLabel">From / To *</label>
                    <select class="form-select form-select-sm" name="pgt_from_to" id="pgdFromTo">
                        <option value="">-Select-</option>
                        <option value="Customer">Customer</option>
                        <option value="ICICI Bank">ICICI Bank</option>
                        <option value="Bandhan Bank">Bandhan Bank</option>
                        <option value="Company">Company</option>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Amount (₹) *</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="pgt_amount" id="pgdTxnAmt" min="0.01">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Ref No. *</label>
                        <input type="text" class="form-control form-control-sm" name="pgt_ref_no" id="pgdTxnRef">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Date *</label>
                        <input type="date" class="form-control form-control-sm" name="pgt_transaction_date" id="pgdTxnDate">
                    </div>
                    <div class="col-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">HH *</label>
                        <select class="form-select form-select-sm" name="pgt_hour">
                            @for($h=0;$h<24;$h++)<option value="{{ str_pad($h,2,'0',STR_PAD_LEFT) }}">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}</option>@endfor
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">MM *</label>
                        <select class="form-select form-select-sm" name="pgt_minute">
                            @for($m=0;$m<60;$m++)<option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}">{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>@endfor
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Remarks</label>
                        <input type="text" class="form-control form-control-sm" name="pgt_remarks" id="pgdTxnRemarks">
                    </div>
                    <div class="col-12 mt-1">
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="pgt_direct_flag" id="pgdDirectFlag">
                                <label class="form-check-label" for="pgdDirectFlag" style="font-size:12px;">Direct Transaction</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="pgt_commission_flag" id="pgdCommissionFlag">
                                <label class="form-check-label" for="pgdCommissionFlag" style="font-size:12px;">Commission</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="pgt_TDS_flag" id="pgdTdsFlag">
                                <label class="form-check-label" for="pgdTdsFlag" style="font-size:12px;">TDS</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="pgdAddTxnMsg" class="mb-2" style="font-size:12px;display:none;"></div>
                <div class="text-end">
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveAddTransaction()">
                        <span id="pgdAddTxnSpinner" class="spinner-border spinner-border-sm d-none"></span> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

{{-- ══ Add Demat Transaction Modal ════════════════════════════════════════ --}}
<div class="pgd-overlay" id="pgdAddDematOverlay" style="align-items:center;">
<div class="pgd-modal" style="max-width:560px;width:100%;">
    <div class="pgd-modal-hdr">
        <h5 id="pgdDematModalTitle">Add Demat Transaction</h5>
        <button class="pgd-modal-close" onclick="closeOverlay('pgdAddDematOverlay')">&times;</button>
    </div>
    <div class="pgd-modal-body">
        <form id="pgdAddDematForm" autocomplete="off">
            {{-- Flow In / Out --}}
            <div class="mb-3">
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="DEMAT_IN_OUT_FLAG" id="dematIn" value="Flow In" onchange="pgdDematDynamic()">
                        <label class="form-check-label" for="dematIn"><span style="color:green;">&#8595;</span> Flow In</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="DEMAT_IN_OUT_FLAG" id="dematOut" value="Flow Out" onchange="pgdDematDynamic()">
                        <label class="form-check-label" for="dematOut"><span style="color:red;">&#8593;</span> Flow Out</label>
                    </div>
                </div>
            </div>
            <div id="pgdDematFields" style="display:none;">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Customer Name</label>
                        <div style="position:relative;">
                            <input type="text" class="form-control form-control-sm" id="pgdDematUserSearch" placeholder="Type name or UID…" autocomplete="off">
                            <input type="hidden" name="DEMAT_USER_ID" id="pgdDematUserId">
                            <div id="pgdDematUserDropdown" style="position:absolute;z-index:9999;width:100%;background:#fff;border:1px solid #ddd;border-radius:4px;display:none;max-height:150px;overflow-y:auto;font-size:12px;"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Company</label>
                        <div style="position:relative;">
                            <input type="text" class="form-control form-control-sm" id="pgdDematStockSearch" placeholder="Type company name…" autocomplete="off">
                            <input type="hidden" name="DEMAT_FINCODE" id="pgdDematFincode">
                            <div id="pgdDematStockDropdown" style="position:absolute;z-index:9999;width:100%;background:#fff;border:1px solid #ddd;border-radius:4px;display:none;max-height:150px;overflow-y:auto;font-size:12px;"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Quantity *</label>
                        <input type="number" step="1" min="1" class="form-control form-control-sm" name="DEMAT_QTY" id="pgdDematQty">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Date *</label>
                        <input type="date" class="form-control form-control-sm" name="DEMAT_DATE" id="pgdDematDate">
                    </div>
                    <div class="col-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">HH</label>
                        <select class="form-select form-select-sm" name="demat_hour">
                            @for($h=0;$h<24;$h++)<option value="{{ str_pad($h,2,'0',STR_PAD_LEFT) }}" {{ $h==19?'selected':'' }}>{{ str_pad($h,2,'0',STR_PAD_LEFT) }}</option>@endfor
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">MM</label>
                        <select class="form-select form-select-sm" name="demat_minute">
                            @for($m=0;$m<60;$m++)<option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ $m==1?'selected':'' }}>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>@endfor
                        </select>
                    </div>
                </div>
                <div id="pgdAddDematMsg" class="mb-2" style="font-size:12px;display:none;"></div>
                <div class="text-end">
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveAddDematTransaction()">
                        <span id="pgdAddDematSpinner" class="spinner-border spinner-border-sm d-none"></span> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

@push('scripts')
<script>
var PGD_TDSTCS_URL    = '{{ url("/admin/pg/dashboard/tds-tcs") }}';
var PGD_COMMISSION_URL = '{{ url("/admin/pg/dashboard/commission") }}';
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

function openUserBalModal()        { document.getElementById('pgdUserBalOverlay').classList.add('open'); }
function openUserBalPendingModal() { document.getElementById('pgdUserBalPendingOverlay').classList.add('open'); }
function openTdsTcsModal()         { document.getElementById('pgdTdsTcsOverlay').classList.add('open'); loadTdsTcsData(); }
function openCommissionModal()     { document.getElementById('pgdCommissionOverlay').classList.add('open'); loadCommissionData(); }
function closeOverlay(id)          { document.getElementById(id).classList.remove('open'); }

// Close on backdrop click
document.querySelectorAll('.pgd-overlay').forEach(function(el) {
    el.addEventListener('click', function(e) { if (e.target === el) el.classList.remove('open'); });
});

function loadTdsTcsData(pageNo) {
    pageNo = pageNo || 0;
    var fy      = $('#pgdFY').val();
    var month   = $('#pgdMonth').val();
    var taxType = $('#pgdTaxType').val();
    var search  = $('#pgdTaxSearch').val();
    $('#pgdTdsTcsRows').html('<p class="text-center py-3"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
    $.ajax({
        type: 'POST', url: PGD_TDSTCS_URL,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        data: { financial_year: fy, filter_month: month, type_of_tax: taxType, filter_searchtext: search, page_no: pageNo },
        dataType: 'json',
        success: function(r) {
            $('#pgdTdsTcsRows').html(r.table);
            $('#pgdTdsTcsPagination').html(r.pagination);
        },
        error: function() { $('#pgdTdsTcsRows').html('<p class="text-danger py-3">Failed to load data.</p>'); }
    });
}

function loadCommissionData(pageNo) {
    pageNo = pageNo || 0;
    var from   = $('#pgdCommFrom').val();
    var to     = $('#pgdCommTo').val();
    var search = $('#pgdCommSearch').val();
    $('#pgdCommissionRows').html('<p class="text-center py-3"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
    $.ajax({
        type: 'POST', url: PGD_COMMISSION_URL,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        data: { from: from, to: to, searchby: search, page_no: pageNo },
        dataType: 'json',
        success: function(r) {
            $('#pgdCommissionRows').html(r.table);
            $('#pgdCommissionPagination').html(r.pagination);
        },
        error: function() { $('#pgdCommissionRows').html('<p class="text-danger py-3">Failed to load data.</p>'); }
    });
}

/* ══ Add Transaction ══════════════════════════════════════════════════════ */
var PGD_ADD_TXN_URL   = '{{ url("/admin/pg/dashboard/add-transaction") }}';
var PGD_ADD_DEMAT_URL = '{{ url("/admin/pg/dashboard/add-demat-transaction") }}';
var PGD_SEARCH_USERS_URL  = '{{ url("/admin/pg/search-users") }}';
var PGD_SEARCH_STOCKS_URL = '{{ url("/admin/pg/search-stocks") }}';

function openAddTransactionModal() {
    $('#pgdAddTxnForm')[0].reset();
    $('#pgdTxnFields').hide();
    $('#pgdAddTxnMsg').hide();
    $('#pgdFromTo').val('');
    $('#pgdTxnDate').val(new Date().toISOString().slice(0,10));
    document.getElementById('pgdAddTxnOverlay').classList.add('open');
}

function pgdTxnDynamic() {
    var flow = $("input[name='pgt_transaction_type']:checked").val();
    var acc  = $("input[name='pgt_bank_account']:checked").val();
    if (flow === 'Flow In') {
        $('#pgdFromToLabel').html('From <span style="color:green;">&#8595;</span> *');
    } else if (flow === 'Flow Out') {
        $('#pgdFromToLabel').html('To <span style="color:red;">&#8593;</span> *');
    }
    // disable the selected account in From/To (can't transfer to same account)
    $('#pgdFromTo option').prop('disabled', false);
    if (acc) $('#pgdFromTo option[value="' + acc + '"]').prop('disabled', true);
    if (flow || acc) $('#pgdTxnFields').show();
}

function saveAddTransaction() {
    var acc  = $("input[name='pgt_bank_account']:checked").val();
    var flow = $("input[name='pgt_transaction_type']:checked").val();
    var from = $('#pgdFromTo').val();
    var amt  = $('#pgdTxnAmt').val();
    var ref  = $('#pgdTxnRef').val();
    var dt   = $('#pgdTxnDate').val();
    var hh   = $("select[name='pgt_hour']").val();
    var mm   = $("select[name='pgt_minute']").val();
    if (!acc)  { alert('Select an account'); return; }
    if (!flow) { alert('Select Flow In / Out'); return; }
    if (!from) { alert('Select From / To'); return; }
    if (acc === from) { alert('Account and From/To cannot be the same'); return; }
    if (!amt || parseFloat(amt) <= 0) { alert('Enter a valid amount'); return; }
    if (!dt)   { alert('Select a date'); return; }
    if (!ref)  { alert('Enter Ref No.'); return; }
    var payload = {
        pgt_bank_account: acc, pgt_transaction_type: flow,
        pgt_from_to: from, pgt_amount: amt,
        pgt_transaction_date: dt, pgt_hour: hh, pgt_minute: mm,
        pgt_ref_no: ref, pgt_remarks: $('#pgdTxnRemarks').val(),
        pgt_transaction_for_user_id: 0,
        pgt_direct_flag:     $('#pgdDirectFlag').is(':checked')     ? 1 : 0,
        pgt_commission_flag: $('#pgdCommissionFlag').is(':checked') ? 1 : 0,
        pgt_TDS_flag:        $('#pgdTdsFlag').is(':checked')        ? 1 : 0
    };
    $('#pgdAddTxnSpinner').removeClass('d-none');
    $.ajax({
        type: 'POST', url: PGD_ADD_TXN_URL,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        data: payload, dataType: 'json',
        success: function(r) {
            $('#pgdAddTxnSpinner').addClass('d-none');
            var msg = $('#pgdAddTxnMsg').show();
            if (r.success) {
                msg.css('color','green').text(r.message);
                setTimeout(function(){ closeOverlay('pgdAddTxnOverlay'); location.reload(); }, 1200);
            } else { msg.css('color','red').text(r.message); }
        },
        error: function() { $('#pgdAddTxnSpinner').addClass('d-none'); alert('Server error'); }
    });
}

/* ══ Add Demat Transaction ════════════════════════════════════════════════ */
function openAddDematTransactionModal(uid, name, companyName, balance, companyId) {
    $('#pgdAddDematForm')[0].reset();
    $('#pgdDematFields').hide();
    $('#pgdAddDematMsg').hide();
    uid = uid || ''; name = name || ''; companyName = companyName || '';
    balance = balance !== undefined ? balance : ''; companyId = companyId || '';
    $('#pgdDematUserSearch').val(name); $('#pgdDematUserId').val(uid);
    $('#pgdDematStockSearch').val(companyName); $('#pgdDematFincode').val(companyId);
    $('#pgdDematDate').val(new Date(Date.now()-86400000).toISOString().slice(0,10));
    $("select[name='demat_hour']").val('19');
    $("select[name='demat_minute']").val('01');
    if (balance !== '') {
        var qty = Math.abs(parseFloat(balance));
        $('#pgdDematQty').val(qty);
        var flag = parseFloat(balance) < 0 ? 'Flow In' : 'Flow Out';
        $("input[name='DEMAT_IN_OUT_FLAG'][value='"+flag+"']").prop('checked', true);
        $('#pgdDematFields').show();
    }
    document.getElementById('pgdAddDematOverlay').classList.add('open');
}

function pgdDematDynamic() { $('#pgdDematFields').show(); }

function saveAddDematTransaction() {
    var flag    = $("input[name='DEMAT_IN_OUT_FLAG']:checked").val();
    var userId  = $('#pgdDematUserId').val();
    var fincode = $('#pgdDematFincode').val();
    var qty     = $('#pgdDematQty').val();
    var dt      = $('#pgdDematDate').val();
    if (!flag)   { alert('Select Flow In / Out'); return; }
    if (!userId) { alert('Select a customer'); return; }
    if (!fincode){ alert('Select a company'); return; }
    if (!qty || parseInt(qty) < 1) { alert('Enter a valid quantity'); return; }
    if (!dt)     { alert('Select a date'); return; }
    var payload = {
        DEMAT_IN_OUT_FLAG: flag, DEMAT_USER_ID: userId,
        DEMAT_FINCODE: fincode, DEMAT_QTY: qty, DEMAT_DATE: dt,
        demat_hour: $("select[name='demat_hour']").val(),
        demat_minute: $("select[name='demat_minute']").val()
    };
    $('#pgdAddDematSpinner').removeClass('d-none');
    $.ajax({
        type: 'POST', url: PGD_ADD_DEMAT_URL,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        data: payload, dataType: 'json',
        success: function(r) {
            $('#pgdAddDematSpinner').addClass('d-none');
            var msg = $('#pgdAddDematMsg').show();
            if (r.success) {
                msg.css('color','green').text(r.message);
                setTimeout(function(){ closeOverlay('pgdAddDematOverlay'); location.reload(); }, 1200);
            } else { msg.css('color','red').text(r.message); }
        },
        error: function() { $('#pgdAddDematSpinner').addClass('d-none'); alert('Server error'); }
    });
}

/* ══ User / Stock autocomplete ════════════════════════════════════════════ */
function pgdMakeDropdown(inputId, dropdownId, hiddenId, searchUrl, labelField, valueField, minLength) {
    minLength = minLength || 2;
    var timer;
    $('#'+inputId).on('input', function() {
        var q = $(this).val().trim();
        if (q.length < minLength) { $('#'+dropdownId).hide(); return; }
        clearTimeout(timer);
        timer = setTimeout(function() {
            $.getJSON(searchUrl, { q: q }, function(rows) {
                var dd = $('#'+dropdownId).empty();
                if (!rows.length) { dd.hide(); return; }
                rows.forEach(function(r) {
                    $('<div>').text(r[labelField]).css({padding:'6px 10px',cursor:'pointer'})
                        .hover(function(){$(this).css('background','#f0f4ff')},function(){$(this).css('background','#fff')})
                        .on('click', function() {
                            $('#'+inputId).val(r[labelField]);
                            $('#'+hiddenId).val(r[valueField]);
                            dd.hide();
                        }).appendTo(dd);
                });
                dd.show();
            });
        }, 250);
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#'+inputId+',#'+dropdownId).length) $('#'+dropdownId).hide();
    });
}

pgdMakeDropdown('pgdDematUserSearch','pgdDematUserDropdown','pgdDematUserId', PGD_SEARCH_USERS_URL, 'label', 'uid', 1);
pgdMakeDropdown('pgdDematStockSearch','pgdDematStockDropdown','pgdDematFincode', PGD_SEARCH_STOCKS_URL, 'label', 'fincode');
pgdMakeDropdown('pgdMapUserSearch','pgdMapUserDropdown','pgdMapUserId', PGD_SEARCH_USERS_URL, 'label', 'uid', 1);

/* ══ Transaction Mapping ══════════════════════════════════════════════════ */
var PGD_MAP_TXN_URL = '{{ url("/admin/pg/dashboard/map-transaction") }}';

function openMappingModal(tid, amount, refNo, txnType) {
    $('#pgdMapTid').val(tid);
    $('#pgdMapUserId').val('');
    $('#pgdMapUserSearch').val('');
    $('#pgdMapUserDropdown').hide();
    $('#pgdMapMsg').hide().text('');
    var typeIcon = txnType === 'Flow In'
        ? '<i class="fa fa-arrow-down" style="color:green"></i>'
        : '<i class="fa fa-arrow-up" style="color:red"></i>';
    $('#pgdMapTidCell').html(tid + ' ' + typeIcon);
    $('#pgdMapAmtCell').text(amount);
    $('#pgdMapRefCell').text(refNo || '—');
    document.getElementById('pgdMappingOverlay').classList.add('open');
}

function saveMappingTransaction() {
    var tid    = $('#pgdMapTid').val();
    var userId = $('#pgdMapUserId').val();
    if (!userId || userId == '0') { alert('Please select a customer'); return; }
    if (!confirm('Map transaction #' + tid + ' to this user?')) return;
    $('#pgdMapSpinner').removeClass('d-none');
    $.ajax({
        type: 'POST', url: PGD_MAP_TXN_URL,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        data: { pgt_tid: tid, map_user_id: userId },
        dataType: 'json',
        success: function(r) {
            $('#pgdMapSpinner').addClass('d-none');
            var msg = $('#pgdMapMsg').show();
            if (r.success) {
                msg.css('color','green').text(r.message);
                setTimeout(function(){ closeOverlay('pgdMappingOverlay'); location.reload(); }, 1200);
            } else { msg.css('color','red').text(r.message); }
        },
        error: function() { $('#pgdMapSpinner').addClass('d-none'); alert('Server error'); }
    });
}
</script>
@endpush

@include('admin.partials.user-dashboard-modal')

@endsection
