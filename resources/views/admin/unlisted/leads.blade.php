@extends('layout.admin')

@section('title', 'Leads | Admin | UnlistedGain')

@push('styles')
<style>
    .leads-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }
    .leads-filters .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .leads-filters label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #999;
    }
    .leads-filters input, .leads-filters select {
        padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 6px;
        font-size: 12px; color: #333; background: #fff; height: 34px; min-width: 140px;
    }
    .leads-filters input:focus, .leads-filters select:focus { outline: none; border-color: #87b942; }
    .leads-filters .filter-btn {
        padding: 0 18px; height: 34px; border-radius: 6px;
        border: none; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    .lead-tag {
        display: inline-block; padding: 1px 8px; border-radius: 10px;
        font-size: 10px; font-weight: 600; margin-right: 3px;
    }
    .lead-alloc-select {
        width: 100%; padding: 5px 8px; border: 1px solid #e0e0e0; border-radius: 6px;
        font-size: 12px; color: #333; background: #fff; cursor: pointer;
    }
    .lead-alloc-select:focus { outline: none; border-color: #87b942; }
    .disp-badge {
        display: inline-block; padding: 2px 10px; border-radius: 12px;
        font-size: 11px; font-weight: 600;
    }
    .disp-edit-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 22px; height: 22px; border: none; background: none; color: #bbb;
        cursor: pointer; border-radius: 4px; padding: 0; margin-left: 4px;
        vertical-align: middle; transition: color 0.15s, background 0.15s;
    }
    .disp-edit-btn:hover { color: #87b942; background: #f0f8e8; }
    #leadsTableWrap { min-height: 200px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .leads-loading { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }
    .leads-pagination {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 20px; border-top: 1px solid #f0f0f0; font-size: 12px; color: #888;
    }
    .leads-pagination .pg-btns { display: flex; gap: 6px; }
    .leads-pagination .pg-btn {
        padding: 4px 12px; border: 1px solid #e0e0e0; border-radius: 5px;
        background: #fff; font-size: 12px; cursor: pointer; color: #555;
    }
    .leads-pagination .pg-btn:hover { background: #f0f8e8; border-color: #87b942; color: #4a7c20; }
    .leads-pagination .pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .leads-pagination .pg-btn.active { background: #87b942; border-color: #87b942; color: #fff; }

    /* Disposition Modal */
    #dispModalOverlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 9000;
        align-items: center; justify-content: center;
    }
    #dispModalOverlay.open { display: flex; }
    #dispModal {
        background: #fff; border-radius: 12px; width: 100%; max-width: 560px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.18); overflow: hidden;
    }
    #dispModal .dm-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 22px; border-bottom: 1px solid #f0f0f0;
    }
    #dispModal .dm-title { font-size: 15px; font-weight: 700; color: #1a1a1a; }
    #dispModal .dm-close { background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; padding: 0 4px; }
    #dispModal .dm-close:hover { color: #333; }
    #dispModal .dm-body { padding: 22px; }
    #dispModal .dm-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    #dispModal .dm-row.full { grid-template-columns: 1fr; }
    #dispModal .dm-field label {
        display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.05em; color: #999; margin-bottom: 5px;
    }
    #dispModal .dm-field select,
    #dispModal .dm-field input,
    #dispModal .dm-field textarea {
        width: 100%; padding: 8px 10px; border: 1px solid #e0e0e0;
        border-radius: 7px; font-size: 13px; color: #333; background: #fff; box-sizing: border-box;
    }
    #dispModal .dm-field select:focus,
    #dispModal .dm-field input:focus,
    #dispModal .dm-field textarea:focus { outline: none; border-color: #87b942; }
    #dispModal .dm-field textarea { resize: vertical; min-height: 70px; }
    #dispModal .cb-row { display: flex; gap: 8px; align-items: center; }
    #dispModal .cb-row input[type="date"] { flex: 1; }
    #dispModal .cb-row select { width: 80px; flex-shrink: 0; }
    #dispModal .dm-footer {
        padding: 14px 22px; border-top: 1px solid #f0f0f0;
        display: flex; justify-content: flex-end; gap: 10px;
    }
    #dispModal .dm-footer button {
        padding: 8px 22px; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; border: none;
    }
    #dmSubmitBtn { background: #87b942; color: #fff; }
    #dmSubmitBtn:hover { background: #6fa030; }
    #dmCancelBtn { background: #f5f5f5; color: #555; border: 1px solid #e0e0e0 !important; }

    @media (max-width: 640px) {
        .leads-filters { padding: 12px; gap: 8px; }
        .leads-filters .filter-group { width: 100%; }
        .leads-filters input, .leads-filters select { min-width: unset; width: 100%; box-sizing: border-box; }
        .leads-filters .filter-btn { flex: 1; }
        #dispModal { margin: 12px; max-width: calc(100vw - 24px); }
        #dispModal .dm-row { grid-template-columns: 1fr; gap: 12px; }
        #dispModal .dm-body { padding: 16px; }
        #dispModal .dm-footer { padding: 12px 16px; }
        #dispModal .cb-row { flex-wrap: wrap; }
        #dispModal .cb-row input[type="date"] { width: 100%; }
        #dispModal .cb-row select { width: calc(50% - 4px); }
    }
