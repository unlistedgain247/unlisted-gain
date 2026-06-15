@extends('layout.app')

@section('title', 'Off Market Annexure - Share Transfer Guide | UnlistedGain')
@section('meta_description', 'Understanding the off-market share transfer process and associated documentation for unlisted share transactions in India. Learn about DIS, CML, and transfer steps.')
@section('meta_keywords', 'off market share transfer, delivery instruction slip, DIS, client master list, CML, unlisted share settlement, off-market deal India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}">
@endpush

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">Off Market <span>Annexure</span></h1>
        <p class="page-subtitle">Understanding the off-market share transfer process and associated documentation
            for unlisted share transactions in India.</p>

        <div class="content-section">
            <h2>What is an Off-Market Transfer?</h2>
            <p>An off-market transfer refers to the transfer of shares directly between two parties without the
                involvement of a stock exchange. Unlike on-market trades executed through exchanges like NSE or BSE,
                off-market transfers are settled directly between the buyer and seller through their respective
                Depository Participants (DPs).</p>
            <p>This is the standard method used for trading unlisted shares, as these shares are not available on
                any recognized stock exchange.</p>
        </div>

        <div class="content-section">
            <h2>Documents Required</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>📋 Delivery Instruction Slip (DIS)</h3>
                    <p>A physical or electronic instruction from the seller to their DP to transfer shares to the
                        buyer's demat account.</p>
                </div>
                <div class="info-card">
                    <h3>🆔 PAN Card Copy</h3>
                    <p>Self-attested PAN card copies of both buyer and seller are required for KYC compliance.</p>
                </div>
                <div class="info-card">
                    <h3>📄 Client Master List (CML)</h3>
                    <p>A document from the buyer's DP containing their demat account details — DP ID, Client ID, and
                        beneficiary name.</p>
                </div>
                <div class="info-card">
                    <h3>🏦 Bank Details</h3>
                    <p>Bank account details of both parties to facilitate payment settlement via NEFT/RTGS/IMPS.</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Transfer Process</h2>
            <ol class="step-list">
                <li>Buyer and seller agree on quantity and price of unlisted shares</li>
                <li>Buyer shares their Client Master List (CML) with the seller</li>
                <li>Seller fills out the Delivery Instruction Slip (DIS) with buyer's demat details</li>
                <li>Seller submits the DIS to their Depository Participant (DP)</li>
                <li>Payment is made by the buyer to the seller's bank account</li>
                <li>Shares are credited to the buyer's demat account within 24–48 hours</li>
            </ol>
        </div>

        <div class="content-section">
            <div class="highlight-box">
                <p><strong>Important:</strong> At UnlistedGain, we handle the entire off-market transfer process
                    end-to-end, ensuring your shares are safely transferred to your demat account with full
                    documentation support.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>Key Points to Remember</h2>
            <ul>
                <li>Off-market transfers are subject to stamp duty as per state regulations</li>
                <li>Capital gains tax is applicable on the sale of unlisted shares</li>
                <li>Both buyer and seller must have active demat accounts</li>
                <li>Always verify the ISIN of the shares before initiating a transfer</li>
                <li>UnlistedGain verifies all counterparties before facilitating any transaction</li>
            </ul>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection
