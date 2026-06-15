@extends('layout.app')

@section('title', 'PAN Requirements for Unlisted Shares | UnlistedGain')
@section('meta_description', 'Everything you need to know about PAN requirements and tax implications when investing in unlisted shares in India. Understand STCG and LTCG for unlisted stocks.')
@section('meta_keywords', 'pan card for unlisted shares, unlisted share tax India, STCG unlisted shares, LTCG unlisted shares, indexation benefit unlisted')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}">
@endpush

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">PAN of <span>Unlisted Shares</span></h1>
        <p class="page-subtitle">Everything you need to know about PAN requirements and tax implications when
            investing in unlisted shares in India.</p>

        <div class="content-section">
            <h2>Why is PAN Mandatory?</h2>
            <p>PAN (Permanent Account Number) is a critical requirement for any securities transaction in India,
                including unlisted shares. SEBI and the Income Tax Department mandate PAN for all demat account
                holders, ensuring traceability and tax compliance for every share transfer.</p>
        </div>

        <div class="content-section">
            <h2>PAN Requirements for Unlisted Share Transactions</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Buyer Requirements</h3>
                    <p>Valid PAN linked to their demat account. Self-attested PAN copy must be provided to the
                        counterparty for KYC verification.</p>
                </div>
                <div class="info-card">
                    <h3>Seller Requirements</h3>
                    <p>Valid PAN linked to their demat account. Seller must declare capital gains from the sale in
                        their Income Tax Return (ITR).</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Tax Implications</h2>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Holding Period</th>
                        <th>Tax Type</th>
                        <th>Tax Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Less than 24 months</td>
                        <td>Short-Term Capital Gains (STCG)</td>
                        <td>As per income tax slab</td>
                    </tr>
                    <tr>
                        <td>More than 24 months</td>
                        <td>Long-Term Capital Gains (LTCG)</td>
                        <td>20% with indexation</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content-section">
            <div class="highlight-box">
                <p><strong>Note:</strong> If the company later gets listed through an IPO, the holding period and
                    tax treatment may change based on the listing date. Always consult a tax advisor for your
                    specific situation.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>Key Compliance Points</h2>
            <ul>
                <li>PAN must be linked to Aadhaar for valid filing</li>
                <li>All off-market transfers are reported to the Income Tax Department</li>
                <li>TDS may be applicable on certain high-value transactions</li>
                <li>Capital gains must be computed based on the actual cost of acquisition</li>
                <li>Stamp duty is applicable at 0.015% for off-market transfers</li>
            </ul>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection
