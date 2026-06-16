<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin | UnlistedGain')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Pace loader -->
    <link href="{{ asset('assets/admin-theme/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/admin-theme/js/pace.min.js') }}"></script>

    <!-- Bootstrap + Theme -->
    <link href="{{ asset('assets/admin-theme/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin-theme/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin-theme/css/app.css') }}" rel="stylesheet">

    <!-- Icons — CDN for reliability -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Admin custom styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    <style>
        /* ── Topbar overrides ── */
        .topbar { border-bottom: 1px solid #e4e4e4; }

        .ug-admin-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 24px 0 0;
            border-right: 1px solid #ebebeb;
            height: 60px;
            min-width: 200px;
            text-decoration: none;
        }
        .ug-admin-brand img { height: 32px; }
        .ug-admin-brand span { font-size: 14px; font-weight: 600; color: #444; }

        /* Topbar icon buttons */
        .topbar-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            font-size: 18px;
            cursor: pointer;
            background: none;
            border: none;
        }
        .topbar-icon-btn:hover { background: #f5f5f5; color: #333; }

        /* User trigger in topbar */
        .ug-user-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 8px 5px 5px;
            border-radius: 25px;
            border: 1.5px solid #e4e4e4;
            cursor: pointer;
            background: none;
            transition: border-color 0.15s, background 0.15s;
            text-decoration: none;
            color: #333;
        }
        .ug-user-trigger:hover { border-color: #87b942; background: #f8fff0; color: #333; }
        .ug-user-trigger .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #87b942;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .ug-user-trigger .user-name { font-size: 13px; font-weight: 500; }
        .ug-user-trigger .bx-chevron-down { font-size: 16px; color: #999; }

        /* ── Horizontal Nav overrides ── */
        .nav-container.primary-menu .navbar-nav a.nav-link.ug-active,
        .nav-container.primary-menu .navbar-nav a.nav-link:hover {
            color: #fff !important;
            background-color: #87b942 !important;
            border-radius: 6px;
        }

        /* ── Page wrapper — full width, no excessive side padding ── */
        .page-wrapper {
            margin-top: 120px !important;
        }
        @media screen and (min-width: 1400px) {
            .page-wrapper {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
        }

        /* ── Mobile nav sidebar ── */
        @media screen and (max-width: 1199px) {
            .page-wrapper {
                margin-top: 60px !important;
            }
            .nav-container {
                position: fixed !important;
                top: 0 !important;
                left: -280px !important;
                width: 260px !important;
                height: 100% !important;
                z-index: 1051 !important;
                background: #fff !important;
                box-shadow: 2px 0 12px rgba(0,0,0,0.15) !important;
                transition: left 0.25s ease !important;
                overflow-y: auto !important;
                padding: 70px 10px 20px !important;
                border-bottom: none !important;
            }
            .wrapper.toggled .nav-container {
                left: 0 !important;
            }
            .nav-container .navbar {
                flex-direction: column !important;
                align-items: flex-start !important;
                padding: 0 !important;
            }
            .nav-container .navbar-nav {
                flex-direction: column !important;
                width: 100% !important;
                gap: 2px !important;
            }
            .nav-container .nav-item {
                width: 100% !important;
            }
            .nav-container .nav-link {
                width: 100% !important;
                padding: 10px 14px !important;
                border-radius: 8px !important;
            }
            /* Overlay backdrop */
            .mobile-nav-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.45);
                z-index: 1050;
            }
            .wrapper.toggled .mobile-nav-overlay {
                display: block;
            }
        }
        @media screen and (min-width: 1200px) {
            .mobile-toggle-menu { display: none !important; }
            .mobile-nav-overlay { display: none !important; }
        }

        /* ── Admin content full width ── */
        .admin-main {
            max-width: 100% !important;
            padding: 24px 20px !important;
        }
    </style>

    @stack('styles')
</head>

<body>
@php
    $adminName    = session('name', session('email', 'Admin'));
    $adminInitial = strtoupper(mb_substr($adminName, 0, 1));
@endphp

<div class="wrapper">
<div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    {{-- ═══════════════════════════════════════
         HEADER WRAPPER (fixed topbar + horiz nav)
         ═══════════════════════════════════════ --}}
    <div class="header-wrapper">

        {{-- ── TOP BAR ── --}}
        <header>
            <div class="topbar d-flex align-items-center px-3">
                <nav class="navbar navbar-expand w-100 p-0">

                    {{-- Mobile hamburger (visible only on mobile via theme CSS) --}}
                    <a href="javascript:;" class="mobile-toggle-menu me-2" style="font-size:22px;color:#555;text-decoration:none;">
                        <i class='bx bx-menu'></i>
                    </a>

                    {{-- Brand / Logo --}}
                    <a href="{{ url('/') }}" class="ug-admin-brand me-3">
                        <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" alt="UnlistedGain">
                        <span>Admin Panel</span>
                    </a>

                    {{-- Right-side utility bar --}}
                    <div class="ms-auto d-flex align-items-center gap-2">

                        {{-- View public site --}}
                        <a href="{{ url('/') }}" target="_blank"
                           class="topbar-icon-btn" title="View Public Site">
                            <i class='bx bx-globe'></i>
                        </a>

                        {{-- Fullscreen toggle --}}
                        <button class="topbar-icon-btn" id="btnFullscreen" title="Fullscreen">
                            <i class='bx bx-fullscreen' id="iconFullscreen"></i>
                        </button>

                        {{-- Divider --}}
                        <div style="width:1px;height:24px;background:#e4e4e4;margin:0 4px;"></div>

                        {{-- User dropdown --}}
                        <div class="dropdown">
                            <a class="ug-user-trigger dropdown-toggle dropdown-toggle-nocaret"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">{{ $adminInitial }}</div>
                                <span class="user-name d-none d-md-inline">{{ $adminName }}</span>
                                <i class='bx bx-chevron-down'></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-1" style="min-width:210px;">
                                {{-- Header --}}
                                <li class="px-3 py-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:38px;height:38px;border-radius:50%;background:#87b942;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0;">{{ $adminInitial }}</div>
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:#111;line-height:1.3">{{ $adminName }}</div>
                                            <div style="font-size:11px;color:#999;">Administrator</div>
                                        </div>
                                    </div>
                                </li>
                                {{-- My Profile --}}
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('profile') }}">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        My Profile
                                    </a>
                                </li>
                                {{-- View Public Site --}}
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ url('/') }}" target="_blank">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                        View Public Site
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                {{-- Logout --}}
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                    </div>
                </nav>
            </div>
        </header>

        {{-- ── HORIZONTAL NAVIGATION ── --}}
        <div class="nav-container primary-menu px-3">
            <nav class="navbar navbar-expand w-100 p-0">
                <ul class="navbar-nav gap-1">

                        @if(session('privilege.admin'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/dashboard') }}"
                               class="nav-link {{ request()->is('admin/dashboard') ? 'ug-active' : '' }}">
                                <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                                <div class="menu-title">Dashboard</div>
                            </a>
                        </li>
                        @endif

                        @php
                            $_navUl     = session('privilege.unlisted', []);
                            $_navAdmin  = !empty(session('privilege.admin')) || !empty(session('privilege.user_master'));
                            $_navHasAny = $_navAdmin || !empty(array_filter($_navUl));
                            $_navUrl    = ($_navAdmin || !empty($_navUl['stockx']))
                                ? url('/admin/unlisted')
                                : url('/admin/unlisted/leads');
                        @endphp
                        @if($_navHasAny)
                        <li class="nav-item">
                            <a href="{{ $_navUrl }}"
                               class="nav-link {{ request()->is('admin/unlisted*') ? 'ug-active' : '' }}">
                                <div class="parent-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
                                <div class="menu-title">Unlisted Stocks</div>
                            </a>
                        </li>
                        @endif

                        @if(session('privilege.user_master'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/users') }}"
                               class="nav-link {{ request()->is('admin/users*') ? 'ug-active' : '' }}">
                                <div class="parent-icon"><i class='bx bx-group'></i></div>
                                <div class="menu-title">Users</div>
                            </a>
                        </li>
                        @endif

                        @php
                            $_navPg     = session('privilege.pg', []);
                            $_navPgAny  = !empty(session('privilege.admin')) || !empty(array_filter($_navPg));
                            $_navPgUrl  = !empty($_navPg['margin']) || !empty(session('privilege.admin'))
                                ? url('/admin/pg/margin')
                                : url('/admin/pg/margin-error');
                        @endphp
                        @if($_navPgAny)
                        <li class="nav-item">
                            <a href="{{ $_navPgUrl }}"
                               class="nav-link {{ request()->is('admin/pg*') ? 'ug-active' : '' }}">
                                <div class="parent-icon"><i class='bx bx-credit-card'></i></div>
                                <div class="menu-title">PG</div>
                            </a>
                        </li>
                        @endif

                    </ul>
            </nav>
        </div>

    </div>
    {{-- ═══════ END HEADER WRAPPER ═══════ --}}

    {{-- ═══════ PAGE CONTENT ═══════ --}}
    <div class="page-wrapper">
        <div class="page-content">
            @yield('content')
        </div>
    </div>

</div>{{-- .wrapper --}}

<script src="{{ asset('assets/admin-theme/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin-theme/js/bootstrap.bundle.min.js') }}"></script>
<script>window.PerfectScrollbar = window.PerfectScrollbar || function() { this.destroy = function(){}; this.update = function(){}; };</script>
<script src="{{ asset('assets/admin-theme/js/app.js') }}"></script>

<script>
    // Mobile sidebar toggle
    var wrapper = document.querySelector('.wrapper');
    var overlay = document.getElementById('mobileNavOverlay');
    document.querySelectorAll('.mobile-toggle-menu').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            wrapper.classList.toggle('toggled');
        });
    });
    if (overlay) {
        overlay.addEventListener('click', function() {
            wrapper.classList.remove('toggled');
        });
    }

    // Fullscreen toggle
    document.getElementById('btnFullscreen').addEventListener('click', function () {
        var icon = document.getElementById('iconFullscreen');
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            icon.className = 'bx bx-exit-fullscreen';
        } else {
            document.exitFullscreen();
            icon.className = 'bx bx-fullscreen';
        }
    });
    document.addEventListener('fullscreenchange', function () {
        var icon = document.getElementById('iconFullscreen');
        icon.className = document.fullscreenElement ? 'bx bx-exit-fullscreen' : 'bx bx-fullscreen';
    });
</script>

@stack('scripts')
</body>
</html>
