<div id="udmWithdrawMsg" style="display:none;margin-bottom:14px;padding:10px 14px;border-radius:7px;font-size:13px;font-weight:500;"></div>

{{-- Cash Section (default) --}}
<div id="udmWtCashSection">
    <div style="display:flex;align-items:flex-end;gap:10px;margin-bottom:14px;">
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Amount</label>
            <input type="number" id="udmWithdrawAmount" step="0.01" min="1"
                style="width:220px;padding:8px 12px;border:1.5px solid #d1d5db;border-radius:6px;font-size:13px;outline:none;"
                onfocus="this.style.borderColor='#2b80b9'" onblur="this.style.borderColor='#d1d5db'"
                placeholder="Enter amount">
        </div>
        <button onclick="udmSaveWithdraw()"
            style="padding:8px 22px;background:#2563eb;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;height:38px;">
            <span id="udmWithdrawSpinner" class="spinner-border spinner-border-sm d-none"></span>
            Submit
        </button>
    </div>
</div>

{{-- Shares Section (triggered from Demat tab Transfer button) --}}
<div id="udmWtSharesSection" style="display:none;">
    <input type="hidden" id="udmWtFincode">
    <input type="hidden" id="udmWtSName">
    <div style="margin-bottom:12px;">
        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Company</label>
        <p id="udmWtCompanyName" style="font-weight:700;font-size:14px;color:#111;margin:0;"></p>
    </div>
    <div style="display:flex;align-items:flex-end;gap:10px;margin-bottom:14px;">
        <div>
            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">QTY</label>
            <input type="number" id="udmWithdrawQty" step="1" min="1"
                style="width:220px;padding:8px 12px;border:1.5px solid #d1d5db;border-radius:6px;font-size:13px;outline:none;"
                onfocus="this.style.borderColor='#2b80b9'" onblur="this.style.borderColor='#d1d5db'"
                placeholder="Enter quantity">
        </div>
        <button onclick="udmSaveWithdraw()"
            style="padding:8px 22px;background:#2563eb;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;height:38px;">
            <span id="udmWithdrawSpinnerShares" class="spinner-border spinner-border-sm d-none"></span>
            Submit
        </button>
    </div>
    <a href="javascript:void(0)" onclick="udmSwitchWithdrawType('Cash')" style="font-size:12px;color:#6b7280;">← Switch to Cash Withdrawal</a>
</div>

<hr style="border:none;border-top:1px solid #f0f0f0;margin:16px 0;">
<p id="udmWtInfoText" style="font-size:13px;color:#6b7280;text-align:center;margin:0;">
    Your request will be logged and the money will be transferred within the next working day.
</p>

<script>
function udmSwitchWithdrawType(type) {
    if (type === 'Cash') {
        $('#udmWtCashSection').show();
        $('#udmWtSharesSection').hide();
        $('#udmWtInfoText').text('Your request will be logged and the money will be transferred within the next working day.');
    } else {
        $('#udmWtCashSection').hide();
        $('#udmWtSharesSection').show();
        $('#udmWtInfoText').text('Your shares will be transferred to your demat account within the next working day once your request is raised.');
    }
}
</script>
