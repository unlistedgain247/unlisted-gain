<div class="pl-overlay" onclick="if(event.target===this)closePriceListModal()">
    <div class="pl-modal">

        <div class="pl-header">
            <h3>Price List &mdash; {{ $stock->UL_STOCKS_COMPNAME }}</h3>
            <button class="pl-close" onclick="closePriceListModal()" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="pl-info">
            {{ $prices->total() }} results found: Showing page {{ $prices->currentPage() }} of {{ $prices->lastPage() }}
        </div>

        <div class="pl-table-wrap">
            <table class="pl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Bid Price</th>
                        <th>Is Invalid</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prices as $row)
                    <tr @class(['pl-row-alt'=> $loop->even]) data-date="{{ $row->UL_PD_DATE }}">
                        <td>{{ \Carbon\Carbon::parse($row->UL_PD_DATE)->format('d-M-Y') }}</td>
                        <td class="pl-bid">{{ $row->UL_PD_BID_PRICE ?? '—' }}</td>
                        <td>
                            @if ($row->UL_PD_INVALID_FLAG)
                            <span style="color:#e53935;font-weight:500">Invalid</span>
                            @else
                            <span style="color:#4a7c20;font-weight:500">Valid</span>
                            @endif
                        </td>
                        <td>
                            <i class="fa-solid fa-pen pl-edit-btn"
                                data-date="{{ $row->UL_PD_DATE }}"
                                data-bid="{{ $row->UL_PD_BID_PRICE ?? '' }}"
                                style="color:#2196f3;cursor:pointer;margin-right:10px"
                                title="Edit"></i>
                            <i class="fa-solid fa-trash pl-delete-btn"
                                data-date="{{ $row->UL_PD_DATE }}"
                                style="color:#e53935;cursor:pointer"
                                title="Delete"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:32px;color:#aaa">
                            <i class="fa-regular fa-folder-open" style="font-size:22px;display:block;margin-bottom:8px"></i>
                            No price data found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginator', [
        'total' => $prices->total(),
        'perPage' => $prices->perPage(),
        'currentPage' => $prices->currentPage(),
        'callback' => 'loadPriceListPage',
        ])

    </div>
</div>

<script>
(function () {
    var STOCKS_BASE = window.STOCKS_BASE;
    var CSRF        = $('meta[name="csrf-token"]').attr('content');
    var $tbody      = $('.pl-table tbody');

    $tbody.on('click', '.pl-edit-btn', function () {
        var $row = $(this).closest('tr');
        var date = $(this).data('date');
        var bid  = $(this).data('bid');
        $row.find('.pl-bid').html('<input type="number" step="0.01" min="0" value="' + bid + '" id="plEditBid" style="width:80px;padding:4px 6px;border:1.5px solid #87b942;border-radius:6px;font-size:13px">');
        $row.find('td:last').html(
            '<i class="fa-solid fa-check pl-save-btn" data-date="' + date + '" style="color:#4a7c20;cursor:pointer;margin-right:10px;font-size:13px" title="Save"></i>' +
            '<i class="fa-solid fa-xmark pl-cancel-btn" data-date="' + date + '" data-bid="' + bid + '" style="color:#e53935;cursor:pointer;font-size:13px" title="Cancel"></i>'
        );
    });

    $tbody.on('click', '.pl-cancel-btn', function () {
        var $row = $(this).closest('tr');
        var date = $(this).data('date');
        var bid  = $(this).data('bid');
        $row.find('.pl-bid').html(bid || '—');
        $row.find('td:last').html(
            '<i class="fa-solid fa-pen pl-edit-btn" data-date="' + date + '" data-bid="' + bid + '" style="color:#2196f3;cursor:pointer;margin-right:10px" title="Edit"></i>' +
            '<i class="fa-solid fa-trash pl-delete-btn" data-date="' + date + '" style="color:#e53935;cursor:pointer" title="Delete"></i>'
        );
    });

    $tbody.on('click', '.pl-save-btn', function () {
        var $row   = $(this).closest('tr');
        var date   = $(this).data('date');
        var newBid = $('#plEditBid').val();
        $.ajax({
            url:         STOCKS_BASE + '/' + window.plFincode + '/price/' + date,
            method:      'PATCH',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            contentType: 'application/json',
            data:        JSON.stringify({ UL_PD_BID_PRICE: newBid }),
        })
        .done(function (res) {
            if (!res.success) return;
            $row.find('.pl-bid').html(newBid || '—');
            $row.find('td:last').html(
                '<i class="fa-solid fa-pen pl-edit-btn" data-date="' + date + '" data-bid="' + newBid + '" style="color:#2196f3;cursor:pointer;margin-right:10px" title="Edit"></i>' +
                '<i class="fa-solid fa-trash pl-delete-btn" data-date="' + date + '" style="color:#e53935;cursor:pointer" title="Delete"></i>'
            );
        })
        .fail(function () { alert('Update failed.'); });
    });

    $tbody.on('click', '.pl-delete-btn', function () {
        if (!confirm('Delete this price entry?')) return;
        var $row = $(this).closest('tr');
        var date = $(this).data('date');
        $.ajax({
            url:     STOCKS_BASE + '/' + window.plFincode + '/price/' + date,
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .done(function (res) {
            if (res.success) $row.fadeOut(200, function () { $(this).remove(); });
        })
        .fail(function () { alert('Delete failed.'); });
    });
}());
</script>