<style>
.pm-overlay {
    display: flex;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.55);
    z-index: 2100;
    align-items: center;
    justify-content: center;
    padding: 16px;
    backdrop-filter: blur(2px);
}
.pm-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.22);
    animation: privSlideIn 0.2s cubic-bezier(0.34,1.56,0.64,1);
    overflow: hidden;
}
.pm-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid #e2e8f0;
}
.pm-header h3 { margin:0; font-size:16px; font-weight:700; color:#1a1a1a; }
.pm-close {
    background:#f1f5f9; border:none; border-radius:8px;
    width:32px; height:32px; display:flex; align-items:center;
    justify-content:center; cursor:pointer; color:#64748b;
    transition:background 0.15s;
}
.pm-close:hover { background:#e2e8f0; color:#1a1a1a; }
.pm-body { padding:20px 22px; display:flex; flex-direction:column; gap:16px; }
.pm-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.pm-field label {
    display:block; font-size:12px; font-weight:600; color:#64748b;
    margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;
}
.pm-field input {
    width:100%; padding:9px 12px;
    border:1.5px solid #e2e8f0; border-radius:8px;
    font-size:13px; color:#1a1a1a; outline:none;
    transition:border-color 0.15s, box-shadow 0.15s;
    box-sizing:border-box;
}
.pm-field input:focus {
    border-color:#87b942;
    box-shadow:0 0 0 3px rgba(135,185,66,0.12);
}
.pm-field input:disabled { background:#f8fafc; color:#94a3b8; cursor:not-allowed; }
.pm-field input.pm-error { border-color:#e53935; box-shadow:0 0 0 3px rgba(229,57,53,0.1); }
.pm-footer {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 22px; border-top:1px solid #e2e8f0; background:#fafafa;
}
.pm-msg { font-size:13px; font-weight:500; }
.pm-submit {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 22px; background:#87b942; color:#fff;
    border:none; border-radius:8px; font-size:13px;
    font-weight:600; cursor:pointer;
    transition:background 0.15s, transform 0.1s;
}
.pm-submit:hover { background:#6e9735; }
.pm-submit:active { transform:scale(0.98); }
</style>

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
