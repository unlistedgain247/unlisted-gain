@extends('layout.app')

@section('title', 'My Profile | UnlistedGain')
@section('meta_description', 'Manage your UnlistedGain account — update personal details and change your password.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pagecss/profile.css') }}">
@endpush

@php
    $initial     = strtoupper(mb_substr($user->name, 0, 1));
    $memberSince = $user->created_at ? $user->created_at->format('M Y') : 'N/A';
    $typeLabel   = $user->unlisted_user_type === 'channel_partner' ? 'Channel Partner' : 'Investor';

    // KYC statuses
    $bankStatus  = $user->bank_verified  ? 'verified' : ($user->bank_account_no  ? 'submitted' : 'pending');
    $dematStatus = $user->demat_verified ? 'verified' : ($user->demat_dp_id      ? 'submitted' : 'pending');
    $panStatus   = $user->user_pan_verified ? 'verified' : ($user->user_pan_no   ? 'submitted' : 'pending');

    $statusLabel = ['verified' => 'Verified', 'submitted' => 'Under Review', 'pending' => 'Not Submitted'];
@endphp

@section('content')

{{-- ── Profile Hero ── --}}
<div class="profile-hero">
    <div class="profile-hero-inner">
        <div class="profile-avatar-wrap">
            @if($user->avatar_path)
                <img src="{{ route('profile.avatar') }}" alt="{{ $user->name }}" class="profile-avatar-img">
            @else
                <div class="profile-avatar">{{ $initial }}</div>
            @endif
            <label class="avatar-upload-btn" for="avatarInput" title="Change photo">
                <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/webp" style="display:none">
        </div>
        <div class="profile-hero-info">
            <h1>{{ $user->name }}</h1>
            <p class="hero-email">{{ $user->email }}</p>
            <div class="profile-badge-row">
                <span class="profile-badge badge-member">
                    <i class="fa-solid fa-user" style="font-size:10px"></i>
                    {{ ucfirst($user->user_type) }}
                </span>
                <span class="profile-badge badge-type">{{ $typeLabel }}</span>
                <span class="profile-badge badge-since">
                    <i class="fa-regular fa-calendar" style="font-size:10px"></i>
                    Member since {{ $memberSince }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ── Body ── --}}