</style>
@endpush

@section('content')

@include('partials.admin-unlisted-subnav')

<div class="admin-main">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h1 class="admin-page-title" style="margin:0;">
            Leads
            <span id="leadsCount" style="font-size:14px;font-weight:400;color:#aaa;margin-left:8px;"></span>
        </h1>
    </div>

    <div class="admin-card" style="padding:0;">

        {{-- Filter Bar --}}
        <div class="leads-filters">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="fSearch" placeholder="Name / Email / Phone / UID" style="min-width:210px;">
            </div>
            <div class="filter-group">
                <label>Disposition</label>
                <select id="fDisposition">
                    <option value="">All</option>
                    <option value="Fresh">Fresh</option>
                    <option value="Interested">Interested</option>
                    <option value="Working">Working</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Sale Closed">Sale Closed</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Callback</label>
                <select id="fCallback">
                    <option value="">All</option>
                    <option value="today">Today</option>
                    <option value="overdue">Overdue</option>
                    <option value="tomorrow">Tomorrow</option>
                    <option value="upcoming">Upcoming</option>
                </select>
            </div>
            @if($canAllocate)
            <div class="filter-group">
                <label>Allocated To</label>
                <select id="fAllocated">
                    <option value="">Anyone</option>
                    <option value="unallocated">— Unallocated —</option>
                    @foreach($leadAgents as $agent)
                        <option value="{{ $agent->uid }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="filter-group">
                <label>Req. Call</label>
                <select id="fReqCall">
                    <option value="">All</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px;">
                    <button class="filter-btn" onclick="loadLeads(1)" style="background:#87b942;color:#fff;">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                    <button class="filter-btn" onclick="resetFilters()" style="background:#f5f5f5;color:#555;border:1px solid #e0e0e0;">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Table (Blade partial injected here) --}}
        <div id="leadsTableWrap">
            <div class="leads-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>

    </div>
</div>

{{-- Activity Drawer --}}
<div id="activityDrawerOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:8000;" onclick="closeActivityDrawer()"></div>
<div id="activityDrawer" style="position:fixed;top:0;right:-420px;width:420px;max-width:100vw;height:100vh;background:#fff;z-index:8001;box-shadow:-4px 0 24px rgba(0,0,0,0.12);display:flex;flex-direction:column;transition:right 0.25s ease;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f0f0f0;flex-shrink:0;">
        <div>
            <div style="font-size:14px;font-weight:700;color:#111827;">Activity Log</div>
            <div id="activityDrawerSub" style="font-size:11px;color:#9ca3af;margin-top:2px;"></div>
        </div>
        <button onclick="closeActivityDrawer()" style="background:none;border:none;font-size:20px;color:#aaa;cursor:pointer;padding:0 4px;line-height:1;">&times;</button>
    </div>
    <div id="activityDrawerBody" style="flex:1;overflow-y:auto;">
        <div style="text-align:center;padding:40px;color:#aaa;"><i class="fa-solid fa-spinner fa-spin"></i></div>
    </div>
</div>

{{-- Disposition Modal --}}
<div id="dispModalOverlay">
    <div id="dispModal">
        <div class="dm-header">
            <div class="dm-title" id="dmTitle">Edit Disposition</div>
            <button class="dm-close" id="dmClose">&times;</button>
        </div>
        <div class="dm-body">
            <div class="dm-row">
                <div class="dm-field">
                    <label>Disposition</label>
                    <select id="dmDisposition">
                        <option value="">— Select —</option>
                        <option value="Interested">Interested</option>
                        <option value="Working">Working</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Sale Closed">Sale Closed</option>
                    </select>
                </div>
                <div class="dm-field">
                    <label>Sub Disposition</label>
                    <select id="dmSubDisposition">
                        <option value="">— Select —</option>
                    </select>
                </div>
            </div>
            <div class="dm-row full">
                <div class="dm-field">
                    <label>Callback Time</label>
                    <div class="cb-row">
                        <input type="date" id="dmCbDate">
                        <select id="dmCbHr"><option value="">Hr</option></select>
                        <select id="dmCbMin"><option value="">Min</option></select>
                    </div>
                </div>
            </div>
            <div class="dm-row full">
                <div class="dm-field">
                    <label>Comment</label>
                    <textarea id="dmComment" placeholder="Add a comment..."></textarea>
                </div>
            </div>
        </div>
        <div class="dm-footer">
            <button id="dmCancelBtn">Cancel</button>
            <button id="dmSubmitBtn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var LEADS_DATA_URL = '{{ url("/admin/unlisted/leads/data") }}';
