@once
@push('styles')
<style>
    .admin-sub-subnav {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
    }
    .admin-sub-subnav-inner {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .admin-sub-subnav-inner::-webkit-scrollbar { display: none; }
    .admin-sub-subnav-tab {
        display: inline-block;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: color 0.15s, border-color 0.15s;
        margin-bottom: -1px;
    }
    .admin-sub-subnav-tab:hover { color: #87b942; }
    .admin-sub-subnav-tab.active { color: #87b942; border-bottom-color: #87b942; font-weight: 600; }
</style>
@endpush
@endonce

@php
    $_pg    = session('privilege.pg', []);
    $_dash  = !empty($_pg['dashboard']);
    $_marg  = !empty($_pg['margin']);
    $_merr  = !empty($_pg['margin_error']);
    $_txn   = !empty($_pg['transactions']);
@endphp

<div class="admin-sub-subnav">
    <div class="admin-sub-subnav-inner">
        @if($_dash)
        <a href="{{ url('/admin/pg/dashboard') }}"
           class="admin-sub-subnav-tab {{ request()->routeIs('admin.pg.dashboard') ? 'active' : '' }}">
            PG Dashboard
        </a>
        <a href="{{ url('/admin/pg/request-dashboard') }}"
           class="admin-sub-subnav-tab {{ request()->routeIs('admin.pg.request-dashboard') ? 'active' : '' }}">
            Request Dashboard
        </a>
        <a href="{{ url('/admin/pg/accounting-report') }}"
           class="admin-sub-subnav-tab {{ request()->routeIs('admin.pg.accounting-report') ? 'active' : '' }}">
            Accounting Report
        </a>
        @endif

        @if($_marg)
        <a href="{{ url('/admin/pg/margin') }}"
           class="admin-sub-subnav-tab {{ request()->routeIs('admin.pg.margin') ? 'active' : '' }}">
            Margin Dashboard
        </a>
        @endif

        @if($_merr)
        <a href="{{ url('/admin/pg/margin-error') }}"
           class="admin-sub-subnav-tab {{ request()->routeIs('admin.pg.margin-error') ? 'active' : '' }}">
            Margin Error
        </a>
        @endif

        @if($_txn)
        <a href="{{ url('/admin/pg/transactions') }}"
           class="admin-sub-subnav-tab {{ request()->routeIs('admin.pg.transactions') ? 'active' : '' }}">
            Transactions
        </a>
        @endif
    </div>
</div>