<div class="profile-body">

    {{-- Personal Details + Change Password — single card, two columns --}}
    <div class="profile-card">
        <div class="pf-two-col">

            {{-- Left: Personal Details --}}
            <div class="pf-section">
                <div class="pf-section-header">
                    <div class="profile-card-icon"><i class="fa-solid fa-user-pen"></i></div>
                    <h2 class="profile-card-title">Personal Details</h2>
                </div>
                <form id="profileForm" autocomplete="off">
                    @csrf
                    <div class="pf-group">
                        <label class="pf-label" for="pf_name">Full Name</label>
                        <input id="pf_name" name="name" type="text" class="pf-input"
                               value="{{ $user->name }}" maxlength="100">
                        <p class="pf-error" id="err_name"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label" for="pf_phone">Phone Number</label>
                        <input id="pf_phone" name="phone" type="tel" class="pf-input"
                               value="{{ $user->phone }}" maxlength="10" inputmode="numeric">
                        <p class="pf-error" id="err_phone"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label" for="pf_email">Email Address</label>
                        <input id="pf_email" type="email" class="pf-input"
                               value="{{ $user->email }}" readonly
                               title="Email cannot be changed. Contact support if needed.">
                    </div>
                    <button type="submit" class="pf-save-btn" id="profileSaveBtn">Save Changes</button>
                    <div class="pf-toast" id="profileToast"></div>
                </form>
            </div>

            {{-- Divider --}}
            <div class="pf-divider"></div>

            {{-- Right: Change Password --}}
            <div class="pf-section">
                <div class="pf-section-header">
                    <div class="profile-card-icon"><i class="fa-solid fa-lock"></i></div>
                    <h2 class="profile-card-title">Change Password</h2>
                </div>
                <form id="passwordForm" autocomplete="off">
                    @csrf
                    <div class="pf-group">
                        <label class="pf-label" for="pf_new_pw">New Password</label>
                        <input id="pf_new_pw" name="new_password" type="password"
                               class="pf-input" autocomplete="new-password">
                        <p class="pf-error" id="err_new_pw"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label" for="pf_conf_pw">Confirm Password</label>
                        <input id="pf_conf_pw" name="new_password_confirmation" type="password"
                               class="pf-input" autocomplete="new-password">
                        <p class="pf-error" id="err_conf_pw"></p>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Password Strength</label>
                        <div class="pf-strength-bar"><span id="strengthFill"></span></div>
                        <p id="strengthText" style="font-size:11px;color:#aaa;margin-top:5px;font-family:'Inter',sans-serif"></p>
                    </div>
                    <button type="submit" class="pf-save-btn" id="passwordSaveBtn">Update Password</button>
                    <div class="pf-toast" id="passwordToast"></div>
                </form>
            </div>

        </div>
    </div>

    {{-- KYC Section --}}
    <p class="kyc-section-title">KYC Verification</p>
    <div class="kyc-grid">

        {{-- Bank --}}
        <div class="kyc-card">
            <div class="kyc-card-top">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="kyc-card-icon" style="background:#e8f5e9">
                        <i class="fa-solid fa-building-columns" style="color:#2e7d32"></i>
                    </div>
                    <div>
                        <p class="kyc-card-label">Bank Account</p>
                        <p class="kyc-card-sub">
                            @if($user->bank_account_no)
                                ••••{{ substr($user->bank_account_no, -4) }}
                                &nbsp;·&nbsp; {{ $user->bank_name }}
                            @else
                                Not added yet
                            @endif
                        </p>
                    </div>
                </div>
                <span class="kyc-status {{ $bankStatus }}">
                    @if($bankStatus === 'verified') <i class="fa-solid fa-circle-check"></i>
                    @elseif($bankStatus === 'submitted') <i class="fa-regular fa-clock"></i>
                    @else <i class="fa-solid fa-circle-minus"></i>
                    @endif
                    {{ $statusLabel[$bankStatus] }}
                </span>
            </div>
            <button class="kyc-upload-btn" onclick="openKycModal('kycBankModal')">
                <i class="fa-solid fa-pen-to-square" style="margin-right:6px"></i>
                {{ $user->bank_account_no ? 'Update Details' : 'Add Bank Details' }}
            </button>
        </div>

        {{-- Demat --}}
        <div class="kyc-card">
            <div class="kyc-card-top">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="kyc-card-icon" style="background:#e3f2fd">
                        <i class="fa-solid fa-chart-line" style="color:#1565c0"></i>
                    </div>
                    <div>
                        <p class="kyc-card-label">Demat Account</p>
                        <p class="kyc-card-sub">
                            @if($user->demat_dp_id)
                                {{ $user->demat_dp_id }}
                                &nbsp;·&nbsp; {{ $user->demat_dp_name }}
                            @else
                                Not added yet
                            @endif
                        </p>
                    </div>
                </div>
                <span class="kyc-status {{ $dematStatus }}">
                    @if($dematStatus === 'verified') <i class="fa-solid fa-circle-check"></i>
                    @elseif($dematStatus === 'submitted') <i class="fa-regular fa-clock"></i>
                    @else <i class="fa-solid fa-circle-minus"></i>
                    @endif
                    {{ $statusLabel[$dematStatus] }}
                </span>
            </div>
            <button class="kyc-upload-btn" onclick="openKycModal('kycDematModal')">
                <i class="fa-solid fa-pen-to-square" style="margin-right:6px"></i>
                {{ $user->demat_dp_id ? 'Update Details' : 'Add Demat Details' }}
            </button>
        </div>

        {{-- PAN --}}
        <div class="kyc-card">
            <div class="kyc-card-top">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="kyc-card-icon" style="background:#fff8e1">
                        <i class="fa-solid fa-id-card" style="color:#f57f17"></i>
                    </div>
                    <div>
                        <p class="kyc-card-label">PAN Card</p>
                        <p class="kyc-card-sub">
                            @if($user->user_pan_no)
                                {{ substr($user->user_pan_no, 0, 3) }}••••{{ substr($user->user_pan_no, -3) }}
                            @else
                                Not added yet
                            @endif
                        </p>
                    </div>
                </div>
                <span class="kyc-status {{ $panStatus }}">
                    @if($panStatus === 'verified') <i class="fa-solid fa-circle-check"></i>
                    @elseif($panStatus === 'submitted') <i class="fa-regular fa-clock"></i>
                    @else <i class="fa-solid fa-circle-minus"></i>
                    @endif
                    {{ $statusLabel[$panStatus] }}
                </span>
            </div>
            <button class="kyc-upload-btn" onclick="openKycModal('kycPanModal')">
                <i class="fa-solid fa-pen-to-square" style="margin-right:6px"></i>
                {{ $user->user_pan_no ? 'Update Details' : 'Add PAN Details' }}
            </button>
        </div>

    </div>

    {{-- Account Info (read-only) --}}
    <div class="profile-card profile-info-card">
        <div class="profile-card-header">
            <div class="profile-card-icon">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <h2 class="profile-card-title">Account Information</h2>
        </div>
        <div class="profile-card-body" style="padding: 8px 24px 16px;">

            <div class="pf-info-row">
                <span class="pf-info-label">Account Type</span>
                <span class="pf-info-value">
                    <span class="pf-type-pill">{{ ucfirst($user->user_type) }}</span>
                </span>
            </div>

            <div class="pf-info-row">
                <span class="pf-info-label">Phone</span>
                <span class="pf-info-value">+91 {{ $user->phone }}</span>
            </div>

            <div class="pf-info-row">
                <span class="pf-info-label">Member Since</span>
                <span class="pf-info-value">{{ $user->created_at ? $user->created_at->format('d F Y') : 'N/A' }}</span>
            </div>

            @if(!empty(session('privilege')))
            <div class="pf-info-row">
                <span class="pf-info-label">Admin Access</span>
                <span class="pf-info-value">
                    <a href="{{ url('/admin') }}"
                       style="color:#87b942;font-weight:600;text-decoration:none;font-size:13px;">
                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;margin-right:4px"></i>
                        Open Admin Panel
                    </a>
                </span>
            </div>
            @endif

        </div>
    </div>

