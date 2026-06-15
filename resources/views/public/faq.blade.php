@extends('layout.app')

@section('title', 'Frequently Asked Questions (FAQ) | UnlistedGain')
@section('meta_description', 'Find answers to commonly asked questions about unlisted shares, pre-IPO investing, buying/selling process, taxes, and security on UnlistedGain.')
@section('meta_keywords', 'unlisted shares faq, pre-ipo questions, buy unlisted shares help, tax on unlisted shares India, unlistedgain support')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/content-page.css') }}">
@endpush

@section('content')
<main>
    <div class="ug-content-page">
        <h1 class="page-title">Frequently Asked <span>Questions</span></h1>
        <p class="page-subtitle">Find answers to the most commonly asked questions about unlisted shares, the
            buying/selling process, and how UnlistedGain works.</p>

        <div class="content-section">
            <h2>General Questions</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        What are unlisted shares?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Unlisted shares are equity shares of companies that are not listed on any recognized
                            stock exchange such as NSE or BSE. These include pre-IPO companies, subsidiaries of
                            listed companies, and private enterprises. They offer the opportunity to invest in
                            high-growth companies before they become publicly available.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        Is it legal to buy and sell unlisted shares in India?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, buying and selling unlisted shares is completely legal in India. These transactions
                            are conducted as off-market transfers through Depository Participants (DPs) and are
                            governed by the Companies Act, 2013 and SEBI regulations. All transactions must be
                            reported for tax purposes.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        What is UnlistedGain?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>UnlistedGain is India's premier marketplace for unlisted and pre-IPO shares. We connect
                            buyers and sellers, facilitate secure transactions, and provide comprehensive research
                            and data on hundreds of unlisted companies to help you make informed investment
                            decisions.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Buying &amp; Selling</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        How do I buy unlisted shares on UnlistedGain?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Browse our catalogue of unlisted companies, select the company you wish to invest in, and
                            place a buy order by contacting our team. We will guide you through KYC verification,
                            payment, and share transfer. Shares are typically credited to your demat account within
                            24–48 hours of payment confirmation.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        What is the minimum investment amount?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>The minimum investment varies by company and depends on the current share price and
                            minimum lot size. Typically, investments start from as low as ₹10,000. Contact our team
                            for specific company details.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        How do I sell my unlisted shares?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Visit our Sell page and submit your sell request with the company name, quantity, and
                            your expected price. Our team will find a buyer, coordinate the off-market transfer from
                            your demat account, and ensure payment is credited to your bank account.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        What documents are required?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You need a valid PAN card, an active demat account, your Client Master List (CML) from
                            your DP, and bank account details. All documents must be self-attested. Our team handles
                            the rest of the documentation including the Delivery Instruction Slip.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Tax &amp; Compliance</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        What are the tax implications of investing in unlisted shares?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>If held for less than 24 months, profits are taxed as Short-Term Capital Gains (STCG) at
                            your income tax slab rate. If held for more than 24 months, profits are taxed as
                            Long-Term Capital Gains (LTCG) at 20% with indexation benefits. We recommend consulting
                            a tax advisor for personalized guidance.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        Is it safe to invest in unlisted shares?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>While unlisted shares carry higher risk than listed securities (including lower liquidity
                            and limited public information), investing through a trusted platform like UnlistedGain
                            mitigates counterparty risks. We verify all sellers, validate share authenticity, and
                            manage the entire transfer process securely.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        What happens when a company I invested in goes for an IPO?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>When the company lists through an IPO, your unlisted shares will be converted to listed
                            shares and will appear in your demat account as tradeable securities. You can then sell
                            them on the stock exchange at the prevailing market price. There may be a lock-in period
                            of 6 months for pre-IPO investors in some cases.</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="last-updated">Last updated: April 2026</p>
    </div>
</main>
@endsection

@push('scripts')
<script>
$(document).on('click', '.faq-question', function () {
    var $item = $(this).closest('.faq-item');
    var $answer = $item.find('.faq-answer');
    var $toggle = $(this).find('.faq-toggle');
    var isOpen = $item.hasClass('open');

    $('.faq-item.open').removeClass('open').find('.faq-answer').slideUp(200);
    $('.faq-toggle').text('+');

    if (!isOpen) {
        $item.addClass('open');
        $answer.slideDown(200);
        $toggle.text('−');
    }
});
</script>
@endpush
