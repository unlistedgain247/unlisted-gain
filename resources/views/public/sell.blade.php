@extends('layout.app')

@section('title', 'Sell Unlisted Shares & Pre-IPO Stocks | UnlistedGain')
@section('meta_description', 'Sell your unlisted and pre-IPO shares in India at a fair, transparent price. Get an instant quote and receive payment quickly through UnlistedGain.')
@section('meta_keywords', 'sell unlisted shares, sell pre-ipo shares, sell nse unlisted, sell csk shares, exit unlisted shares India, unlisted share buyer')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pagecss/sell.css') }}?v={{ filemtime(public_path('assets/css/pagecss/sell.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/invest-modal.css') }}?v={{ filemtime(public_path('assets/css/invest-modal.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Sell Unlisted Shares']]])
@endsection

@section('content')
<main>
    {{-- Hero --}}
    <section class="sell-hero">
        <div class="sell-hero-inner">
            <span class="sell-hero-eyebrow"><i class="fa-solid fa-hand-holding-dollar"></i> Sell Your Holdings</span>
            <h1 class="sell-hero-title">Sell Unlisted &amp; <span>Pre-IPO Shares</span></h1>
            <p class="sell-hero-subtitle">Get a fair, transparent quote and exit your holdings quickly &mdash; no long waiting periods.</p>

            <div class="sell-hero-search">
                <div class="search-box-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="shareSearch" placeholder="Search the company you want to sell...">
                </div>
                <div class="sell-hero-chips">
                    <span class="sell-hero-chips-label">Popular:</span>
                    @foreach(['NSE', 'OYO', 'CSK', 'HDB Financial', 'Boat'] as $chip)
                    <button type="button" class="pi-chip" data-term="{{ $chip }}">{{ $chip }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="sell-stats-section">
        <div class="sell-stats-grid">
            <div class="sell-stat-card">
                <span class="sell-stat-icon"><i class="fa-solid fa-building-columns"></i></span>
                <div>
                    <div class="sell-stat-value">{{ count($stocks) }}+</div>
                    <div class="sell-stat-label">Companies We Cover</div>
                </div>
            </div>
            <div class="sell-stat-card">
                <span class="sell-stat-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <div>
                    <div class="sell-stat-value">Verified</div>
                    <div class="sell-stat-label">Buyer Network</div>
                </div>
            </div>
            <div class="sell-stat-card">
                <span class="sell-stat-icon"><i class="fa-solid fa-bolt"></i></span>
                <div>
                    <div class="sell-stat-value">Fast Payout</div>
                    <div class="sell-stat-label">Funds In T+2 Days</div>
                </div>
            </div>
            <div class="sell-stat-card">
                <span class="sell-stat-icon"><i class="fa-solid fa-chart-line"></i></span>
                <div>
                    <div class="sell-stat-value">100%</div>
                    <div class="sell-stat-label">Price Transparency</div>
                </div>
            </div>
        </div>
    </section>

    {{-- How selling works --}}
    <section class="sell-how-section">
        <h2 class="sell-how-title">How <span>Selling</span> Works</h2>
        <div class="sell-steps-list">
            <div class="sell-step-item">
                <span class="sell-step-number">1</span>
                <div>
                    <h4>Find your company</h4>
                    <p>Search the listing below and select the company whose shares you hold.</p>
                </div>
            </div>
            <div class="sell-step-item">
                <span class="sell-step-number">2</span>
                <div>
                    <h4>Click Sell &amp; enter quantity</h4>
                    <p>Tell us how many shares you want to sell &mdash; you'll get an indicative quote.</p>
                </div>
            </div>
            <div class="sell-step-item">
                <span class="sell-step-number">3</span>
                <div>
                    <h4>Verification &amp; transfer</h4>
                    <p>Our team verifies your holding and coordinates the demat transfer to the buyer.</p>
                </div>
            </div>
            <div class="sell-step-item">
                <span class="sell-step-number">4</span>
                <div>
                    <h4>Get paid</h4>
                    <p>Funds are settled directly to your bank account once the transfer is confirmed.</p>
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
            <p><strong>Note:</strong> Your final payout depends on verification and the prevailing market price.
                Connect with our team for the most accurate, up-to-date quote.</p>
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
                How do I sell my unlisted shares in India?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>Search for your company in the listing below, click <strong>Sell</strong>, and enter the quantity
                    you hold. Our team will verify your holding and connect you with a matched buyer.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                How is the price of my shares determined?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>Pricing is based on the prevailing unlisted market price for that company, factoring in recent
                    transactions, demand and company fundamentals like P/E and market cap.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                How long does the payout take once I sell?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>Once your holding is verified and the demat transfer to the buyer is confirmed, funds are
                    typically settled to your bank account within 2&ndash;3 working days.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                Is it safe to sell my shares through UnlistedGain?
                <span class="faq-icon"></span>
            </div>
            <div class="faq-answer">
                <p>Yes. Every sale is verified before the transfer is initiated, and shares only move once a buyer
                    is matched and payment is confirmed.</p>
            </div>
        </div>

        <div class="faq-extra-items" style="display: none;">
            <div class="faq-item">
                <div class="faq-question">What documents do I need to sell my shares?<span class="faq-icon"></span>
                </div>
                <div class="faq-answer">
                    <p>You'll need your demat account details and PAN for verification before the transfer can be
                        initiated.</p>
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
                url: '/sell',
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
