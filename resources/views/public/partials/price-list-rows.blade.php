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
                <th class="col-pe">P/E Ratio</th>
                <th class="col-action">Buy / Sell</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $i => $stock)
            @php
                $price = $stock->current_price !== null ? '₹' . number_format((float)$stock->current_price, 2) : '—';
                $fv    = $stock->face_value    !== null ? '₹' . number_format((float)$stock->face_value, 0)    : '—';
                $bv    = $stock->book_value    !== null ? '₹' . number_format((float)$stock->book_value, 2)    : '—';
                $mcap  = $stock->market_cap    !== null ? '₹' . number_format((float)$stock->market_cap, 0) . ' Cr.' : '—';
                $pe    = $stock->pe_ratio      !== null ? number_format((float)$stock->pe_ratio, 1)               : '—';
            @endphp
            <tr>
                <td>{{ $offset + $loop->iteration }}</td>
                <td>
                    <div class="company-info">
                        @if($stock->UL_STOCKS_LOGO_LINK)
                        <img src="{{ url($stock->UL_STOCKS_LOGO_LINK) }}"
                             alt="{{ $stock->UL_STOCKS_COMPNAME }}"
                             onerror="this.style.display='none'">
                        @endif
                        <span>{{ $stock->UL_STOCKS_COMPNAME }}</span>
                    </div>
                </td>
                <td class="td-price">{{ $price }}</td>
                <td>{{ $fv }}</td>
                <td>{{ $bv }}</td>
                <td class="td-mcap">{{ $mcap }}</td>
                <td>{{ $pe }}</td>
                <td>
                    <div class="action-btns">
                        <button class="buy-btn invest-trigger"
                            data-type="buy"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $stock->current_price }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }}">Buy</button>
                        <button class="sell-btn invest-trigger"
                            data-type="sell"
                            data-company="{{ $stock->UL_STOCKS_COMPNAME }}"
                            data-price="{{ $stock->current_price }}"
                            data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                            data-lot-size="{{ $stock->UL_STOCKS_LOT_SIZE ?? 50 }}">Sell</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:40px;color:#aaa;">
                    <i class="fa-regular fa-folder-open" style="font-size:22px;display:block;margin-bottom:8px"></i>
                    No companies found.
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
