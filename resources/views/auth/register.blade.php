@extends('layout.app')

@section('title', 'Sign Up | UnlistedGain')

@push('styles')
    <link href="{{ asset('assets/auth/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/auth/js/pace.min.js') }}"></script>
    <link href="{{ asset('assets/auth/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/auth/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/auth/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/auth/css/icons.css') }}" rel="stylesheet">
    <style>
        .auth-page-wrapper {
            background-image: url("{{ asset('assets/auth/images/login-images/02.png') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .auth-error { color: #dc3545; font-size: 12px; margin-top: 4px; display: none; }
        .auth-alert { border-radius: 10px; font-size: 13px; padding: 10px 14px; margin-bottom: 10px; display: none; }
        .is-invalid { border-color: #dc3545 !important; }
    </style>
@endpush

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2">
            <div class="col mx-auto">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="border p-4 rounded-4">
                            <div class="text-center">
                                <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" height="50" alt="UnlistedGain">
                                <h5 class="mt-3 mb-0">Welcome</h5>
                                <p class="mb-4">Create Your New Account</p>
                            </div>

                            <div class="form-body">
                                <div id="registerAlert" class="auth-alert alert"></div>
                                <form class="row g-3" id="registerForm">
                                    @csrf
                                    {{-- Honeypot: bots fill this, humans never see it --}}
                                    <input type="text" name="_hp" value="" style="display:none!important" tabindex="-1" autocomplete="off" aria-hidden="true">
                                    <input type="hidden" name="landing_page" value="{{ $landingPage ?? '' }}">
                                    <div class="col-12">
                                        <label for="inputName" class="form-label">Full Name</label>
                                        <input type="text" class="form-control rounded-5" id="inputName" placeholder="John Doe">
                                        <div class="auth-error" id="errName">Full name is required.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputEmailAddress" class="form-label">Email Address</label>
                                        <input type="email" class="form-control rounded-5" id="inputEmailAddress" placeholder="example@user.com">
                                        <div class="auth-error" id="errEmail">Please enter a valid email address.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputChoosePassword" class="form-label">Password</label>
                                        <div class="input-group" id="show_hide_password">
                                            <input type="password" class="form-control rounded-5" id="inputChoosePassword" placeholder="Enter Password (min 6 characters)">
                                        </div>
                                        <div class="auth-error" id="errPassword">Password must be at least 6 characters.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputPhone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control rounded-5" id="inputPhone" placeholder="9811333333">
                                        <div class="auth-error" id="errPhone">Please enter a valid 10-digit phone number.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputUnlistedUserType" class="form-label">I am interested in</label>
                                        <select class="form-select rounded-5" id="inputUnlistedUserType">
                                            <option value="">-- Select --</option>
                                            <option value="unlisted">Unlisted</option>
                                            <option value="channel_partner">Channel Partner</option>
                                        </select>
                                        <div class="auth-error" id="errUserType">Please select a user type.</div>
                                    </div>
                                    {{-- <div class="col-12">
                                        <label for="inputSelectCountry" class="form-label">Country</label>
                                        <select class="form-select rounded-5" id="inputSelectCountry">
                                            <option selected>India</option>
                                            <option value="1">United Kingdom</option>
                                            <option value="2">America</option>
                                            <option value="3">Dubai</option>
                                        </select>
                                    </div> --}}
                                    {{-- <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="agreeTerms">
                                            <label class="form-check-label" for="agreeTerms">I read and agree to Terms &amp; Conditions</label>
                                        </div>
                                        <div class="auth-error" id="errTerms">You must agree to the Terms &amp; Conditions.</div>
                                    </div> --}}
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button type="submit" id="registerBtn" class="btn btn-gradient-info rounded-5">
                                                <i class="bx bx-user"></i> Sign Up
                                            </button>
                                        </div>
                                    </div>
                                    {{-- <div class="col-12">
                                        <div class="login-separater text-center">
                                            <span>OR SIGN UP WITH</span>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <a class="btn mb-3 shadow-sm btn-white rounded-5" href="#">
                                                <span class="d-flex justify-content-center align-items-center">
                                                    <img class="me-2" src="{{ asset('assets/auth/images/icons/search.svg') }}" width="16" alt="Google">
                                                    <span>Sign Up with Google</span>
                                                </span>
                                            </a>
                                            <a href="#" class="btn shadow-sm btn-white rounded-5">
                                                <i class="bx bxl-facebook"></i> Sign Up with Facebook
                                            </a>
                                        </div>
                                    </div> --}}
                                    <div class="col-12 text-center">
                                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/auth/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/auth/js/jquery.min.js') }}"></script>
    <script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(function () {

        function clearErrors() {
            $('.auth-error').hide();
            $('.form-control, .form-select').removeClass('is-invalid');
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

        $('#registerForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            var name      = $.trim($('#inputName').val());
            var email     = $.trim($('#inputEmailAddress').val());
            var password  = $('#inputChoosePassword').val();
            var phone     = $.trim($('#inputPhone').val());
            var userType  = $('#inputUnlistedUserType').val();
            // var terms     = $('#agreeTerms').is(':checked');
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
            // if (!terms) {
            //     $('#errTerms').show();
            //     valid = false;
            // }

            if (!valid) return;

            var btn = $('#registerBtn');
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Signing up...');

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
                        $('#registerAlert').addClass('alert alert-success').text(res.message).show();
                        $('#registerForm')[0].reset();
                        if (res.redirect) window.location.href = res.redirect;
                    } else {
                        $('#registerAlert').addClass('alert alert-danger').text(res.message).show();
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
                        $('#registerAlert').addClass('alert alert-danger').text('Something went wrong. Please try again.').show();
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="bx bx-user"></i> Sign Up');
                }
            });
        });

    });
    </script>
@endpush
