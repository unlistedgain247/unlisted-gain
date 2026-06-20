@extends('layout.app')

@section('title', 'Terms of Use | UnlistedGain')
@section('meta_description', 'Review the Terms of Use for UnlistedGain. Understand our service eligibility, user responsibilities, and the legal framework for trading unlisted shares on our platform.')
@section('meta_keywords', 'terms of use, user agreement, legal terms, unlistedgain terms, service eligibility')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}?v={{ filemtime(public_path('assets/css/pagecss/content-page.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Terms of Use']]])
@endsection

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">Terms of <span>Use</span></h1>
        <p class="page-subtitle">Please read these Terms of Use carefully before using the UnlistedGain platform
            operated by Unlisted Gain Pvt. Ltd.</p>

        <div class="content-section">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing or using the UnlistedGain platform ("Platform"), you agree to be bound by these Terms of
                Use. If you do not agree with any part of these terms, you must not use our Platform. These terms
                constitute a legally binding agreement between you and Unlisted Gain Pvt. Ltd.</p>
        </div>

        <div class="content-section">
            <h2>2. Eligibility</h2>
            <p>To use our Platform, you must:</p>
            <ul>
                <li>Be at least 18 years of age</li>
                <li>Be a resident of India or an NRI with a valid PAN and demat account</li>
                <li>Have a valid PAN card and an active demat account</li>
                <li>Not be prohibited from entering into legally binding agreements under Indian law</li>
            </ul>
        </div>

        <div class="content-section">
            <h2>3. Services</h2>
            <p>UnlistedGain provides a marketplace for buying and selling unlisted and pre-IPO shares. Our services
                include:</p>
            <ul>
                <li>Facilitating connections between buyers and sellers of unlisted shares</li>
                <li>Providing market data, pricing information, and research on unlisted companies</li>
                <li>Managing the off-market share transfer process</li>
                <li>KYC verification and documentation support</li>
            </ul>
            <div class="highlight-box">
                <p><strong>Important:</strong> UnlistedGain acts as a facilitator and intermediary. We do not act as
                    a stockbroker, investment advisor, or portfolio manager. All investment decisions are made
                    solely by the investor.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>4. User Responsibilities</h2>
            <ul>
                <li>Provide accurate and truthful information during registration and KYC</li>
                <li>Maintain the confidentiality of your account credentials</li>
                <li>Ensure sufficient funds for transactions you initiate</li>
                <li>Comply with all applicable laws, including tax reporting obligations</li>
                <li>Not engage in fraudulent, misleading, or illegal activities on the Platform</li>
            </ul>
        </div>

        <div class="content-section">
            <h2>5. Pricing and Payments</h2>
            <p>Share prices displayed on our Platform are indicative and may change based on market conditions.
                Final transaction prices are confirmed at the time of order execution. Payments must be made through
                approved bank channels (NEFT/RTGS/IMPS). We do not accept cash payments.</p>
        </div>

        <div class="content-section">
            <h2>6. Cancellation and Refund</h2>
            <p>Once a transaction is confirmed and shares are transferred, it cannot be cancelled. Refunds are only
                applicable in cases where shares could not be transferred due to technical issues or seller default.
                Refund processing may take 5–7 business days.</p>
        </div>

        <div class="content-section">
            <h2>7. Intellectual Property</h2>
            <p>All content on the Platform, including text, graphics, logos, data, and software, is the property of
                Unlisted Gain Pvt. Ltd. and is protected by intellectual property laws. You may not reproduce,
                distribute, or create derivative works from our content without prior written permission.</p>
        </div>

        <div class="content-section">
            <h2>8. Disclaimer of Warranties</h2>
            <p>The Platform and its content are provided on an "as is" and "as available" basis. We make no
                warranties, express or implied, regarding the accuracy of share prices, company information, or the
                outcome of any investment. Past performance does not guarantee future results.</p>
        </div>

        <div class="content-section">
            <h2>9. Limitation of Liability</h2>
            <p>Unlisted Gain Pvt. Ltd. shall not be liable for any direct, indirect, incidental, or consequential
                damages arising from your use of the Platform, including but not limited to investment losses, data
                breaches, or service interruptions.</p>
        </div>

        <div class="content-section">
            <h2>10. Governing Law</h2>
            <p>These Terms of Use shall be governed by and construed in accordance with the laws of India. Any
                disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts in
                New Delhi, India.</p>
        </div>

        <div class="content-section">
            <h2>11. Contact</h2>
            <p>For questions regarding these Terms of Use, contact us at:</p>
            <ul>
                <li><strong>Email:</strong> legal@unlistedgain.com</li>
                <li><strong>Address:</strong> 113/2, 1st Floor, Meenakshi Garden, Tilak Nagar, New Delhi - 110018
                </li>
            </ul>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection
