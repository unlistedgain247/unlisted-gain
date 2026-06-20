@extends('layout.app')

@section('title', 'Buy Unlisted Shares & Pre-IPO Stocks | UnlistedGain')
@section('meta_description', 'Browse and invest in top unlisted and pre-IPO shares in India. Real-time price discovery and secure transactions.')
@section('meta_keywords', 'buy unlisted shares, pre-ipo invest, trending unlisted stocks, purchase pre-ipo India, buy nse unlisted, buy csk shares')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pagecss/buy.css') }}?v={{ filemtime(public_path('assets/css/pagecss/buy.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
    <style>
        .shares-table-wrapper { overflow-x: auto; }

        .shares-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Inter', sans-serif;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .shares-table thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #eee;
        }

        .shares-table thead th {
            padding: 14px 20px;
            text-align: left;
            font-size: 13px;
            color: #888;
            font-weight: 600;
            white-space: nowrap;
        }

        .shares-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .shares-table tbody tr:hover { background: #f8fcf2; }

        .shares-table tbody td {
            padding: 16px 20px;
            font-size: 14px;
            color: #333;
            vertical-align: middle;
        }

        .company-cell { display: flex; align-items: center; gap: 12px; }

        .company-cell img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 4px;
            background: #fff;
        }

        .company-cell span { font-weight: 600; color: #1a1a1a; font-size: 15px; }

        .td-mcap, .td-price { font-weight: 700; color: #1a1a1a; }
        .td-pe   { color: #555; }

        .action-btns { display: flex; gap: 8px; }

        .buy-btn, .sell-btn {
            padding: 7px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            display: inline-block;
            transition: background 0.2s;
        }
        .buy-btn  { background: #28a745; color: #fff; }
        .sell-btn { background: #dc3545; color: #fff; }
        .buy-btn:hover  { background: #218838; color: #fff; text-decoration: none; }
        .sell-btn:hover { background: #c82333; color: #fff; text-decoration: none; }

        .no-results {
            text-align: center;
            padding: 50px 20px;
            color: #888;
            font-size: 15px;
        }

        .pricing-disclaimer {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .pricing-disclaimer i {
            flex-shrink: 0;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .shares-table thead th,
            .shares-table tbody td { padding: 12px 12px; }
            .company-cell img { width: 34px; height: 34px; }
            .company-cell span { font-size: 13px; }
            .action-btns { flex-direction: column; gap: 5px; }
            .buy-btn, .sell-btn { padding: 5px 14px; font-size: 12px; text-align: center; }
        }

        @media (max-width: 480px) {
            .shares-table thead th { font-size: 11px; padding: 10px 8px; }
            .shares-table tbody td { font-size: 12px; padding: 10px 8px; }
            /* Hide P/E on tiny screens to save space */
            .shares-table th:nth-child(4),
            .shares-table td:nth-child(4) { display: none; }
            .company-cell { gap: 8px; }
            .company-cell img { width: 28px; height: 28px; }
            .company-cell span { font-size: 12px; }
            .pricing-disclaimer p { font-size: 12px; }
        }

        #sharesLoading {
            text-align: center;
            padding: 60px 20px;
            color: #888;
            font-size: 15px;
        }
        #sharesLoading i { font-size: 28px; display: block; margin-bottom: 12px; color: #87b942; }
    </style>
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Buy Unlisted Shares']]])
@endsection

@section('content')
<main>
    <section class="ug-shares-page">
        <div class="shares-header">
            <div class="filter-tabs">
                <button class="tab-btn active" data-filter="all">All Shares</button>
                <button class="tab-btn" data-filter="trending">Trending Shares</button>
                <button class="tab-btn" data-filter="others">Others</button>
                <button class="tab-btn" data-filter="now-listed">Now Listed</button>
                <button class="tab-btn" data-filter="unavailable">Unavailable</button>
            </div>
        </div>

        <h1 class="main-title">Top Unlisted Shares / <span>Pre-IPO Shares</span></h1>

        <div class="controls-row">
            <div class="search-wrapper">
                <input type="text" id="shareSearch" placeholder="Search by company name...">
                <i class="fas fa-search"></i>
            </div>
            <div class="sort-wrapper">
                <select id="alphaSort">
                    <option value="mcap">Market Cap ↓</option>
                    <option value="asc">A &ndash; Z</option>
                    <option value="desc">Z &ndash; A</option>
                </select>
            </div>
        </div>

        <div class="pricing-disclaimer">
            <i class="fas fa-info-circle"></i>
            <p><strong>Note:</strong> Pricing and availability of unlisted shares are subject to change on a daily
                basis. Please connect with our team for the latest price updates and availability.</p>
        </div>

        {{-- Loading state — shown until JS initialises --}}
        <div id="sharesLoading">
            <i class="fas fa-spinner fa-spin"></i>
            Loading shares&hellip;
        </div>

        <div id="sharesContainer" class="shares-table-wrapper" style="display:none">
            <table class="shares-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Market Cap (&#8377; In Cr.)</th>
                        <th>Current Price (&#8377;)</th>
                        <th>P/E Ratio</th>
                        <th>Buy / Sell</th>
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

        // --- Tab buttons (UI only for now) ---
        $('.tab-btn').on('click', function () {
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
        });

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
                $tbody.html('<tr><td colspan="5" class="no-results">No shares found.</td></tr>');
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
    });
</script>
@endpush
