@extends('layout.admin')

@section('title', 'Accounting Report | PG | Admin')

@push('styles')
<style>
    /* ── Filters ──────────────────────────────────────────────── */
    .acc-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
        padding: 14px 18px;
        background: #fafafa;
        border: 1px solid #eef0f3;
        border-radius: 14px;
        margin-bottom: 20px;
    }
    .acc-filters .filter-group { display: flex; flex-direction: column; gap: 5px; }
    .acc-filters label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #9ca3af;
    }
    .acc-filters input {
        padding: 0 12px; border: 1px solid #e2e4e9; border-radius: 8px;
        font-size: 13px; color: #1f2937; background: #fff; height: 38px; min-width: 150px;
        transition: border-color .15s, box-shadow .15s;
    }
    .acc-filters input:focus {
        outline: none; border-color: #87b942; box-shadow: 0 0 0 3px rgba(135,185,66,.15);
    }
    .acc-filters .filter-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 20px; height: 38px; border-radius: 8px;
        border: none; font-size: 13px; font-weight: 600; cursor: pointer;
        background: #87b942; color: #fff; transition: background .15s;
    }
    .acc-filters .filter-btn:hover { background: #769e39; }

    /* ── KPI stat tiles (Report 1) ───────────────────────────────── */
    .acc-kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .acc-kpi-tile {
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 14px;
        padding: 16px 18px;
        position: relative;
        overflow: hidden;
    }
    .acc-kpi-tile::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: #87b942;
    }
    .acc-kpi-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: #9ca3af; margin: 0 0 6px;
        display: flex; align-items: center; gap: 6px;
    }
    .acc-kpi-value {
        font-size: 24px; font-weight: 700; color: #111827; line-height: 1.15;
    }
    .acc-kpi-breakdown {
        display: flex; gap: 14px; margin-top: 10px; padding-top: 10px;
        border-top: 1px solid #f1f3f5;
    }
    .acc-kpi-breakdown div { font-size: 11px; color: #6b7280; }
    .acc-kpi-breakdown span {
        display: block; font-size: 13px; font-weight: 600; color: #374151;
        font-variant-numeric: tabular-nums;
    }

    /* ── Report cards (Opening / Closing holdings) ──────────────── */
    .acc-card { background: #fff; border: 1px solid #eef0f3; border-radius: 14px; overflow: hidden; }
    .acc-card-hdr {
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
        padding: 12px 16px; border-bottom: 1px solid #eef0f3; background: #fafbfc;
    }
    .acc-card-title { font-size: 13px; font-weight: 700; color: #1f2937; margin: 0; }
    .acc-card-sub { font-size: 11px; color: #9ca3af; margin: 0; }
    .acc-accent-bar {
        display: inline-block; width: 3px; height: 14px; border-radius: 2px;
        background: #87b942; flex-shrink: 0;
    }

    .acc-tbl { font-size: 12.5px; width: 100%; border-collapse: collapse; }
    .acc-tbl thead th {
        position: sticky; top: 0; z-index: 1;
        background: #f8fafc; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em; color: #6c757d;
        padding: 9px 12px; border-bottom: 2px solid #eef0f3; white-space: nowrap;
    }
    .acc-tbl tbody td {
        padding: 8px 12px; border-bottom: 1px solid #f4f5f7;
        font-variant-numeric: tabular-nums; white-space: nowrap;
    }
    .acc-tbl tbody tr:nth-child(even) { background: #fbfcfd; }
    .acc-tbl tbody tr:hover { background: #f4faee; }
    .acc-tbl tfoot td {
        padding: 10px 12px; background: #f0f7e8; font-weight: 700;
        font-variant-numeric: tabular-nums; border-top: 2px solid #e1ebd3;
    }
    .acc-tbl-scroll { max-height: 420px; overflow-y: auto; }

    .acc-val-pos { color: #0f7a3d; font-weight: 600; }
    .acc-val-neg { color: #d0342c; font-weight: 600; }

    .acc-reports-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    /* ── Mobile ──────────────────────────────────────────────── */
    @media (max-width: 767.98px) {
        .acc-filters { flex-direction: column; align-items: stretch; }
        .acc-filters .filter-group { width: 100%; }
        .acc-filters input { width: 100%; }
        .acc-filters .filter-btn { width: 100%; justify-content: center; }

        .acc-kpi-row { grid-template-columns: 1fr; }
        .acc-kpi-value { font-size: 21px; }

        .acc-reports-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

@include('partials.admin-pg-subnav')

<div class="admin-main">
    <div style="margin-bottom:16px;">
        <h1 class="admin-page-title" style="margin:0;">Accounting Report</h1>
        <p style="margin:2px 0 0;font-size:12.5px;color:#9ca3af;">Sale / purchase / commission summary and stock-holding reconciliation</p>
    </div>

    <form method="GET" class="acc-filters">
        <div class="filter-group">
            <label>From</label>
            <input type="date" name="from_date" value="{{ $fromDate }}">
        </div>
        <div class="filter-group">
            <label>To</label>
            <input type="date" name="to_date" value="{{ $toDate }}">
        </div>
        <div class="filter-group">
            <button type="submit" class="filter-btn"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
        </div>
    </form>

    {{-- KPI tiles: Sale / Purchase / Commission --}}
    @php
        $kpis = [
            ['label' => 'Total Sale',       'icon' => 'bx-trending-up',   'total' => $summary->total_sale_amount,       'direct' => $summary->direct_sale_amount,       'nonDirect' => $summary->non_direct_sale_amount],
            ['label' => 'Total Purchase',   'icon' => 'bx-trending-down', 'total' => $summary->total_purchase_amount,   'direct' => $summary->direct_purchase_amount,   'nonDirect' => $summary->non_direct_purchase_amount],
            ['label' => 'Total Commission', 'icon' => 'bx-coin-stack',    'total' => $summary->total_commission_paid,   'direct' => $summary->direct_commission_paid,   'nonDirect' => $summary->non_direct_commission_paid],
        ];
    @endphp
    <div class="acc-kpi-row">
        @foreach($kpis as $kpi)
        <div class="acc-kpi-tile">
            <p class="acc-kpi-label"><i class='bx {{ $kpi['icon'] }}'></i>{{ $kpi['label'] }}</p>
            <div class="acc-kpi-value">₹{{ number_format((float) $kpi['total'], 2) }}</div>
            <div class="acc-kpi-breakdown">
                <div>Direct<span>₹{{ number_format((float) $kpi['direct'], 2) }}</span></div>
                <div>Non-direct<span>₹{{ number_format((float) $kpi['nonDirect'], 2) }}</span></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Opening / Closing holdings --}}
    <div class="acc-reports-grid">

        @php
            $holdingBlocks = [
                ['title' => 'Opening Holdings', 'sub' => $fromDate ? 'As of '.date('d M Y', strtotime($fromDate.' -1 day')) : 'All time', 'rows' => $openingRows],
                ['title' => 'Closing Holdings', 'sub' => $toDate   ? 'As of '.date('d M Y', strtotime($toDate))                : 'All time', 'rows' => $closingRows],
            ];
        @endphp

        @foreach($holdingBlocks as $block)
        <div class="acc-card">
            <div class="acc-card-hdr">
                <span class="acc-accent-bar"></span>
                <p class="acc-card-title">{{ $block['title'] }}</p>
                <p class="acc-card-sub">— {{ $block['sub'] }}</p>
            </div>
            <div class="table-responsive acc-tbl-scroll">
                <table class="acc-tbl">
                    <thead>
                        <tr>
                            <th class="text-start">Company</th>
                            <th class="text-end">Debits</th>
                            <th class="text-end">Credits</th>
                            <th class="text-end">Balance</th>
                            <th class="text-end">LTP</th>
                            <th class="text-end">Mkt Value</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $blockTotal = 0; @endphp
                    @forelse($block['rows'] as $r)
                        @php
                            $mktValue = ($r->balance ?? 0) * ($r->last_traded_price ?? 0) * -1;
                            $blockTotal += $mktValue;
                        @endphp
                        <tr>
                            <td class="text-start">{{ $r->UL_STOCKS_S_NAME ?? $r->company_id }}</td>
                            <td class="text-end">{{ number_format($r->debits) }}</td>
                            <td class="text-end">{{ number_format($r->credits) }}</td>
                            <td class="text-end {{ $r->balance < 0 ? 'acc-val-neg' : ($r->balance > 0 ? 'acc-val-pos' : '') }}">{{ number_format($r->balance) }}</td>
                            <td class="text-end">{{ isset($r->last_traded_price) ? number_format($r->last_traded_price, 2) : '—' }}</td>
                            <td class="text-end {{ $mktValue < 0 ? 'acc-val-neg' : ($mktValue > 0 ? 'acc-val-pos' : '') }}">{{ number_format($mktValue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted fst-italic py-4">No holdings</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end">Total</td>
                            <td class="text-end {{ $blockTotal < 0 ? 'acc-val-neg' : ($blockTotal > 0 ? 'acc-val-pos' : '') }}">{{ number_format($blockTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endforeach

    </div>
</div>

@endsection
