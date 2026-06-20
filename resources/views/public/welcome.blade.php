@extends('layout.app')

@section('title', 'UnlistedGain | India\'s #1 Marketplace to Buy & Sell Unlisted Shares')
@section('meta_description', 'UnlistedGain is the most trusted platform to buy and sell unlisted, pre-IPO, and ESOP shares in India at the best prices. Invest in high-growth companies early with real-time price discovery.')
@section('meta_keywords', 'unlisted shares, pre-IPO shares, buy unlisted shares India, sell unlisted shares, NSE unlisted price, HDFC Securities unlisted, invest in startups India, ESOP liquidation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ filemtime(public_path('assets/css/styles.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
    <style>
        .item-icon-fallback {
            width: 40px; height: 40px; border-radius: 8px;
            background: linear-gradient(135deg, #87b942, #5a8a1e);
            color: #fff; font-weight: 700; font-size: 13px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')

    {{-- Hero Section --}}
    <main>
        <section class="ug-hero">
            <div class="hero-container">
                <div class="hero-content">
                    <h1 class="hero-title">
                        India's Premier Marketplace for <br>
                        <span class="highlight">Unlisted Shares & Pre-IPO Investments</span>
                    </h1>
                    <h2 class="hero-subtitle">Buy and Sell Unlisted Shares at the Most Competitive Prices</h2>

                    <div class="hero-description">
                        <p>Unlock early-stage investment opportunities with India's most trusted platform for
                            <strong>Unlisted Shares, Pre-IPO stocks, and ESOPs.</strong> We bridge the gap between
                            savvy investors and high-growth companies before they hit the mainboard exchanges.
                        </p>
                    </div>

                    <a href="#" class="hero-cta">Get Latest Trending Stocks</a>
                </div>
            </div>
        </section>

        {{-- Search Section --}}
        <section class="ug-search-section">
            <div class="search-container">
                <div class="search-box-wrapper">
                    <input type="text" id="shareSearchInput"
                        placeholder="Search for Unlisted Shares (e.g. NSE, HDFC...)" autocomplete="off">
                    <div id="searchDropdown" class="search-dropdown"></div>
                    <button class="search-btn">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor"
                                d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        {{-- Icon Slider Section --}}
        <section class="ug-icon-slider-section">
            <div class="icon-slider-container">
                <div class="icon-slider-track" id="iconSliderTrack">
                    {{-- Icons loaded dynamically --}}
                </div>
            </div>
        </section>

        {{-- Stats Strip --}}
        <section class="ug-stats-strip">
            <div class="stats-strip-inner">
                <div class="stat-pill">
                    <div class="stat-pill-icon">📊</div>
                    <div class="stat-pill-body">
                        <span class="stat-pill-num">100+</span>
                        <span class="stat-pill-label">Unlisted Companies</span>
                    </div>
                </div>
                <div class="stat-pill">
                    <div class="stat-pill-icon">💰</div>
                    <div class="stat-pill-body">
                        <span class="stat-pill-num">
                            @if($totalMcap >= 1000)
                                ₹{{ number_format($totalMcap / 1000, 1) }}K+
                            @else
                                ₹{{ number_format($totalMcap, 0) }}+
                            @endif
                        </span>
                        <span class="stat-pill-label">Cr Combined Market Cap</span>
                    </div>
                </div>
                <div class="stat-pill">
                    <div class="stat-pill-icon">🛡️</div>
                    <div class="stat-pill-body">
                        <span class="stat-pill-num">100%</span>
                        <span class="stat-pill-label">Secure Transfers</span>
                    </div>
                </div>
                <div class="stat-pill">
                    <div class="stat-pill-icon">⭐</div>
                    <div class="stat-pill-body">
                        <span class="stat-pill-num">5,000+</span>
                        <span class="stat-pill-label">Happy Investors</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Popular Unlisted Shares (Dynamic) --}}
        @if($topStocks->isNotEmpty())
        <section class="popular-shares-section">
            <div class="slider-header">
                <h2>Popular Unlisted <span>Shares In India</span>: High-Growth Opportunities</h2>
                <p>Explore High-Demand Unlisted Companies & Pre-IPO Stocks — Live Prices</p>
            </div>

            <div class="slider-container-wrapper">
                <button class="nav-btn prev" id="slidePrev">&#8592;</button>

                <div class="cards-viewport" id="sharesViewport">
                    <div class="cards-track">
                        @foreach($topStocks as $stock)
                        <div class="share-card" onclick="location.href='/companies/{{ $stock->slug }}/'">
                            <div class="logo-box">
                                @if($stock->logo)
                                    <img src="{{ url($stock->logo) }}" alt="{{ $stock->name }}" onerror="this.style.display='none'">
                                @else
                                    <div style="font-weight:700;font-size:22px;color:#87b942;">{{ strtoupper(substr($stock->name,0,2)) }}</div>
                                @endif
                            </div>
                            <h3 class="card-title">{{ $stock->s_name ?: $stock->name }}</h3>
                            <div class="card-meta">
                                <span class="category">{{ $stock->industry ?: 'Unlisted' }}</span>
                            </div>
                            <div class="card-stats">
                                <div class="cs-item">
                                    <span class="cs-label">Price</span>
                                    <span class="cs-val">₹{{ number_format($stock->price) }}</span>
                                </div>
                                <div class="cs-item">
                                    <span class="cs-label">MCap</span>
                                    <span class="cs-val">
                                        @if($stock->mcap)
                                            ₹{{ $stock->mcap >= 1000 ? number_format($stock->mcap/1000,1).'K' : $stock->mcap }}Cr
                                        @else
                                            <span class="no-data">—</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="cs-item">
                                    <span class="cs-label">P/E</span>
                                    <span class="cs-val">
                                        @if($stock->pe && $stock->pe > 0)
                                            {{ $stock->pe }}x
                                        @else
                                            <span class="no-data">—</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <a href="/companies/{{ $stock->slug }}/" class="learn-more-btn" onclick="event.stopPropagation()">View Details</a>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button class="nav-btn next" id="slideNext">&#8594;</button>
            </div>
        </section>
        @endif

        {{-- Why UnlistedGain --}}
        <section class="ug-why-section">
            <div class="ug-why-inner">
                <div class="ug-why-header">
                    <span class="ug-section-tag">WHY UNLISTEDGAIN?</span>
                    <h2>Invest <span>Smarter</span> in Unlisted Shares</h2>
                    <p>India's most trusted platform for pre-IPO and unlisted share investments — transparent, research-backed and secure.</p>
                </div>

                <div class="ug-why-bento">
                    {{-- Large featured card --}}
                    <div class="why-card why-card--hero">
                        <div class="why-card-icon">🏆</div>
                        <h3>Best Price<br>Guarantee</h3>
                        <p>We offer the most competitive bid and ask prices in the unlisted market. No hidden markups, no last-minute surprises — what you see is what you pay.</p>
                        <div class="why-hero-stat">
                            <span class="whs-num">₹500 Cr+</span>
                            <span class="whs-label">Deals Executed</span>
                        </div>
                    </div>

                    {{-- 3 smaller cards stacked --}}
                    <div class="why-card-stack">
                        <div class="why-card why-card--sm">
                            <div class="why-sm-left">
                                <div class="why-card-icon why-card-icon--sm">📋</div>
                                <div>
                                    <h4>Research-Backed Picks</h4>
                                    <p>In-depth financials, P&L, balance sheet and ratios for every company — so you invest with confidence, not guesswork.</p>
                                </div>
                            </div>
                        </div>
                        <div class="why-card why-card--sm">
                            <div class="why-sm-left">
                                <div class="why-card-icon why-card-icon--sm">⚡</div>
                                <div>
                                    <h4>Same-Day Share Delivery</h4>
                                    <p>Shares transferred directly to your demat account — fast, paperless and fully compliant with SEBI regulations.</p>
                                </div>
                            </div>
                        </div>
                        <div class="why-card why-card--sm">
                            <div class="why-sm-left">
                                <div class="why-card-icon why-card-icon--sm">🛡️</div>
                                <div>
                                    <h4>100% Deal Guarantee</h4>
                                    <p>Every transaction is secured end-to-end. No backouts, no fraud — your investment is protected at every step.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom wide card --}}
                    <div class="why-card why-card--wide">
                        <div class="why-card-icon">📈</div>
                        <div>
                            <h3>Early-Stage Access = Outsized Returns</h3>
                            <p>Investors who bought NSE, Tata Technologies, Swiggy and Waaree Energies in the unlisted market saw <strong>up to 25X gains</strong> before they even listed. UnlistedGain gives you that edge — every day.</p>
                        </div>
                        <a href="{{ url('/unlisted-shares-price-list-india') }}" class="why-cta">Explore Companies</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Testimonials --}}
        <section class="ug-testimonials">
            <div class="ug-testimonials-inner">
                <div class="ug-why-header">
                    <span class="ug-section-tag">TESTIMONIALS</span>
                    <h2>What Our <span>Investors</span> Say</h2>
                    <div class="testi-rating-badge">
                        <svg width="22" height="22" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <strong>4.8</strong>
                        <span class="testi-stars">★★★★★</span>
                        <span class="testi-count">Google Rating</span>
                    </div>
                </div>

                <div class="testi-grid">
                    <div class="testi-card">
                        <div class="testi-quote">"</div>
                        <p class="testi-text">Bought NSE unlisted shares through UnlistedGain 2 years ago. The process was seamless — shares came to my demat the same day. Their research section really helped me understand the valuation before investing.</p>
                        <div class="testi-footer">
                            <div class="testi-avatar">RK</div>
                            <div>
                                <div class="testi-name">Rahul Khanna</div>
                                <div class="testi-city">Mumbai • NSE Shares Investor</div>
                            </div>
                            <div class="testi-stars-sm">★★★★★</div>
                        </div>
                    </div>

                    <div class="testi-card testi-card--accent">
                        <div class="testi-quote">"</div>
                        <p class="testi-text">First time buying unlisted shares and I was nervous. The team was patient, explained every step and I never felt pressured. Got Swiggy shares before the IPO at a great price. Extremely happy with the returns!</p>
                        <div class="testi-footer">
                            <div class="testi-avatar">PS</div>
                            <div>
                                <div class="testi-name">Priya Sharma</div>
                                <div class="testi-city">Delhi • Swiggy Pre-IPO Investor</div>
                            </div>
                            <div class="testi-stars-sm">★★★★★</div>
                        </div>
                    </div>

                    <div class="testi-card">
                        <div class="testi-quote">"</div>
                        <p class="testi-text">What sets UnlistedGain apart is the data — actual financials, P/E, book value right on the company page. I compared 6 platforms and this one had the most transparent pricing and the best research.</p>
                        <div class="testi-footer">
                            <div class="testi-avatar">AM</div>
                            <div>
                                <div class="testi-name">Ankit Mehta</div>
                                <div class="testi-city">Bangalore • HDB Financial Investor</div>
                            </div>
                            <div class="testi-stars-sm">★★★★★</div>
                        </div>
                    </div>

                    <div class="testi-card">
                        <div class="testi-quote">"</div>
                        <p class="testi-text">Bought Tata Technologies unlisted shares, held them, and sold post-listing at 3X. The price discovery on this platform is genuinely better than what I've seen elsewhere. Will invest again for sure.</p>
                        <div class="testi-footer">
                            <div class="testi-avatar">VN</div>
                            <div>
                                <div class="testi-name">Vikram Nair</div>
                                <div class="testi-city">Pune • Tata Tech Investor</div>
                            </div>
                            <div class="testi-stars-sm">★★★★★</div>
                        </div>
                    </div>

                    <div class="testi-card">
                        <div class="testi-quote">"</div>
                        <p class="testi-text">The financial highlights section is incredibly detailed. YOY growth, quarterly data, ratios — everything in one place. As a value investor, this is exactly what I need to make informed decisions.</p>
                        <div class="testi-footer">
                            <div class="testi-avatar">SJ</div>
                            <div>
                                <div class="testi-name">Sneha Joshi</div>
                                <div class="testi-city">Hyderabad • NSE & NSDL Investor</div>
                            </div>
                            <div class="testi-stars-sm">★★★★★</div>
                        </div>
                    </div>

                    <div class="testi-card testi-card--accent">
                        <div class="testi-quote">"</div>
                        <p class="testi-text">Transferred shares to my demat in under 24 hours. No running around, no paperwork headaches. The support team answered every query instantly. This is how investing should feel.</p>
                        <div class="testi-footer">
                            <div class="testi-avatar">DG</div>
                            <div>
                                <div class="testi-name">Deepak Gupta</div>
                                <div class="testi-city">Chennai • Waaree Energies Investor</div>
                            </div>
                            <div class="testi-stars-sm">★★★★★</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- FAQ Section --}}
    <section class="ug-faq-section">
        <h2 class="faq-title">Frequently Asked <span>Questions</span></h2>

        <div class="faq-container" id="faqContainer">
            <div class="faq-item active">
                <div class="faq-question">
                    What are the factors to consider when buying stocks in India?
                    <span class="faq-icon"></span>
                </div>
                <div class="faq-answer">
                    <p>When it comes to buying unlisted shares in India, there are multiple factors to consider.</p>
                    <ul>
                        <li>Strong product/Service Offering</li>
                        <li>Qualified & Trusted Management</li>
                        <li>Robust financial management</li>
                        <li>Share Price and Intrinsic Value</li>
                        <li>Streamlined Positive Cash Flow</li>
                        <li>Strong Business Growth Model</li>
                        <li>Key Financial metrics like PE Ratio | Dividend Ratio, Debt-Equity Ratio | Price-Sales Ratio
                            | Price-Books Ratio | Market Cap, etc.</li>
                    </ul>
                    <p>At UnlistedGain, we research and shortlist the most profitable unlisted stocks in India.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Which are the best unlisted shares to buy in India?
                    <span class="faq-icon"></span>
                </div>
                <div class="faq-answer">
                    <p>The best unlisted shares depend on market trends and company performance. Currently, companies
                        like NSE, HDB Financial, and Tata Technologies are highly sought after.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    How do you know if a stock is a good investment?
                    <span class="faq-icon"></span>
                </div>
                <div class="faq-answer">
                    <p>Analyze company fundamentals, debt levels, revenue growth, and the valuation relative to its
                        listed peers.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    How to buy the best unlisted shares in India?
                    <span class="faq-icon"></span>
                </div>
                <div class="faq-answer">
                    <p>You can buy them through specialized platforms like UnlistedGain, which facilitate the transfer
                        of shares from sellers to buyers in the unlisted market.</p>
                </div>
            </div>

            <div class="faq-extra-items" style="display: none;">
                <div class="faq-item">
                    <div class="faq-question">
                        Is it safe to invest in unlisted shares?
                        <span class="faq-icon"></span>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, if done through regulated platforms and ensuring the shares are transferred to your
                            demat account.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-footer">
            <button id="viewAllFaq" class="view-all-btn">View All</button>
        </div>
    </section>

    {{-- Lead Modal --}}
    <div id="ugModal" class="stock-modal-overlay">
        <div class="stock-modal-content">
            <button class="modal-close" id="closeModal">&times;</button>

            <div class="modal-header">
                <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" alt="UnlistedGain" class="modal-logo">
            </div>

            <form id="trendingStocksForm">
                <div class="form-group">
                    <input type="text" placeholder="First Name" required name="fname">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Last Name" required name="lname">
                </div>
                <div class="form-group phone-group">
                    <select name="countryCode">
                        <option value="+91">+91 - India</option>
                    </select>
                    <input type="tel" placeholder="Mobile" required name="mobile">
                </div>
                <div class="form-group">
                    <input type="email" placeholder="Email" required name="email">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="City" required name="city">
                </div>

                <div class="captcha-wrapper">
                    <label>What Is <span id="mathQuestion">2 + 3</span>?</label>
                    <input type="number" id="captchaInput" placeholder="?" required>
                    <button type="button" id="refreshCaptcha" class="refresh-btn">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor"
                                d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
                        </svg>
                        New
                    </button>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn-reset">Reset</button>
                    <button type="submit" class="btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div id="successToast" class="success-toast">
        <div class="toast-content">
            <span class="check-icon">&#10003;</span>
            <p>Form submitted successfully!</p>
            <button class="toast-close">&times;</button>
        </div>
        <div class="toast-progress"></div>
    </div>

@push('scripts')
<script src="{{ asset('assets/js/home-search.js') }}"></script>
@endpush

@endsection
