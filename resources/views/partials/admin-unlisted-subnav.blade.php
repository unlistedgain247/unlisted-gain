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

    .admin-sub-subnav-inner::-webkit-scrollbar {
        display: none;
    }

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

    .admin-sub-subnav-tab:hover {
        color: #87b942;
    }

    .admin-sub-subnav-tab.active {
        color: #87b942;
        border-bottom-color: #87b942;
        font-weight: 600;
    }
</style>
@endpush
@endonce

@php
    $_ul      = session('privilege.unlisted', []);
    $_isAdmin = !empty(session('privilege.admin')) || !empty(session('privilege.user_master'));
    $_stockx  = $_isAdmin || !empty($_ul['stockx']);
    $_leads   = $_isAdmin || !empty($_ul['leads']) || !empty($_ul['leads_allocation']);
    $_orders  = $_isAdmin || !empty($_ul['orders']);
    $_reports = !empty($_ul['unlisted_reports']);
@endphp

<div class="admin-sub-subnav">
    <div class="admin-sub-subnav-inner">
        @if($_stockx)
        <a href="{{ url('/admin/unlisted') }}"
            class="admin-sub-subnav-tab {{ request()->routeIs('admin.unlisted') ? 'active' : '' }}">
            Dashboard
        </a>
        @endif

        @if($_leads)
        <a href="{{ url('/admin/unlisted/leads') }}"
            class="admin-sub-subnav-tab {{ request()->routeIs('admin.unlisted.leads') ? 'active' : '' }}">
            Leads
        </a>
        @endif

        @if($_orders)
        <a href="{{ url('/admin/unlisted/orders') }}"
            class="admin-sub-subnav-tab {{ request()->routeIs('admin.unlisted.orders') ? 'active' : '' }}">
            Orders
        </a>
        @endif

        @if($_reports)
        <a href="{{ url('/admin/unlisted/reports') }}"
            class="admin-sub-subnav-tab {{ request()->routeIs('admin.unlisted.reports') ? 'active' : '' }}">
            Reports
        </a>
        @endif

        @if($_stockx && request()->routeIs('admin.unlisted'))
        <button type="button" class="admin-sub-subnav-tab" id="stocksNavBtn">
            + Add Stocks
        </button>
        <button type="button" class="admin-sub-subnav-tab" id="industryNavBtn">
            + Add Industry
        </button>
        @endif
    </div>
</div>
