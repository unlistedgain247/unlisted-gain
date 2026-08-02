<div class="lmw-card" data-url="{{ route('public.market-widget.data') }}">
    <div class="lmw-header">
        <span class="lmw-dot"></span>
        <span class="lmw-title">Live Unlisted Market</span>
        <span class="lmw-updated">Updated regularly</span>
    </div>

    <div class="lmw-controls">
        <div class="lmw-tabs" role="tablist">
            <button type="button" class="lmw-tab active" data-tab="trending">Trending</button>
            <button type="button" class="lmw-tab" data-tab="gainers">Gainers</button>
            <button type="button" class="lmw-tab" data-tab="losers">Losers</button>
        </div>
        <div class="lmw-ranges">
            @foreach(['1d' => '1D', '1w' => '1W', '1m' => '1M', '3m' => '3M', '6m' => '6M', '1y' => '1Y'] as $val => $label)
            <button type="button" class="lmw-range {{ $val === '1w' ? 'active' : '' }}" data-range="{{ $val }}">{{ $label }}</button>
            @endforeach
        </div>
    </div>

    <div class="lmw-table-wrap">
        <table class="lmw-table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Price &#8377;</th>
                    <th>Chg</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody id="lmwBody">
                @include('partials.live-market-rows', ['rows' => $rows])
            </tbody>
        </table>
    </div>

    <div class="lmw-footer">
        <span>{{ $totalStocks }}+ companies tracked</span>
        <a href="{{ route('public.price-list') }}">View all &#8599;</a>
    </div>
</div>
