@extends('layout.admin')

@section('title', 'Admin Dashboard | UnlistedGain')

@php
    $fmtInr = function ($n) {
        $n = (float) $n;
        if ($n >= 10000000) return '₹' . number_format($n / 10000000, 2) . ' Cr';
        if ($n >= 100000)   return '₹' . number_format($n / 100000, 2) . ' L';
        return '₹' . number_format($n, 0);
    };

    $statusBadge = function ($status) {
        $status = trim((string) $status);
        return match (true) {
            $status === 'Completed' => 'badge-completed',
            $status === '' => 'badge-pending',
            str_contains(strtolower($status), 'pending') => 'badge-pending',
            str_contains(strtolower($status), 'cancel') => 'badge-cancelled',
            default => 'badge-pending',
        };
    };
@endphp

@section('content')
<div class="admin-main">

    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-title mb-0">Dashboard</h1>
            <p class="admin-page-subtitle">Welcome back, {{ session('name', session('email', 'Admin')) }} — here's what's happening today.</p>
        </div>
        <div class="admin-page-head-date">
            <i class="fa-regular fa-calendar"></i> {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- ═══════ STAT CARDS ═══════ --}}
    <div class="admin-stats">

        <div class="admin-stat-card">
            <div class="stat-icon stat-icon-blue"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ number_format($totalUsers) }}</span>
                <span class="stat-label">Total Users</span>
                <span class="stat-sub stat-sub-up"><i class="fa-solid fa-arrow-trend-up"></i> {{ $newUsers30d }} new in 30 days</span>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon stat-icon-purple"><i class="fa-solid fa-user-shield"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ number_format($adminUsers) }}</span>
                <span class="stat-label">Admin Users</span>
                <span class="stat-sub">{{ $unlistedUsers }} unlisted · {{ $channelPartners }} channel partners</span>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon stat-icon-teal"><i class="fa-solid fa-building"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ number_format($totalStocks) }}</span>
                <span class="stat-label">Unlisted Companies</span>
                <span class="stat-sub">{{ $activeStocks }} active listings</span>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon stat-icon-orange"><i class="fa-solid fa-receipt"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ number_format($totalOrders) }}</span>
                <span class="stat-label">Total Orders</span>
                <span class="stat-sub">{{ $completedOrders }} completed</span>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon stat-icon-green"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ $fmtInr($totalOrderValue) }}</span>
                <span class="stat-label">Completed Order Value</span>
                <span class="stat-sub">{{ $buyCount }} buy · {{ $sellCount }} sell</span>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon stat-icon-pink"><i class="fa-solid fa-bullhorn"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ number_format($totalLeads) }}</span>
                <span class="stat-label">Total Leads</span>
                <span class="stat-sub">{{ $leads30d }} in last 30 days</span>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon {{ $pendingWithdrawals > 0 ? 'stat-icon-red' : 'stat-icon-teal' }}"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <div class="stat-info">
                <span class="stat-number">{{ number_format($pendingWithdrawals) }}</span>
                <span class="stat-label">Pending Withdrawals</span>
                <span class="stat-sub">{{ $pendingWithdrawals > 0 ? 'Needs review' : 'All clear' }}</span>
            </div>
        </div>

    </div>

    {{-- ═══════ CHARTS ═══════ --}}
    <div class="admin-chart-grid">

        <div class="admin-card admin-chart-card admin-chart-card-wide">
            <h3 class="admin-chart-title">Order Volume — Last 6 Months</h3>
            <div class="admin-chart-canvas-wrap">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

        <div class="admin-card admin-chart-card">
            <h3 class="admin-chart-title">Buy vs Sell</h3>
            <div class="admin-chart-canvas-wrap admin-chart-canvas-wrap-sm">
                <canvas id="buySellChart"></canvas>
            </div>
        </div>

        <div class="admin-card admin-chart-card">
            <h3 class="admin-chart-title">Top Companies by Order Value</h3>
            <div class="admin-chart-canvas-wrap">
                <canvas id="topStocksChart"></canvas>
            </div>
        </div>

    </div>

    {{-- ═══════ RECENT ORDERS ═══════ --}}
    <div class="admin-card">
        <h3 class="admin-chart-title">Recent Orders</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->UL_ORD_ID }}</td>
                            <td>{{ $order->UL_STOCKS_COMPNAME ?? '—' }}</td>
                            <td>{{ $order->user_name ?? '—' }}</td>
                            <td>
                                <span class="admin-badge {{ $order->UL_ORD_TYPE === 'Sell' ? 'badge-sell' : 'badge-buy' }}">
                                    {{ $order->UL_ORD_TYPE ?? '—' }}
                                </span>
                            </td>
                            <td>{{ number_format($order->UL_ORD_QUANTITY ?? 0) }}</td>
                            <td>{{ $fmtInr($order->UL_ORD_AMOUNT ?? 0) }}</td>
                            <td>
                                <span class="admin-badge {{ $statusBadge($order->UL_ORD_STATUS) }}">
                                    {{ $order->UL_ORD_STATUS ?: 'Pending' }}
                                </span>
                            </td>
                            <td>{{ $order->UL_ORD_DATE ? \Illuminate\Support\Carbon::parse($order->UL_ORD_DATE)->format('d M Y') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:#999;padding:24px;">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var brandGreen = '#87b942';
    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.color = '#666';

    // Orders volume — count (bars) + amount (line)
    new Chart(document.getElementById('ordersChart'), {
        type: 'bar',
        data: {
            labels: @json($orderMonthLabels),
            datasets: [
                {
                    type: 'bar',
                    label: 'Orders',
                    data: @json($orderMonthCounts),
                    backgroundColor: 'rgba(135, 185, 66, 0.65)',
                    borderRadius: 6,
                    yAxisID: 'y',
                    order: 2,
                },
                {
                    type: 'line',
                    label: 'Value (₹)',
                    data: @json($orderMonthAmounts),
                    borderColor: '#1565c0',
                    backgroundColor: '#1565c0',
                    tension: 0.35,
                    yAxisID: 'y1',
                    order: 1,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y:  { beginAtZero: true, position: 'left', title: { display: true, text: 'Orders' }, grid: { color: '#f0f0f0' } },
                y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Value (₹)' }, grid: { drawOnChartArea: false } },
            },
            plugins: { legend: { position: 'bottom' } },
        }
    });

    // Buy vs Sell
    new Chart(document.getElementById('buySellChart'), {
        type: 'doughnut',
        data: {
            labels: ['Buy', 'Sell'],
            datasets: [{
                data: [{{ $buyCount }}, {{ $sellCount }}],
                backgroundColor: [brandGreen, '#e57373'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
        }
    });

    // Top stocks by order value
    new Chart(document.getElementById('topStocksChart'), {
        type: 'bar',
        data: {
            labels: @json($topStocks->pluck('name')),
            datasets: [{
                label: 'Order Value (₹)',
                data: @json($topStocks->pluck('total')),
                backgroundColor: 'rgba(21, 101, 192, 0.65)',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#f0f0f0' } }, y: { grid: { display: false } } },
        }
    });
});
</script>
@endpush
