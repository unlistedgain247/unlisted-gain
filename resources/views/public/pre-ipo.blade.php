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

            <div class="info-row">
                <div class="info-text">
                    <h2 class="main-title">Best Platform To <span>Buy Pre-IPO | Unlisted Shares</span></h2>

                    <div class="tab-buttons">
                        <button class="tab-btn active" data-tab="what"><i class="fa-solid fa-circle-info"></i> What?</button>
                        <button class="tab-btn" data-tab="why"><i class="fa-solid fa-chart-line"></i> Why?</button>
                        <button class="tab-btn" data-tab="how"><i class="fa-solid fa-list-check"></i> How?</button>
                    </div>

                    <div class="content-box" id="tab-what">
                        <h3>What Are Pre-IPO Shares?</h3>
                        <div class="definition-box">
                            <i class="fa-solid fa-quote-left definition-icon"></i>
                            <p>Pre-IPO Companies are private firms who intend to have a listing on the stock market. In India that would mean being listed on the NSE or BSE or both. Once listed, these companies are referred to as publicly listed companies.</p>
                        </div>
                        <p>Companies who have opened their IPO but have not yet been listed on the stock market, or have not yet made an Initial Public Offering, are referred to as Unlisted/Pre-IPO shares.</p>
                    </div>

                    <div class="content-box" id="tab-why" style="display:none;">
                        <h3>Why Invest in Pre-IPO Shares?</h3>
                        <p>Pre-IPO investing offers the opportunity to buy shares at a price lower than the eventual IPO price, allowing early investors to benefit from significant listing gains.</p>
                        <ul class="benefit-list">
                            <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Potential for multifold returns compared to listed stocks</li>
                            <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Early access before public listing drives valuations higher</li>
                            <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Portfolio diversification with high-growth private companies</li>
                            <li><span class="benefit-check"><i class="fa-solid fa-check"></i></span> Less volatility compared to listed markets in growth phase</li>
                        </ul>
                    </div>

                    <div class="content-box" id="tab-how" style="display:none;">
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

                <div class="info-visual">
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
            </div>

            <div class="promo-banner">
                <h2 class="promo-title">Investment In Pre-IPO | Unlisted Shares <span>Delivers Multifold Returns.</span></h2>
                <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="promo-cta">Browse all listings <i class="fa-solid fa-arrow-right"></i></a>
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

    // Tab switching
    $('.tab-btn').on('click', function () {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.content-box').hide();
        $('#tab-' + $(this).data('tab')).show();
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
