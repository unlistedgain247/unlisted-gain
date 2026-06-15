@extends('layout.app')

@section('title', 'UnlistedGain | India\'s #1 Marketplace to Buy & Sell Unlisted Shares')
@section('meta_description', 'UnlistedGain is the most trusted platform to buy and sell unlisted, pre-IPO, and ESOP shares in India at the best prices. Invest in high-growth companies early with real-time price discovery.')
@section('meta_keywords', 'unlisted shares, pre-IPO shares, buy unlisted shares India, sell unlisted shares, NSE unlisted price, HDFC Securities unlisted, invest in startups India, ESOP liquidation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}">
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

        {{-- Popular Unlisted Shares --}}
        <section class="popular-shares-section">
            <div class="slider-header">
                <h2>Popular Unlisted <span>Shares In India</span>: High-Growth Opportunities</h2>
                <p>Explore High-Demand Unlisted Companies & Pre-IPO Stocks</p>
            </div>

            <div class="slider-container-wrapper">
                <button class="nav-btn prev" id="slidePrev">&#8592;</button>

                <div class="cards-viewport" id="sharesViewport">
                    <div class="cards-track">
                        <div class="share-card" onclick="location.href='/companies/nse-india-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/national-stock-exchange-ltd-nse.webp') }}" alt="NSE India"></div>
                            <h3 class="card-title">NSE India Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">Stock Exchange</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/hdfc-securities-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/hdfc-securities-ltd.webp') }}" alt="HDFC Securities"></div>
                            <h3 class="card-title">HDFC Securities Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">Brokerage</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/chennai-super-kings-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/chennai-super-kings.webp') }}" alt="CSK"></div>
                            <h3 class="card-title">Chennai Super Kings Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Sports</span>
                                <span class="sub-category">Entertainment</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/boat-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/boat.webp') }}" alt="Boat"></div>
                            <h3 class="card-title">Boat Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Consumer Electronics</span>
                                <span class="sub-category">Audio</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/sbi-fund-management-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/sbi-fund-management-limited.webp') }}" alt="SBI Fund"></div>
                            <h3 class="card-title">SBI Fund Management Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">Asset Management</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/oravel-stays-ltd-oyo-rooms-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/oyo-rooms-oravel-stays-ltd.webp') }}" alt="OYO"></div>
                            <h3 class="card-title">Oravel Stays (OYO) Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Hospitality</span>
                                <span class="sub-category">Travel & Tourism</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/hero-fincorp-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/hero-fincorp-ltd.webp') }}" alt="Hero Fincorp"></div>
                            <h3 class="card-title">Hero Fincorp Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">NBFC</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/zepto-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/zepto.webp') }}" alt="Zepto"></div>
                            <h3 class="card-title">Zepto Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">E-Commerce</span>
                                <span class="sub-category">Quick Commerce</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="nav-btn next" id="slideNext">&#8594;</button>
            </div>
        </section>

        {{-- Still In Pre-IPO Stage --}}
        <section class="popular-shares-section">
            <div class="slider-header">
                <h2>Guess What? Investors Have Received Up To 25X Returns,<br> While
                    <span>Still In Pre-IPO Stage!!!</span>
                </h2>
            </div>

            <div class="slider-container-wrapper">
                <button class="nav-btn prev" id="slidePrev2">&#8592;</button>

                <div class="cards-viewport" id="sharesViewport2">
                    <div class="cards-track">
                        <div class="share-card" onclick="location.href='/companies/cochin-international-airport-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/cochin-international-airport-ltd-cial.webp') }}" alt="CIAL"></div>
                            <h3 class="card-title">Cochin International Airport Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Infrastructure</span>
                                <span class="sub-category">Aviation</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/ncdex-national-commodity-derivatives-exchange-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/national-commodity-derivatives-exchange-ltd.webp') }}" alt="NCDEX"></div>
                            <h3 class="card-title">NCDEX Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">Commodity Exchange</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/metropolitan-stock-exchange-msei-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/metropolitan-stock-exchange-msei.webp') }}" alt="MSEI"></div>
                            <h3 class="card-title">Metropolitan Stock Exchange Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">Stock Exchange</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/jupiter-international-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/jupiter-international-ltd.webp') }}" alt="Jupiter"></div>
                            <h3 class="card-title">Jupiter International Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Manufacturing</span>
                                <span class="sub-category">Industrial</span>
                            </div>
                        </div>

                        <div class="share-card">
                            <div class="logo-box"><img src="{{ asset('assets/img/nsdl-ltd.webp') }}" alt="NSDL"></div>
                            <h3 class="card-title">NSDL Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">Depository</span>
                            </div>
                        </div>

                        <div class="share-card">
                            <div class="logo-box"><img src="{{ asset('assets/img/hdb-financial-services.webp') }}" alt="HDB Financial"></div>
                            <h3 class="card-title">HDB Financial Services Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Financial Services</span>
                                <span class="sub-category">NBFC</span>
                            </div>
                        </div>

                        <div class="share-card">
                            <div class="logo-box"><img src="{{ asset('assets/img/vikram-solar-ltd.webp') }}" alt="Vikram Solar"></div>
                            <h3 class="card-title">Vikram Solar Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Energy</span>
                                <span class="sub-category">Solar</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/paymate-india-ltd-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/pinelabs.webp') }}" alt="Pine Labs"></div>
                            <h3 class="card-title">Pine Labs Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Fintech</span>
                                <span class="sub-category">Payments</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="nav-btn next" id="slideNext2">&#8594;</button>
            </div>
        </section>

        {{-- Now Listed --}}
        <section class="popular-shares-section">
            <div class="slider-header">
                <h2>Success Stories From Past Unlisted Shares: <span>Now Listed</span></h2>
            </div>

            <div class="slider-container-wrapper">
                <button class="nav-btn prev" id="slidePrev3">&#8592;</button>

                <div class="cards-viewport" id="sharesViewport3">
                    <div class="cards-track">
                        <div class="share-card" onclick="location.href='/companies/tata-technologies-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/tata-technologies.webp') }}" alt="Tata Technologies"></div>
                            <h3 class="card-title">Tata Technologies Listed Shares</h3>
                            <div class="card-meta">
                                <span class="category">IT Services</span>
                                <span class="sub-category">Technology</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/waaree-energies-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/waaree-energies-limited.webp') }}" alt="Waaree Energies"></div>
                            <h3 class="card-title">Waaree Energies Listed Shares</h3>
                            <div class="card-meta">
                                <span class="category">Energy</span>
                                <span class="sub-category">Solar</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/swiggy-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/swiggy.webp') }}" alt="Swiggy"></div>
                            <h3 class="card-title">Swiggy Listed Shares</h3>
                            <div class="card-meta">
                                <span class="category">E-Commerce</span>
                                <span class="sub-category">Food Delivery</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/groww-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/groww.webp') }}" alt="Groww"></div>
                            <h3 class="card-title">Groww Listed Shares</h3>
                            <div class="card-meta">
                                <span class="category">Fintech</span>
                                <span class="sub-category">Investment Platform</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/studds-accessories-ltd-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/studds-accessories-ltd.webp') }}" alt="Studds"></div>
                            <h3 class="card-title">Studds Accessories Listed Shares</h3>
                            <div class="card-meta">
                                <span class="category">Manufacturing</span>
                                <span class="sub-category">Auto Accessories</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/sambhv-steel-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/sambhv-steel.webp') }}" alt="Sambhv Steel"></div>
                            <h3 class="card-title">Sambhv Steel Listed Shares</h3>
                            <div class="card-meta">
                                <span class="category">Manufacturing</span>
                                <span class="sub-category">Steel</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/sbi-general-insurance-ltd-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/sbi-fund-management-limited.webp') }}" alt="SBI General Insurance"></div>
                            <h3 class="card-title">SBI General Insurance Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">Insurance</span>
                                <span class="sub-category">General Insurance</span>
                            </div>
                        </div>

                        <div class="share-card" onclick="location.href='/companies/capgemini-technology-services-india-limited-unlisted-shares/'">
                            <div class="logo-box"><img src="{{ asset('assets/img/cochin-international-airport-ltd-cial.webp') }}" alt="Capgemini"></div>
                            <h3 class="card-title">Capgemini Technology Unlisted Shares</h3>
                            <div class="card-meta">
                                <span class="category">IT Services</span>
                                <span class="sub-category">Technology</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="nav-btn next" id="slideNext3">&#8594;</button>
            </div>

            <a href="{{ url('/unlisted-shares-price-list-india') }}" class="popular-cta">View More</a>
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

@endsection
