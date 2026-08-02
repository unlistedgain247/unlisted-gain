@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-stocks-modal.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-stocks-modal.css')) }}">
@endpush
@endonce

<div id="stocksOverlay" class="stocks-overlay" onclick="if(event.target===this)closeStocksModal()">
    <div class="stocks-modal">

        <div class="stocks-modal-header">
            <div class="stocks-modal-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                Stocks
            </div>
            <button class="stocks-modal-close" onclick="closeStocksModal()" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <div class="stocks-modal-body">

            <div class="stocks-input-row">
                <input type="text" id="stockInput" class="stocks-input"
                       placeholder="Enter company name e.g. Tata Motors">
                <button type="button" class="stocks-add-btn" id="stockAddBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add
                </button>
            </div>

            <div id="stocksMsg"></div>

        </div>

    </div>
</div>

@push('scripts')
<script>
$(function () {

    const STORE_URL = '{{ route("admin.unlisted.stocks.store") }}';
    const CSRF      = $('meta[name="csrf-token"]').attr('content');
    let   tableInit = false;

    // ── Open ──────────────────────────────────────────────
    $('#stocksNavBtn').on('click', function () {
        $('#stocksOverlay').addClass('open');
        $('#stockInput').focus();
    });

    // ── Close ─────────────────────────────────────────────
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeStocksModal();
    });

    // ── Add ───────────────────────────────────────────────
    $('#stockAddBtn').on('click', addStock);
    $('#stockInput').on('keydown', function (e) {
        if (e.key === 'Enter') addStock();
    });
    $('#stockInput').on('input', function () {
        $(this).removeClass('stocks-input-error');
        $('#stocksMsg').html('');
    });

    function addStock() {
        const name = $('#stockInput').val().trim();

        if (!name) {
            $('#stockInput').addClass('stocks-input-error').focus();
            return;
        }

        const $btn = $('#stockAddBtn').prop('disabled', true).text('Adding…');

        $.ajax({
            url:         STORE_URL,
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            data:        JSON.stringify({ name }),
        })
        .done(function (res) {
            if (res.success) {
                showMsg('<i class="fa-solid fa-circle-check"></i> ' + res.message + ' Redirecting…', 'success');
                setTimeout(function () { window.location.reload(); }, 1200);
            } else {
                showMsg(res.message || 'Something went wrong.', 'error');
            }
        })
        .fail(function (xhr) {
            const msg = xhr.responseJSON?.errors?.name?.[0]
                      || xhr.responseJSON?.message
                      || 'Request failed.';
            showMsg(msg, 'error');
        })
        .always(function () {
            $btn.prop('disabled', false).html(
                '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add'
            );
        });
    }

    function showMsg(text, type) {
        const color = type === 'success' ? '#4a7c20' : '#e53935';
        $('#stocksMsg').html(
            '<p style="margin:0;font-size:13px;font-weight:500;color:' + color + '">' +
            text +
            '</p>'
        );
    }

});

function closeStocksModal() {
    $('#stocksOverlay').removeClass('open');
}
</script>
@endpush