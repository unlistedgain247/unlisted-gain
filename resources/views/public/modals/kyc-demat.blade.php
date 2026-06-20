<div class="kyc-modal-overlay" id="kycDematModal">
    <div class="kyc-modal">

        <div class="kyc-modal-header">
            <div class="kyc-modal-icon" style="background:#e3f2fd">
                <i class="fa-solid fa-chart-line" style="color:#1565c0"></i>
            </div>
            <div>
                <h3 class="kyc-modal-title">Demat Account Details</h3>
                <p class="kyc-modal-sub">Required for share transfers</p>
            </div>
            <button class="kyc-modal-close" onclick="closeKycModal('kycDematModal')" type="button">&times;</button>
        </div>

        <form id="dematKycForm" enctype="multipart/form-data">
            @csrf

            <div class="kyc-modal-body">

                <div class="kyc-field-row">
                    <div class="pf-group">
                        <label class="pf-label" for="dm_dp_id">BO ID</label>
                        <input id="dm_dp_id" name="demat_dp_id" type="text" class="pf-input"
                               value="{{ $user->demat_dp_id ?? '' }}" placeholder="e.g. IN301234">
                        <p class="pf-error" id="err_dm_dp_id"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label" for="dm_dp_name">Depository Participant (DP) Name</label>
                        <input id="dm_dp_name" name="demat_dp_name" type="text" class="pf-input"
                               value="{{ $user->demat_dp_name ?? '' }}" placeholder="e.g. Zerodha, HDFC Sec">
                        <p class="pf-error" id="err_dm_dp_name"></p>
                    </div>
                </div>

                <div class="pf-group">
                    <label class="pf-label" for="dm_cml">CML Copy
                        <span style="font-weight:400;text-transform:none;color:#aaa">(JPG / PNG / PDF, max 5 MB)</span>
                    </label>
                    <div class="kyc-upload-box" id="dmCmlBox" onclick="document.getElementById('dm_cml').click()">
                        @if($user->demat_cml_copy)
                            <i class="fa-solid fa-file-circle-check" style="color:#87b942;font-size:22px"></i>
                            <span class="kyc-upload-label">File uploaded — click to replace</span>
                        @else
                            <i class="fa-solid fa-cloud-arrow-up" style="color:#aaa;font-size:22px"></i>
                            <span class="kyc-upload-label">Click to upload CML copy</span>
                        @endif
                    </div>
                    <input id="dm_cml" name="demat_cml_copy" type="file"
                           accept=".jpg,.jpeg,.png,.pdf" style="display:none"
                           onchange="previewUpload(this,'dmCmlBox','dmCmlName')">
                    <p class="kyc-file-name" id="dmCmlName">
                        @if($user->demat_cml_copy)
                            {{ basename($user->demat_cml_copy) }}
                        @endif
                    </p>
                    <p class="pf-error" id="err_dm_cml"></p>
                </div>

            </div>

            <div class="kyc-modal-footer">
                <button type="button" class="kyc-cancel-btn" onclick="closeKycModal('kycDematModal')">Cancel</button>
                <button type="submit" class="pf-save-btn" id="dematKycSaveBtn"
                        style="width:auto;padding:10px 32px;margin:0">Save Demat Details</button>
            </div>

            <div class="pf-toast" id="dematKycToast" style="margin:0 24px 16px"></div>
        </form>

    </div>
</div>
