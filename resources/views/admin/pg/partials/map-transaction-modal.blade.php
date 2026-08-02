@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pg-map-transaction-modal.css') }}?v={{ filemtime(public_path('assets/css/admin/pg-map-transaction-modal.css')) }}">
@endpush
@endonce

{{-- ══ Transaction Mapping Modal ══════════════════════════════════════════ --}}
<div class="pgd-overlay" id="pgdMappingOverlay" style="align-items:center;">
<div class="pgd-modal" style="max-width:480px;width:100%;">
    <div class="pgd-modal-hdr">
        <h5>Add Transaction Mapping</h5>
        <button class="pgd-modal-close" onclick="closeOverlay('pgdMappingOverlay')">&times;</button>
    </div>
    <div class="pgd-modal-body">
        {{-- Transaction summary row --}}
        <table class="table table-sm table-bordered mb-3" style="font-size:12px;">
            <thead><tr style="background:#fdebeb;">
                <th>TID</th><th>Amount</th><th>Ref ID</th>
            </tr></thead>
            <tbody>
            <tr>
                <td id="pgdMapTidCell" style="font-weight:600;"></td>
                <td id="pgdMapAmtCell"></td>
                <td id="pgdMapRefCell" style="word-break:break-all;"></td>
            </tr>
            </tbody>
        </table>
        {{-- User search --}}
        <div class="mb-3">
            <label class="form-label" style="font-size:12px;font-weight:600;">Customer Name / UID <span class="text-danger">*</span></label>
            <div style="position:relative;">
                <input type="text" class="form-control form-control-sm" id="pgdMapUserSearch" placeholder="Type name or UID…" autocomplete="off">
                <input type="hidden" id="pgdMapUserId">
                <div id="pgdMapUserDropdown" style="position:absolute;z-index:9999;width:100%;background:#fff;border:1px solid #ddd;border-radius:4px;display:none;max-height:160px;overflow-y:auto;font-size:12px;"></div>
            </div>
        </div>
        <input type="hidden" id="pgdMapTid">
        <div id="pgdMapMsg" class="mb-2" style="font-size:12px;display:none;"></div>
        <div class="text-end">
            <button type="button" class="btn btn-primary btn-sm" onclick="saveMappingTransaction()">
                <span id="pgdMapSpinner" class="spinner-border spinner-border-sm d-none"></span> Save
            </button>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
if (typeof CSRF_TOKEN === 'undefined') {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
}

function closeOverlay(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

document.querySelectorAll('.pgd-overlay').forEach(function(el) {
    el.addEventListener('click', function(e) { if (e.target === el) el.classList.remove('open'); });
});

function pgdMakeDropdown(inputId, dropdownId, hiddenId, searchUrl, labelField, valueField, minLength) {
    minLength = minLength || 2;
    var timer;
    $('#'+inputId).on('input', function() {
        var q = $(this).val().trim();
        if (q.length < minLength) { $('#'+dropdownId).hide(); return; }
        clearTimeout(timer);
        timer = setTimeout(function() {
            $.getJSON(searchUrl, { q: q }, function(rows) {
                var dd = $('#'+dropdownId).empty();
                if (!rows.length) { dd.hide(); return; }
                rows.forEach(function(r) {
                    $('<div>').text(r[labelField]).css({padding:'6px 10px',cursor:'pointer'})
                        .hover(function(){$(this).css('background','#f0f4ff')},function(){$(this).css('background','#fff')})
                        .on('click', function() {
                            $('#'+inputId).val(r[labelField]);
                            $('#'+hiddenId).val(r[valueField]);
                            dd.hide();
                        }).appendTo(dd);
                });
                dd.show();
            });
        }, 250);
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#'+inputId+',#'+dropdownId).length) $('#'+dropdownId).hide();
    });
}

var PGD_MAP_TXN_URL      = '{{ url("/admin/pg/dashboard/map-transaction") }}';
var PGD_SEARCH_USERS_URL = '{{ url("/admin/pg/search-users") }}';

pgdMakeDropdown('pgdMapUserSearch','pgdMapUserDropdown','pgdMapUserId', PGD_SEARCH_USERS_URL, 'label', 'uid', 1);

function openMappingModal(tid, amount, refNo, txnType) {
    $('#pgdMapTid').val(tid);
    $('#pgdMapUserId').val('');
    $('#pgdMapUserSearch').val('');
    $('#pgdMapUserDropdown').hide();
    $('#pgdMapMsg').hide().text('');
    var typeIcon = txnType === 'Flow In'
        ? '<i class="fa fa-arrow-down" style="color:green"></i>'
        : '<i class="fa fa-arrow-up" style="color:red"></i>';
    $('#pgdMapTidCell').html(tid + ' ' + typeIcon);
    $('#pgdMapAmtCell').text(amount);
    $('#pgdMapRefCell').text(refNo || '—');
    document.getElementById('pgdMappingOverlay').classList.add('open');
}

function saveMappingTransaction() {
    var tid    = $('#pgdMapTid').val();
    var userId = $('#pgdMapUserId').val();
    if (!userId || userId == '0') { alert('Please select a customer'); return; }
    if (!confirm('Map transaction #' + tid + ' to this user?')) return;
    $('#pgdMapSpinner').removeClass('d-none');
    $.ajax({
        type: 'POST', url: PGD_MAP_TXN_URL,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        data: { pgt_tid: tid, map_user_id: userId },
        dataType: 'json',
        success: function(r) {
            $('#pgdMapSpinner').addClass('d-none');
            var msg = $('#pgdMapMsg').show();
            if (r.success) {
                msg.css('color','green').text(r.message);
                setTimeout(function(){ closeOverlay('pgdMappingOverlay'); location.reload(); }, 1200);
            } else { msg.css('color','red').text(r.message); }
        },
        error: function() { $('#pgdMapSpinner').addClass('d-none'); alert('Server error'); }
    });
}
</script>
@endpush
