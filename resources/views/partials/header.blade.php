<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" alt="UnlistedGain">
            </a>
        </div>

        <nav id="mainNav" class="nav-menu">
            <ul>
                <li class="has-dropdown">
                    <a href="#" class="nav-link">About Us <span class="arrow"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/connect') }}">Connect</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="#" class="nav-link">Services <span class="arrow"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/pre-ipo-unlisted-shares') }}">Pre-IPO | Unlisted Shares</a></li>
                    </ul>
                </li>
                <li class="has-dropdown">
                    <a href="#" class="nav-link {{ request()->is('unlisted') || request()->is('unlisted/*') || request()->is('buy') || request()->is('sell') ? 'nav-current' : '' }}">Buy/Sell <span class="arrow"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/unlisted') }}" class="{{ request()->is('unlisted') || request()->is('unlisted/*') ? 'nav-current' : '' }}">Buy</a></li>
                        <li><a href="{{ url('/unlisted') }}" class="{{ request()->is('unlisted') || request()->is('unlisted/*') ? 'nav-current' : '' }}">Sell</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ url('/unlisted-shares-price-list-india') }}" class="nav-link">Unlisted Share Price</a>
                </li>
                <li class="nav-cta-item">
                    <a href="https://wa.me/919891881886" class="nav-cta-btn" target="_blank">Contact Us</a>
                </li>
            </ul>

            <div class="nav-auth-mobile">
                @if(session('uid'))
                    @if(!empty(session('privilege')))
                        <a href="{{ url('/admin') }}" class="auth-btn auth-signin" style="width:100%;text-align:center">Admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="width:100%">
                        @csrf
                        <button type="submit" class="auth-btn auth-logout w-100">Logout</button>
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
                        <span class="account-avatar">{{ $initial }}</span>
                        <span class="account-text">My Account</span>
                        <span class="arrow"></span>
                    </button>
                    <ul class="dropdown-menu account-menu">
                        <li class="account-menu-header">
                            <span class="account-menu-avatar">{{ $initial }}</span>
                            <div class="account-menu-info">
                                <span class="account-menu-name">{{ $displayName }}</span>
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
