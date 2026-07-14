@php
    $wa  = $restriction ? $restriction->whatsapp : 1;
    $em  = $restriction ? $restriction->email    : 1;
    $sms = $restriction ? $restriction->sms      : 1;
@endphp
<div style="max-width:380px;margin:0 auto;padding:8px 0;">
    <p style="font-size:12px;color:#6b7280;margin-bottom:16px;">
        Enable or disable communication channels for this user. Unchecked = restricted.
    </p>

    <div id="udmCommMsg" style="display:none;margin-bottom:12px;padding:10px 14px;border-radius:7px;font-size:13px;font-weight:500;"></div>

    <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:20px;">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="udmCommWhatsapp" {{ $wa ? 'checked' : '' }}>
            <label class="form-check-label" for="udmCommWhatsapp" style="font-size:13px;font-weight:500;">
                <i class="fa-brands fa-whatsapp" style="color:#25D366;margin-right:6px;"></i> WhatsApp
            </label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="udmCommEmail" {{ $em ? 'checked' : '' }}>
            <label class="form-check-label" for="udmCommEmail" style="font-size:13px;font-weight:500;">
                <i class="fa-regular fa-envelope" style="color:#2b80b9;margin-right:6px;"></i> Email
            </label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="udmCommSms" {{ $sms ? 'checked' : '' }}>
            <label class="form-check-label" for="udmCommSms" style="font-size:13px;font-weight:500;">
                <i class="fa-solid fa-message" style="color:#6366f1;margin-right:6px;"></i> SMS
            </label>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-primary btn-sm" onclick="udmSaveCommunication()">
            <span id="udmCommSpinner" class="spinner-border spinner-border-sm d-none"></span>
            Save Preferences
        </button>
    </div>
</div>