var ALLOCATE_URL   = '{{ url("/admin/unlisted/leads") }}';
var currentPage    = 1;
var dmLeadId       = null;

var SUB_DISP_OPTIONS = {
    'Interested':  ['Prospect', 'Interested Might Convert', 'Slight Interest Shown'],
    'Working':     ['NC1', 'NC2', 'NC3', 'NC4', 'NC5', 'Busy', 'Call Back Later'],
    'Rejected':    ['Never Contacted', 'Not Interested', 'Wrong Party Contact', 'Not Convinced', 'Test Lead'],
    'Sale Closed': ['Order Placed', 'Regular Customer'],
};

// Populate Hr / Min dropdowns
(function () {
    var hrSel = document.getElementById('dmCbHr');
    var minSel = document.getElementById('dmCbMin');
    for (var h = 0; h <= 23; h++) {
        var o = document.createElement('option');
        o.value = h; o.text = (h < 10 ? '0' : '') + h;
        hrSel.appendChild(o);
    }
    for (var m = 0; m <= 55; m += 5) {
        var o2 = document.createElement('option');
        o2.value = m; o2.text = (m < 10 ? '0' : '') + m;
        minSel.appendChild(o2);
    }
})();

function getFilters() {
    return {
        searchtext:       $('#fSearch').val().trim(),
        disposition:      $('#fDisposition').val(),
        callback:         $('#fCallback').val(),
        allocated_to:     $('#fAllocated').val() || '',
        request_for_call: $('#fReqCall').val(),
        page:             currentPage,
    };
}

function resetFilters() {
    $('#fSearch, #fAllocated, #fReqCall').val('');
    $('#fDisposition, #fCallback').val('');
    loadLeads(1);
}

function loadLeads(page) {
    currentPage = page || 1;
    $('#leadsTableWrap').html('<div class="leads-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');

    $.get(LEADS_DATA_URL, getFilters())
        .done(function (html) {
            $('#leadsTableWrap').html(html);
            var total = $('#leadsTableWrap #leadsMeta').data('total');
            if (total !== undefined) $('#leadsCount').text(total + ' total');
        })
        .fail(function () {
            $('#leadsTableWrap').html('<div class="leads-loading" style="color:#e53935;">Failed to load leads.</div>');
        });
}

// ── Disposition Modal ──────────────────────────────────────

function updateSubDispOptions(disp, currentSub) {
    var $sub = $('#dmSubDisposition');
    $sub.html('<option value="">— Select —</option>');
    $.each(SUB_DISP_OPTIONS[disp] || [], function (i, opt) {
        $sub.append('<option value="' + opt + '"' + (opt === currentSub ? ' selected' : '') + '>' + opt + '</option>');
    });
}

$(document).on('click', '.open-disp-modal', function () {
    var lead = $(this).data('lead');
    dmLeadId = lead.UL_LEAD_ID;

    $('#dmTitle').text('Edit Disposition — LID: ' + lead.UL_LEAD_ID + ' / Name: ' + (lead.user_name || lead.UL_LEAD_UID));
    $('#dmDisposition').val(lead.UL_LEAD_DISPOSITION || '');
    updateSubDispOptions(lead.UL_LEAD_DISPOSITION || '', lead.UL_LEAD_SUB_DISPOSITION || '');
    $('#dmComment').val(lead.UL_LEAD_DISPOSITION_COMMENT || '');

    var cb = lead.UL_LEAD_CALLBACK_TIME;
    if (cb && cb !== '0000-00-00 00:00:00') {
        var parts = cb.split(' ');
        $('#dmCbDate').val(parts[0]);
        if (parts[1]) {
            var t = parts[1].split(':');
            $('#dmCbHr').val(parseInt(t[0], 10));
            var rMin = Math.round(parseInt(t[1], 10) / 5) * 5;
            $('#dmCbMin').val(rMin === 60 ? 55 : rMin);
        }
    } else {
        $('#dmCbDate, #dmCbHr, #dmCbMin').val('');
    }

    $('#dispModalOverlay').addClass('open');
});

$('#dmDisposition').on('change', function () {
    updateSubDispOptions($(this).val(), '');
});

function closeDispModal() {
    $('#dispModalOverlay').removeClass('open');
    dmLeadId = null;
}