</div>

{{-- ── KYC Modals ── --}}
@include('public.modals.kyc-bank')
@include('public.modals.kyc-demat')
@include('public.modals.kyc-pan')

@endsection

@push('scripts')
<script>
(function () {
    var CSRF = $('meta[name="csrf-token"]').attr('content');

    // ── Avatar upload ────────────────────────────────────────
    $('#avatarInput').on('change', function () {
        var file = this.files[0];
        if (!file) return;

        var form = new FormData();
        form.append('avatar', file);
        form.append('_token', CSRF);

        $.ajax({
            url: '{{ route("profile.avatar.upload") }}',
            method: 'POST',
            data: form,
            processData: false,
            contentType: false,
            success: function () {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var $wrap = $('.profile-avatar-wrap');
                    $wrap.find('.profile-avatar, .profile-avatar-img').remove();
                    var $img = $('<img>').attr({
                        src: e.target.result,
                        alt: 'Profile Photo'
                    }).addClass('profile-avatar-img');
                    $wrap.prepend($img);
                };
                reader.readAsDataURL(file);
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.avatar)
                    ? xhr.responseJSON.errors.avatar[0]
                    : 'Upload failed. Try again.';
                alert(msg);
            }
        });
    });

    // ── Modal open / close ───────────────────────────────────
    window.openKycModal = function (id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeKycModal = function (id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    };

    // Close on backdrop click
    document.querySelectorAll('.kyc-modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeKycModal(overlay.id);
        });
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.kyc-modal-overlay.open').forEach(function (el) {
                closeKycModal(el.id);
            });
        }
    });

    // ── File upload preview ──────────────────────────────────
    window.previewUpload = function (input, boxId, nameId) {
        if (!input.files.length) return;
        var file    = input.files[0];
        var box     = document.getElementById(boxId);
        var namePEl = document.getElementById(nameId);
        box.innerHTML =
            '<i class="fa-solid fa-file-circle-check" style="color:#87b942;font-size:22px"></i>' +
            '<span class="kyc-upload-label">' + file.name + '</span>';
        namePEl.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    };

    // ── KYC form submit helper ───────────────────────────────
    function submitKycForm(formId, url, btnId, toastId, errMap, modalId) {
        $('#' + formId).on('submit', function (e) {
            e.preventDefault();
            $('.pf-error').text('').hide();
            $('#' + toastId).hide().removeClass('success error');

            var $btn = $('#' + btnId);
            $btn.prop('disabled', true).text('Saving…');

            var formData = new FormData(this);

            $.ajax({
                url:         url,
                method:      'POST',
                processData: false,
                contentType: false,
                headers:     { 'X-CSRF-TOKEN': CSRF },
                data:        formData,
            })
            .done(function (res) {
                showKycToast(toastId, 'success', res.message || 'Saved.');
                setTimeout(function () { location.reload(); }, 1400);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    var errs = xhr.responseJSON.errors || {};
                    Object.keys(errMap).forEach(function (field) {
                        if (errs[field]) {
                            $('#' + errMap[field]).text(errs[field][0]).show();
                        }
                    });
                } else {
                    showKycToast(toastId, 'error', 'Something went wrong. Please try again.');
                }
            })
            .always(function () { $btn.prop('disabled', false).text('Save'); });
        });
    }

    // ── Wire up all three KYC forms ──────────────────────────
    submitKycForm('bankKycForm', '{{ route("profile.kyc.bank") }}', 'bankKycSaveBtn', 'bankKycToast', {
        bank_holder_name:    'err_bk_holder',
        bank_name:           'err_bk_name',
        bank_account_no:     'err_bk_accno',
        bank_ifsc_code:      'err_bk_ifsc',
        bank_cancelled_check:'err_bk_check',
    });

    submitKycForm('dematKycForm', '{{ route("profile.kyc.demat") }}', 'dematKycSaveBtn', 'dematKycToast', {
        demat_dp_id:   'err_dm_dp_id',
        demat_dp_name: 'err_dm_dp_name',
        demat_cml_copy:'err_dm_cml',
    });

    submitKycForm('panKycForm', '{{ route("profile.kyc.pan") }}', 'panKycSaveBtn', 'panKycToast', {
        user_pan_no:   'err_pan_no',
        user_pan_image:'err_pan_img',
    });

    // ── Password strength ─────────────────────────────────────
    $('#pf_new_pw').on('input', function () {
        var val = $(this).val();
        var fill = $('#strengthFill');
        var text = $('#strengthText');
        var score = 0;
        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var levels = [
            { w: '0%',   bg: '#ccc',    t: '' },
            { w: '25%',  bg: '#e53935', t: 'Weak' },
            { w: '50%',  bg: '#f57f17', t: 'Fair' },
            { w: '75%',  bg: '#87b942', t: 'Good' },
            { w: '100%', bg: '#4a7c20', t: 'Strong' },
        ];
        var l = levels[Math.min(score, 4)];
        if (!val) l = levels[0];
        fill.css({ width: l.w, background: l.bg });
        text.text(l.t).css('color', l.bg);
    });

    // ── Profile form ─────────────────────────────────────────
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors(['err_name', 'err_phone']);
        hideToast('profileToast');
        var $btn = $('#profileSaveBtn');
        $btn.prop('disabled', true).text('Saving…');
        $.ajax({
            url:     '{{ route("profile.update") }}',
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            data:    { name: $('#pf_name').val(), phone: $('#pf_phone').val() },
        })
        .done(function (res)  { showToast('profileToast', 'success', res.message || 'Saved.'); })
        .fail(function (xhr)  {
            if (xhr.status === 422) {
                var errs = xhr.responseJSON.errors || {};
                if (errs.name)  showFieldError('err_name',  'pf_name',  errs.name[0]);
                if (errs.phone) showFieldError('err_phone', 'pf_phone', errs.phone[0]);
            } else {
                showToast('profileToast', 'error', 'Something went wrong.');
            }
        })
        .always(function () { $btn.prop('disabled', false).text('Save Changes'); });
    });

    // ── Password form ─────────────────────────────────────────
    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors(['err_new_pw', 'err_conf_pw']);
        hideToast('passwordToast');
        var $btn = $('#passwordSaveBtn');
        $btn.prop('disabled', true).text('Updating…');
        $.ajax({
            url:     '{{ route("profile.password") }}',
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            data:    {
                new_password:              $('#pf_new_pw').val(),
                new_password_confirmation: $('#pf_conf_pw').val(),
            },
        })
        .done(function (res) {
            showToast('passwordToast', 'success', res.message || 'Password updated.');
            $('#passwordForm')[0].reset();
        })
        .fail(function (xhr) {
            if (xhr.status === 422) {
                var errs = xhr.responseJSON.errors || {};
                if (errs.new_password) showFieldError('err_new_pw', 'pf_new_pw', errs.new_password[0]);
            } else {
                showToast('passwordToast', 'error', 'Something went wrong.');
            }
        })
        .always(function () { $btn.prop('disabled', false).text('Update Password'); });
    });

    // ── Helpers ───────────────────────────────────────────────
    function showFieldError(errId, inputId, msg) {
        $('#' + errId).text(msg).show();
        $('#' + inputId).addClass('is-error');
    }

    function clearErrors(errIds) {
        errIds.forEach(function (id) { $('#' + id).text('').hide(); });
        $('.pf-input').removeClass('is-error');
    }

    function showToast(id, type, msg) {
        var icon = type === 'success'
            ? '<i class="fa-solid fa-circle-check"></i>'
            : '<i class="fa-solid fa-circle-exclamation"></i>';
        $('#' + id).removeClass('success error').addClass(type).html(icon + ' ' + msg).show();
    }

    function hideToast(id) { $('#' + id).hide().removeClass('success error'); }

    function showKycToast(id, type, msg) {
        var icon = type === 'success'
            ? '<i class="fa-solid fa-circle-check"></i>'
            : '<i class="fa-solid fa-circle-exclamation"></i>';
        $('#' + id).removeClass('success error').addClass(type).html(icon + ' ' + msg).css('display','flex');
    }
}());
</script>
@endpush
