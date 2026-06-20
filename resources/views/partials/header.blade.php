<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" alt="UnlistedGain">
            </a>
        </div>

        <nav id="mainNav" class="nav-menu">
            <div class="nav-sidebar-head">
                <span class="nav-sidebar-title">Navigation</span>
            </div>
            <ul>
                <li class="has-dropdown">
                    <a href="#" class="nav-link {{ request()->is('about') || request()->is('connect') ? 'nav-current' : '' }}">
                        <i class="fa-solid fa-circle-info nav-icon"></i>
                        <span>About Us</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/about') }}"><i class="fa-solid fa-building-columns sub-icon"></i>About</a></li>
                        <li><a href="{{ url('/connect') }}"><i class="fa-solid fa-address-book sub-icon"></i>Connect</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="#" class="nav-link {{ request()->is('pre-ipo*') ? 'nav-current' : '' }}">
                        <i class="fa-solid fa-layer-group nav-icon"></i>
                        <span>Services</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/pre-ipo-unlisted-shares') }}"><i class="fa-solid fa-rocket sub-icon"></i>Pre-IPO | Unlisted Shares</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="#" class="nav-link {{ request()->is('unlisted') || request()->is('unlisted/*') || request()->is('buy') || request()->is('sell') ? 'nav-current' : '' }}">
                        <i class="fa-solid fa-chart-line nav-icon"></i>
                        <span>Buy / Sell</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/unlisted') }}" class="{{ request()->is('unlisted') || request()->is('unlisted/*') ? 'nav-current' : '' }}"><i class="fa-solid fa-cart-shopping sub-icon"></i>Buy</a></li>
                        <li><a href="{{ url('/unlisted') }}" class="{{ request()->is('sell') ? 'nav-current' : '' }}"><i class="fa-solid fa-hand-holding-dollar sub-icon"></i>Sell</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ url('/unlisted-shares-price-list-india') }}" class="nav-link {{ request()->is('unlisted-shares-price-list-india') ? 'nav-current' : '' }}">
                        <i class="fa-solid fa-tags nav-icon"></i>
                        <span>Share Price List</span>
                    </a>
                </li>
                <li class="nav-cta-item">
                    <a href="https://wa.me/919891881886" class="nav-cta-btn" target="_blank">
                        <i class="fa-brands fa-whatsapp"></i> Chat with Us
                    </a>
                </li>
            </ul>

            <div class="nav-auth-mobile">
                @if(session('uid'))
                    @if(!empty(session('privilege')))
                        <a href="{{ url('/admin') }}" class="auth-btn auth-signin" style="width:100%;text-align:center">Admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="width:100%">
                        @csrf
                        <button type="submit" class="auth-btn auth-logout">Logout</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="auth-btn auth-signin">Sign In</a>
                    <a href="{{ url('/register') }}" class="auth-btn auth-signup">Sign Up</a>
                @endif
            </div>
        </nav>

        <div class="header-auth">
            @if(session('uid'))
                @php
                    $displayName = session('name', session('email', 'U'));
                    $initial = strtoupper(mb_substr($displayName, 0, 1));
                @endphp
                <div class="account-wrapper has-dropdown">
                    <button class="account-trigger" type="button">
                        <span class="account-avatar">
                            <img src="{{ route('profile.avatar', session('uid')) }}" alt="" class="account-avatar-dp" onerror="this.style.display='none'">
                            <span class="account-avatar-initial">{{ $initial }}</span>
                        </span>
                        <span class="account-text">{{ explode(' ', trim($displayName))[0] }}</span>
                        <span class="arrow"></span>
                    </button>
                    <ul class="dropdown-menu account-menu">
                        <li class="account-menu-header">
                            <span class="account-menu-avatar">
                                <img src="{{ route('profile.avatar', session('uid')) }}" alt="" class="account-menu-dp" onerror="this.style.display='none'">
                                <span class="account-menu-initial">{{ $initial }}</span>
                            </span>
                            <div class="account-menu-info">
                                <span class="account-menu-name">{{ $displayName }}</span>
                                <span class="account-menu-email">{{ session('email') }}</span>
                            </div>
                        </li>
                        <li class="account-menu-divider"></li>
                        <li>
                            <a href="{{ route('profile') }}" class="account-menu-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                My Profile
                            </a>
                        </li>
                        @if(!empty(session('privilege')))
                            <li>
                                <a href="{{ url('/admin') }}" class="account-menu-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    Admin Panel
                                </a>
                            </li>
                        @endif
                        <li class="account-menu-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="margin:0;padding:0">
                                @csrf
                                <button type="submit" class="account-menu-logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ url('/login') }}" class="auth-btn auth-signin">Sign In</a>
                <a href="{{ url('/register') }}" class="auth-btn auth-signup">Sign Up</a>
            @endif
        </div>

        <button id="mobileToggle" class="mobile-hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>

<script>
$(function () {
    // Remove green bg from avatar circle when DP loads successfully
    function applyDp($img) {
        if ($img[0].complete && $img[0].naturalWidth > 0) {
            $img.show().closest('.account-avatar, .account-menu-avatar')
                .css('background', 'transparent')
                .find('.account-avatar-initial, .account-menu-initial').hide();
        }
    }
    $('.account-avatar-dp, .account-menu-dp').each(function () {
        var $img = $(this);
        $img.on('load', function () { applyDp($img); })
            .on('error', function () { $img.hide(); });
        applyDp($img);
    });

    var $overlay = $('#navOverlay');
    var $nav     = $('#mainNav');
    var $toggle  = $('#mobileToggle');

    function openSidebar()  { $nav.addClass('active'); $toggle.addClass('open'); $overlay.addClass('active'); }
    function closeSidebar() { $nav.removeClass('active'); $toggle.removeClass('open'); $overlay.removeClass('active'); }

    $(document).on('click', '#mobileToggle', function () {
        $nav.hasClass('active') ? closeSidebar() : openSidebar();
    });

    $(document).on('click', '#sidebarClose', function () { closeSidebar(); });
    $overlay.on('click', function () { closeSidebar(); });
});
</script>
