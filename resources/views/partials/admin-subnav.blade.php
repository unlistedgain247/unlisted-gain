@php
    // Privilege::get() re-reads the DB each request instead of trusting the
    // snapshot session() took at login — a revoked grant hides the tab
    // immediately instead of only after the user logs back in.
    $hasUnlisted = !empty(array_filter(\App\Helpers\Privilege::get('unlisted') ?? []));
@endphp

<div class="admin-subnav">
    <div class="admin-subnav-inner">
        @if(\App\Helpers\Privilege::get('admin'))
            <a href="{{ url('/admin/dashboard') }}"
               class="admin-subnav-tab {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        @endif

        @if(\App\Helpers\Privilege::get('user_master'))
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
