@extends('layout.app')

@section('title', 'What is Pre-IPO Investment? Benefits & How to Buy | UnlistedGain')
@section('meta_description', 'Learn everything about Pre-IPO investing. Discover how it works, the potential for multifold returns, and the best way to buy pre-IPO shares before they hit the stock exchange.')
@section('meta_keywords', 'what is pre-ipo, benefits of pre-ipo investing, pre-ipo investment guide, invest before ipo, pre-ipo shares India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/pre-ipo.css') }}?v={{ filemtime(public_path('assets/css/pagecss/pre-ipo.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Pre-IPO Investing']]])
@endsection

@section('content')
<main>
    {{-- Hero --}}
    <section class="pi-hero">
        <div class="pi-hero-inner">
            <span class="pi-hero-eyebrow">Private Market Investing</span>
            <h1 class="pi-hero-title">Pre-IPO &amp; <span>Unlisted Shares</span></h1>
            <p class="pi-hero-subtitle">Invest in India&rsquo;s fastest growing private companies before they go public.</p>

            <div class="pi-hero-search">
                <div class="search-box-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="piSearch" value="{{ $q }}" placeholder="Search company name...">
                    <button type="button" class="pi-hero-search-btn" aria-label="Search">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
                <div class="pi-hero-chips">
                    <span class="pi-hero-chips-label">Popular:</span>
                    @foreach(['NSE', 'OYO', 'CSK', 'HDB Financial', 'Boat'] as $chip)
                    <button type="button" class="pi-chip" data-term="{{ $chip }}">{{ $chip }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="pi-stats-section">
        <div class="pi-stats-grid">
            <div class="pi-stat-card">
                <span class="pi-stat-icon"><i class="fa-solid fa-building-columns"></i></span>
                <div>
                    <div class="pi-stat-value">{{ $stocks->total() }}+</div>
                    <div class="pi-stat-label">Companies Listed</div>
                </div>
            </div>
            <div class="pi-stat-card">
                <span class="pi-stat-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <div>
                    <div class="pi-stat-value">Verified</div>
                    <div class="pi-stat-label">Listings &amp; Sellers</div>
                </div>
            </div>
            <div class="pi-stat-card">
                <span class="pi-stat-icon"><i class="fa-solid fa-wallet"></i></span>
                <div>
                    <div class="pi-stat-value">Same-Day</div>
                    <div class="pi-stat-label">Demat Transfer</div>
                </div>
            </div>
            <div class="pi-stat-card">
                <span class="pi-stat-icon"><i class="fa-solid fa-chart-line"></i></span>
                <div>
                    <div class="pi-stat-value">100%</div>
                    <div class="pi-stat-label">Price Transparency</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stock listing --}}
    <section class="pi-listing-section" id="preIpoListing">
        <h2 class="section-title">Pre-IPO &amp; <span>Unlisted Share Listings</span></h2>
        <p class="pi-listing-subtitle">Browse verified companies, compare valuations and invest directly.</p>

        <div class="pi-controls">
            <div class="pi-sort-field">
                <select id="piSort">
                    <option value="mcap" {{ $sort === 'mcap' ? 'selected' : '' }}>Market Cap ↓</option>
                    <option value="asc"  {{ $sort === 'asc'  ? 'selected' : '' }}>A &ndash; Z</option>
                    <option value="desc" {{ $sort === 'desc' ? 'selected' : '' }}>Z &ndash; A</option>
                </select>
            </div>
        </div>

        <div id="preIpoContainer">
            @include('public.partials.pre-ipo-rows', ['stocks' => $stocks])
        </div>
    </section>

    {{-- Info section --}}
    <section class="ug-info-section">
        <div class="info-container">

            <div class="info-section-head">
                <h2 class="main-title">Best Platform To <span>Buy Pre-IPO | Unlisted Shares</span></h2>
            </div>

            {{-- What --}}
            <div class="wwh-row">
                <div class="wwh-visual">
                    <svg viewBox="0 0 280 220" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="4" width="272" height="212" rx="24" fill="url(#whatBg)"/>
                        <rect x="82" y="46" width="100" height="128" rx="8" fill="#fff" stroke="#cfe3ac" stroke-width="2"/>
                        <rect x="98" y="66" width="68" height="8" rx="4" fill="#dcecc0"/>
                        <rect x="98" y="84" width="68" height="6" rx="3" fill="#e9f3d9"/>
                        <rect x="98" y="98" width="52" height="6" rx="3" fill="#e9f3d9"/>
                        <rect x="98" y="120" width="68" height="6" rx="3" fill="#e9f3d9"/>
                        <rect x="98" y="134" width="40" height="6" rx="3" fill="#e9f3d9"/>
                        <circle cx="132" cy="152" r="10" fill="#87b942"/>
                        <path d="M127 152l4 4 8-8" stroke="#fff" stroke-width="2.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="188" cy="150" r="30" fill="none" stroke="#4a7c20" stroke-width="7"/>
                        <line x1="209" y1="171" x2="230" y2="192" stroke="#4a7c20" stroke-width="8" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="whatBg" x1="0" y1="0" x2="280" y2="220" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#f2f8ea"/>
                                <stop offset="1" stop-color="#e4f0d3"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="wwh-content">
                    <span class="wwh-tag"><i class="fa-solid fa-circle-info"></i> What</span>
                    <h3>What Are Pre-IPO Shares?</h3>
                    <div class="definition-box">
                        <i class="fa-solid fa-quote-left definition-icon"></i>
                        <p>Pre-IPO Companies are private firms who intend to have a listing on the stock market. In India that would mean being listed on the NSE or BSE or both. Once listed, these companies are referred to as publicly listed companies.</p>
                    </div>
                    <p>Companies who have opened their IPO but have not yet been listed on the stock market, or have not yet made an Initial Public Offering, are referred to as Unlisted/Pre-IPO shares.</p>
                </div>
            </div>

            {{-- Why --}}
            <div class="wwh-row reverse">
                <div class="wwh-visual">
                    <svg viewBox="0 0 280 220" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="4" width="272" height="212" rx="24" fill="url(#whyBg)"/>
                        <line x1="60" y1="170" x2="230" y2="170" stroke="#cfe3ac" stroke-width="3" stroke-linecap="round"/>
                        <rect x="72" y="130" width="26" height="40" rx="4" fill="#cfe3ac"/>
                        <rect x="110" y="104" width="26" height="66" rx="4" fill="#a9cd6f"/>
                        <rect x="148" y="118" width="26" height="52" rx="4" fill="#87b942"/>
                        <rect x="186" y="80" width="26" height="90" rx="4" fill="#4a7c20"/>
                        <path d="M66 96l38-22 30 16 46-38" stroke="#2d5711" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M162 44h20v20" stroke="#2d5711" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="whyBg" x1="0" y1="0" x2="280" y2="220" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#f2f8ea"/>
                                <stop offset="1" stop-color="#e4f0d3"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="wwh-content">
                    <span class="wwh-tag"><i class="fa-solid fa-chart-line"></i> Why</span>
                    <h3>Why Invest in Pre-IPO Shares?</h3>
                    <p>Pre-IPO investing offers the opportunity to buy shares at a price lower than the eventual IPO price, allowing early investors to benefit from significant listing gains.</p>
                    <ul class="benefit-list">
                        <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Potential for multifold returns compared to listed stocks</li>
                        <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Early access before public listing drives valuations higher</li>
                        <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Portfolio diversification with high-growth private companies</li>
                        <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Less volatility compared to listed markets in growth phase</li>
                    </ul>
                </div>
            </div>

            {{-- How --}}
            <div class="wwh-row">
                <div class="wwh-visual">
                    <svg viewBox="0 0 280 220" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="4" width="272" height="212" rx="24" fill="url(#howBg)"/>
                        <rect x="70" y="40" width="90" height="140" rx="10" fill="#fff" stroke="#cfe3ac" stroke-width="2"/>
                        <rect x="98" y="32" width="34" height="14" rx="4" fill="#87b942"/>
                        <circle cx="92" cy="70" r="8" fill="#87b942"/>
                        <path d="M88 70l3 3 6-6" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="108" y="66" width="38" height="7" rx="3.5" fill="#dcecc0"/>
                        <circle cx="92" cy="98" r="8" fill="#87b942"/>
                        <path d="M88 98l3 3 6-6" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="108" y="94" width="38" height="7" rx="3.5" fill="#dcecc0"/>
                        <circle cx="92" cy="126" r="8" fill="none" stroke="#cfe3ac" stroke-width="2.4"/>
                        <rect x="108" y="122" width="30" height="7" rx="3.5" fill="#e9f3d9"/>
                        <path d="M175 100l30-14v56l-30-14z" fill="#4a7c20"/>
                        <circle cx="222" cy="100" r="20" fill="#87b942"/>
                        <path d="M214 100l6 6 12-12" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="howBg" x1="0" y1="0" x2="280" y2="220" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#f2f8ea"/>
                                <stop offset="1" stop-color="#e4f0d3"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="wwh-content">
                    <span class="wwh-tag"><i class="fa-solid fa-list-check"></i> How</span>
                    <h3>How to Buy Pre-IPO Shares?</h3>
                    <p>Buying pre-IPO shares in India is straightforward through UnlistedGain:</p>
                    <div class="steps-list">
                        <div class="step-item">
                            <span class="step-number">1</span>
                            <p>Browse the listing below and select your desired company</p>
                        </div>
                        <div class="step-item">
                            <span class="step-number">2</span>
                            <p>Click <strong>Buy</strong> and fill in the quantity</p>
                        </div>
                        <div class="step-item">
                            <span class="step-number">3</span>
                            <p>Our team will contact you to complete the transfer to your Demat account</p>
                        </div>
                        <div class="step-item">
                            <span class="step-number">4</span>
                            <p>Shares appear in your Demat account within 2&ndash;3 working days</p>
                        </div>
                    </div>
                    <p class="steps-note"><i class="fa-solid fa-circle-info"></i> Minimum investment varies per company based on lot size.</p>
                </div>
            </div>

            <div class="info-highlights">
                <div class="info-highlight-item">
                    <span class="info-highlight-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <h4>Verified Companies</h4>
                        <p>Every listing is checked before it goes live.</p>
                    </div>
                </div>
                <div class="info-highlight-item">
                    <span class="info-highlight-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <div>
                        <h4>Transparent Pricing</h4>
                        <p>Real market cap, P/E and price data, no hidden markups.</p>
                    </div>
                </div>
                <div class="info-highlight-item">
                    <span class="info-highlight-icon"><i class="fa-solid fa-wallet"></i></span>
                    <div>
                        <h4>Secure Demat Transfer</h4>
                        <p>Shares move straight into your demat account.</p>
                    </div>
                </div>
                <div class="info-highlight-item">
                    <span class="info-highlight-icon"><i class="fa-solid fa-headset"></i></span>
                    <div>
                        <h4>Dedicated Support</h4>
                        <p>Our team guides you through every transaction.</p>
                    </div>
                </div>
            </div>

            <div class="action-btns">
                <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="outline-btn">View Listing</a>
                <a href="#faqSection" class="outline-btn">View FAQ</a>
            </div>

        </div>
    </section>

