@extends('layout.app')

@section('title', 'Sign Up | UnlistedGain')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pagecss/auth.css') }}?v={{ filemtime(public_path('assets/css/pagecss/auth.css')) }}">
@endpush

@section('content')
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Welcome</h1>
            <p class="auth-card__subtitle">Create your new account</p>
        </div>

        <div id="registerAlert" class="auth-alert"></div>

        <form id="registerForm" novalidate>
            @csrf
            {{-- Honeypot: bots fill this, humans never see it --}}
            <input type="text" name="_hp" value="" style="display:none!important" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="landing_page" value="{{ $landingPage ?? '' }}">

            <div class="auth-field" data-reg-step="1">
                <label for="inputName" class="auth-label">Full Name</label>
                <input type="text" class="auth-input" id="inputName" placeholder="John Doe">
                <div class="auth-error" id="errName">Full name is required.</div>
            </div>
            <div class="auth-field" data-reg-step="1">
                <label for="inputEmailAddress" class="auth-label">Email Address</label>
                <input type="email" class="auth-input" id="inputEmailAddress" placeholder="example@user.com">
                <div class="auth-error" id="errEmail">Please enter a valid email address.</div>
            </div>
            <div class="auth-field" data-reg-step="1">
                <button type="button" id="sendOtpBtn" class="auth-btn auth-btn-primary">
                    <i class="fa-solid fa-envelope"></i> Send OTP
                </button>
            </div>

            <div class="auth-field" data-reg-step="otp" style="display:none">
                <label for="inputOtp" class="auth-label">Enter the OTP sent to your email</label>
                <input type="text" class="auth-input" id="inputOtp" placeholder="6-digit code" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
                <div class="auth-error" id="errOtp">Please enter the 6-digit code.</div>
                <div class="auth-otp-meta">
                    <a href="#" id="changeEmailLink">Change email</a>
                    &nbsp;&middot;&nbsp;
                    <a href="#" id="resendOtpLink">Resend OTP</a>
                    <span id="resendTimer"></span>
                </div>
            </div>
            <div class="auth-field" data-reg-step="otp" style="display:none">
                <button type="button" id="verifyOtpBtn" class="auth-btn auth-btn-primary">
                    <i class="fa-solid fa-shield-halved"></i> Verify OTP
                </button>
            </div>

            <div class="auth-field" data-reg-step="2" style="display:none">
                <label for="inputChoosePassword" class="auth-label">Password</label>
                <input type="password" class="auth-input" id="inputChoosePassword" placeholder="Enter Password (min 6 characters)">
                <div class="auth-error" id="errPassword">Password must be at least 6 characters.</div>
            </div>
            <div class="auth-field" data-reg-step="2" style="display:none">
                <label for="inputPhone" class="auth-label">Phone Number</label>
                <input type="tel" class="auth-input" id="inputPhone" placeholder="9811333333">
                <div class="auth-error" id="errPhone">Please enter a valid 10-digit phone number.</div>
            </div>
            <div class="auth-field" data-reg-step="2" style="display:none">
                <label for="inputUnlistedUserType" class="auth-label">I am interested in</label>
                <select class="auth-input" id="inputUnlistedUserType">
                    <option value="">-- Select --</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="channel_partner">Channel Partner</option>
                </select>
                <div class="auth-error" id="errUserType">Please select a user type.</div>
            </div>
            <div class="auth-field" data-reg-step="2" style="display:none">
                <button type="submit" id="registerBtn" class="auth-btn auth-btn-primary">
                    <i class="fa-solid fa-user"></i> Sign Up
                </button>
            </div>

            <p class="auth-links">Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
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

        function clearErrors() {
            $('.auth-error').hide();
            $('.auth-input').removeClass('is-invalid');
            $('#registerAlert').hide().removeClass('alert-danger alert-success').text('');
        }

        function showError(inputId, errId, msg) {
            $('#' + inputId).addClass('is-invalid');
            $('#' + errId).text(msg).show();
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function isValidPhone(phone) {
            return /^[6-9]\d{9}$/.test(phone.replace(/[\s\+\-]/g, ''));
        }

        var otpVerified   = false;
        var resendSeconds = 0;
        var resendTimerId = null;

        function startResendCooldown(seconds) {
            resendSeconds = seconds;
            clearInterval(resendTimerId);
            $('#resendOtpLink').addClass('disabled').css('pointer-events', 'none');
            resendTimerId = setInterval(function () {
                resendSeconds--;
                if (resendSeconds <= 0) {
                    clearInterval(resendTimerId);
                    $('#resendTimer').text('');
                    $('#resendOtpLink').removeClass('disabled').css('pointer-events', '');
                } else {
                    $('#resendTimer').text('(' + resendSeconds + 's)');
                }
            }, 1000);
        }

        function goToStep1() {
            otpVerified = false;
            clearInterval(resendTimerId);
            $('[data-reg-step="otp"]').hide();
            $('[data-reg-step="2"]').hide();
            $('[data-reg-step="1"]').show();
            $('#inputName, #inputEmailAddress').prop('readonly', false);
            $('#inputOtp').val('');
        }

        function sendOtp() {
            clearErrors();

            var name  = $.trim($('#inputName').val());
            var email = $.trim($('#inputEmailAddress').val());
            var valid = true;

            if (!name) {
                showError('inputName', 'errName', 'Full name is required.');
                valid = false;
            }
            if (!email || !isValidEmail(email)) {
                showError('inputEmailAddress', 'errEmail', 'Please enter a valid email address.');
                valid = false;
            }
            if (!valid) return;

            var btn = $('#sendOtpBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Sending...');

            $.ajax({
                url: '{{ route("register.send-otp") }}',
                method: 'POST',
                data: { name: name, email: email },
                success: function (res) {
                    if (res.success) {
                        $('#registerAlert').addClass('alert-success').text(res.message).show();
                        $('[data-reg-step="1"]').hide();
                        $('[data-reg-step="otp"]').show();
                        $('#inputName, #inputEmailAddress').prop('readonly', true);
                        $('#inputOtp').val('').focus();
                        startResendCooldown(30);
                    } else {
                        $('#registerAlert').addClass('alert-danger').text(res.message).show();
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;
                    if (errors) {
                        if (errors.name)  showError('inputName', 'errName', errors.name[0]);
                        if (errors.email) showError('inputEmailAddress', 'errEmail', errors.email[0]);
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
                        $('#registerAlert').addClass('alert-danger').text(msg).show();
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-envelope"></i> Send OTP');
                }
            });
        }

        $('#sendOtpBtn').on('click', sendOtp);

        $('#resendOtpLink').on('click', function (e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;
            sendOtp();
        });

        $('#changeEmailLink').on('click', function (e) {
            e.preventDefault();
            goToStep1();
        });

        $('#verifyOtpBtn').on('click', function () {
            $('#errOtp').hide();
            $('#inputOtp').removeClass('is-invalid');
            $('#registerAlert').hide();

            var email = $.trim($('#inputEmailAddress').val());
            var otp   = $.trim($('#inputOtp').val());

            if (!/^\d{6}$/.test(otp)) {
                showError('inputOtp', 'errOtp', 'Please enter the 6-digit code.');
                return;
            }

            var btn = $('#verifyOtpBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Verifying...');

            $.ajax({
                url: '{{ route("register.verify-otp") }}',
                method: 'POST',
                data: { email: email, otp: otp },
                success: function (res) {
                    if (res.success) {
                        otpVerified = true;
                        clearInterval(resendTimerId);
                        $('[data-reg-step="otp"]').hide();
                        $('[data-reg-step="2"]').show();
                        $('#registerAlert').addClass('alert-success').text(res.message).show();
                    } else {
                        showError('inputOtp', 'errOtp', res.message);
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
                    showError('inputOtp', 'errOtp', msg);
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-shield-halved"></i> Verify OTP');
                }
            });
        });

        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            if (!otpVerified) {
                // Guards against an implicit Enter-key submit from a step-1/otp
                // field reaching the (hidden) Sign Up submit button early.
                return;
            }

            clearErrors();

            var name      = $.trim($('#inputName').val());
            var email     = $.trim($('#inputEmailAddress').val());
            var password  = $('#inputChoosePassword').val();
            var phone     = $.trim($('#inputPhone').val());
            var userType  = $('#inputUnlistedUserType').val();
            var valid     = true;

            if (!name) {
                showError('inputName', 'errName', 'Full name is required.');
                valid = false;
            }
            if (!email || !isValidEmail(email)) {
                showError('inputEmailAddress', 'errEmail', 'Please enter a valid email address.');
                valid = false;
            }
            if (!password || password.length < 6) {
                showError('inputChoosePassword', 'errPassword', 'Password must be at least 6 characters.');
                valid = false;
            }
            if (!phone || !isValidPhone(phone)) {
                showError('inputPhone', 'errPhone', 'Please enter a valid 10-digit phone number.');
                valid = false;
            }
            if (!userType) {
                showError('inputUnlistedUserType', 'errUserType', 'Please select a user type.');
                valid = false;
            }

            if (!valid) return;

            var btn = $('#registerBtn');
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Signing up...');

            $.ajax({
                url: '{{ route("register.post") }}',
                method: 'POST',
                data: {
                    name:               name,
                    email:              email,
                    password:           password,
                    phone:              phone,
                    unlisted_user_type: userType,
                    landing_page:       $('input[name="landing_page"]').val()
                },
                success: function (res) {
                    if (res.success) {
                        $('#registerAlert').addClass('alert-success').text(res.message).show();
                        $('#registerForm')[0].reset();
                        if (res.redirect) window.location.href = res.redirect;
                    } else {
                        $('#registerAlert').addClass('alert-danger').text(res.message).show();
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;
                    if (errors) {
                        if (errors.name)      showError('inputName', 'errName', errors.name[0]);
                        if (errors.email)     showError('inputEmailAddress', 'errEmail', errors.email[0]);
                        if (errors.password)  showError('inputChoosePassword', 'errPassword', errors.password[0]);
                        if (errors.phone)     showError('inputPhone', 'errPhone', errors.phone[0]);
                        if (errors.unlisted_user_type) showError('inputUnlistedUserType', 'errUserType', errors.unlisted_user_type[0]);
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
                        $('#registerAlert').addClass('alert-danger').text(msg).show();
                        if (msg.indexOf('verify your email') !== -1) {
                            goToStep1();
                        }
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-user"></i> Sign Up');
                }
            });
        });

    });
    </script>
@endpush
