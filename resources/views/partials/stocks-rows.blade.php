@forelse($stocks as $stock)
@php
    $mcapRaw = $stock->market_cap !== null ? (float) $stock->market_cap : null;
    $mcap = $mcapRaw === null
        ? '&mdash;'
        : ($mcapRaw >= 100000
            ? '&#8377;' . number_format($mcapRaw / 100000, 2) . ' Lakh Cr.'
            : '&#8377;' . number_format($mcapRaw, 0) . ' Cr.');

    $price = $stock->current_price !== null ? '&#8377;' . number_format((float) $stock->current_price, 2) : '&mdash;';

    $peRaw = $stock->pe_ratio !== null ? (float) $stock->pe_ratio : null;
    $pe    = $peRaw !== null ? number_format($peRaw, 1) : '&mdash;';
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
<tr class="stock-row"
    data-name="{{ strtolower($stock->UL_STOCKS_COMPNAME) }}"
    data-mcap="{{ $stock->market_cap ?? 0 }}">
    <td data-label="Company">
        <a href="/companies/{{ $stock->UL_STOCKS_SLUG }}/" class="company-cell-link">
            <div class="company-cell">
                <div class="company-logo">
                    <span class="company-logo-fallback">{{ $initial }}</span>
                    @if($stock->UL_STOCKS_LOGO_LINK)
                    <img src="{{ url($stock->UL_STOCKS_LOGO_LINK) }}"
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
    <td class="td-mcap" data-label="Market Cap"><span class="mcap-value">{!! $mcap !!}</span></td>
    <td class="td-price" data-label="Price">
        {!! $price !!}
        @if($stock->current_price !== null)
        <span class="price-caption">Latest Price</span>
        @endif
    </td>
    <td class="td-pe" data-label="P/E"><span class="pe-badge {{ $peClass }}" @if($peTitle) title="{{ $peTitle }}" @endif>{!! $pe !!}</span></td>
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