</main>

{{-- FAQ --}}
<section class="ug-faq-section" id="faqSection">
    <h2 class="faq-title">Frequently Asked <span>Questions</span></h2>
    <div class="faq-container" id="faqContainer">
        <div class="faq-item active">
            <div class="faq-question">What are the factors to consider when buying stocks in India?<span class="faq-icon"></span></div>
            <div class="faq-answer">
                <p>When it comes to buying unlisted shares in India, there are multiple factors to consider.</p>
                <ul>
                    <li>Strong product/Service Offering</li>
                    <li>Qualified &amp; Trusted Management</li>
                    <li>Robust financial management</li>
                    <li>Share Price and Intrinsic Value</li>
                    <li>Streamlined Positive Cash Flow</li>
                    <li>Strong Business Growth Model</li>
                    <li>Key Financial metrics like PE Ratio | Dividend Ratio, Debt-Equity Ratio | Price-Sales Ratio | Price-Books Ratio | Market Cap, etc.</li>
                </ul>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">Which are the best unlisted shares to buy in India?<span class="faq-icon"></span></div>
            <div class="faq-answer">
                <p>The best unlisted shares depend on market trends and company performance. Currently, companies like NSE, HDB Financial, and Tata Technologies are highly sought after.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">How do you know if a stock is a good investment?<span class="faq-icon"></span></div>
            <div class="faq-answer">
                <p>Analyze company fundamentals, debt levels, revenue growth, and the valuation relative to its listed peers.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">How to buy the best unlisted shares in India?<span class="faq-icon"></span></div>
            <div class="faq-answer">
                <p>You can buy them through UnlistedGain, which facilitates the transfer of shares from sellers to buyers in the unlisted market.</p>
            </div>
        </div>
        <div class="faq-extra-items" style="display:none;">
            <div class="faq-item">
                <div class="faq-question">Is it safe to invest in unlisted shares?<span class="faq-icon"></span></div>
                <div class="faq-answer">
                    <p>Yes, if done through regulated platforms and ensuring the shares are transferred to your demat account.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="faq-footer">
        <button id="viewAllFaq" class="view-all-btn">View All</button>
    </div>
