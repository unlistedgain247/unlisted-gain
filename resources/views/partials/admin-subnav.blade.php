@php
    $hasUnlisted = !empty(array_filter(session('privilege.unlisted', [])));
@endphp

<div class="admin-subnav">
    <div class="admin-subnav-inner">
        @if(session('privilege.admin'))
            <a href="{{ url('/admin/dashboard') }}"
               class="admin-subnav-tab {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        @endif

        @if(session('privilege.user_master'))
            <a href="{{ url('/admin/users') }}"
               class="admin-subnav-tab {{ request()->is('admin/users*') ? 'active' : '' }}">
                Users
            </a>
        @endif

        @if($hasUnlisted)
            <a href="{{ url('/admin/unlisted') }}"
               class="admin-subnav-tab {{ request()->is('admin/unlisted*') ? 'active' : '' }}">
                Unlisted
            </a>
        @endif
    </div>
</div>
