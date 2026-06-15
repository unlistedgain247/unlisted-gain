@extends('layout.admin')

@section('title', 'Orders | Admin | UnlistedGain')

@push('styles')
<style>
    .ord-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }
    .ord-filters .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .ord-filters label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #999;
    }
    .ord-filters input, .ord-filters select {
        padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 6px;
        font-size: 12px; color: #333; background: #fff; height: 34px; min-width: 130px;
    }
    .ord-filters input:focus, .ord-filters select:focus { outline: none; border-color: #87b942; }
    .ord-filters .filter-btn {
        padding: 0 18px; height: 34px; border-radius: 6px;
        border: none; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    #ordersTableWrap { min-height: 200px; }
    .ord-loading { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }

    /* ── Edit Order Modal ──────────────────────────────────── */
    #editOrdOverlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 9000;
        align-items: flex-start; justify-content: center;
        padding: 30px 16px; overflow-y: auto;
    }
    #editOrdOverlay.open { display: flex; }
    #editOrdModal {
        background: #fff; border-radius: 12px; width: 100%; max-width: 700px;
        box-shadow: 0 12px 48px rgba(0,0,0,0.22); overflow: hidden;
        flex-shrink: 0; margin: auto;
    }
    #editOrdModal .eom-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 24px; border-bottom: 1px solid #f0f0f0;
    }
    #editOrdModal .eom-title { font-size: 16px; font-weight: 700; color: #111827; }
    #editOrdModal .eom-close {
        background: none; border: none; font-size: 22px; color: #9ca3af; cursor: pointer; padding: 0 4px; line-height: 1;
    }
    #editOrdModal .eom-close:hover { color: #374151; }
    #editOrdModal .eom-body { padding: 22px 24px; }
    .eom-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .eom-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .eom-field label {
        display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 5px;
    }
    .eom-field input, .eom-field select {
        width: 100%; padding: 8px 10px; border: 1px solid #e0e0e0;
        border-radius: 7px; font-size: 13px; color: #333; background: #fff;
        box-sizing: border-box; height: 36px;
    }
    .eom-field input:focus, .eom-field select:focus { outline: none; border-color: #87b942; }
    .eom-field input:disabled { background: #f9fafb; color: #6b7280; cursor: not-allowed; }
    .eom-section-label {
        font-size: 13px; font-weight: 700; color: #374151;
        margin: 18px 0 12px; padding-bottom: 6px; border-bottom: 1px solid #f0f0f0;
    }
    .eom-date-row { display: flex; gap: 6px; }
    .eom-date-row input[type="date"] { flex: 1; }
    .eom-date-row select { width: 68px; flex-shrink: 0; }
    /* Direct Flag toggle */
    .eom-toggle-wrap { display: flex; align-items: center; gap: 10px; margin-top: 6px; height: 36px; }
    .eom-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .eom-switch input { opacity: 0; width: 0; height: 0; }
    .eom-slider {
        position: absolute; cursor: pointer; inset: 0;
        background: #d1d5db; border-radius: 24px; transition: background 0.2s;
    }
    .eom-slider:before {
        content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px;
        background: #fff; border-radius: 50%; transition: transform 0.2s;
    }
    .eom-switch input:checked + .eom-slider { background: #ef4444; }
    .eom-switch input:checked + .eom-slider:before { transform: translateX(20px); }
    .eom-toggle-label { font-size: 13px; color: #374151; font-weight: 500; }
    /* Footer */
    #editOrdModal .eom-footer {
        padding: 16px 24px; border-top: 1px solid #f0f0f0;
        display: flex; justify-content: flex-start; gap: 10px;
    }
    #eomSubmitBtn {
        padding: 9px 28px; border-radius: 7px; font-size: 13px; font-weight: 600;
        cursor: pointer; border: none; background: #1d4ed8; color: #fff;
    }
    #eomSubmitBtn:hover { background: #1e40af; }
    #eomSubmitBtn:disabled { opacity: 0.6; cursor: not-allowed; }
    #eomCancelBtn {
        padding: 9px 20px; border-radius: 7px; font-size: 13px; font-weight: 600;
        cursor: pointer; background: #f5f5f5; color: #555; border: 1px solid #e0e0e0;
    }
</style>
@endpush

@section('content')

@include('partials.admin-unlisted-subnav')

<div class="admin-main">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h1 class="admin-page-title" style="margin:0;">
            Orders
            <span id="ordersCount" style="font-size:14px;font-weight:400;color:#aaa;margin-left:8px;"></span>
        </h1>
    </div>

    <div class="admin-card" style="padding:0;">

        {{-- Filter Bar --}}
        <div class="ord-filters">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="fSearch" placeholder="Order ID / Name / Share / UID" style="min-width:220px;">
            </div>
            <div class="filter-group">
                <label>Type</label>
                <select id="fType">
                    <option value="">All</option>
                    <option value="buy">Buy</option>
                    <option value="sell">Sell</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Deal Status</label>
                <select id="fStatus">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Date From</label>
                <input type="date" id="fDateFrom">
            </div>
            <div class="filter-group">
                <label>Date To</label>
                <input type="date" id="fDateTo">
            </div>
            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px;">
                    <button class="filter-btn" onclick="loadOrders(1)" style="background:#87b942;color:#fff;">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                    <button class="filter-btn" onclick="resetFilters()" style="background:#f5f5f5;color:#555;border:1px solid #e0e0e0;">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <div id="ordersTableWrap">
            <div class="ord-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>

    </div>
</div>

{{-- ── Edit Order Modal ──────────────────────────────────────── --}}
<div id="editOrdOverlay">
    <div id="editOrdModal">
        <div class="eom-header">
            <div class="eom-title" id="eomTitle">Edit Order</div>
            <button class="eom-close" id="eomClose">&times;</button>
        </div>
        <div class="eom-body">

            {{-- Name + Company --}}
            <div class="eom-grid-2">
                <div class="eom-field">
                    <label>UID</label>
                    <input type="text" id="eomCustomerUid" disabled>
                </div>
                <div class="eom-field">
                    <label>Company</label>
                    <input type="text" id="eomCompany" disabled>
                </div>
            </div>

            {{-- Type + QTY + Price --}}
            <div class="eom-grid-3">
                <div class="eom-field">
                    <label>Type</label>
                    <select id="eomType">
                        <option value="buy">Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                </div>
                <div class="eom-field">
                    <label id="eomQtyLabel">QTY</label>
                    <input type="number" id="eomQty" min="1" placeholder="0">
                </div>
                <div class="eom-field">
                    <label>Price Per Share</label>
                    <input type="number" id="eomPrice" min="0" step="0.01" placeholder="0">
                </div>
            </div>

            {{-- Deal section --}}
            <div class="eom-section-label">Deal :</div>
            <div class="eom-grid-2">
                <div class="eom-field">
                    <label>Status</label>
                    <select id="eomStatus">
                        <option value="">Select</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="eom-field">
                    <label>Order Date</label>
                    <div class="eom-date-row">
                        <input type="date" id="eomDate">
                        <select id="eomHr"></select>
                        <select id="eomMin"></select>
                    </div>
                </div>
            </div>
            <div class="eom-grid-2" id="eomSubStatusRow" style="display:none;">
                <div class="eom-field">
                    <label>Sub Status</label>
                    <select id="eomSubStatus">
                        <option value="">--Sub Status--</option>
                        <option value="Invalid order">Invalid order</option>
                        <option value="Not Available">Not Available</option>
                        <option value="Customer Backed Out">Customer Backed Out</option>
                        <option value="Test Order">Test Order</option>
                        <option value="Order by Mistake">Order by Mistake</option>
                        <option value="Price Not Realistic">Price Not Realistic</option>
                        <option value="Margin Lower than Desired">Margin Lower than Desired</option>
                        <option value="Convertible with Better Sourcing">Convertible with Better Sourcing</option>
                    </select>
                </div>
            </div>

            @php
                $_canIntermediary = !empty(session('privilege.unlisted', [])['order_backend']);
            @endphp
            @if($_canIntermediary)
            {{-- Intermediary section --}}
            <div class="eom-section-label">Intermediary :</div>
            <div class="eom-grid-2">
                <div class="eom-field">
                    <label>User ID</label>
                    <input type="text" id="eomIntermediary" placeholder="">
                </div>
                <div class="eom-field">
                    <label>Commission</label>
                    <input type="number" id="eomCommission" step="0.01" placeholder="">
                </div>
            </div>
            <div class="eom-grid-3">
                <div class="eom-field">
                    <label>LP</label>
                    <input type="number" id="eomLp" step="0.01" placeholder="">
                </div>
                <div class="eom-field">
                    <label>MLP</label>
                    <input type="number" id="eomMlp" step="0.01" placeholder="0">
                </div>
                <div class="eom-field">
                    <label>Price - LP</label>
                    <input type="text" id="eomPriceLp" readonly style="background:#f9fafb;color:#6b7280;">
                </div>
            </div>
            <div class="eom-grid-2">
                <div class="eom-field">
                    <label>Order Added By</label>
                    <select id="eomAddedBy">
                        <option value="">— Select —</option>
                        @foreach($adminUsers as $au)
                        <option value="{{ $au->uid }}">{{ $au->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="eom-field">
                    <label>Direct Flag</label>
                    <div class="eom-toggle-wrap">
                        <label class="eom-switch">
                            <input type="checkbox" id="eomDirectFlag">
                            <span class="eom-slider"></span>
                        </label>
                        <span class="eom-toggle-label" id="eomDirectFlagLabel">Off</span>
                    </div>
                </div>
            </div>
            @endif

        </div>
        <div class="eom-footer">
            <button id="eomSubmitBtn">Submit</button>
            <button id="eomCancelBtn">Cancel</button>
        </div>
    </div>
</div>

{{-- ── Quick Status Modal ────────────────────────────────────── --}}
<div id="qsOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9100;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 12px 48px rgba(0,0,0,0.22);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid #f0f0f0;">
            <div style="font-size:16px;font-weight:700;color:#111827;" id="qsTitle">Edit Order</div>
            <button id="qsClose" style="background:none;border:none;font-size:22px;color:#9ca3af;cursor:pointer;padding:0 4px;line-height:1;">&times;</button>
        </div>
        <div style="padding:22px 24px;">
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;margin-bottom:5px;">Status</label>
                <select id="qsStatus" style="width:100%;padding:8px 10px;border:1px solid #e0e0e0;border-radius:7px;font-size:13px;color:#333;background:#fff;height:36px;box-sizing:border-box;">
                    <option value="">Select</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div id="qsSubStatusRow" style="display:none;margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;margin-bottom:5px;">Sub Status</label>
                <select id="qsSubStatus" style="width:100%;padding:8px 10px;border:1px solid #e0e0e0;border-radius:7px;font-size:13px;color:#333;background:#fff;height:36px;box-sizing:border-box;">
                    <option value="">--Sub Status--</option>
                    <option value="Invalid order">Invalid order</option>
                    <option value="Not Available">Not Available</option>
                    <option value="Customer Backed Out">Customer Backed Out</option>
                    <option value="Test Order">Test Order</option>
                    <option value="Order by Mistake">Order by Mistake</option>
                    <option value="Price Not Realistic">Price Not Realistic</option>
                    <option value="Margin Lower than Desired">Margin Lower than Desired</option>
                    <option value="Convertible with Better Sourcing">Convertible with Better Sourcing</option>
                </select>
            </div>
        </div>
        <div style="padding:0 24px 20px;">
            <button id="qsSubmitBtn" style="padding:9px 28px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:#1d4ed8;color:#fff;">Submit</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var ORDERS_DATA_URL   = '{{ url("/admin/unlisted/orders/data") }}';
var ORDERS_UPDATE_URL = '{{ url("/admin/unlisted/orders") }}';
var currentPage = 1;
var eomOrderId  = null;
var qsOrderId   = null;

// Populate Hr / Min dropdowns
(function () {
    var hr = document.getElementById('eomHr');
    var mn = document.getElementById('eomMin');
    for (var h = 0; h <= 23; h++) {
        var o = document.createElement('option');
        o.value = h; o.text = (h < 10 ? '0' : '') + h;
        hr.appendChild(o);
    }
    for (var m = 0; m <= 55; m += 5) {
        var o2 = document.createElement('option');
        o2.value = m; o2.text = (m < 10 ? '0' : '') + m;
        mn.appendChild(o2);
    }
})();

function getFilters() {
    return {
        searchtext: $('#fSearch').val().trim(),
        type:       $('#fType').val(),
        status:     $('#fStatus').val(),
        date_from:  $('#fDateFrom').val(),
        date_to:    $('#fDateTo').val(),
        page:       currentPage,
    };
}

function resetFilters() {
    $('#fSearch').val('');
    $('#fType, #fStatus').val('');
    $('#fDateFrom, #fDateTo').val('');
    loadOrders(1);
}

function loadOrders(page) {
    currentPage = page || 1;
    $('#ordersTableWrap').html('<div class="ord-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');

    $.get(ORDERS_DATA_URL, getFilters())
        .done(function (html) {
            $('#ordersTableWrap').html(html);
            var $meta    = $('#ordersTableWrap #ordersMeta');
            var total    = $meta.data('total');
            var lastPage = $meta.data('last-page');
            if (total !== undefined) {
                $('#ordersCount').text(total + ' results found' + (lastPage > 1 ? ' · Showing page ' + currentPage + ' of ' + lastPage : ''));
            }
        })
        .fail(function () {
            $('#ordersTableWrap').html('<div class="ord-loading" style="color:#e53935;">Failed to load orders.</div>');
        });
}

// ── Copy order details ──────────────────────────────────────
$(document).on('click', '.copy-order-btn', function () {
    var $btn = $(this);
    var text = $btn.data('copy');
    navigator.clipboard.writeText(text).then(function () {
        $btn.html('<i class="fa-solid fa-check" style="color:#87b942;"></i>');
        setTimeout(function () { $btn.html('<i class="fa-regular fa-copy"></i>'); }, 1500);
    }).catch(function () {
        var $tmp = $('<textarea>').val(text).appendTo('body').select();
        document.execCommand('copy');
        $tmp.remove();
        $btn.html('<i class="fa-solid fa-check" style="color:#87b942;"></i>');
        setTimeout(function () { $btn.html('<i class="fa-regular fa-copy"></i>'); }, 1500);
    });
});

// ── Edit Order Modal ────────────────────────────────────────
function calcPriceLp() {
    var price = parseFloat($('#eomPrice').val()) || 0;
    var lp    = parseFloat($('#eomLp').val()) || 0;
    $('#eomPriceLp').val(price - lp || '');
}

$('#eomPrice, #eomLp').on('input', calcPriceLp);

$('#eomDirectFlag').on('change', function () {
    $('#eomDirectFlagLabel').text(this.checked ? 'On' : 'Off');
});

$('#eomStatus').on('change', function () {
    if ($(this).val() === 'Cancelled') {
        $('#eomSubStatusRow').show();
    } else {
        $('#eomSubStatusRow').hide();
        $('#eomSubStatus').val('');
    }
});

$(document).on('click', '.open-edit-ord', function () {
    var ord    = $(this).data('order');
    eomOrderId = ord.UL_ORD_ID;

    $('#eomTitle').text('Edit Order' + (ord.share_name ? ' - ' + ord.share_name : ''));
    $('#eomCustomerUid').val(ord.UL_ORD_USER_ID || '');
    $('#eomCompany').val(ord.share_name || '');

    // Type
    $('#eomType').val((ord.UL_ORD_TYPE || 'buy').toLowerCase());

    // QTY label with lot size
    var lotSize = ord.lot_size || 1;
    $('#eomQtyLabel').text('QTY (Min QTY - ' + lotSize + ')');
    $('#eomQty').val(ord.UL_ORD_QUANTITY || '');
    $('#eomPrice').val(ord.UL_ORD_PRICE_PER_SHARE || '');

    // Status + Sub Status
    var status = ord.UL_ORD_STATUS || '';
    $('#eomStatus').val(status);
    if (status === 'Cancelled') {
        $('#eomSubStatusRow').show();
        $('#eomSubStatus').val(ord.UL_ORD_SUB_STATUS || '');
    } else {
        $('#eomSubStatusRow').hide();
        $('#eomSubStatus').val('');
    }

    // Order Date (UL_ORD_DATE, fallback to INSERT_TIME)
    var dt = ord.UL_ORD_DATE || ord.UL_ORD_INSERT_TIME || '';
    if (dt) {
        var parts = dt.split(' ');
        // Convert to yyyy-mm-dd for input[type=date]
        var datePart = parts[0] || '';
        $('#eomDate').val(datePart);
        if (parts[1]) {
            var t = parts[1].split(':');
            $('#eomHr').val(parseInt(t[0], 10));
            var rMin = Math.round(parseInt(t[1], 10) / 5) * 5;
            $('#eomMin').val(rMin >= 60 ? 55 : rMin);
        }
    } else {
        $('#eomDate, #eomHr, #eomMin').val('');
    }

    // Intermediary
    $('#eomIntermediary').val(ord.UL_ORD_INTERMEDIARY_USER_ID || '');
    $('#eomMargin').val(ord.UL_ORD_INTERMEDIARY_MARGIN || '');
    $('#eomCommission').val(ord.UL_ORD_INTERMEDIARY_COMMISSION || '');
    $('#eomLp').val(ord.UL_ORD_LP != null ? ord.UL_ORD_LP : '');
    $('#eomMlp').val(ord.UL_ORD_MLP != null ? ord.UL_ORD_MLP : '0');

    // Order Added By
    $('#eomAddedBy').val(ord.UL_ORD_ADDED_BY || '');

    // Direct Flag
    var isDirect = !!parseInt(ord.UL_ORD_DIRECT_FLAG || 0);
    $('#eomDirectFlag').prop('checked', isDirect);
    $('#eomDirectFlagLabel').text(isDirect ? 'On' : 'Off');

    // Calc price - LP
    calcPriceLp();

    $('#editOrdOverlay').addClass('open');
});

function closeEditOrdModal() {
    $('#editOrdOverlay').removeClass('open');
    eomOrderId = null;
}

$('#eomClose, #eomCancelBtn').on('click', closeEditOrdModal);
$('#editOrdOverlay').on('click', function (e) {
    if ($(e.target).is('#editOrdOverlay')) closeEditOrdModal();
});

$('#eomSubmitBtn').on('click', function () {
    if (!eomOrderId) return;
    $(this).prop('disabled', true).text('Saving...');

    $.ajax({
        url:     ORDERS_UPDATE_URL + '/' + eomOrderId + '/update',
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            type:             $('#eomType').val(),
            qty:              $('#eomQty').val(),
            price_per_share:  $('#eomPrice').val(),
            status:           $('#eomStatus').val(),
            sub_status:       $('#eomSubStatus').val() || '',
            order_date:       $('#eomDate').val(),
            order_hr:         $('#eomHr').val(),
            order_min:        $('#eomMin').val(),
            intermediary_uid: $('#eomIntermediary').val(),
            margin:           $('#eomMargin').val(),
            commission:       $('#eomCommission').val(),
            lp:               $('#eomLp').val(),
            mlp:              $('#eomMlp').val(),
            added_by:         $('#eomAddedBy').val(),
            direct_flag:      $('#eomDirectFlag').is(':checked') ? 1 : 0,
        },
    })
    .done(function (res) {
        if (res.success) {
            closeEditOrdModal();
            loadOrders(currentPage);
        } else {
            alert(res.message || 'Failed to update.');
        }
    })
    .fail(function () { alert('Failed to update order.'); })
    .always(function () {
        $('#eomSubmitBtn').prop('disabled', false).text('Submit');
    });
});

// ── Quick Status Modal ──────────────────────────────────────
$('#qsStatus').on('change', function () {
    if ($(this).val() === 'Cancelled') {
        $('#qsSubStatusRow').show();
    } else {
        $('#qsSubStatusRow').hide();
        $('#qsSubStatus').val('');
    }
});

$(document).on('click', '.open-quick-status', function () {
    var ord   = $(this).data('order');
    qsOrderId = ord.UL_ORD_ID;
    $('#qsTitle').text('Edit Order' + (ord.share_name ? ' - ' + ord.share_name : ''));
    var status = ord.UL_ORD_STATUS || '';
    $('#qsStatus').val(status);
    if (status === 'Cancelled') {
        $('#qsSubStatusRow').show();
        $('#qsSubStatus').val(ord.UL_ORD_SUB_STATUS || '');
    } else {
        $('#qsSubStatusRow').hide();
        $('#qsSubStatus').val('');
    }
    $('#qsOverlay').css('display', 'flex');
});

function closeQs() {
    $('#qsOverlay').hide();
    qsOrderId = null;
}

$('#qsClose').on('click', closeQs);
$('#qsOverlay').on('click', function (e) {
    if ($(e.target).is('#qsOverlay')) closeQs();
});

$('#qsSubmitBtn').on('click', function () {
    if (!qsOrderId) return;
    $(this).prop('disabled', true).text('Saving...');

    $.ajax({
        url:     ORDERS_UPDATE_URL + '/' + qsOrderId + '/update',
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data:    { status: $('#qsStatus').val(), sub_status: $('#qsSubStatus').val() || '' },
    })
    .done(function (res) {
        if (res.success) { closeQs(); loadOrders(currentPage); }
        else alert(res.message || 'Failed to update.');
    })
    .fail(function () { alert('Failed to update status.'); })
    .always(function () { $('#qsSubmitBtn').prop('disabled', false).text('Submit'); });
});

// Search on Enter
$('#fSearch').on('keydown', function (e) { if (e.key === 'Enter') loadOrders(1); });

$(function () { loadOrders(1); });
</script>
@endpush

@endsection
