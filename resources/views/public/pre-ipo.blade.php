@extends('layout.app')

@section('title', 'What is Pre-IPO Investment? Benefits & How to Buy | UnlistedGain')
@section('meta_description', 'Learn everything about Pre-IPO investing. Discover how it works, the potential for multifold returns, and the best way to buy pre-IPO shares before they hit the stock exchange.')
@section('meta_keywords', 'what is pre-ipo, benefits of pre-ipo investing, pre-ipo investment guide, invest before ipo, pre-ipo shares India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/pre-ipo.css') }}?v={{ filemtime(public_path('assets/css/pagecss/pre-ipo.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
<style>
    .pi-listing-section { padding: 40px 24px 20px; max-width: 1400px; margin: 0 auto; }
    .pi-listing-section .section-title { font-size: 22px; font-weight: 700; margin-bottom: 20px; color: #1a1a1a; }
    .pi-listing-section .section-title span { color: #87b942; }
    .pi-controls { display: flex; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
    .pi-controls input[type="text"] {
        flex: 1; min-width: 200px; padding: 9px 14px;
        border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none;
    }
    .pi-controls input[type="text"]:focus { border-color: #87b942; }
    .pi-controls select {
        padding: 9px 14px; border: 1.5px solid #e2e8f0;
        border-radius: 8px; font-size: 14px; background: #fff; cursor: pointer; outline: none;
    }
    .pi-loading { text-align: center; padding: 48px; color: #94a3b8; font-size: 15px; }
    .shares-table-wrapper { overflow-x: auto; }
    .shares-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    .shares-table thead tr { background: #0c3105; }
    .shares-table thead th { padding: 13px 18px; text-align: left; font-size: 13px; color: #fff; font-weight: 600; white-space: nowrap; }
    .shares-table tbody tr { border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background .15s; }
    .shares-table tbody tr:hover { background: #f8fcf2; }
    .shares-table tbody td { padding: 14px 18px; font-size: 14px; color: #333; vertical-align: middle; }
    .company-cell { display: flex; align-items: center; gap: 10px; }
    .company-cell img { width: 36px; height: 36px; object-fit: contain; border: 1px solid #eee; border-radius: 6px; padding: 3px; background: #fff; }
    .td-price { font-weight: 700; color: #1a1a1a; }
    .action-btns { display: flex; gap: 6px; }
    .buy-btn, .sell-btn { padding: 5px 14px; border-radius: 5px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; transition: background .2s; }
    .buy-btn  { background: #28a745; color: #fff; }
    .sell-btn { background: #dc3545; color: #fff; }
    .buy-btn:hover  { background: #218838; }
    .sell-btn:hover { background: #c82333; }
    .no-results { text-align: center; padding: 32px; color: #aaa; }
</style>
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Pre-IPO Investing']]])
@endsection

@section('content')
<main>
    {{-- Stock listing --}}
    <section class="pi-listing-section" id="preIpoListing">
        <h2 class="section-title">Pre-IPO &amp; <span>Unlisted Share Listings</span></h2>

        <div class="pi-controls">
            <input type="text" id="piSearch" value="{{ $q }}" placeholder="Search by company name...">
            <select id="piSort">
                <option value="mcap" {{ $sort === 'mcap' ? 'selected' : '' }}>Market Cap ↓</option>
                <option value="asc"  {{ $sort === 'asc'  ? 'selected' : '' }}>A &ndash; Z</option>
                <option value="desc" {{ $sort === 'desc' ? 'selected' : '' }}>Z &ndash; A</option>
            </select>
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
                        <button class="tab-btn active" data-tab="what">What?</button>
                        <button class="tab-btn" data-tab="why">Why?</button>
                        <button class="tab-btn" data-tab="how">How?</button>
                    </div>

                    <div class="content-box" id="tab-what">
                        <h3>What Are Pre-IPO Shares?</h3>
                        <p>Pre-IPO Companies are private firms who intend to have a listing on the stock market. In India that would mean being listed on the NSE or BSE or both. Once listed, these companies are referred to as publicly listed companies.</p>
                        <p>Companies who have opened their IPO but have not yet been listed on the stock market, or have not yet made an Initial Public Offering, are referred to as Unlisted/Pre-IPO shares.</p>
                    </div>

                    <div class="content-box" id="tab-why" style="display:none;">
                        <h3>Why Invest in Pre-IPO Shares?</h3>
                        <p>Pre-IPO investing offers the opportunity to buy shares at a price lower than the eventual IPO price, allowing early investors to benefit from significant listing gains.</p>
                        <ul>
                            <li>Potential for multifold returns compared to listed stocks</li>
                            <li>Early access before public listing drives valuations higher</li>
                            <li>Portfolio diversification with high-growth private companies</li>
                            <li>Less volatility compared to listed markets in growth phase</li>
                        </ul>
                    </div>

                    <div class="content-box" id="tab-how" style="display:none;">
                        <h3>How to Buy Pre-IPO Shares?</h3>
                        <p>Buying pre-IPO shares in India is straightforward through UnlistedGain:</p>
                        <ol>
                            <li>Browse the listing below and select your desired company</li>
                            <li>Click <strong>Buy</strong> and fill in the quantity</li>
                            <li>Our team will contact you to complete the transfer to your Demat account</li>
                            <li>Shares appear in your Demat account within 2–3 working days</li>
                        </ol>
                        <p>Minimum investment varies per company based on lot size.</p>
                    </div>
                </div>

                <div class="info-visual">
                    <div class="video-wrapper">
                        <div class="video-placeholder">
                            <div class="play-btn"></div>
                        </div>
                    </div>
                    <div class="action-btns">
                        <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="outline-btn">View Listing</a>
                        <a href="#faqSection" class="outline-btn">View FAQ</a>
                    </div>
                </div>
            </div>

            <div class="info-row reverse">
                <div class="info-text">
                    <h2 class="promo-title">Investment In Pre-IPO | Unlisted Shares <span>Delivers Multifold Returns.</span></h2>
                    <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="video-link">Browse all listings &rarr;</a>
                </div>

                <div class="info-visual">
                    <div class="video-wrapper">
                        <div class="video-placeholder">
                            <div class="play-btn"></div>
                        </div>
                    </div>
                </div>
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

    $('#preIpoContainer').html('<div class="pi-loading"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:22px"></i></div>');

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
