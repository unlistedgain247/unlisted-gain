@extends('layout.admin')

@section('title', 'Transactions | PG | Admin')

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
    .pg-filters .filter-group { display: flex; flex-direction: column; gap: 4px; position: relative; }
    .pg-filters label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #999;
    }
    .pg-filters input, .pg-filters select {
        padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 6px;
        font-size: 12px; color: #333; background: #fff; height: 34px; min-width: 130px;
    }
    .pg-filters input:focus, .pg-filters select:focus { outline: none; border-color: #87b942; }
    .pg-filters .filter-btn {
        padding: 0 18px; height: 34px; border-radius: 6px;
        border: none; font-size: 12px; font-weight: 600; cursor: pointer;
        text-decoration: none; display: inline-flex; align-items: center;
    }
    .txn-dropdown {
        position: absolute; top: 100%; left: 0; z-index: 9999; width: 100%;
        background: #fff; border: 1px solid #ddd; border-radius: 4px;
        display: none; max-height: 160px; overflow-y: auto; font-size: 12px;
    }
    #pgTxnTableWrap { min-height: 200px; }
    .pg-loading { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }
</style>
@endpush

@section('content')

@include('partials.admin-pg-subnav')

<div class="admin-main">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h1 class="admin-page-title" style="margin:0;">Transactions</h1>
    </div>

    <div class="admin-card" style="padding:0;">

        {{-- Filters --}}
        <div class="pg-filters">
            <div class="filter-group">
                <label>Account</label>
                <select id="txnAccount">
                    <option value="">All</option>
                    <option value="ICICI Bank">ICICI Bank</option>
                    <option value="Bandhan Bank">Bandhan Bank</option>
                    <option value="Demat">Demat</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Flow Type</label>
                <select id="txnFlow">
                    <option value="">All</option>
                    <option value="Flow_In">Flow In</option>
                    <option value="Flow_Out">Flow Out</option>
                </select>
            </div>

            <div class="filter-group" id="txnCompanyGroup" style="display:none;">
                <label>Company</label>
                <input type="text" id="txnCompanySearch" placeholder="Search stocks…" autocomplete="off">
                <input type="hidden" id="txnFincode">
                <div class="txn-dropdown" id="txnCompanyDropdown"></div>
            </div>

            <div class="filter-group">
                <label>User</label>
                <input type="text" id="txnUserSearch" placeholder="Name or UID" autocomplete="off">
                <input type="hidden" id="txnUserId">
                <div class="txn-dropdown" id="txnUserDropdown"></div>
            </div>

            <div class="filter-group">
                <label>TID</label>
                <input type="text" id="txnTid" placeholder="TID" style="min-width:80px;">
            </div>

            <div id="txnBankFilters" style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;">
                <div class="filter-group">
                    <label>Direct</label>
                    <select id="txnDirect">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Commission</label>
                    <select id="txnCommission">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>TDS</label>
                    <select id="txnTds">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div class="filter-group">
                <label>From</label>
                <input type="date" id="txnFrom">
            </div>
            <div class="filter-group">
                <label>To</label>
                <input type="date" id="txnTo">
            </div>

            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px;">
                    <button class="filter-btn" onclick="loadTransactionsData(0)" style="background:#87b942;color:#fff;">
                        <i class="fa-solid fa-magnifying-glass"></i>&nbsp;Search
                    </button>
                    <button class="filter-btn" onclick="resetTransactionFilters()" style="background:#f5f5f5;color:#555;border:1px solid #e0e0e0;">
                        Reset
                    </button>
                    <a class="filter-btn" id="txnExportBtn" href="#" target="_blank" style="background:#fff;color:#087f5b;border:1px solid #087f5b;">
                        <i class="fa-solid fa-file-export"></i>&nbsp;Export
                    </a>
                </div>
            </div>
        </div>

        <div id="pgTxnTableWrap">
            <div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>

    </div>
</div>

@include('admin.pg.partials.map-transaction-modal')

@push('scripts')
<script>
var TXN_DATA_URL          = '{{ url("/admin/pg/transactions/data") }}';
var TXN_EXPORT_URL        = '{{ url("/admin/pg/transactions/export") }}';
var TXN_SEARCH_USERS_URL  = '{{ url("/admin/pg/search-users") }}';
var TXN_SEARCH_STOCKS_URL = '{{ url("/admin/pg/search-stocks") }}';

function txnToggleAccountFields() {
    var isDemat = $('#txnAccount').val() === 'Demat';
    $('#txnCompanyGroup').toggle(isDemat);
    $('#txnBankFilters').toggle(!isDemat);
}
$('#txnAccount').on('change', txnToggleAccountFields);
txnToggleAccountFields();

function getTransactionFilters() {
    return {
        px_account_filter:  $('#txnAccount').val(),
        px_account_flow:    $('#txnFlow').val(),
        fincode_filter:     $('#txnFincode').val(),
        user_id_filter:     $('#txnUserId').val(),
        tid_filter:         $('#txnTid').val().trim(),
        direct_filter:      $('#txnDirect').val(),
        commission_filter:  $('#txnCommission').val(),
        tds_filter:         $('#txnTds').val(),
        from_date:          $('#txnFrom').val(),
        to_date:            $('#txnTo').val(),
    };
}

function updateExportLink() {
    var q = $.param(getTransactionFilters());
    $('#txnExportBtn').attr('href', TXN_EXPORT_URL + '?' + q);
}

function resetTransactionFilters() {
    $('#txnAccount, #txnFlow, #txnDirect, #txnCommission, #txnTds').val('');
    $('#txnCompanySearch, #txnFincode, #txnUserSearch, #txnUserId, #txnTid, #txnFrom, #txnTo').val('');
    txnToggleAccountFields();
    loadTransactionsData(0);
}

function loadTransactionsPage(pageNo) { loadTransactionsData(pageNo); }

function loadTransactionsData(pageNo) {
    pageNo = pageNo || 0;
    updateExportLink();
    var filters = getTransactionFilters();
    filters.page_no = pageNo;
    $('#pgTxnTableWrap').html('<div class="pg-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');
    $.ajax({ url: TXN_DATA_URL, data: filters, cache: false })
        .done(function (html) { $('#pgTxnTableWrap').html(html); })
        .fail(function () { $('#pgTxnTableWrap').html('<div class="pg-loading" style="color:#e53935;">Failed to load data.</div>'); });
}

pgdMakeDropdown('txnCompanySearch', 'txnCompanyDropdown', 'txnFincode', TXN_SEARCH_STOCKS_URL, 'label', 'fincode');
pgdMakeDropdown('txnUserSearch', 'txnUserDropdown', 'txnUserId', TXN_SEARCH_USERS_URL, 'label', 'uid', 1);

$('#txnTid, #txnFrom, #txnTo').on('keydown', function (e) { if (e.key === 'Enter') loadTransactionsData(0); });

updateExportLink();
loadTransactionsData(0);
</script>
@endpush

@endsection
