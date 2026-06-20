@extends('layout.admin')

@section('title', 'Unlisted Stocks | Admin | UnlistedGain')

@push('styles')
<style>
    /* ── Toggle switch ─────────────── */
    .tgl-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        cursor: pointer;
    }

    .tgl-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .tgl-slider {
        position: absolute;
        inset: 0;
        background: #ccc;
        border-radius: 24px;
        transition: background 0.2s;
    }

    .tgl-slider::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    }

    .tgl-switch input:checked+.tgl-slider {
        background: #87b942;
    }

    .tgl-switch input:checked+.tgl-slider::before {
        transform: translateX(20px);
    }

    /* ── Price / Thesis / Financials cell ─ */
    .ptf-cell {
        white-space: nowrap;
        font-size: 13px;
    }

    .ptf-label {
        color: #1a1a1a;
        font-weight: 500;
    }

    .ptf-sep {
        color: #ccc;
        margin: 0 4px;
    }

    .ptf-icon-edit {
        color: #2196f3;
        font-size: 11px;
        cursor: pointer;
    }

    .ptf-icon-add {
        color: #4caf50;
        font-size: 11px;
        cursor: pointer;
    }

    .ptf-icon-view {
        color: #2196f3;
        font-size: 11px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
@include('partials.admin-unlisted-subnav')
<div class="admin-main">

    <h1 class="admin-page-title">Dashboard</h1>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Fincode</th>
                        <th>Company</th>
                        <th>Latest Price</th>
                        <th>Price / Thesis / Financials</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $stock->UL_STOCKS_FINCODE }}</td>
                        <td>
                            <a href="{{ url('/companies/' . $stock->UL_STOCKS_SLUG) }}/" target="_blank" rel="noopener"
                               style="color:inherit;text-decoration:none;font-weight:inherit;">
                                {{ $stock->UL_STOCKS_COMPNAME }}
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px;color:#94a3b8;margin-left:4px;"></i>
                            </a>
                        </td>
                        <td style="white-space:nowrap;font-size:13px;font-weight:600;color:#1a1a1a">
                            @php $lp = $latestPrices->get($stock->UL_STOCKS_FINCODE); @endphp
                            @if($lp)
                                &#8377;{{ number_format($lp->UL_PD_BID_PRICE, 2) }}
                            @else
                                <span style="color:#cbd5e1;font-weight:400">—</span>
                            @endif
                        </td>
                        <td class="ptf-cell">
                            <span class="ptf-label">Overview</span>
                            <i class="fa-solid fa-pen ptf-icon-edit overview-btn"
                                data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                                style="cursor:pointer" title="Edit overview"></i>
                            <span class="ptf-sep">|</span>
                            <span class="ptf-label">Price</span>
                            <i class="fa-solid fa-plus ptf-icon-add price-add-btn"
                                data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                                style="cursor:pointer" title="Add price"></i>
                            <i class="fa-regular fa-eye ptf-icon-view price-view-btn"
                                data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                                style="cursor:pointer" title="View prices"></i>
                            <span class="ptf-sep">|</span>
                            <span class="ptf-label">Financials</span>
                            <i class="fa-solid fa-plus ptf-icon-add fin-add-btn"
                               data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                               style="cursor:pointer" title="Add financials"></i>
                            <i class="fa-regular fa-eye ptf-icon-view fin-view-btn"
                               data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                               style="cursor:pointer" title="View financials"></i>
                            <span class="ptf-sep">|</span>
                            <span class="ptf-label">Thesis</span>
                            <i class="fa-solid fa-pen ptf-icon-edit thesis-btn"
                               data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                               style="cursor:pointer" title="Edit thesis"></i>
                        </td>
                        <td>
                            <span class="admin-badge {{ $stock->UL_STOCKS_STATUS === '1' ? 'badge-admin' : 'badge-locked' }}">
                                {{ $stock->UL_STOCKS_STATUS === '1' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <label class="tgl-switch" title="Toggle status">
                                <input type="checkbox"
                                    class="stock-toggle"
                                    data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
                                    {{ $stock->UL_STOCKS_STATUS === '1' ? 'checked' : '' }}>
                                <span class="tgl-slider"></span>
                            </label>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#aaa;padding:32px">
                            <i class="fa-regular fa-folder-open" style="font-size:24px;display:block;margin-bottom:8px"></i>
                            No stocks added yet. Click <strong>+ Add Stocks</strong> to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
        <div style="margin-top:16px">{{ $stocks->links() }}</div>
        @endif
    </div>

</div>

@include('admin.unlisted.stocks-modal')
@include('admin.unlisted.industry-modal')

{{-- Overview modal container —  injected via AJAX --}}
<div id="overviewModalWrap"></div>

{{-- Price modal container — injected via AJAX --}}
<div id="priceModalWrap"></div>

{{-- Price list modal container --}}
<div id="priceListModalWrap"></div>

{{-- Financials modal container — injected via AJAX --}}
<div id="finModalWrap"></div>

{{-- Financials list modal container --}}
<div id="finListModalWrap"></div>

{{-- Financials edit modal container (opens on top of list) --}}
<div id="finEditModalWrap"></div>

{{-- Thesis modal container — injected via AJAX --}}
<div id="thesisModalWrap"></div>
@endsection

@push('scripts')
<script>
    // globally available so modal blade scripts can reference them
    window.STOCKS_BASE = '{{ url("/admin/unlisted/stocks") }}';
    var CSRF = $('meta[name="csrf-token"]').attr('content');

    // ── Stock toggle ───────────────────────────────────────
    $(document).on('change', '.stock-toggle', function () {
        var $cb     = $(this);
        var fincode = $cb.data('fincode');
        var $badge  = $cb.closest('tr').find('.admin-badge');
        $.ajax({ url: window.STOCKS_BASE + '/' + fincode + '/toggle', method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
            .done(function (res) {
                if (res.success) {
                    var active = res.status === '1';
                    $badge.text(active ? 'Active' : 'Inactive')
                          .removeClass('badge-admin badge-locked')
                          .addClass(active ? 'badge-admin' : 'badge-locked');
                } else { $cb.prop('checked', !$cb.prop('checked')); }
            })
            .fail(function () { $cb.prop('checked', !$cb.prop('checked')); });
    });

    // ── Overview modal ─────────────────────────────────────
    $(document).on('click', '.overview-btn', function () {
        var fincode = $(this).data('fincode');
        $('#overviewModalWrap').html(loadingSpinner());
        $.get(window.STOCKS_BASE + '/' + fincode + '/overview')
            .done(function (html) { $('#overviewModalWrap').html(html); })
            .fail(function ()     { $('#overviewModalWrap').empty(); alert('Failed to load.'); });
    });
    function closeOverviewModal() { $('#overviewModalWrap').empty(); }

    // ── Price add modal ────────────────────────────────────
    $(document).on('click', '.price-add-btn', function () {
        var fincode = $(this).data('fincode');
        $('#priceModalWrap').html(loadingSpinner());
        $.get(window.STOCKS_BASE + '/' + fincode + '/price')
            .done(function (html) { $('#priceModalWrap').html(html); })
            .fail(function ()     { $('#priceModalWrap').empty(); alert('Failed to load.'); });
    });
    function closePriceModal() { $('#priceModalWrap').empty(); }

    // ── Price list modal ───────────────────────────────────
    window.plFincode = null;

    $(document).on('click', '.price-view-btn', function () {
        window.plFincode = $(this).data('fincode');
        loadPriceListPage(1);
    });

    function loadPriceListPage(page) {
        if (!window.plFincode) return;
        $('#priceListModalWrap').html(loadingSpinner());
        $.ajax({
            url:    window.STOCKS_BASE + '/' + window.plFincode + '/price-list',
            method: 'POST',
            data:   { _token: CSRF, page: page },
        })
        .done(function (html) { $('#priceListModalWrap').html(html); })
        .fail(function ()     { $('#priceListModalWrap').empty(); alert('Failed to load.'); });
    }

    function closePriceListModal() {
        $('#priceListModalWrap').empty();
        window.plFincode = null;
    }

    // paginator — single permanent handler for all pages in the project
    $(document).on('click', '.pagi-btn:not(:disabled)', function () {
        var fn   = $(this).data('cb');
        var page = $(this).data('page');
        if (fn && typeof window[fn] === 'function') window[fn](page);
    });

    // ── Financials add modal ───────────────────────────────
    $(document).on('click', '.fin-add-btn', function () {
        var fincode = $(this).data('fincode');
        $('#finModalWrap').html(loadingSpinner());
        $.get(window.STOCKS_BASE + '/' + fincode + '/financials')
            .done(function (html) { $('#finModalWrap').html(html); })
            .fail(function ()     { $('#finModalWrap').empty(); alert('Failed to load.'); });
    });
    function closeFinancialsModal() { $('#finModalWrap').empty(); }

    // ── Financials list modal ──────────────────────────────
    window.flFincode = null;

    $(document).on('click', '.fin-view-btn', function () {
        window.flFincode = $(this).data('fincode');
        loadFinancialsListPage(1);
    });

    function loadFinancialsListPage(page) {
        if (!window.flFincode) return;
        $('#finListModalWrap').html(loadingSpinner());
        $.ajax({
            url:    window.STOCKS_BASE + '/' + window.flFincode + '/financials-list',
            method: 'POST',
            data:   { _token: CSRF, page: page },
        })
        .done(function (html) { $('#finListModalWrap').html(html); })
        .fail(function ()     { $('#finListModalWrap').empty(); alert('Failed to load.'); });
    }

    function closeFinancialsListModal() {
        $('#finListModalWrap').empty();
        window.flFincode = null;
    }

    function closeFinancialsEditModal() { $('#finEditModalWrap').empty(); loadFinancialsListPage(1); }

    // ── Thesis modal ───────────────────────────────────────
    $(document).on('click', '.thesis-btn', function () {
        var fincode = $(this).data('fincode');
        $('#thesisModalWrap').html(loadingSpinner());
        $.get(window.STOCKS_BASE + '/' + fincode + '/thesis')
            .done(function (html) { $('#thesisModalWrap').html(html); })
            .fail(function ()     { $('#thesisModalWrap').empty(); alert('Failed to load.'); });
    });
    function closeThesisModal() {
        if (typeof tinymce !== 'undefined') tinymce.remove('#UL_THESIS_CONTENT1');
        $('#thesisModalWrap').empty();
    }

    // ── Shared loading spinner ─────────────────────────────
    function loadingSpinner() {
        return '<div style="position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:2100;display:flex;align-items:center;justify-content:center">' +
               '<div style="background:#fff;border-radius:12px;padding:40px;color:#888;font-size:14px">Loading…</div></div>';
    }
</script>
<script src="{{ asset('js/tinymce_6.1.2/tinymce.min.js') }}"></script>
@endpush