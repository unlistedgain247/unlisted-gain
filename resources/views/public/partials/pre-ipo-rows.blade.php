@php $offset = ($stocks->currentPage() - 1) * $stocks->perPage(); @endphp

<div class="shares-table-wrapper">
    <table class="shares-table" id="preIpoTable">
        <thead>
            <tr>
                <th>Company</th>
                <th>Market Cap</th>
                <th>Price</th>
                <th>P/E</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            @php
                $slug      = $stock->UL_STOCKS_SLUG . '-unlisted-shares';
                $detailUrl = '/companies/' . $slug . '/';
                $mcap  = $stock->market_cap    !== null ? '₹' . number_format((float)$stock->market_cap, 0)    . ' Cr.' : '—';
                $price = $stock->current_price !== null ? '₹' . number_format((float)$stock->current_price, 2) : '—';
                $pe    = $stock->pe_ratio      !== null ? number_format((float)$stock->pe_ratio, 1)             : '—';
            @endphp
            <tr class="stock-row" data-href="{{ $detailUrl }}">
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
                <td class="td-mcap">{{ $mcap }}</td>
                <td class="td-price">{{ $price }}</td>
                <td class="td-pe">{{ $pe }}</td>
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
            <tr>
                <td colspan="5" class="no-results">No shares found.</td>
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
