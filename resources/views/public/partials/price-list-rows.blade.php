@php $offset = ($stocks->currentPage() - 1) * $stocks->perPage(); @endphp

<div class="table-wrapper">
    <table id="sharesTable">
        <thead>
            <tr>
                <th class="col-sl">SL</th>
                <th class="col-company">Company Name</th>
                <th class="col-price">Current Price</th>
                <th class="col-face">Face Value</th>
                <th class="col-book">Book Value</th>
                <th class="col-market">Market Cap</th>
                <th class="col-pe">P/E <i class="fa-solid fa-circle-info pe-info" title="P/E ratio vs. earnings — Green: up to 25 (attractively valued) · Orange: 25–45 (moderately valued) · Red: above 45 or negative (richly valued / currently loss-making)"></i></th>
                <th class="col-action"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $i => $stock)
            @php
                $price = $stock->current_price !== null ? '₹' . number_format((float) $stock->current_price, 2) : '—';
                $fv    = $stock->face_value    !== null ? '₹' . number_format((float) $stock->face_value, 0)    : '—';
                $bv    = $stock->book_value    !== null ? '₹' . number_format((float) $stock->book_value, 2)    : '—';

                $mcapRaw = $stock->market_cap !== null ? (float) $stock->market_cap : null;
                $mcap = $mcapRaw === null
                    ? '—'
                    : ($mcapRaw >= 100000
                        ? '₹' . number_format($mcapRaw / 100000, 2) . ' Lakh Cr.'
                        : '₹' . number_format($mcapRaw, 0) . ' Cr.');

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

                $initial = strtoupper(substr($stock->UL_STOCKS_COMPNAME, 0, 1));
            @endphp
            <tr class="stock-row" data-href="/companies/{{ $stock->UL_STOCKS_SLUG }}/">
                <td class="td-sl" data-label="SL">{{ $offset + $loop->iteration }}</td>
                <td data-label="Company">
                    <a href="/companies/{{ $stock->UL_STOCKS_SLUG }}/" class="company-cell-link">
                        <div class="company-info">
                            <div class="company-logo">
                                <span class="company-logo-fallback">{{ $initial }}</span>
                                @if($stock->UL_STOCKS_LOGO_LINK)
                                <img src="{{ url($stock->UL_STOCKS_LOGO_LINK) }}"
                                     alt="{{ $stock->UL_STOCKS_COMPNAME }}"
                                     onerror="this.style.display='none'">
                                @endif
                            </div>
                            <span class="company-name">{{ $stock->UL_STOCKS_COMPNAME }}</span>
                        </div>
                    </a>
                </td>
                <td class="td-price" data-label="Price">
                    {{ $price }}
                    @if($stock->current_price !== null)
                    <span class="price-caption">Latest Price</span>
                    @endif
                </td>
                <td data-label="Face Value">{{ $fv }}</td>
                <td data-label="Book Value">{{ $bv }}</td>
                <td class="td-mcap" data-label="Market Cap"><span class="mcap-value">{{ $mcap }}</span></td>
                <td data-label="P/E"><span class="pe-badge {{ $peClass }}" @if($peTitle) title="{{ $peTitle }}" @endif>{{ $pe }}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="buy-btn invest-trigger"
                            data-type="buy"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $stock->current_price }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }}"><i class="fa-solid fa-cart-shopping"></i> Buy</button>
                        <button class="sell-btn invest-trigger"
                            data-type="sell"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $stock->current_price }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }}"><i class="fa-solid fa-hand-holding-dollar"></i> Sell</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="no-results">
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
    'callback'    => 'loadPriceListPage',
])
