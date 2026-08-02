@extends('layout.admin')

@section('title', 'Accounting Report | PG | Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pg-accounting-report.css') }}?v={{ filemtime(public_path('assets/css/admin/pg-accounting-report.css')) }}">
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
