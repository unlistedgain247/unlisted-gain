@extends('layout.admin')

@section('title', 'Margin Dashboard | PG | Admin')

@push('styles')
<style>
    .pg-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }
    .pg-filters .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .pg-filters label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #999;
    }
    .pg-filters input {
        padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 6px;
        font-size: 12px; color: #333; background: #fff; height: 34px; min-width: 140px;
    }
    .pg-filters input:focus { outline: none; border-color: #87b942; }
    .pg-filters .filter-btn {
        padding: 0 18px; height: 34px; border-radius: 6px;
        border: none; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    #pgMarginTableWrap { min-height: 200px; }
    .pg-loading { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }

    /* ── Margin Modal ─────────────────────────────── */
    #pgMarginOverlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 9000;
        align-items: flex-start; justify-content: center;
        padding: 30px 16px; overflow-y: auto;
    }
    #pgMarginOverlay.open { display: flex; }
    #pgMarginModal {
        background: #fff; border-radius: 12px; width: 100%; max-width: 1000px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.22); overflow: hidden;
        flex-shrink: 0; margin: auto;
    }
    #pgMarginModal .pgm-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 24px; border-bottom: 1px solid #f0f0f0;
    }
    #pgMarginModal .pgm-title { font-size: 16px; font-weight: 700; color: #111827; }
    #pgMarginModal .pgm-close {
        background: none; border: none; font-size: 22px; color: #9ca3af; cursor: pointer; line-height: 1;
    }
    #pgMarginModal .pgm-close:hover { color: #374151; }
    .pgm-radio-bar {
        display: flex; gap: 6px; padding: 12px 24px;
        border-bottom: 1px solid #f0f0f0; background: #fafafa; flex-wrap: wrap;
    }
    .pgm-radio-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 20px; border: 1.5px solid #e0e0e0;
        font-size: 12px; font-weight: 600; cursor: pointer; background: #fff;
        transition: all 0.15s; color: #555;
    }
    .pgm-radio-btn input { display: none; }
    .pgm-radio-btn.selected { background: #87b942; color: #fff; border-color: #87b942; }
    #pgMarginModalBody { padding: 20px 24px; min-height: 200px; }
    .pgm-body-loading { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }
</style>
@endpush

@section('content')

@include('partials.admin-pg-subnav')

<div class="admin-main">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h1 class="admin-page-title" style="margin:0;">Margin Dashboard</h1>
    </div>

    <div class="admin-card" style="padding:0;">

        {{-- Filters --}}
        <div class="pg-filters">
            <div class="filter-group">
                <label>Date From</label>
                <input type="date" id="pgFFrom">
            </div>
            <div class="filter-group">
                <label>Date To</label>
                <input type="date" id="pgFTo">
            </div>
            <div class="filter-group">
                <label>Company / Fincode</label>
                <input type="text" id="pgFSearch" placeholder="Company name or Fincode">
            </div>
            <div class="filter-group">
                <label>Advisor</label>
                <input type="text" id="pgFAdvisor" placeholder="Advisor name or UID">
            </div>
            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px;">
                    <button class="filter-btn" onclick="loadMarginData()" style="background:#87b942;color:#fff;">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                    <button class="filter-btn" onclick="resetMarginFilters()" style="background:#f5f5f5;color:#555;border:1px solid #e0e0e0;">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <div id="pgMarginTableWrap">
            <div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>

    </div>
</div>

{{-- ── Margin Modal ──────────────────────────────────────────── --}}
<div id="pgMarginOverlay">
    <div id="pgMarginModal">
        <div class="pgm-header">
            <div class="pgm-title" id="pgmTitle">Margin Breakdown</div>
            <button class="pgm-close" id="pgmClose">&times;</button>
        </div>

        <div class="pgm-radio-bar">
            <label class="pgm-radio-btn selected" data-view="advisor">
                <input type="radio" name="pgmView" value="advisor" checked>
                Advisor
            </label>
            <label class="pgm-radio-btn" data-view="company">
                <input type="radio" name="pgmView" value="company">
                Company
            </label>
            <label class="pgm-radio-btn" data-view="customer">
                <input type="radio" name="pgmView" value="customer">
                Customers
            </label>
            <label class="pgm-radio-btn" data-view="buyDeal">
                <input type="radio" name="pgmView" value="buyDeal">
                Client Size
            </label>
        </div>

        <div id="pgMarginModalBody">
            <div class="pgm-body-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
var PG_MARGIN_DATA_URL  = '{{ url("/admin/pg/margin/data") }}';
var PG_MARGIN_MODAL_URL = '{{ url("/admin/pg/margin/modal") }}';

// Set default dates: 6 months ago to today
$(function () {
    var today  = new Date();
    var sixAgo = new Date();
    sixAgo.setMonth(sixAgo.getMonth() - 6);

    function fmt(d) {
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var dy = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + dy;
    }

    $('#pgFFrom').val(fmt(sixAgo));
    $('#pgFTo').val(fmt(today));
    loadMarginData();
});

function getMarginFilters() {
    return {
        from_date:      $('#pgFFrom').val(),
        to_date:        $('#pgFTo').val(),
        searchtext:     $('#pgFSearch').val().trim(),
        advisor_search: $('#pgFAdvisor').val().trim(),
    };
}

function resetMarginFilters() {
    $('#pgFSearch, #pgFAdvisor').val('');
    var today  = new Date();
    var sixAgo = new Date();
    sixAgo.setMonth(sixAgo.getMonth() - 6);
    function fmt(d) {
        return d.getFullYear() + '-' + ('0'+(d.getMonth()+1)).slice(-2) + '-' + ('0'+d.getDate()).slice(-2);
    }
    $('#pgFFrom').val(fmt(sixAgo));
    $('#pgFTo').val(fmt(today));
    loadMarginData();
}

function loadMarginData() {
    $('#pgMarginTableWrap').html('<div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');
    $.get(PG_MARGIN_DATA_URL, getMarginFilters())
        .done(function (html) { $('#pgMarginTableWrap').html(html); })
        .fail(function () { $('#pgMarginTableWrap').html('<div class="pg-loading" style="color:#e53935;">Failed to load data.</div>'); });
}

// ── Row click → open modal ──────────────────────────────────
$(document).on('click', '.pg-margin-row', function () {
    var $row    = $(this);
    var isTotal = $row.data('is-total') || 'no';
    var month   = isTotal === 'yes' ? '' : ($row.data('month') || '');
    var year    = isTotal === 'yes' ? '' : ($row.data('year')  || '');
    var fFrom   = $row.data('from')    || '';
    var fTo     = $row.data('to')      || '';
    var fSearch = $row.data('search')  || '';
    var fAdv    = $row.data('advisor') || '';

    var title = isTotal === 'yes' ? 'Total Margin Breakdown' : 'Margin — ' + $row.find('td:first').text().trim();
    $('#pgmTitle').text(title);

    // Store on overlay for radio reload
    $('#pgMarginOverlay')
        .data('is-total', isTotal)
        .data('month', month).data('year', year)
        .data('from', fFrom).data('to', fTo)
        .data('search', fSearch).data('advisor', fAdv);

    // Reset to advisor view
    $('.pgm-radio-btn').removeClass('selected');
    $('[data-view="advisor"]').addClass('selected');

    $('#pgMarginOverlay').addClass('open');
    loadMarginModal(isTotal, month, year, 'advisor', fFrom, fTo, fSearch, fAdv);
});

function loadMarginModal(isTotal, month, year, viewOption, fFrom, fTo, fSearch, fAdvisor) {
    $('#pgMarginModalBody').html('<div class="pgm-body-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');
    $.ajax({
        url:     PG_MARGIN_MODAL_URL,
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            is_total:       isTotal || 'no',
            month:          month || '',
            year:           year  || '',
            view_option:    viewOption,
            from_date:      fFrom    || '',
            to_date:        fTo      || '',
            search_text:    fSearch  || '',
            advisor_search: fAdvisor || '',
        },
    })
    .done(function (res) { $('#pgMarginModalBody').html(res.html || '<p style="color:#999;padding:20px;">No data.</p>'); })
    .fail(function () { $('#pgMarginModalBody').html('<p style="color:#e53935;padding:20px;">Failed to load.</p>'); });
}

// Radio change in modal
$(document).on('click', '.pgm-radio-btn', function () {
    $('.pgm-radio-btn').removeClass('selected');
    $(this).addClass('selected');

    var rowData = $(this).closest('#pgMarginModal');
    // Re-read current stored data from the overlay's data attributes
    var $overlay = $('#pgMarginOverlay');
    loadMarginModal(
        $overlay.data('is-total'),
        $overlay.data('month'),
        $overlay.data('year'),
        $(this).data('view'),
        $overlay.data('from'),
        $overlay.data('to'),
        $overlay.data('search'),
        $overlay.data('advisor')
    );
});

// Close modal
function closePgMarginModal() {
    $('#pgMarginOverlay').removeClass('open');
}
$('#pgmClose').on('click', closePgMarginModal);
$('#pgMarginOverlay').on('click', function (e) {
    if ($(e.target).is('#pgMarginOverlay')) closePgMarginModal();
});

// Search on Enter
$('#pgFSearch, #pgFAdvisor').on('keydown', function (e) { if (e.key === 'Enter') loadMarginData(); });
</script>
@endpush

@endsection
