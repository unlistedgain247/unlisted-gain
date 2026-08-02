@extends('layout.app')

@section('title', 'Buy Unlisted Shares & Pre-IPO Stocks | UnlistedGain')
@section('meta_description', 'Browse and invest in top unlisted and pre-IPO shares in India. Real-time price discovery and secure transactions.')
@section('meta_keywords', 'buy unlisted shares, pre-ipo invest, trending unlisted stocks, purchase pre-ipo India, buy nse unlisted, buy csk shares')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pagecss/buy.css') }}?v={{ filemtime(public_path('assets/css/pagecss/buy.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Buy Unlisted Shares']]])
@endsection

@section('content')
<main>
    {{-- Hero --}}
    <section class="buy-hero">
        <div class="buy-hero-inner">
            <span class="buy-hero-eyebrow">Buy &amp; Sell Directly</span>
            <h1 class="buy-hero-title">Top Unlisted &amp; <span>Pre-IPO Shares</span></h1>
            <p class="buy-hero-subtitle">Trade directly with verified sellers at transparent, competitive pricing.</p>

            <div class="buy-hero-search">
                <div class="search-box-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="shareSearch" placeholder="Search company name...">
                </div>
                <div class="buy-hero-chips">
                    <span class="buy-hero-chips-label">Popular:</span>
                    @foreach(['NSE', 'OYO', 'CSK', 'HDB Financial', 'Boat'] as $chip)
                    <button type="button" class="pi-chip" data-term="{{ $chip }}">{{ $chip }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="buy-stats-section">
        <div class="buy-stats-grid">
            <div class="buy-stat-card">
                <span class="buy-stat-icon"><i class="fa-solid fa-building-columns"></i></span>
                <div>
                    <div class="buy-stat-value">{{ count($stocks) }}+</div>
                    <div class="buy-stat-label">Companies Listed</div>
                </div>
            </div>
            <div class="buy-stat-card">
                <span class="buy-stat-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <div>
                    <div class="buy-stat-value">Verified</div>
                    <div class="buy-stat-label">Listings &amp; Sellers</div>
                </div>
            </div>
            <div class="buy-stat-card">
                <span class="buy-stat-icon"><i class="fa-solid fa-wallet"></i></span>
                <div>
                    <div class="buy-stat-value">Same-Day</div>
                    <div class="buy-stat-label">Demat Transfer</div>
                </div>
            </div>
            <div class="buy-stat-card">
                <span class="buy-stat-icon"><i class="fa-solid fa-chart-line"></i></span>
                <div>
                    <div class="buy-stat-value">100%</div>
                    <div class="buy-stat-label">Price Transparency</div>
                </div>
            </div>
        </div>
    </section>

    <section class="ug-shares-page">
        <div class="controls-row">
            <div class="sort-wrapper">
                <select id="alphaSort">
                    <option value="mcap">Market Cap ↓</option>
                    <option value="asc">A &ndash; Z</option>
                    <option value="desc">Z &ndash; A</option>
                </select>
            </div>
        </div>

        <div class="pricing-disclaimer">
            <i class="fa-solid fa-circle-info"></i>
            <p><strong>Note:</strong> Pricing and availability of unlisted shares are subject to change on a daily
                basis. Please connect with our team for the latest price updates and availability.</p>
        </div>

        {{-- Loading state — shown until JS initialises --}}
        <div id="sharesLoading">
            <div class="pi-skeleton">
                <div class="pi-skeleton-row"></div>
                <div class="pi-skeleton-row"></div>
                <div class="pi-skeleton-row"></div>
                <div class="pi-skeleton-row"></div>
                <div class="pi-skeleton-row"></div>
            </div>
        </div>

        <div id="sharesContainer" class="shares-table-wrapper" style="display:none">
            <table class="shares-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Market Cap</th>
                        <th>Price</th>
                        <th>P/E <i class="fa-solid fa-circle-info pe-info" title="P/E ratio vs. earnings — Green: up to 25 (attractively valued) · Orange: 25–45 (moderately valued) · Red: above 45 or negative (richly valued / currently loss-making)"></i></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="sharesTableBody">
                    @include('partials.stocks-rows', ['stocks' => $stocks])
                </tbody>
            </table>
        </div>

        <div class="pagination-container" id="paginationWrapper" style="display: none;">
            <button class="pag-btn" id="prevPage">Previous</button>
            <div class="page-numbers" id="pageNumbers"></div>
            <button class="pag-btn" id="nextPage">Next</button>
        </div>
    </section>
</main>

<section class="ug-faq-section">
    <h2 class="faq-title">Frequently Asked <span>Questions</span></h2>

    <div class="faq-container" id="faqContainer">
        <div class="faq-item active">
            <div class="faq-question">
                What are the factors to consider when buying stocks in India?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>When it comes to buying unlisted shares in India, there are multiple factors to consider. These
                    factors remain the same in almost every country so read carefully.</p>
                <ul>
                    <li>Strong product/Service Offering</li>
                    <li>Qualified &amp; Trusted Management</li>
                    <li>Robust financial management</li>
                    <li>Share Price and Intrinsic Value</li>
                    <li>Streamlined Positive Cash Flow</li>
                    <li>Strong Business Growth Model</li>
                    <li>Key Financial metrics like PE Ratio | Dividend Ratio, Debt-Equity Ratio | Price-Sales Ratio
                        | Price-Books Ratio | Market Cap, etc.</li>
                </ul>
                <p>At ug, we research and shortlist the most profitable unlisted stocks in India.</p>
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
                <p>You can buy them through specialized platforms like ug, which facilitate the transfer of shares
                    from sellers to buyers in the unlisted market.</p>
            </div>
        </div>

        <div class="faq-extra-items" style="display: none;">
            <div class="faq-item">
                <div class="faq-question">Is it safe to invest in unlisted shares?<span class="faq-icon"></span>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        var perPage     = 25;
        var currentPage = 1;
        var allRows     = [];

        // --- Initial load: rows already rendered by Blade ---
        loadRows($('#sharesTableBody .stock-row').toArray());

        // --- On search input (debounced 350ms) ---
        var debounceTimer;
        $('#shareSearch').on('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchRows, 350);
        });

        // --- On sort change: fetch immediately ---
        $('#alphaSort').on('change', fetchRows);

        // --- Fetch rows from backend, swap tbody ---
        function fetchRows() {
            var q    = $('#shareSearch').val().trim();
            var sort = $('#alphaSort').val();

            $('#sharesContainer').hide();
            $('#sharesLoading').show();

            $.ajax({
                url: '/unlisted',
                data: { q: q, sort: sort },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (html) {
                    $('#sharesTableBody').html(html);
                    loadRows($('#sharesTableBody .stock-row').toArray());
                },
                error: function () {
                    $('#sharesLoading').hide();
                    $('#sharesContainer').show();
                }
            });
        }

        // --- Takes a fresh array of rows, detaches them, sets up pagination ---
        function loadRows(rows) {
            allRows = rows;
            $(allRows).detach();
            currentPage = 1;
            renderPage();
            renderPagination();
            $('#sharesLoading').hide();
            $('#sharesContainer').show();
        }

        // --- Render the current page slice ---
        function renderPage() {
            var start    = (currentPage - 1) * perPage;
            var pageData = allRows.slice(start, start + perPage);
            var $tbody   = $('#sharesTableBody');
            $tbody.empty();

            if (pageData.length === 0) {
                $tbody.html('<tr><td colspan="5" class="no-results">' +
                    '<div class="no-results-icon"><i class="fa-solid fa-magnifying-glass"></i></div>' +
                    '<div class="no-results-title">No matching company found.</div>' +
                    '<div class="no-results-sub">Try another keyword.</div>' +
                    '</td></tr>');
                return;
            }

            $.each(pageData, function (i, row) { $tbody.append(row); });
        }

        // --- Render pagination buttons ---
        function renderPagination() {
            var pageCount    = Math.ceil(allRows.length / perPage);
            var $wrapper     = $('#paginationWrapper');
            var $pageNumbers = $('#pageNumbers');
            $pageNumbers.empty();

            if (pageCount <= 1) { $wrapper.hide(); return; }
            $wrapper.show();

            for (var i = 1; i <= pageCount; i++) {
                (function (page) {
                    var $btn = $('<button class="page-num">' + page + '</button>');
                    if (page === currentPage) $btn.addClass('active');
                    $btn.on('click', function () {
                        currentPage = page;
                        renderPage();
                        renderPagination();
                        $('html,body').animate({ scrollTop: $('#sharesContainer').offset().top - 100 }, 300);
                    });
                    $pageNumbers.append($btn);
                })(i);
            }

            $('#prevPage').prop('disabled', currentPage === 1);
            $('#nextPage').prop('disabled', currentPage === pageCount);
        }

        $('#prevPage').on('click', function () {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
                renderPagination();
                $('html,body').animate({ scrollTop: $('#sharesContainer').offset().top - 100 }, 300);
            }
        });

        $('#nextPage').on('click', function () {
            var pageCount = Math.ceil(allRows.length / perPage);
            if (currentPage < pageCount) {
                currentPage++;
                renderPage();
                renderPagination();
                $('html,body').animate({ scrollTop: $('#sharesContainer').offset().top - 100 }, 300);
            }
        });

        // Quick-search chips
        $('.pi-chip').on('click', function () {
            $('#shareSearch').val($(this).data('term'));
            fetchRows();
            $('html,body').animate({ scrollTop: $('.ug-shares-page').offset().top - 80 }, 400);
        });
    });
</script>
@endpush
