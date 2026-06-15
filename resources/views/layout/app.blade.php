<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UnlistedGain | India\'s #1 Marketplace to Buy & Sell Unlisted Shares')</title>
    <meta name="description" content="@yield('meta_description', 'UnlistedGain is the most trusted platform to buy and sell unlisted, pre-IPO, and ESOP shares in India at the best prices.')">
    <meta name="keywords" content="@yield('meta_keywords', 'unlisted shares, pre-IPO shares, buy unlisted shares India, sell unlisted shares, NSE unlisted price')">
    <meta name="author" content="UnlistedGain">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/global.css') }}">

    @stack('styles')
</head>

<body>

    <script>
        window.UG_AUTH = {{ session('uid') ? 'true' : 'false' }};
        window.pendingInvest = {!! (session()->has('invest_intent') && session('uid')) ? json_encode(session('invest_intent')) : 'null' !!};
    </script>

    @include('partials.header')

    @yield('subheader')

    @yield('content')

    @include('partials.footer')

    @include('components.invest-modal')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/slider.js') }}"></script>
    <script src="{{ asset('assets/js/hero-carousel.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/search-shares.js') }}"></script> --}}
    <script src="{{ asset('assets/js/shares-icon-slider.js') }}"></script>
    <script src="{{ asset('assets/js/trending-stocks.js') }}"></script>
    <script src="{{ asset('assets/js/invest-modal.js') }}"></script>

    @stack('scripts')

</body>

</html>
