@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/admin-pg-subnav.css') }}?v={{ filemtime(public_path('assets/css/admin/admin-pg-subnav.css')) }}">
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
