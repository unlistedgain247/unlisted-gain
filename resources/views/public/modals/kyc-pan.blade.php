<div class="kyc-modal-overlay" id="kycPanModal">
    <div class="kyc-modal">

        <div class="kyc-modal-header">
            <div class="kyc-modal-icon" style="background:#fff8e1">
                <i class="fa-solid fa-id-card" style="color:#f57f17"></i>
            </div>
            <div>
                <h3 class="kyc-modal-title">PAN Card Details</h3>
                <p class="kyc-modal-sub">Mandatory for tax compliance</p>
            </div>
            <button class="kyc-modal-close" onclick="closeKycModal('kycPanModal')" type="button">&times;</button>
        </div>

        <form id="panKycForm" enctype="multipart/form-data">
            @csrf

            <div class="kyc-modal-body">

                <div class="pf-group">
                    <label class="pf-label" for="pan_no">PAN Number</label>
                    <input id="pan_no" name="user_pan_no" type="text" class="pf-input"
                           value="{{ $user->user_pan_no ?? '' }}" placeholder="e.g. ABCDE1234F"
                           maxlength="10" style="text-transform:uppercase;letter-spacing:2px;font-weight:600">
                    <p style="font-size:11px;color:#aaa;margin-top:4px;font-family:'Inter',sans-serif">
                        Format: 5 letters + 4 digits + 1 letter (e.g. ABCDE1234F)
                    </p>
                    <p class="pf-error" id="err_pan_no"></p>
                </div>

                <div class="pf-group">
                    <label class="pf-label" for="pan_img">PAN Card Image
                        <span style="font-weight:400;text-transform:none;color:#aaa">(JPG / PNG / PDF, max 5 MB)</span>
                    </label>
                    <div class="kyc-upload-box" id="panImgBox" onclick="document.getElementById('pan_img').click()">
                        @if($user->user_pan_image)
                            <i class="fa-solid fa-file-circle-check" style="color:#87b942;font-size:22px"></i>
                            <span class="kyc-upload-label">File uploaded — click to replace</span>
                        @else
                            <i class="fa-solid fa-cloud-arrow-up" style="color:#aaa;font-size:22px"></i>
                            <span class="kyc-upload-label">Click to upload PAN card image</span>
                        @endif
                    </div>
                    <input id="pan_img" name="user_pan_image" type="file"
                           accept=".jpg,.jpeg,.png,.pdf" style="display:none"
                           onchange="previewUpload(this,'panImgBox','panImgName')">
                    <p class="kyc-file-name" id="panImgName">
                        @if($user->user_pan_image)
                            {{ basename($user->user_pan_image) }}
                        @endif
                    </p>
                    <p class="pf-error" id="err_pan_img"></p>
                </div>

            </div>

            <div class="kyc-modal-footer">
                <button type="button" class="kyc-cancel-btn" onclick="closeKycModal('kycPanModal')">Cancel</button>
                <button type="submit" class="pf-save-btn" id="panKycSaveBtn"
                        style="width:auto;padding:10px 32px;margin:0">Save PAN Details</button>
            </div>

            <div class="pf-toast" id="panKycToast" style="margin:0 24px 16px"></div>
        </form>

    </div>
</div>
