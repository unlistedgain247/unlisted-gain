@php $offset = ($stocks->currentPage() - 1) * $stocks->perPage(); @endphp

<div class="shares-table-wrapper">
    <table class="shares-table" id="preIpoTable">
        <thead>
            <tr>
                <th>Company</th>
                <th>Market Cap</th>
                <th>Price</th>
                <th>P/E <i class="fa-solid fa-circle-info pe-info" title="P/E ratio vs. earnings — Green: up to 25 (attractively valued) · Orange: 25–45 (moderately valued) · Red: above 45 or negative (richly valued / currently loss-making)"></i></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            @php
                $detailUrl = '/companies/' . $stock->UL_STOCKS_SLUG . '/';

                $mcapRaw = $stock->market_cap !== null ? (float) $stock->market_cap : null;
                $mcap = $mcapRaw === null
                    ? '—'
                    : ($mcapRaw >= 100000
                        ? '₹' . number_format($mcapRaw / 100000, 2) . ' Lakh Cr.'
                        : '₹' . number_format($mcapRaw, 0) . ' Cr.');

                $price = $stock->current_price !== null ? '₹' . number_format((float) $stock->current_price, 2) : '—';

                $peRaw = $stock->pe_ratio !== null ? (float) $stock->pe_ratio : null;
                $pe    = $peRaw !== null ? number_format($peRaw, 1) : '—';
                $peTitle = null;
                $peClass = 'pe-na';
                if ($peRaw !== null) {
                    if ($peRaw < 0) {
                        $peClass = 'pe-high';
                        $peTitle = 'Negative P/E — company is currently loss-making';
                    } else {
                        $peClass = $peRaw <= 25 ? 'pe-good' : ($peRaw <= 45 ? 'pe-mid' : 'pe-high');
                    }
                }

                $industry = trim((string) ($stock->UL_STOCKS_INDUSTRY ?? '')) !== ''
                    ? $stock->UL_STOCKS_INDUSTRY
                    : 'Private Company';

                $initial = strtoupper(substr($stock->UL_STOCKS_COMPNAME, 0, 1));
            @endphp
            <tr class="stock-row" data-href="{{ $detailUrl }}">
                <td data-label="Company">
                    <a href="{{ $detailUrl }}" class="company-cell-link">
                        <div class="company-cell">
                            <div class="company-logo">
                                <span class="company-logo-fallback">{{ $initial }}</span>
                                @if($stock->UL_STOCKS_LOGO_LINK)
                                <img class="lazy-img"
                                     data-src="{{ url($stock->UL_STOCKS_LOGO_LINK) }}"
                                     alt="{{ $stock->UL_STOCKS_COMPNAME }}"
                                     onerror="this.style.display='none'">
                                @endif
                            </div>
                            <div class="company-meta">
                                <span class="company-name">{{ $stock->UL_STOCKS_COMPNAME }}</span>
                                <span class="company-industry">{{ $industry }}</span>
                            </div>
                        </div>
                    </a>
                </td>
                <td class="td-mcap" data-label="Market Cap"><span class="mcap-value">{{ $mcap }}</span></td>
                <td class="td-price" data-label="Price">
                    {{ $price }}
                    @if($stock->current_price !== null)
                    <span class="price-caption">Latest Price</span>
                    @endif
                </td>
                <td data-label="P/E"><span class="pe-badge {{ $peClass }}" @if($peTitle) title="{{ $peTitle }}" @endif>{{ $pe }}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="buy-btn invest-trigger"
                            data-type="buy"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $stock->current_price }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->lot_size ?? 50 }}"><i class="fa-solid fa-cart-shopping"></i> Buy</button>
                        <button class="sell-btn invest-trigger"
                            data-type="sell"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $stock->current_price }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->lot_size ?? 50 }}"><i class="fa-solid fa-hand-holding-dollar"></i> Sell</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="no-results">
                    <div class="no-results-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div class="no-results-title">No matching company found.</div>
                    <div class="no-results-sub">Try another keyword.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.paginator', [
    'total'       => $stocks->total(),
    'perPage'     => $stocks->perPage(),
    'currentPage' => $stocks->currentPage(),
    'callback'    => 'loadPreIpoPage',
])