</section>
@endsection

@push('scripts')
<script>
function loadPreIpoPage(page) {
    var q    = $('#piSearch').val().trim();
    var sort = $('#piSort').val();

    var skeletonRows = '';
    for (var i = 0; i < 5; i++) { skeletonRows += '<div class="pi-skeleton-row"></div>'; }
    $('#preIpoContainer').html('<div class="pi-skeleton">' + skeletonRows + '</div>');

    $.get('{{ route("public.pre-ipo.data") }}', { q: q, sort: sort, page: page })
     .done(function (html) {
         $('#preIpoContainer').html(html);
     })
     .fail(function () {
         $('#preIpoContainer').html('<div class="pi-loading">Failed to load. Please refresh.</div>');
     });
}

$(document).ready(function () {
    // Common paginator handler
    $(document).on('click', '.pagi-btn:not(:disabled)', function () {
        var fn   = $(this).data('cb');
        var page = $(this).data('page');
        if (fn && typeof window[fn] === 'function') window[fn](page);
    });

    // Row click → company page
    $(document).on('click', '.stock-row', function (e) {
        if ($(e.target).closest('.invest-trigger').length) return;
        var href = $(this).data('href');
        if (href) window.location.href = href;
    });

    // Search (debounced)
    var debounce;
    $('#piSearch').on('input', function () {
        clearTimeout(debounce);
        debounce = setTimeout(function () { loadPreIpoPage(1); }, 350);
    });

    // Sort
    $('#piSort').on('change', function () { loadPreIpoPage(1); });

    // Hero search button
    $('.pi-hero-search-btn').on('click', function () { loadPreIpoPage(1); });

    // Quick-search chips
    $('.pi-chip').on('click', function () {
        $('#piSearch').val($(this).data('term'));
        loadPreIpoPage(1);
        var target = $('#preIpoListing');
        if (target.length) {
            $('html,body').animate({ scrollTop: target.offset().top - 80 }, 400);
        }
    });

    // Smooth scroll for anchor buttons
    $('a[href^="#"]').on('click', function (e) {
        var target = $($(this).attr('href'));
        if (target.length) {
            e.preventDefault();
            $('html,body').animate({ scrollTop: target.offset().top - 80 }, 400);
        }
    });
});
</script>
@endpush
