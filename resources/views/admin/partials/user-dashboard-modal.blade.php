@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/user-dashboard-modal.css') }}?v={{ filemtime(public_path('assets/css/admin/user-dashboard-modal.css')) }}">
@endpush

{{-- ══ User Dashboard Modal ══════════════════════════════════════════════════ --}}
<div id="udmOverlay">
<div class="udm-modal">

    {{-- Header --}}
    <div class="udm-modal-hdr">
        <h5 id="udmTitle">Dashboard</h5>
        <span id="udmBalance" style="background:#f59e0b;color:#fff;font-size:12px;padding:4px 12px;border-radius:20px;font-weight:600;display:none;"></span>
        <button class="udm-close-btn" onclick="document.getElementById('udmOverlay').classList.remove('open')">&times;</button>
    </div>

    {{-- Tab Buttons --}}
    <div id="udmTabs" style="padding:10px 18px;border-bottom:1px solid #f0f0f0;display:flex;gap:6px;flex-wrap:wrap;background:#fafafa;">
        <button class="udm-tab-btn" data-tab="orders"          onclick="udmLoadTab('orders')">Orders</button>
        <button class="udm-tab-btn" data-tab="demat"           onclick="udmLoadTab('demat')">Demat</button>
        <button class="udm-tab-btn" data-tab="portfolio"       onclick="udmLoadTab('portfolio')">Portfolio</button>
        <button class="udm-tab-btn" data-tab="transactions"    onclick="udmLoadTab('transactions')">Transactions</button>
        <button class="udm-tab-btn" data-tab="request-history" onclick="udmLoadTab('request-history')">Request History</button>
        <button class="udm-tab-btn" data-tab="bank-demat"      onclick="udmLoadTab('bank-demat')">Bank &amp; Demat</button>
        <button class="udm-tab-btn" data-tab="withdraw"        onclick="udmLoadTab('withdraw')">Withdraw Money</button>
        <button class="udm-tab-btn" data-tab="communication"   onclick="udmLoadTab('communication')">Communication restriction</button>
    </div>

    {{-- Content Area --}}
    <div class="udm-modal-body" id="udmContent" style="min-height:320px;">
        <div style="text-align:center;padding:60px;color:#9ca3af;">
            <i class="fa fa-spinner fa-spin" style="font-size:24px;"></i>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
var UDM_UID              = 0;
var UDM_BASE_URL         = '{{ url("/admin/users") }}';
var UDM_CSRF             = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var UDM_PENDING_WITHDRAW = null;

function openUserDashboard(uid, name) {
    UDM_UID = uid;
    $('#udmTitle').text('Dashboard - ' + uid + ' - ' + (name || ''));
    $('#udmBalance').hide().text('');
    $('.udm-tab-btn').removeClass('active');
    document.getElementById('udmOverlay').classList.add('open');
    udmLoadTab('profile');
}

function udmLoadTab(tab) {
    $('.udm-tab-btn').removeClass('active');
    $('.udm-tab-btn[data-tab="' + tab + '"]').addClass('active');

    $('#udmContent').html('<div style="text-align:center;padding:60px;color:#9ca3af;"><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i></div>');

    var url = UDM_BASE_URL + '/' + UDM_UID + '/dashboard';
    if (tab !== 'profile') url += '/' + tab;

    $.get(url)
        .done(function (html) {
            $('#udmContent').html(html);
            if (tab === 'profile') {
                var bal = parseFloat($('#udmBalanceData').data('balance') || 0);
                if (!isNaN(bal)) {
                    $('#udmBalance')
                        .text('Balance : Rs.' + bal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' /-')
                        .show();
                }
            }
            if (tab === 'withdraw' && UDM_PENDING_WITHDRAW) {
                udmApplyWithdrawData(UDM_PENDING_WITHDRAW);
                UDM_PENDING_WITHDRAW = null;
            }
        })
        .fail(function () {
            $('#udmContent').html('<div style="color:#b91c1c;padding:24px;font-size:13px;">Failed to load data. Please try again.</div>');
        });
}

