<div class="kyc-modal-overlay" id="kycBankModal">
    <div class="kyc-modal">

        <div class="kyc-modal-header">
            <div class="kyc-modal-icon" style="background:#e8f5e9">
                <i class="fa-solid fa-building-columns" style="color:#2e7d32"></i>
            </div>
            <div>
                <h3 class="kyc-modal-title">Bank Details</h3>
                <p class="kyc-modal-sub">For smooth transactions &amp; settlements</p>
            </div>
            <button class="kyc-modal-close" onclick="closeKycModal('kycBankModal')" type="button">&times;</button>
        </div>

        <form id="bankKycForm" enctype="multipart/form-data">
            @csrf

            <div class="kyc-modal-body">

                <div class="kyc-field-row">
                    <div class="pf-group">
                        <label class="pf-label" for="bk_holder">Account Holder Name</label>
                        <input id="bk_holder" name="bank_holder_name" type="text" class="pf-input"
                               value="{{ $user->bank_holder_name ?? '' }}" placeholder="As per bank records">
                        <p class="pf-error" id="err_bk_holder"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label" for="bk_name">Bank Name</label>
                        <input id="bk_name" name="bank_name" type="text" class="pf-input"
                               value="{{ $user->bank_name ?? '' }}" placeholder="e.g. HDFC Bank">
                        <p class="pf-error" id="err_bk_name"></p>
                    </div>
                </div>

                <div class="kyc-field-row">
                    <div class="pf-group">
                        <label class="pf-label" for="bk_accno">Account Number</label>
                        <input id="bk_accno" name="bank_account_no" type="text" class="pf-input"
                               value="{{ $user->bank_account_no ?? '' }}" placeholder="Enter account number"
                               inputmode="numeric" maxlength="30">
                        <p class="pf-error" id="err_bk_accno"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label" for="bk_ifsc">IFSC Code</label>
                        <input id="bk_ifsc" name="bank_ifsc_code" type="text" class="pf-input"
                               value="{{ $user->bank_ifsc_code ?? '' }}" placeholder="e.g. HDFC0001234"
                               maxlength="11" style="text-transform:uppercase">
                        <p class="pf-error" id="err_bk_ifsc"></p>
                    </div>
                </div>

                <div class="pf-group">
                    <label class="pf-label" for="bk_check">Cancelled Cheque
                        <span style="font-weight:400;text-transform:none;color:#aaa">(JPG / PNG / PDF, max 5 MB)</span>
                    </label>
                    <div class="kyc-upload-box" id="bkCheckBox" onclick="document.getElementById('bk_check').click()">
                        @if($user->bank_cancelled_check)
                            <i class="fa-solid fa-file-circle-check" style="color:#87b942;font-size:22px"></i>
                            <span class="kyc-upload-label">File uploaded — click to replace</span>
                        @else
                            <i class="fa-solid fa-cloud-arrow-up" style="color:#aaa;font-size:22px"></i>
                            <span class="kyc-upload-label">Click to upload cancelled cheque</span>
                        @endif
                    </div>
                    <input id="bk_check" name="bank_cancelled_check" type="file"
                           accept=".jpg,.jpeg,.png,.pdf" style="display:none"
                           onchange="previewUpload(this,'bkCheckBox','bkCheckName')">
                    <p class="kyc-file-name" id="bkCheckName">
                        @if($user->bank_cancelled_check)
                            {{ basename($user->bank_cancelled_check) }}
                        @endif
                    </p>
                    <p class="pf-error" id="err_bk_check"></p>
                </div>

            </div>

            <div class="kyc-modal-footer">
                <button type="button" class="kyc-cancel-btn" onclick="closeKycModal('kycBankModal')">Cancel</button>
                <button type="submit" class="pf-save-btn" id="bankKycSaveBtn"
                        style="width:auto;padding:10px 32px;margin:0">Save Bank Details</button>
            </div>

            <div class="pf-toast" id="bankKycToast" style="margin:0 24px 16px"></div>
        </form>

    </div>
</div>
