@extends('layout.app')

@section('title', 'Unlisted Share Price List 2026 | Daily Updated Prices India')
@section('meta_description', 'Get the latest, daily updated prices for all major unlisted and pre-IPO shares in India. Check face value, book value, and market cap of top unlisted companies.')
@section('meta_keywords', 'unlisted share price list, pre-ipo share prices India, unlisted stock market price, latest unlisted price, nse unlisted share price')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/unlisted.css') }}?v={{ filemtime(public_path('assets/css/pagecss/unlisted.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
<style>
    .ug-price-section .company-info img {
        width: 35px; height: 35px; object-fit: contain;
        border: 1px solid #eee; border-radius: 6px;
        padding: 3px; background: #fff;
    }
    .ug-price-section .action-btns { display: flex; gap: 6px; }
    .ug-price-section .buy-btn, .ug-price-section .sell-btn {
        padding: 5px 14px; border-radius: 5px; font-size: 12px;
        font-weight: 600; cursor: pointer; border: none;
        transition: background 0.2s;
    }
    .ug-price-section .buy-btn  { background: #28a745; color: #fff; }
    .ug-price-section .sell-btn { background: #dc3545; color: #fff; }
    .ug-price-section .buy-btn:hover  { background: #218838; }
    .ug-price-section .sell-btn:hover { background: #c82333; }
    .ug-price-section td.td-price,
    .ug-price-section td.td-mcap { font-weight: 700; color: #1a1a1a; }
    .pricing-disclaimer-row {
        font-size: 13px; color: #666; margin-bottom: 16px;
        display: flex; align-items: center; gap: 6px;
    }
    .pricing-disclaimer-row i { color: #87b942; flex-shrink: 0; }
    #priceTableContainer .pl-loading {
        text-align: center; padding: 48px; color: #94a3b8; font-size: 15px;
    }
</style>
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Unlisted Share Price List']]])
@endsection

@section('content')
<main>
    <section class="ug-price-section">

        <div class="price-header">
            <h1>Unlisted <span>Share Price List</span></h1>
            <p class="last-updated">Last Updated: {{ \Carbon\Carbon::now()->format('F j, Y') }}</p>
        </div>

        <div class="table-controls">
            <div class="search-box">
                <input type="text" id="companySearch" value="{{ $q }}" placeholder="Search by company name...">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path fill="#888" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/>
                </svg>
            </div>
            <div class="sort-box">
                <select id="sortSelect">
                    <option value="mcap"  {{ $sort === 'mcap'  ? 'selected' : '' }}>Market Cap ↓</option>
                    <option value="asc"   {{ $sort === 'asc'   ? 'selected' : '' }}>A &ndash; Z</option>
                    <option value="desc"  {{ $sort === 'desc'  ? 'selected' : '' }}>Z &ndash; A</option>
                </select>
            </div>
        </div>

        <p class="pricing-disclaimer-row">
            <i class="fas fa-info-circle"></i>
            Pricing is tentative &amp; subject to change at the time of execution.
        </p>

        <h2 class="about-title">About <span>Unlisted Shares</span></h2>

        <div id="priceTableContainer">
            @include('public.partials.price-list-rows', ['stocks' => $stocks])
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
function loadPriceListPage(page) {
    var q    = $('#companySearch').val().trim();
    var sort = $('#sortSelect').val();

    $('#priceTableContainer').html('<div class="pl-loading"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:22px"></i></div>');

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
});
</script>
@endpush
