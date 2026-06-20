@extends('layout.app')

@section('title', 'Privacy Policy | UnlistedGain')
@section('meta_description', 'Read our Privacy Policy to understand how UnlistedGain collects, uses, and protects your personal and financial information while using our unlisted share marketplace.')
@section('meta_keywords', 'privacy policy, data protection, unlistedgain privacy, secure trading, investor data safety')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}?v={{ filemtime(public_path('assets/css/pagecss/content-page.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Privacy Policy']]])
@endsection

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">Privacy <span>Policy</span></h1>
        <p class="page-subtitle">This Privacy Policy explains how Unlisted Gain Pvt. Ltd. ("UnlistedGain", "we", "us")
            collects, uses, and protects your personal information.</p>

        <div class="content-section">
            <h2>1. Information We Collect</h2>
            <p>We may collect the following types of information when you use our platform:</p>
            <ul>
                <li><strong>Personal Information:</strong> Name, email address, phone number, PAN number, Aadhaar
                    number, and demat account details</li>
                <li><strong>Financial Information:</strong> Bank account details, transaction history, and
                    investment preferences</li>
                <li><strong>Technical Information:</strong> IP address, browser type, device information, and
                    cookies</li>
                <li><strong>Communication Data:</strong> Records of correspondence through email, phone, or WhatsApp
                </li>
            </ul>
        </div>

        <div class="content-section">
            <h2>2. How We Use Your Information</h2>
            <p>Your information is used for the following purposes:</p>
            <ul>
                <li>To facilitate unlisted share transactions and off-market transfers</li>
                <li>To verify your identity and comply with KYC requirements</li>
                <li>To communicate transaction updates and investment opportunities</li>
                <li>To improve our platform and user experience</li>
                <li>To comply with legal and regulatory obligations</li>
                <li>To prevent fraud and ensure the security of transactions</li>
            </ul>
        </div>

        <div class="content-section">
            <h2>3. Information Sharing</h2>
            <p>We do not sell your personal information to third parties. We may share your information in the
                following circumstances:</p>
            <ul>
                <li>With counterparties involved in your share transactions (limited to necessary details)</li>
                <li>With Depository Participants for share transfer processing</li>
                <li>With regulatory authorities as required by law (SEBI, Income Tax Department)</li>
                <li>With our auditors for compliance verification</li>
                <li>With payment processors for facilitating transactions</li>
            </ul>
        </div>

        <div class="content-section">
            <h2>4. Data Security</h2>
            <p>We implement industry-standard security measures to protect your personal information, including
                encryption of sensitive data, secure server infrastructure, and regular security audits. However, no
                method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.
            </p>
        </div>

        <div class="content-section">
            <h2>5. Data Retention</h2>
            <p>We retain your personal information for as long as necessary to fulfil the purposes for which it was
                collected, or as required by applicable laws and regulations. Transaction records are maintained for
                a minimum of 8 years as per regulatory requirements.</p>
        </div>

        <div class="content-section">
            <h2>6. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access and review your personal information</li>
                <li>Request correction of inaccurate data</li>
                <li>Request deletion of your data (subject to legal retention requirements)</li>
                <li>Opt out of marketing communications</li>
                <li>Withdraw consent for data processing</li>
            </ul>
        </div>

        <div class="content-section">
            <h2>7. Cookies</h2>
            <p>Our website uses cookies to enhance your browsing experience. Cookies help us understand how you use
                our platform and enable certain features. You can disable cookies through your browser settings,
                though this may affect the functionality of our website.</p>
        </div>

        <div class="content-section">
            <h2>8. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with an
                updated revision date. We encourage you to review this policy periodically.</p>
        </div>

        <div class="content-section">
            <h2>9. Contact Us</h2>
            <p>If you have any questions or concerns about this Privacy Policy, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> privacy@unlistedgain.com</li>
                <li><strong>Address:</strong> 113/2, 1st Floor, Meenakshi Garden, Tilak Nagar, New Delhi - 110018
                </li>
            </ul>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection
