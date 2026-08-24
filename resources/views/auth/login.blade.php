@extends('layout.app')

@section('title', 'Sign In | UnlistedGain')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pagecss/auth.css') }}?v={{ filemtime(public_path('assets/css/pagecss/auth.css')) }}">
@endpush

@section('content')
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Welcome Back</h1>
            <p class="auth-card__subtitle">Sign in to your account</p>
        </div>

        <div class="auth-tabs" id="loginTabs">
            <a href="#" class="auth-tab active" data-login-tab="password">Phone &amp; Password</a>
            <a href="#" class="auth-tab" data-login-tab="otp">Email OTP</a>
        </div>

        <div id="loginAlert" class="auth-alert"></div>

        <form id="loginForm" novalidate>
            @csrf
            {{-- Honeypot: bots fill this, humans never see it --}}
            <input type="text" name="_hp" value="" style="display:none!important" tabindex="-1" autocomplete="off" aria-hidden="true">

            {{-- Phone + Password tab --}}
            <div class="auth-field" data-login-panel="password">
                <label for="inputPhone" class="auth-label">Phone Number</label>
                <input type="tel" class="auth-input" id="inputPhone" placeholder="9811333333">
                <div class="auth-error" id="errPhone">Please enter a valid 10-digit phone number.</div>
            </div>
            <div class="auth-field" data-login-panel="password">
                <label for="inputChoosePassword" class="auth-label">Password</label>
                <input type="password" class="auth-input" id="inputChoosePassword" placeholder="Enter Password">
                <div class="auth-error" id="errPassword">Password is required.</div>
            </div>
            <div class="auth-field" data-login-panel="password">
                <button type="submit" id="loginBtn" class="auth-btn auth-btn-primary">
                    <i class="fa-solid fa-lock-open"></i> Sign In
                </button>
            </div>

            {{-- Email OTP tab --}}
            <div class="auth-field" data-login-panel="otp" style="display:none">
                <label for="inputOtpEmail" class="auth-label">Email Address</label>
                <input type="email" class="auth-input" id="inputOtpEmail" placeholder="example@user.com">
                <div class="auth-error" id="errOtpEmail">Please enter a valid email address.</div>
            </div>
            <div class="auth-field" data-login-panel="otp" data-otp-stage="request" style="display:none">
                <button type="button" id="loginSendOtpBtn" class="auth-btn auth-btn-primary">
                    <i class="fa-solid fa-envelope"></i> Send OTP
                </button>
            </div>
            <div class="auth-field" data-login-panel="otp" data-otp-stage="verify" style="display:none">
                <label for="inputLoginOtp" class="auth-label">Enter the OTP sent to your email</label>
                <input type="text" class="auth-input" id="inputLoginOtp" placeholder="6-digit code" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
                <div class="auth-error" id="errLoginOtp">Please enter the 6-digit code.</div>
                <div class="auth-otp-meta">
                    <a href="#" id="changeLoginEmailLink">Change email</a>
                    &nbsp;&middot;&nbsp;
                    <a href="#" id="resendLoginOtpLink">Resend OTP</a>
                    <span id="resendLoginTimer"></span>
                </div>
            </div>
            <div class="auth-field" data-login-panel="otp" data-otp-stage="verify" style="display:none">
                <button type="button" id="verifyLoginOtpBtn" class="auth-btn auth-btn-primary">
                    <i class="fa-solid fa-shield-halved"></i> Verify &amp; Sign In
                </button>
            </div>

            <p class="auth-links">Don't have an account? <a href="{{ route('register') }}">Sign up here</a></p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {

        // --- Helpers ---
        function clearErrors() {
            $('.auth-error').hide();
            $('.auth-input').removeClass('is-invalid');
            $('#loginAlert').hide().removeClass('alert-danger alert-success').text('');
        }

        function showError(inputId, errId, msg) {
            $('#' + inputId).addClass('is-invalid');
            $('#' + errId).text(msg).show();
        }

        function isValidEmail(val) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
        }

        function isValidPhone(val) {
            return /^[6-9]\d{9}$/.test(val.replace(/[\s\+\-]/g, ''));
        }

        // --- Tabs ---
        var activeLoginTab = 'password';

        $('[data-login-tab]').on('click', function (e) {
            e.preventDefault();
            var tab = $(this).data('login-tab');
            if (tab === activeLoginTab) return;

            activeLoginTab = tab;
            clearErrors();
            $('[data-login-tab]').removeClass('active');
            $(this).addClass('active');
            $('[data-login-panel]').hide();
            $('[data-login-panel="' + tab + '"]').show();

            if (tab === 'otp') {
                $('[data-login-panel="otp"][data-otp-stage="verify"]').hide();
                $('[data-login-panel="otp"][data-otp-stage="request"]').show();
            }
        });

        // --- Phone + Password submit ---
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();

            if (activeLoginTab !== 'password') {
                // Guards against an implicit Enter-key submit reaching the
                // (hidden) password-tab Sign In button while on the OTP tab.
                return;
            }

            clearErrors();

            var phone      = $.trim($('#inputPhone').val());
            var password   = $('#inputChoosePassword').val();
            var valid      = true;

            if (!phone || !isValidPhone(phone)) {
                showError('inputPhone', 'errPhone', 'Please enter a valid 10-digit phone number.');
                valid = false;
            }

            if (!password) {
                showError('inputChoosePassword', 'errPassword', 'Password is required.');
                valid = false;
            }

            if (!valid) return;

            var btn = $('#loginBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Signing in...');

            $.ajax({
                url: '{{ route("login.post") }}',
                method: 'POST',
                data: {
                    login_type: 'phone',
                    phone:      phone,
                    password:   password
                },
                success: function (res) {
                    if (res.success) {
                        $('#loginAlert').addClass('alert-success').text(res.message).show();
                        if (res.redirect) window.location.href = res.redirect;
                    } else {
                        $('#loginAlert').addClass('alert-danger').text(res.message).show();
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;
                    if (errors) {
                        if (errors.phone) showError('inputPhone', 'errPhone', errors.phone[0]);
                        if (errors.password) showError('inputChoosePassword', 'errPassword', errors.password[0]);
                    } else {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Something went wrong. Please try again.';
                        $('#loginAlert').addClass('alert-danger').text(msg).show();
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-lock-open"></i> Sign In');
                }
            });
        });

        // --- Email OTP tab ---
        var loginResendSeconds = 0;
        var loginResendTimerId = null;

        function startLoginResendCooldown(seconds) {
            loginResendSeconds = seconds;
            clearInterval(loginResendTimerId);
            $('#resendLoginOtpLink').addClass('disabled').css('pointer-events', 'none');
            loginResendTimerId = setInterval(function () {
                loginResendSeconds--;
                if (loginResendSeconds <= 0) {
                    clearInterval(loginResendTimerId);
                    $('#resendLoginTimer').text('');
                    $('#resendLoginOtpLink').removeClass('disabled').css('pointer-events', '');
                } else {
                    $('#resendLoginTimer').text('(' + loginResendSeconds + 's)');
                }
            }, 1000);
        }

        function sendLoginOtp() {
            clearErrors();

            var email = $.trim($('#inputOtpEmail').val());
            if (!email || !isValidEmail(email)) {
                showError('inputOtpEmail', 'errOtpEmail', 'Please enter a valid email address.');
                return;
            }

            var btn = $('#loginSendOtpBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Sending...');

            $.ajax({
                url: '{{ route("login.send-otp") }}',
                method: 'POST',
                data: { email: email },
                success: function (res) {
                    if (res.success) {
                        $('#loginAlert').addClass('alert-success').text(res.message).show();
                        $('[data-login-panel="otp"][data-otp-stage="request"]').hide();
                        $('[data-login-panel="otp"][data-otp-stage="verify"]').show();
                        $('#inputOtpEmail').prop('readonly', true);
                        $('#inputLoginOtp').val('').focus();
                        startLoginResendCooldown(30);
                    } else {
                        $('#loginAlert').addClass('alert-danger').text(res.message).show();
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;
                    if (errors && errors.email) {
                        showError('inputOtpEmail', 'errOtpEmail', errors.email[0]);
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
                        $('#loginAlert').addClass('alert-danger').text(msg).show();
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-envelope"></i> Send OTP');
                }
            });
        }

        $('#loginSendOtpBtn').on('click', sendLoginOtp);

        $('#resendLoginOtpLink').on('click', function (e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;
            sendLoginOtp();
        });

        $('#changeLoginEmailLink').on('click', function (e) {
            e.preventDefault();
            clearInterval(loginResendTimerId);
            $('[data-login-panel="otp"][data-otp-stage="verify"]').hide();
            $('[data-login-panel="otp"][data-otp-stage="request"]').show();
            $('#inputOtpEmail').prop('readonly', false);
            $('#inputLoginOtp').val('');
        });

        $('#verifyLoginOtpBtn').on('click', function () {
            $('#errLoginOtp').hide();
            $('#inputLoginOtp').removeClass('is-invalid');
            $('#loginAlert').hide();

            var email = $.trim($('#inputOtpEmail').val());
            var otp   = $.trim($('#inputLoginOtp').val());

            if (!/^\d{6}$/.test(otp)) {
                showError('inputLoginOtp', 'errLoginOtp', 'Please enter the 6-digit code.');
                return;
            }

            var btn = $('#verifyLoginOtpBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Verifying...');

            $.ajax({
                url: '{{ route("login.verify-otp") }}',
                method: 'POST',
                data: { email: email, otp: otp },
                success: function (res) {
                    if (res.success) {
                        clearInterval(loginResendTimerId);
                        $('#loginAlert').addClass('alert-success').text(res.message).show();
                        if (res.redirect) window.location.href = res.redirect;
                    } else {
                        showError('inputLoginOtp', 'errLoginOtp', res.message);
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
                    showError('inputLoginOtp', 'errLoginOtp', msg);
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-shield-halved"></i> Verify & Sign In');
                }
            });
        });

    });
    </script>
@endpush
