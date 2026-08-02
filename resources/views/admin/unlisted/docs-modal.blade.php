@once
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-docs-modal.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-docs-modal.css')) }}">
@endpush
@endonce

<div id="docsOverlay" class="docs-overlay" onclick="if(event.target===this)closeDocsModal()">
    <div class="docs-modal">

        <div class="docs-modal-header">
            <div class="docs-modal-title" id="docsModalTitle">Add Document</div>
            <button class="docs-modal-close" type="button" onclick="closeDocsModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="docsForm">
            <div class="docs-modal-body">

                <div class="docs-summary-error" id="docsSummaryError"></div>

                <input type="hidden" name="id" id="docId">

                <div class="docs-row">
                    <div class="docs-field" id="fld_fincode">
                        <label>Company <span class="req">*</span></label>
                        <select name="fincode" id="docFincode" required>
                            <option value="">Select company…</option>
                            @foreach($stocks as $s)
                            <option value="{{ $s->UL_STOCKS_FINCODE }}">{{ $s->UL_STOCKS_COMPNAME }}</option>
                            @endforeach
                        </select>
                        <div class="docs-field-error"></div>
                    </div>
                    <div class="docs-field" id="fld_type">
                        <label>Doc Type <span class="req">*</span></label>
                        <select name="type" id="docType" required>
                            <option value="">Select type…</option>
                            @foreach(\App\Models\UnlistedDocument::DOC_TYPES as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <div class="docs-field-error"></div>
                    </div>
                </div>

                <div class="docs-row" id="researchHouseRow" style="display:none;">
                    <div class="docs-field" id="fld_research_house">
                        <label>Research House <span class="req">*</span></label>
                        <select name="research_house" id="docResearchHouse">
                            <option value="">Select research house…</option>
                            @foreach(\App\Models\UnlistedDocument::RESEARCH_HOUSES as $house)
                            <option value="{{ $house }}">{{ $house }}</option>
                            @endforeach
                        </select>
                        <div class="docs-field-error"></div>
                    </div>
                </div>

                <div class="docs-row">
                    <div class="docs-field" id="fld_date">
                        <label>Date</label>
                        <input type="date" name="date" id="docDate">
                        <div class="docs-field-error"></div>
                    </div>
                    <div class="docs-field" id="fld_period_mm">
                        <label>Period (Quarter)</label>
                        <select name="period_mm" id="docPeriodMm">
                            <option value="">—</option>
                            <option value="03">Q4 (Mar)</option>
                            <option value="06">Q1 (Jun)</option>
                            <option value="09">Q2 (Sep)</option>
                            <option value="12">Q3 (Dec)</option>
                        </select>
                        <div class="docs-field-error"></div>
                    </div>
                    <div class="docs-field" id="fld_period_yy">
                        <label>Period Year</label>
                        <select name="period_yy" id="docPeriodYy">
                            <option value="">—</option>
                            @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                        <div class="docs-field-error"></div>
                    </div>
                </div>

                <div class="docs-field" id="fld_description">
                    <label>Description</label>
                    <textarea name="description" id="docDescription" rows="2" maxlength="500"></textarea>
                    <div class="docs-field-error"></div>
                </div>

                <div class="docs-field">
                    <label>Source <span class="req">*</span></label>
                    <div class="docs-radio-row">
                        <label><input type="radio" name="upload_type" value="file" id="docUploadFile" checked> Upload File</label>
                        <label><input type="radio" name="upload_type" value="link" id="docUploadLink"> Upload Link</label>
                    </div>
                </div>

                <div class="docs-row" id="fileRow">
                    <div class="docs-field" id="fld_file" style="flex:1;">
                        <label>Document File</label>
                        <input type="file" name="file" id="docFile" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="docs-field-error"></div>
                        <div id="docCurrentFile" style="font-size:11px;color:#6b7280;"></div>
                    </div>
                </div>

                <div class="docs-row" id="linkRow" style="display:none;">
                    <div class="docs-field" id="fld_link" style="flex:1;">
                        <label>Document Link</label>
                        <input type="text" name="link" id="docLink" placeholder="https://…">
                        <div class="docs-field-error"></div>
                    </div>
                </div>

                <div class="docs-field" id="fld_status">
                    <label>Status <span class="req">*</span></label>
                    <select name="status" id="docStatus" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <div class="docs-field-error"></div>
                </div>

            </div>

            <div class="docs-modal-footer">
                <span id="docsSaveMsg" style="font-size:12px;font-weight:500;"></span>
                <button type="submit" class="docs-save-btn" id="docsSaveBtn">Save Document</button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
$(function () {

    var CSRF = $('meta[name="csrf-token"]').attr('content');

    // Searchable company dropdown — plain <select> works fine at ~20 companies,
    // but Select2 keeps it usable as the list grows.
    $('#docFincode').select2({
        dropdownParent: $('.docs-modal'),
        placeholder: 'Select company…',
        width: '100%',
    });

    function resetDocsForm() {
        $('#docsForm')[0].reset();
        $('#docId').val('');
        $('#docFincode').val('').trigger('change');
        $('.docs-field').removeClass('has-error');
        $('.docs-field-error').text('');
        $('#docsSummaryError').hide().text('');
        $('#docsSaveMsg').text('');
        $('#researchHouseRow').hide();
        $('#fileRow').show();
        $('#linkRow').hide();
        $('#docCurrentFile').text('');
    }

    // ── Open: Add ──────────────────────────────────────────
    $('#addDocBtn').on('click', function () {
        resetDocsForm();
        $('#docsModalTitle').text('Add Document');
        $('#docsOverlay').addClass('open');
    });

    // ── Open: Edit (row data is already embedded as JSON, no extra request needed) ──
    $(document).on('click', '.doc-edit-btn', function () {
        var doc = $(this).data('doc');
        resetDocsForm();
        $('#docsModalTitle').text('Edit Document');
        $('#docId').val(doc.UL_DOC_ID);
        $('#docFincode').val(doc.UL_DOC_FINCODE).trigger('change');
        $('#docType').val(doc.UL_DOC_TYPE).trigger('change');
        $('#docResearchHouse').val(doc.UL_DOC_RESEARCH_HOUSE || '');
        $('#docDate').val(doc.UL_DOC_DATE ? doc.UL_DOC_DATE.substring(0, 10) : '');
        $('#docPeriodMm').val(doc.UL_DOC_PERIOD_MM || '');
        $('#docPeriodYy').val(doc.UL_DOC_PERIOD_YY || '');
        $('#docDescription').val(doc.UL_DOC_DESCRIPTION || '');
        $('#docStatus').val(doc.UL_DOC_STATUS);

        if (doc.UL_DOC_FILE_PATH) {
            $('#docUploadFile').prop('checked', true).trigger('change');
            $('#docCurrentFile').html('Current: <a href="' + window.location.origin + '/' + doc.UL_DOC_FILE_PATH + '" target="_blank">view file</a> (choose a new file only to replace it)');
        } else {
            $('#docUploadLink').prop('checked', true).trigger('change');
            $('#docLink').val(doc.UL_DOC_FILELINK || '');
        }

        $('#docsOverlay').addClass('open');
    });

    // ── Close ──────────────────────────────────────────────
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeDocsModal();
    });

    // ── Type → toggle Research House ──────────────────────
    $('#docType').on('change', function () {
        var isResearch = $(this).val() === 'Research Report';
        $('#researchHouseRow').toggle(isResearch);
        if (!isResearch) $('#docResearchHouse').val('');
    });

    // ── Upload type → toggle File / Link rows ─────────────
    $('input[name="upload_type"]').on('change', function () {
        var isFile = $(this).val() === 'file' && $(this).is(':checked');
        if ($('input[name="upload_type"]:checked').val() === 'file') {
            $('#fileRow').show();
            $('#linkRow').hide();
        } else {
            $('#fileRow').hide();
            $('#linkRow').show();
        }
    });

    // ── Clear field error on change ───────────────────────
    $('#docsForm').on('input change', 'input, select, textarea', function () {
        $(this).closest('.docs-field').removeClass('has-error').find('.docs-field-error').text('');
        $('#docsSummaryError').hide().text('');
    });

    // ── Submit ─────────────────────────────────────────────
    $('#docsForm').on('submit', function (e) {
        e.preventDefault();

        $('.docs-field').removeClass('has-error');
        $('.docs-field-error').text('');
        $('#docsSummaryError').hide().text('');

        var id     = $('#docId').val();
        var isEdit = !!id;
        var url    = isEdit ? (window.DOCS_BASE + '/' + id) : window.DOCS_BASE;

        var fd = new FormData(this);
        if (isEdit) fd.append('_method', 'PUT');

        var $btn = $('#docsSaveBtn').prop('disabled', true).text('Saving…');
        $('#docsSaveMsg').text('');

        $.ajax({
            url:         url,
            method:      'POST',
            data:        fd,
            processData: false,
            contentType: false,
            headers:     { 'X-CSRF-TOKEN': CSRF },
        })
        .done(function (res) {
            if (res.success) {
                $('#docsSaveMsg').css('color', '#4a7c20').text(res.message || 'Saved.');
                setTimeout(function () {
                    closeDocsModal();
                    if (typeof loadDocsPage === 'function') loadDocsPage(currentDocsPage || 1);
                }, 500);
            } else {
                showSummaryError(res.message || 'Something went wrong.');
            }
        })
        .fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                var errors  = xhr.responseJSON.errors;
                var firstEl = null;

                $.each(errors, function (field, messages) {
                    var $field = $('#fld_' + field);
                    if ($field.length) {
                        $field.addClass('has-error').find('.docs-field-error').text(messages[0]);
                        if (!firstEl) firstEl = $field;
                    }
                });

                showSummaryError('Please fix the highlighted field(s) below.');
                if (firstEl) firstEl[0].scrollIntoView({ block: 'center', behavior: 'smooth' });
            } else {
                showSummaryError(xhr.responseJSON?.message || 'Request failed. Please try again.');
            }
        })
        .always(function () {
            $btn.prop('disabled', false).text('Save Document');
        });
    });

    function showSummaryError(text) {
        $('#docsSummaryError').text(text).show();
    }

});

function closeDocsModal() {
    $('#docsOverlay').removeClass('open');
}
</script>
@endpush
