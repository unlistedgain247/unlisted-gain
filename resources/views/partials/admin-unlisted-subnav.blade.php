@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/admin-unlisted-subnav.css') }}?v={{ filemtime(public_path('assets/css/admin/admin-unlisted-subnav.css')) }}">
@endpush
@endonce

@php
    // No 'admin' bypass — RequirePrivilege requires a real 'unlisted' sub-privilege
    // regardless of the admin flag, so these tabs must match that exactly.
    $_ul      = \App\Helpers\Privilege::get('unlisted') ?? [];
    $_stocks  = !empty($_ul['stocks']);
    $_leads   = !empty($_ul['leads']) || !empty($_ul['leads_allocation']);
    $_orders  = !empty($_ul['orders']);
    $_reports = !empty($_ul['unlisted_reports']);
@endphp

<div class="admin-sub-subnav">
    <div class="admin-sub-subnav-inner">
        @if($_stocks)
        <a href="{{ url('/admin/unlisted') }}"
            class="admin-sub-subnav-tab {{ request()->routeIs('admin.unlisted') ? 'active' : '' }}">
            Dashboard
        </a>
        @endif

        @if($_stocks)
        <a href="{{ url('/admin/unlisted/docs') }}"
            class="admin-sub-subnav-tab {{ request()->routeIs('admin.unlisted.docs') ? 'active' : '' }}">
            Docs
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

        @if($_stocks && request()->routeIs('admin.unlisted'))
        <button type="button" class="admin-sub-subnav-tab" id="stocksNavBtn">
            + Add Stocks
        </button>
        <button type="button" class="admin-sub-subnav-tab" id="industryNavBtn">
            + Add Industry
        </button>
        @endif
    </div>
</div>
