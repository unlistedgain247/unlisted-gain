<div class="pm-overlay" id="priceOverlay" onclick="if(event.target===this)closePriceModal()">
<div class="pm-modal">

    <div class="pm-header">
        <h3>Add Price &mdash; {{ $stock->UL_STOCKS_COMPNAME }}</h3>
        <button class="pm-close" onclick="closePriceModal()" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form id="priceForm" data-fincode="{{ $stock->UL_STOCKS_FINCODE }}">
        @csrf
        <div class="pm-body">

            <div class="pm-row">
                <div class="pm-field">
                    <label>Fincode</label>
                    <div class="pm-display">{{ $stock->UL_STOCKS_FINCODE }}</div>
                </div>
                <div class="pm-field">
                    <label>Date</label>
                    <input type="date" name="UL_PD_DATE" id="pmDate" max="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="pm-row">
                <div class="pm-field">
                    <label>Bid Price</label>
                    <input type="number" name="UL_PD_BID_PRICE" id="pmBidPrice"
                           step="0.01" min="0" placeholder="0.00">
                </div>
            </div>

        </div>

        <div class="pm-footer">
            <span id="pmMsg" class="pm-msg"></span>
            <button type="submit" class="pm-submit">
                <i class="fa-solid fa-plus"></i> Submit
            </button>
        </div>
    </form>

</div>
</div>

<script>
(function () {
    var STOCKS_BASE = window.STOCKS_BASE;
    var CSRF        = $('meta[name="csrf-token"]').attr('content');

    $('#priceForm').on('submit', function (e) {
        e.preventDefault();
        var fincode = $(this).data('fincode');
        var $btn    = $(this).find('.pm-submit').prop('disabled', true)
                             .html('<i class="fa-solid fa-spinner fa-spin"></i> Saving…');

        $.ajax({
            url:         STOCKS_BASE + '/' + fincode + '/price',
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            data:        JSON.stringify({
                UL_PD_DATE:      $('#pmDate').val(),
                UL_PD_BID_PRICE: $('#pmBidPrice').val(),
            }),
        })
        .done(function (res) {
            var color = res.success ? '#4a7c20' : '#e53935';
            $('#pmMsg').css('color', color).text(res.message || (res.success ? 'Saved.' : 'Error.'));
            if (res.success) {
                $('#pmDate').val('');
                $('#pmBidPrice').val('');
            }
        })
        .fail(function (xhr) {
            var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
            var msg    = errors.UL_PD_DATE && errors.UL_PD_DATE[0]
                       || errors.UL_PD_BID_PRICE && errors.UL_PD_BID_PRICE[0]
                       || (xhr.responseJSON && xhr.responseJSON.message)
                       || 'Request failed.';
            $('#pmMsg').css('color', '#e53935').text(msg);
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-plus"></i> Submit');
        });
    });
}());
</script>