$('#dmClose, #dmCancelBtn').on('click', closeDispModal);
$('#dispModalOverlay').on('click', function (e) {
    if ($(e.target).is('#dispModalOverlay')) closeDispModal();
});

$('#dmSubmitBtn').on('click', function () {
    if (!dmLeadId) return;

    var cbDate = $('#dmCbDate').val();
    var cbHr   = $('#dmCbHr').val();
    var cbMin  = $('#dmCbMin').val();
    var cbTime = '';
    if (cbDate && cbHr !== '' && cbMin !== '') {
        cbTime = cbDate + ' ' + ('0' + cbHr).slice(-2) + ':' + ('0' + cbMin).slice(-2) + ':00';
    } else if (cbDate) {
        cbTime = cbDate + ' 00:00:00';
    }

    $('#dmSubmitBtn').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

    $.ajax({
        url:     ALLOCATE_URL + '/' + dmLeadId + '/disposition',
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            disposition:     $('#dmDisposition').val(),
            sub_disposition: $('#dmSubDisposition').val(),
            callback_time:   cbTime,
            comment:         $('#dmComment').val().trim(),
        },
    })
    .done(function (res) {
        if (res.success) {
            closeDispModal();
            loadLeads(currentPage);
            if (currentActivityLeadId == dmLeadId) {
                loadActivityDrawerContent(dmLeadId);
            }
        } else {
            alert(res.message || 'Failed to save.');
        }
    })
    .fail(function () { alert('Failed to save disposition.'); })
    .always(function () {
        $('#dmSubmitBtn').prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save');
    });
});

// ── Allocation change ──────────────────────────────────────
$(document).on('change', '.lead-alloc-select', function () {
    var $sel      = $(this);
    var leadId    = $sel.data('lead-id');
    var agentUid  = $sel.val();
    var agentName = $sel.find('option:selected').text();

    $sel.prop('disabled', true);

    $.ajax({
        url:     ALLOCATE_URL + '/' + leadId + '/allocate',
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data:    { allocated_to: agentUid },
    })
    .done(function (res) {
        if (res.success) {
            var $lbl = $sel.closest('td').find('.alloc-label');
            if (agentUid) {
                $lbl.html('<i class="fa-solid fa-circle-check" style="font-size:10px;"></i> ' + agentName).css('color', '#87b942');
            } else {
                $lbl.text('Not assigned').css('color', '#bbb');
            }
            if (currentActivityLeadId == leadId) {
                loadActivityDrawerContent(leadId);
            }
        }
    })
    .fail(function () { alert('Failed to save allocation.'); })
    .always(function () { $sel.prop('disabled', false); });
});

// ── Activity Drawer ────────────────────────────────────────
var currentActivityLeadId = null;

function loadActivityDrawerContent(leadId) {
    $('#activityDrawerBody').html('<div style="text-align:center;padding:40px;color:#aaa;"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');
    $.get(ALLOCATE_URL + '/' + leadId + '/activity')
        .done(function (html) { $('#activityDrawerBody').html(html); })
        .fail(function () { $('#activityDrawerBody').html('<div style="text-align:center;padding:30px;color:#e53935;">Failed to load activity.</div>'); });
}

function openActivityDrawer(leadId, leadName) {
    currentActivityLeadId = leadId;
    $('#activityDrawerSub').text('LID: ' + leadId + (leadName ? ' · ' + leadName : ''));
    $('#activityDrawerOverlay').show();
    $('#activityDrawer').css('right', '0');
    loadActivityDrawerContent(leadId);
}

function closeActivityDrawer() {
    $('#activityDrawer').css('right', '-420px');
    $('#activityDrawerOverlay').hide();
    currentActivityLeadId = null;
}

$(document).on('click', '.open-activity-drawer', function () {
    openActivityDrawer($(this).data('lead-id'), $(this).data('lead-name'));
});

// ── Clear callback request ─────────────────────────────────
$(document).on('click', '.req-call-badge', function () {
    var $badge = $(this);
    var leadId = $badge.closest('.req-call-cell').data('lead-id');
    $badge.css('opacity', '0.4').css('pointer-events', 'none');

    $.ajax({
        url:     ALLOCATE_URL + '/' + leadId + '/clear-callback-request',
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    })
    .done(function (res) {
        if (res.success) {
            $badge.closest('.req-call-cell').html('<span style="color:#d1d5db;font-size:13px;">--</span>');
        } else {
            $badge.css('opacity', '1').css('pointer-events', '');
        }
    })
    .fail(function () {
        $badge.css('opacity', '1').css('pointer-events', '');
        alert('Failed to update.');
    });
});

// Search on Enter
$('#fSearch').on('keydown', function (e) { if (e.key === 'Enter') loadLeads(1); });

// Load on ready
$(function () { loadLeads(1); });
</script>
@endpush

@endsection
