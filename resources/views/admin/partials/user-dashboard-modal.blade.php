@push('styles')
<style>
/* ── User Dashboard Modal overlay ───────────────────────────────────────── */
#udmOverlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 9500;
    align-items: flex-start;
    justify-content: center;
    padding: 30px 16px;
    overflow-y: auto;
}
#udmOverlay.open { display: flex; }
#udmOverlay .udm-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 900px;
    box-shadow: 0 12px 48px rgba(0,0,0,.22);
    flex-shrink: 0;
    margin: auto;
    overflow: hidden;
}
#udmOverlay .udm-modal-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f0f0;
    flex-wrap: wrap;
    gap: 8px;
}
#udmOverlay .udm-modal-hdr h5 { font-size: 15px; font-weight: 700; color: #111; margin: 0; }
#udmOverlay .udm-close-btn {
    background: none;
    border: none;
    font-size: 22px;
    color: #9ca3af;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    margin-left: auto;
}
#udmOverlay .udm-close-btn:hover { color: #374151; }
#udmOverlay .udm-modal-body {
    padding: 16px 20px;
    max-height: 68vh;
    overflow-y: auto;
}
.udm-tab-btn {
    padding: 5px 12px;
    border-radius: 5px;
    border: 1.5px solid #d1d5db;
    background: #fff;
    color: #374151;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}
.udm-tab-btn:hover  { border-color: #2b80b9; color: #2b80b9; }
.udm-tab-btn.active { background: #2b80b9; color: #fff; border-color: #2b80b9; }
</style>
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
