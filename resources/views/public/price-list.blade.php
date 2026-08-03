@extends('layout.app')

@section('title', 'Unlisted Share Price List 2026 | Daily Updated Prices India')
@section('meta_description', 'Get the latest, daily updated prices for all major unlisted and pre-IPO shares in India. Check face value, book value, and market cap of top unlisted companies.')
@section('meta_keywords', 'unlisted share price list, pre-ipo share prices India, unlisted stock market price, latest unlisted price, nse unlisted share price')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/unlisted.css') }}?v={{ filemtime(public_path('assets/css/pagecss/unlisted.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Unlisted Share Price List']]])
@endsection

@section('content')
<main>
    {{-- Hero --}}
    <section class="price-hero">
        <div class="price-hero-inner">
            <span class="price-hero-eyebrow">Updated {{ \Carbon\Carbon::now()->format('F j, Y') }}</span>
            <h1 class="price-hero-title">Unlisted <span>Share Price List</span></h1>
            <p class="price-hero-subtitle">Daily updated prices, face value, book value and market cap for every major unlisted company in India.</p>

            <div class="price-hero-search">
                <div class="search-box-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="companySearch" value="{{ $q }}" placeholder="Search company name...">
                </div>
                <div class="price-hero-chips">
                    <span class="price-hero-chips-label">Popular:</span>
                    @foreach(['NSE', 'OYO', 'CSK', 'HDB Financial', 'Boat'] as $chip)
                    <button type="button" class="pi-chip" data-term="{{ $chip }}">{{ $chip }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="price-stats-section">
        <div class="price-stats-grid">
            <div class="price-stat-card">
                <span class="price-stat-icon"><i class="fa-solid fa-building-columns"></i></span>
                <div>
                    <div class="price-stat-value">{{ $stocks->total() }}+</div>
                    <div class="price-stat-label">Companies Tracked</div>
                </div>
            </div>
            <div class="price-stat-card">
                <span class="price-stat-icon"><i class="fa-solid fa-calendar-check"></i></span>
                <div>
                    <div class="price-stat-value">Daily</div>
                    <div class="price-stat-label">Price Updates</div>
                </div>
            </div>
            <div class="price-stat-card">
                <span class="price-stat-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <div>
                    <div class="price-stat-value">Verified</div>
                    <div class="price-stat-label">Company Data</div>
                </div>
            </div>
            <div class="price-stat-card">
                <span class="price-stat-icon"><i class="fa-solid fa-chart-line"></i></span>
                <div>
                    <div class="price-stat-value">100%</div>
                    <div class="price-stat-label">Price Transparency</div>
                </div>
            </div>
        </div>
    </section>

    <section class="ug-price-section">

        <div class="table-controls">
            <div class="sort-box">
                <select id="sortSelect">
                    <option value="mcap"  {{ $sort === 'mcap'  ? 'selected' : '' }}>Market Cap ↓</option>
                    <option value="asc"   {{ $sort === 'asc'   ? 'selected' : '' }}>A &ndash; Z</option>
                    <option value="desc"  {{ $sort === 'desc'  ? 'selected' : '' }}>Z &ndash; A</option>
                </select>
            </div>
            <a id="downloadPdfBtn" href="{{ route('public.price-list.pdf', ['q' => $q, 'sort' => $sort]) }}" class="download-pdf-btn">
                <i class="fa-solid fa-file-arrow-down"></i> Download PDF
            </a>
        </div>

        <p class="pricing-disclaimer-row">
            <i class="fa-solid fa-circle-info"></i>
            Pricing is tentative &amp; subject to change at the time of execution.
        </p>

        <div id="priceTableContainer">
            @include('public.partials.price-list-rows', ['stocks' => $stocks])
        </div>

        <div class="about-unlisted-box">
            <h2 class="about-title">About <span>Unlisted Shares</span></h2>
            <p>Unlisted shares are equity in companies that are not yet listed on a stock exchange like the NSE or BSE. This price list shows the latest available trading price, face value, book value, market capitalisation and P/E ratio for each company, sourced daily so you can track valuations before buying or selling.</p>
        </div>

    </section>
</main>

{{-- FAQ --}}
<section class="ug-faq-section">
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
                <p>At UnlistedGain, we research and shortlist the most profitable unlisted stocks in India.</p>
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
function updatePdfLink() {
    var q    = $('#companySearch').val().trim();
    var sort = $('#sortSelect').val();
    var base = '{{ route("public.price-list.pdf") }}';
    $('#downloadPdfBtn').attr('href', base + '?q=' + encodeURIComponent(q) + '&sort=' + encodeURIComponent(sort));
}

function loadPriceListPage(page) {
    var q    = $('#companySearch').val().trim();
    var sort = $('#sortSelect').val();
    updatePdfLink();

    var skeletonRows = '';
    for (var i = 0; i < 5; i++) { skeletonRows += '<div class="pi-skeleton-row"></div>'; }
    $('#priceTableContainer').html('<div class="pi-skeleton">' + skeletonRows + '</div>');

    $.get('{{ route("public.price-list.data") }}', { q: q, sort: sort, page: page })
     .done(function (html) {
         $('#priceTableContainer').html(html);
     })
     .fail(function () {
         $('#priceTableContainer').html('<div class="pl-loading">Failed to load. Please refresh.</div>');
     });
}

$(document).ready(function () {
    // Common paginator handler (same pattern as admin panel)
    $(document).on('click', '.pagi-btn:not(:disabled)', function () {
        var fn   = $(this).data('cb');
        var page = $(this).data('page');
        if (fn && typeof window[fn] === 'function') window[fn](page);
    });

    // Search (debounced)
    var debounce;
    $('#companySearch').on('input', function () {
        clearTimeout(debounce);
        debounce = setTimeout(function () { loadPriceListPage(1); }, 350);
    });

    // Sort
    $('#sortSelect').on('change', function () {
        loadPriceListPage(1);
    });

    // Row click → company page
    $(document).on('click', '.stock-row', function (e) {
        if ($(e.target).closest('.invest-trigger').length) return;
        var href = $(this).data('href');
        if (href) window.location.href = href;
    });

    // Quick-search chips
    $('.pi-chip').on('click', function () {
        $('#companySearch').val($(this).data('term'));
        loadPriceListPage(1);
        var target = $('.ug-price-section');
        if (target.length) {
            $('html,body').animate({ scrollTop: target.offset().top - 80 }, 400);
        }
    });
});
</script>
@endpush
