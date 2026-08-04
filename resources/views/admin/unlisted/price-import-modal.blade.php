@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-price-import-modal.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-price-import-modal.css')) }}">
@endpush
@endonce

<div id="priceImportOverlay" class="pim-overlay" onclick="if(event.target===this)closePriceImportModal()">
    <div class="pim-modal">

        <div class="pim-header">
            <div class="pim-title">
                <i class="fa-solid fa-file-excel"></i> Bulk Upload Prices
            </div>
            <button class="pim-close" onclick="closePriceImportModal()" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <div class="pim-body">

            <div class="pim-hint">
                <strong>How this works</strong>
                <ul style="margin:6px 0 0; padding-left:18px;">
                    <li>File must be <strong>Excel (.xlsx)</strong>, header exactly <code>UL_PD_FINCODE,UL_PD_DATE,UL_PD_BID_PRICE</code> in the first row — one row per price point.</li>
                    <li><code>UL_PD_FINCODE</code> — the stock's Fincode (whole number, shown in the Fincode column below). <code>UL_PD_DATE</code> — a real Excel date, or typed as <code>YYYY-MM-DD HH:MM:SS</code>. <code>UL_PD_BID_PRICE</code> — plain number.</li>
                    <li>Nothing is saved until you click <strong>Preview</strong> and review the table — check it here first.</li>
                    <li>Rows with an unrecognised Fincode are shown but <strong>skipped automatically</strong> — they won't block the rest of the file.</li>
                    <li>If a row's Fincode + Date already has a price on file, this <strong>overwrites</strong> it. There's no separate "undo" — check the date/price before confirming.</li>
                    <li>Large files are uploaded in batches automatically — safe to upload thousands of rows in one file.</li>
                </ul>
                <a class="pim-sample-link" href="{{ asset('assets/samples/unlisted-price-import-sample.xlsx') }}" download>
                    <i class="fa-solid fa-download"></i> Download sample Excel file
                </a>
            </div>

            <div class="pim-upload-row">
                <input type="file" id="pimFileInput" class="pim-file-input" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                <button type="button" class="pim-btn" id="pimPreviewBtn">
                    <i class="fa-solid fa-magnifying-glass"></i> Preview
                </button>
            </div>

            <div id="pimMsg"></div>

            <div id="pimPreviewWrap" style="display:none; flex-direction:column; gap:14px;">
                <div class="pim-summary" id="pimSummary"></div>
                <div class="pim-errors" id="pimErrors" style="display:none;"></div>
                <div class="pim-table-wrap">
                    <table class="pim-table">
                        <thead>
                            <tr>
                                <th>Fincode</th>
                                <th>Company</th>
                                <th>Date</th>
                                <th style="text-align:right">Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="pimTableBody"></tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="pim-footer" id="pimFooter" style="display:none;">
            <button type="button" class="pim-btn pim-btn-secondary" id="pimBackBtn">Choose Different File</button>
            <button type="button" class="pim-btn" id="pimConfirmBtn">
                <i class="fa-solid fa-check"></i> Confirm &amp; Save
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(function () {

    const PREVIEW_URL = '{{ route("admin.unlisted.stocks.price-import.preview") }}';
    const EXECUTE_URL = '{{ route("admin.unlisted.stocks.price-import") }}';
    const CSRF        = $('meta[name="csrf-token"]').attr('content');
    let   pimRows      = [];

    $('#priceImportNavBtn').on('click', function () {
        $('#priceImportOverlay').addClass('open');
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closePriceImportModal();
    });

    $('#pimPreviewBtn').on('click', function () {
        const file = $('#pimFileInput')[0].files[0];
        if (!file) {
            showPimMsg('Please choose an Excel (.xlsx) file first.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('price_csv', file);

        const $btn = $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Reading…');

        $.ajax({
            url: PREVIEW_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .done(function (res) {
            if (!res.success) {
                showPimMsg(res.message || 'Could not read file.', 'error');
                $('#pimPreviewWrap').hide();
                $('#pimFooter').hide();
                return;
            }

            pimRows = res.rows;
            renderPimPreview(res.rows, res.errors, res.unknown_count);
        })
        .fail(function (xhr) {
            showPimMsg(xhr.responseJSON?.message || 'Upload failed.', 'error');
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-magnifying-glass"></i> Preview');
        });
    });

    function renderPimPreview(rows, errors, unknownCount) {
        $('#pimMsg').empty();

        let summaryHtml = '<span class="pim-pill ok">' + rows.length + ' row(s) parsed</span>';
        if (unknownCount > 0) {
            summaryHtml += '<span class="pim-pill warn">' + unknownCount + ' unknown FINCODE — will be skipped</span>';
        }
        if (errors && errors.length) {
            summaryHtml += '<span class="pim-pill bad">' + errors.length + ' row(s) with errors — ignored</span>';
        }
        $('#pimSummary').html(summaryHtml);

        if (errors && errors.length) {
            $('#pimErrors').html(errors.map(e => escapeHtml(e)).join('<br>')).show();
        } else {
            $('#pimErrors').hide().empty();
        }

        const $tbody = $('#pimTableBody').empty();
        rows.forEach(function (row) {
            const badge = row.known
                ? '<span class="pim-badge ok">Matched</span>'
                : '<span class="pim-badge warn">Unknown fincode</span>';
            const $tr = $('<tr>' +
                '<td>' + row.UL_PD_FINCODE + '</td>' +
                '<td>' + (row.company ? escapeHtml(row.company) : '<span style="color:#cbd5e1">—</span>') + '</td>' +
                '<td>' + row.UL_PD_DATE + '</td>' +
                '<td class="pim-num">' + Number(row.UL_PD_BID_PRICE).toFixed(2) + '</td>' +
                '<td>' + badge + '</td>' +
                '</tr>');
            if (!row.known) $tr.addClass('pim-row-unknown');
            $tbody.append($tr);
        });

        $('#pimPreviewWrap').css('display', 'flex');
        $('#pimFooter').toggle(rows.length > 0);
    }

    $('#pimBackBtn').on('click', function () {
        $('#pimPreviewWrap').hide();
        $('#pimFooter').hide();
        $('#pimFileInput').val('');
        $('#pimMsg').empty();
        pimRows = [];
    });

    $('#pimConfirmBtn').on('click', function () {
        const knownRows = pimRows.filter(r => r.known);

        if (knownRows.length === 0) {
            showPimMsg('No rows with a matching FINCODE to save.', 'error');
            return;
        }

        if (!confirm('Save prices for ' + knownRows.length + ' row(s)? This will overwrite any existing price for the same FINCODE + date.')) {
            return;
        }

        const $btn = $(this).prop('disabled', true);
        $('#pimBackBtn').prop('disabled', true);

        // Submitted in batches of 150 rows (450 POST fields) rather than one
        // giant array — PHP's default max_input_vars (1000) silently
        // truncates larger multi-dimensional POST arrays, which otherwise
        // corrupts the request before validation even runs.
        const BATCH_SIZE = 150;
        const batches = [];
        for (let i = 0; i < knownRows.length; i += BATCH_SIZE) {
            batches.push(knownRows.slice(i, i + BATCH_SIZE));
        }

        let totalUpdated = 0;
        let totalSkipped = 0;
        let batchIndex = 0;

        function runNextBatch() {
            if (batchIndex >= batches.length) {
                showPimMsg(totalUpdated + ' price row(s) saved' + (totalSkipped ? ', ' + totalSkipped + ' skipped (unknown FINCODE)' : '') + '.', 'success');
                $('#pimFooter').hide();
                $('#pimPreviewWrap').hide();
                $('#pimFileInput').val('');
                pimRows = [];
                setTimeout(function () { window.location.reload(); }, 1400);
                return;
            }

            $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Saving ' + (batchIndex + 1) + ' / ' + batches.length + '…');

            $.ajax({
                url: EXECUTE_URL,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: { rows: batches[batchIndex] },
            })
            .done(function (res) {
                totalUpdated += res.updated || 0;
                totalSkipped += res.skipped || 0;
                batchIndex++;
                runNextBatch();
            })
            .fail(function (xhr) {
                showPimMsg(
                    (xhr.responseJSON?.message || 'Save failed.') +
                    ' (' + totalUpdated + ' row(s) already saved before this failure.)',
                    'error'
                );
                $btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Confirm &amp; Save');
                $('#pimBackBtn').prop('disabled', false);
            });
        }

        runNextBatch();
    });

    function showPimMsg(text, type) {
        $('#pimMsg').html('<p class="pim-msg ' + type + '">' + escapeHtml(text) + '</p>');
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }
});

function closePriceImportModal() {
    $('#priceImportOverlay').removeClass('open');
}
</script>
@endpush
