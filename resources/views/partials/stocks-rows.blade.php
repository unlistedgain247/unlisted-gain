@forelse($stocks as $stock)
@php
    $mcap  = $stock->market_cap    !== null ? '&#8377;' . number_format((float)$stock->market_cap, 0)    . ' Cr.' : '&mdash;';
    $price = $stock->current_price !== null ? '&#8377;' . number_format((float)$stock->current_price, 2) : '&mdash;';
    $pe    = $stock->pe_ratio      !== null ? number_format((float)$stock->pe_ratio, 1)                  : '&mdash;';
@endphp
<tr class="stock-row"
    data-name="{{ strtolower($stock->UL_STOCKS_COMPNAME) }}"
    data-mcap="{{ $stock->market_cap ?? 0 }}">
    <td>
        <div class="company-cell">
            @if($stock->UL_STOCKS_LOGO_LINK)
            <img src="{{ url($stock->UL_STOCKS_LOGO_LINK) }}"
                 alt="{{ $stock->UL_STOCKS_COMPNAME }}"
                 onerror="this.style.display='none'">
            @endif
            <span>{{ $stock->UL_STOCKS_COMPNAME }}</span>
        </div>
    </td>
    <td class="td-mcap">{!! $mcap !!}</td>
    <td class="td-price">{!! $price !!}</td>
    <td class="td-pe">{!! $pe !!}</td>
    <td>
        <div class="action-btns">
            <button class="buy-btn invest-trigger"
               data-type="buy"
               data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
               data-price="{{ $stock->current_price }}"
               data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
               data-lot-size="{{ $stock->lot_size ?? 50 }}">Buy</button>
            <button class="sell-btn invest-trigger"
               data-type="sell"
               data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
               data-price="{{ $stock->current_price }}"
               data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
               data-lot-size="{{ $stock->lot_size ?? 50 }}">Sell</button>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="5" class="no-results">No shares found.</td></tr>
@endforelse
