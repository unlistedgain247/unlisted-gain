@extends('layout.app')

@section('title', 'Sign In | UnlistedGain')

@push('styles')
    <link href="{{ asset('assets/auth/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/auth/js/pace.min.js') }}"></script>
    <link href="{{ asset('assets/auth/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/auth/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/auth/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/auth/css/icons.css') }}" rel="stylesheet">
    <style>
        .auth-page-wrapper {
            background-image: url("{{ asset('assets/auth/images/login-images/01.png') }}");
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
    <div class="container-fluid">
        <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
            <div class="col mx-auto">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="border p-4 rounded-4">
                            <div class="text-center">
                                <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" height="50" alt="UnlistedGain">
                                <h5 class="mt-3 mb-0">Welcome Back</h5>
                                <p class="mb-4">Sign in to your account</p>
                            </div>

                            <div class="form-body">
                                <div id="loginAlert" class="auth-alert alert"></div>
                                <form class="row g-3" id="loginForm">
                                    @csrf
                                    {{-- Honeypot: bots fill this, humans never see it --}}
                                    <input type="text" name="_hp" value="" style="display:none!important" tabindex="-1" autocomplete="off" aria-hidden="true">
                                    <div class="col-12">
                                        <label for="inputIdentifier" class="form-label">Email or Phone</label>
                                        <input type="text" class="form-control rounded-5" id="inputIdentifier" placeholder="Email or 10-digit Phone">
                                        <div class="auth-error" id="errIdentifier">Please enter a valid email or 10-digit phone number.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="inputChoosePassword" class="form-label">Password</label>
                                        <input type="password" class="form-control rounded-5" id="inputChoosePassword" placeholder="Enter Password">
                                        <div class="auth-error" id="errPassword">Password is required.</div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-md-6 text-end">
                                        <a href="{{ url('/forgot-password') }}">Forgot Password?</a>
                                    </div> --}}
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button type="submit" id="loginBtn" class="btn btn-gradient-info rounded-5">
                                                <i class="bx bxs-lock-open"></i> Sign In
                                            </button>
                                        </div>
                                    </div>
                                    {{-- <div class="col-12">
                                        <div class="login-separater text-center">
                                            <span>OR SIGN IN WITH</span>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <a class="btn mb-3 shadow-sm btn-white rounded-5" href="#">
                                                <span class="d-flex justify-content-center align-items-center">
                                                    <img class="me-2" src="{{ asset('assets/auth/images/icons/search.svg') }}" width="16" alt="Google">
                                                    <span>Sign In with Google</span>
                                                </span>
                                            </a>
                                            <a href="#" class="btn shadow-sm btn-white rounded-5">
                                                <i class="bx bxl-facebook"></i> Sign In with Facebook
                                            </a>
                                        </div>
                                    </div> --}}
                                    <div class="col-12 text-center">
                                        <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Sign up here</a></p>
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

        // --- Helpers ---
        function clearErrors() {
            $('.auth-error').hide();
            $('.form-control').removeClass('is-invalid');
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

        // --- Form submit ---
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            var identifier = $.trim($('#inputIdentifier').val());
            var password   = $('#inputChoosePassword').val();
            var valid      = true;

            if (!identifier || (!isValidEmail(identifier) && !isValidPhone(identifier))) {
                showError('inputIdentifier', 'errIdentifier', 'Please enter a valid email or 10-digit phone number.');
                valid = false;
            }

            if (!password) {
                showError('inputChoosePassword', 'errPassword', 'Password is required.');
                valid = false;
            }

            if (!valid) return;

            var loginType = isValidEmail(identifier) ? 'email' : 'phone';
            var btn = $('#loginBtn');
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Signing in...');

            $.ajax({
                url: '{{ route("login.post") }}',
                method: 'POST',
                data: {
                    login_type: loginType,
                    email:      loginType === 'email' ? identifier : '',
                    phone:      loginType === 'phone' ? identifier : '',
                    password:   password,
                    // remember:   $('#rememberMe').is(':checked') ? 1 : 0
                },
                success: function (res) {
                    if (res.success) {
                        $('#loginAlert').addClass('alert alert-success').text(res.message).show();
                        if (res.redirect) window.location.href = res.redirect;
                    } else {
                        $('#loginAlert').addClass('alert alert-danger').text(res.message).show();
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;
                    if (errors) {
                        if (errors.email || errors.phone) showError('inputIdentifier', 'errIdentifier', (errors.email || errors.phone)[0]);
                        if (errors.password) showError('inputChoosePassword', 'errPassword', errors.password[0]);
                    } else {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Something went wrong. Please try again.';
                        $('#loginAlert').addClass('alert alert-danger').text(msg).show();
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="bx bxs-lock-open"></i> Sign In');
                }
            });
        });

    });
    </script>
@endpush
