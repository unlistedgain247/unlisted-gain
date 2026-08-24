@extends('layout.app')

@section('title', 'Bank Account Details | UnlistedGain')
@section('meta_description', 'Official bank account details of UnlistedGain Advantage Solutions Pvt. Ltd. for NEFT/RTGS/IMPS transfers.')
@section('meta_keywords', 'unlistedgain bank details, unlistedgain account number, unlistedgain ifsc code, unlistedgain payment details')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}?v={{ filemtime(public_path('assets/css/pagecss/content-page.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Bank Account Details']]])
@endsection

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">Bank Account <span>Details</span></h1>
        <p class="page-subtitle">Official bank accounts of UnlistedGain Advantage Solutions Pvt. Ltd. Use these details only for making payments related to your transactions with us.</p>

        <div class="content-section">
            <div class="highlight-box">
                <p><strong>Verify before you pay:</strong> Always confirm these account details directly on this page before transferring any funds. UnlistedGain will never ask you to pay into an account not listed here. If you receive bank details through SMS, WhatsApp, or email that don't match this page, do not proceed — contact us first.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>Our Bank Accounts</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>ICICI Bank</h3>
                    <p><strong>Account Name:</strong> UnlistedGain Advantage Solutions Pvt. Ltd.</p>
                    <p><strong>Account Number:</strong> 008705011429</p>
                    <p><strong>IFSC Code:</strong> ICIC0000087</p>
                    <p><strong>Account Type:</strong> Current Account</p>
                    <p><strong>Branch:</strong> New Delhi – Janakpuri Branch, Mahatta Towers, 54, B-Block, Community Centre, Janakpuri, New Delhi – 110058</p>
                </div>
                <div class="info-card">
                    <h3>Bandhan Bank</h3>
                    <p><strong>Account Name:</strong> UnlistedGain Advantage Solutions Pvt. Ltd.</p>
                    <p><strong>Account Number:</strong> 20100079496491</p>
                    <p><strong>IFSC Code:</strong> BDBL0002801</p>
                    <p><strong>Account Type:</strong> Current Account</p>
                    <p><strong>Branch:</strong> Tilak Nagar Branch, Tilak Nagar, New Delhi – 110018</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Need Help?</h2>
            <p>If you've made a payment and need to share the transaction proof, or have any questions about which account to use, please <a href="{{ url('/contact-us') }}">contact our team</a> or call us at <a href="tel:+919891881886">+91 98918 81886</a>.</p>
        </div>

        <p class="last-updated">Last updated: {{ now()->format('F Y') }}</p>
    </div>
</main>
@endsection
