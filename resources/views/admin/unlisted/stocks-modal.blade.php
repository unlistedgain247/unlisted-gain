@once
@push('styles')
<style>
    .stocks-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 16px;
        backdrop-filter: blur(2px);
    }

    .stocks-overlay.open {
        display: flex;
    }

    .stocks-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 520px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
        animation: privSlideIn 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }

    .stocks-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        flex-shrink: 0;
    }

    .stocks-modal-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .stocks-modal-title svg {
        color: #87b942;
    }

    .stocks-modal-close {
        background: #f1f5f9;
        border: none;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748b;
        transition: background 0.15s, color 0.15s;
    }

    .stocks-modal-close:hover {
        background: #e2e8f0;
        color: #1a1a1a;
    }

    .stocks-modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .stocks-input-row {
        display: flex;
        gap: 10px;
    }

    .stocks-input {
        flex: 1;
        padding: 9px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        color: #1a1a1a;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .stocks-input:focus {
        border-color: #87b942;
        box-shadow: 0 0 0 3px rgba(135, 185, 66, 0.12);
    }

    .stocks-input.stocks-input-error {
        border-color: #e53935;
        box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.1);
    }

    .stocks-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        background: #87b942;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s, transform 0.1s;
    }

    .stocks-add-btn:hover {
        background: #6e9735;
    }

    .stocks-add-btn:active {
        transform: scale(0.97);
    }

    .stocks-empty {
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        padding: 32px 0;
        margin: 0;
    }
    .stocks-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .stocks-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 8px 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .stocks-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.1s;
    }
    .stocks-table tbody tr:hover { background: #f8fafc; }
    .stocks-table tbody td {
        padding: 9px 12px;
        color: #334155;
        vertical-align: middle;
    }
    .stocks-table code {
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        color: #475569;
    }
</style>
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