function udmSaveCommunication() {
    var spinner = $('#udmCommSpinner');
    var msg     = $('#udmCommMsg');
    spinner.removeClass('d-none');
    msg.hide();

    $.ajax({
        type: 'POST',
        url:  UDM_BASE_URL + '/' + UDM_UID + '/dashboard/communication',
        headers: { 'X-CSRF-TOKEN': UDM_CSRF },
        data: {
            whatsapp: $('#udmCommWhatsapp').is(':checked') ? 1 : 0,
            email:    $('#udmCommEmail').is(':checked')    ? 1 : 0,
            sms:      $('#udmCommSms').is(':checked')      ? 1 : 0,
        },
        dataType: 'json',
        success: function (r) {
            spinner.addClass('d-none');
            msg.css({ background: r.success ? '#d1fae5' : '#fee2e2', color: r.success ? '#065f46' : '#b91c1c' })
               .text(r.message).show();
        },
        error: function () { spinner.addClass('d-none'); alert('Server error'); }
    });
}

function udmInitWithdraw(type, fincode, qty, sname) {
    UDM_PENDING_WITHDRAW = { type: type, fincode: fincode, qty: qty, sname: sname };
    udmLoadTab('withdraw');
}

function udmApplyWithdrawData(data) {
    if (data.type === 'Shares') {
        $('#udmWtCompanyName').text(data.sname);
        $('#udmWtFincode').val(data.fincode);
        $('#udmWtSName').val(data.sname);
        $('#udmWithdrawQty').val(data.qty);
        if (typeof udmSwitchWithdrawType === 'function') udmSwitchWithdrawType('Shares');
    }
}

function udmSaveWithdraw() {
    var isShares = $('#udmWtSharesSection').is(':visible');
    var type     = isShares ? 'Shares' : 'Cash';
    var spinner  = isShares ? $('#udmWithdrawSpinnerShares') : $('#udmWithdrawSpinner');
    var msg      = $('#udmWithdrawMsg');
    var payload  = { type: type };

    if (!isShares) {
        var amt = parseFloat($('#udmWithdrawAmount').val());
        if (!amt || amt <= 0) { alert('Enter a valid amount'); return; }
        payload.amount = amt;
    } else {
        var fincode = parseInt($('#udmWtFincode').val());
        var qty     = parseFloat($('#udmWithdrawQty').val());
        if (!fincode || fincode <= 0) { alert('Invalid stock'); return; }
        if (!qty || qty <= 0)         { alert('Enter a valid quantity'); return; }
        payload.fincode = fincode;
        payload.qty     = qty;
    }

    spinner.removeClass('d-none');
    msg.hide();

    $.ajax({
        type: 'POST',
        url:  UDM_BASE_URL + '/' + UDM_UID + '/dashboard/withdraw',
        headers: { 'X-CSRF-TOKEN': UDM_CSRF },
        data: payload,
        dataType: 'json',
        success: function (r) {
            spinner.addClass('d-none');
            msg.css({ background: r.success ? '#d1fae5' : '#fee2e2', color: r.success ? '#065f46' : '#b91c1c' })
               .text(r.message).show();
            if (r.success) setTimeout(function () { udmLoadTab('request-history'); }, 1200);
        },
        error: function () { spinner.addClass('d-none'); alert('Server error'); }
    });
}

function udmCancelRequest(requestId) {
    if (!confirm('You are about to cancel request ID ' + requestId + '. Please confirm')) return;
    $.ajax({
        type: 'POST',
        url:  UDM_BASE_URL + '/' + UDM_UID + '/dashboard/request-history/' + requestId + '/cancel',
        headers: { 'X-CSRF-TOKEN': UDM_CSRF },
        dataType: 'json',
        success: function (r) {
            if (r.success) {
                udmLoadTab('request-history');
            } else {
                alert(r.message);
            }
        },
        error: function () { alert('Server error'); }
    });
}

$(document).ready(function () {
    $('#udmOverlay').on('click', function (e) {
        if (e.target === this) this.classList.remove('open');
    });
});
</script>
@endpush
