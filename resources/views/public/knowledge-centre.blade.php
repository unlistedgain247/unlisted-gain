@extends('layout.app')

@section('title', 'Knowledge Centre - Learn About Unlisted Shares | UnlistedGain')
@section('meta_description', 'Your comprehensive resource hub for understanding unlisted shares, pre-IPO investments, and building wealth before companies go public. Learn how to invest safely.')
@section('meta_keywords', 'unlisted share knowledge, pre-ipo education, learn unlisted investing, share market guide India, startup equity guide')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}?v={{ filemtime(public_path('assets/css/pagecss/content-page.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Knowledge Centre']]])
@endsection

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">Knowledge <span>Centre</span></h1>
        <p class="page-subtitle">Your comprehensive resource hub for understanding unlisted shares, pre-IPO
            investments, and building wealth before companies go public.</p>

        <div class="content-section">
            <h2>What are Unlisted Shares?</h2>
            <p>Unlisted shares are equity shares of companies that are not listed on any recognized stock exchange
                such as NSE or BSE. These shares represent ownership in private companies, pre-IPO companies, or
                companies that have chosen not to list on public exchanges.</p>
            <p>Investing in unlisted shares gives you the opportunity to own a stake in high-growth companies before
                they become publicly available, potentially at a significant discount to their eventual listing
                price.</p>
        </div>

        <div class="content-section">
            <h2>Types of Unlisted Shares</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Pre-IPO Shares</h3>
                    <p>Shares of companies that have filed or plan to file their DRHP with SEBI. These companies are
                        on the path to becoming publicly listed.</p>
                </div>
                <div class="info-card">
                    <h3>Subsidiary Shares</h3>
                    <p>Shares of unlisted subsidiaries of listed parent companies. E.g., HDB Financial Services
                        (subsidiary of HDFC Bank).</p>
                </div>
                <div class="info-card">
                    <h3>Startup Equity (ESOPs)</h3>
                    <p>Employee Stock Option Plans from startups and growth-stage companies that employees wish to
                        liquidate before an IPO.</p>
                </div>
                <div class="info-card">
                    <h3>Delisted Shares</h3>
                    <p>Shares of companies that were previously listed but have been delisted from stock exchanges
                        for various reasons.</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>How to Invest in Unlisted Shares</h2>
            <ol class="step-list">
                <li>Open a demat account with any SEBI-registered Depository Participant</li>
                <li>Research companies using our detailed share profiles and financial data</li>
                <li>Place a buy order on UnlistedGain specifying the company and quantity</li>
                <li>Complete KYC verification and share your Client Master List</li>
                <li>Make payment via bank transfer (NEFT/RTGS/IMPS)</li>
                <li>Shares are transferred to your demat account within 24–48 hours</li>
            </ol>
        </div>

        <div class="content-section">
            <h2>Benefits of Investing in Unlisted Shares</h2>
            <ul>
                <li><strong>Early Access:</strong> Invest in high-growth companies before their IPO at potentially
                    lower valuations</li>
                <li><strong>Portfolio Diversification:</strong> Add a unique asset class that is uncorrelated with
                    public markets</li>
                <li><strong>Listing Gains:</strong> When the company lists through an IPO, early investors often see
                    significant returns</li>
                <li><strong>Long-term Wealth:</strong> Participate in the growth journey of India's most promising
                    companies</li>
            </ul>
        </div>

        <div class="content-section">
            <h2>Risks to Consider</h2>
            <div class="highlight-box">
                <p><strong>Disclaimer:</strong> Unlisted shares carry higher risk compared to listed securities. Key
                    risks include limited liquidity, lack of public disclosure, valuation uncertainty, and longer
                    holding periods. Always invest based on your risk appetite and consult a financial advisor.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>Useful Resources</h2>
            <ul>
                <li><a href="{{ url('/off-market-annexure') }}">Off Market Annexure — Transfer Process</a></li>
                <li><a href="{{ url('/pan-unlisted-shares') }}">PAN &amp; Tax Implications</a></li>
                <li><a href="{{ url('/sebi-guidelines') }}">SEBI Regulatory Framework</a></li>
                <li><a href="{{ url('/faq') }}">Frequently Asked Questions</a></li>
            </ul>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection
