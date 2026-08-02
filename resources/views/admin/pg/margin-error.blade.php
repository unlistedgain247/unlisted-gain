@extends('layout.admin')

@section('title', 'Margin Error | PG | Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pg-margin-error.css') }}?v={{ filemtime(public_path('assets/css/admin/pg-margin-error.css')) }}">
@endpush

@section('content')

@include('partials.admin-pg-subnav')

<div class="admin-main">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h1 class="admin-page-title" style="margin:0;">Margin Error</h1>
        <div style="font-size:12px;color:#9ca3af;">Sorted by absolute difference (highest first). Click any row to see month-wise breakdown.</div>
    </div>

    <div class="admin-card" style="padding:0;">
        <div id="pgMerrTableWrap">
            <div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>
    </div>
</div>

{{-- ── Margin Error Detail Modal ─────────────────────────────── --}}
<div id="pgMerrOverlay">
    <div id="pgMerrModal">
        <div class="pge-header">
            <div class="pge-title" id="pgeMerrTitle">Margin Error — Month Breakdown</div>
            <button class="pge-close" id="pgeMerrClose">&times;</button>
        </div>
        <div id="pgMerrModalBody">
            <div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
var PG_MERR_DATA_URL  = '{{ url("/admin/pg/margin-error/data") }}';
var PG_MERR_MODAL_URL = '{{ url("/admin/pg/margin-error/modal") }}';

$(function () { loadMerrData(); });

function loadMerrData() {
    $('#pgMerrTableWrap').html('<div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');
    $.get(PG_MERR_DATA_URL)
        .done(function (html) { $('#pgMerrTableWrap').html(html); })
        .fail(function () { $('#pgMerrTableWrap').html('<div class="pg-loading" style="color:#e53935;">Failed to load data.</div>'); });
}

// ── Row click → open modal ──────────────────────────────────
$(document).on('click', '.pg-merr-row', function () {
    var fincode  = $(this).data('fincode');
    var compName = $(this).find('td:nth-child(2)').text().trim() || ('Fincode ' + fincode);

    $('#pgeMerrTitle').text('Margin Error — ' + compName + ' (' + fincode + ')');
    $('#pgMerrOverlay').addClass('open');

    $('#pgMerrModalBody').html('<div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');
    $.ajax({
        url:     PG_MERR_MODAL_URL,
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data:    { fincode: fincode },
    })
    .done(function (res) { $('#pgMerrModalBody').html(res.html || '<p style="color:#999;padding:20px;">No data.</p>'); })
    .fail(function () { $('#pgMerrModalBody').html('<p style="color:#e53935;padding:20px;">Failed to load.</p>'); });
});

// Close modal
function closePgMerrModal() {
    $('#pgMerrOverlay').removeClass('open');
}
$('#pgeMerrClose').on('click', closePgMerrModal);
$('#pgMerrOverlay').on('click', function (e) {
    if ($(e.target).is('#pgMerrOverlay')) closePgMerrModal();
});
</script>
@endpush

@endsection
