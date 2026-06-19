@extends('layout.app')

@section('title', 'SEBI Guidelines for Unlisted Share Trading | UnlistedGain')
@section('meta_description', 'A comprehensive overview of the regulatory framework governing unlisted shares and pre-IPO investments in India. Learn about SEBI regulations and ICDR guidelines.')
@section('meta_keywords', 'sebi guidelines unlisted shares, unlisted share regulations, ICDR guidelines, DRHP SEBI, insider trading unlisted, investor protection India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'SEBI Guidelines']]])
@endsection

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">SEBI <span>Guidelines</span></h1>
        <p class="page-subtitle">A comprehensive overview of the regulatory framework governing unlisted shares and
            pre-IPO investments in India.</p>

        <div class="content-section">
            <h2>Regulatory Framework</h2>
            <p>The Securities and Exchange Board of India (SEBI) is the primary regulator overseeing the securities
                market in India. While unlisted shares are not traded on recognized stock exchanges, they still fall
                under the broader regulatory umbrella of SEBI and the Companies Act, 2013.</p>
        </div>

        <div class="content-section">
            <h2>Key SEBI Regulations for Unlisted Shares</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Share Transfer Norms</h3>
                    <p>Unlisted shares can only be transferred in dematerialized (demat) form as per SEBI mandate.
                        Physical share certificates must be converted to demat before transfer.</p>
                </div>
                <div class="info-card">
                    <h3>KYC Requirements</h3>
                    <p>Both buyer and seller must complete KYC verification through their Depository Participant.
                        PAN, Aadhaar, and address proof are mandatory.</p>
                </div>
                <div class="info-card">
                    <h3>DRHP Filing</h3>
                    <p>Companies planning an IPO must file a Draft Red Herring Prospectus (DRHP) with SEBI, which
                        becomes a key document for evaluating pre-IPO investments.</p>
                </div>
                <div class="info-card">
                    <h3>Insider Trading Rules</h3>
                    <p>SEBI's insider trading regulations apply to unlisted companies as well. Trading based on
                        Unpublished Price Sensitive Information (UPSI) is prohibited.</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Important Regulations</h2>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Regulation</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SEBI (LODR) Regulations, 2015</td>
                        <td>Governs listing obligations and disclosure requirements for companies</td>
                    </tr>
                    <tr>
                        <td>SEBI (PIT) Regulations, 2015</td>
                        <td>Prohibits insider trading in securities of listed and to-be-listed companies</td>
                    </tr>
                    <tr>
                        <td>SEBI (ICDR) Regulations, 2018</td>
                        <td>Governs the issuance of capital and disclosure requirements for IPOs</td>
                    </tr>
                    <tr>
                        <td>Companies Act, 2013</td>
                        <td>Regulates share transfer, buyback, and corporate governance of all companies</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content-section">
            <div class="highlight-box">
                <p><strong>UnlistedGain's Compliance:</strong> We operate in full compliance with all applicable
                    SEBI regulations and ensure that every transaction on our platform adheres to the highest
                    standards of regulatory compliance and investor protection.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>Investor Protection Measures</h2>
            <ul>
                <li>All shares are verified for authenticity before listing on our platform</li>
                <li>Counterparty verification is mandatory for every transaction</li>
                <li>We maintain complete transaction records for audit purposes</li>
                <li>Investors are educated about risks associated with unlisted investments</li>
                <li>Our processes are regularly audited by independent auditors</li>
            </ul>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection
