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
            <div class="bank-grid">
                <div class="bank-card">
                    <div class="bank-card__header">
                        <div class="bank-card__badge">ICICI</div>
                        <div>
                            <h3>ICICI Bank</h3>
                            <span class="bank-card__type">Current Account</span>
                        </div>
                    </div>
                    <table class="bank-table">
                        <tr>
                            <td>Account Name</td>
                            <td>UnlistedGain Advantage Solutions Pvt. Ltd.</td>
                        </tr>
                        <tr>
                            <td>Account Number</td>
                            <td class="bank-value">008705011429</td>
                        </tr>
                        <tr>
                            <td>IFSC Code</td>
                            <td class="bank-value">ICIC0000087</td>
                        </tr>
                        <tr>
                            <td>Branch</td>
                            <td>New Delhi – Janakpuri Branch, Mahatta Towers, 54, B-Block, Community Centre, Janakpuri, New Delhi – 110058</td>
                        </tr>
                        <tr>
                            <td>UPI ID</td>
                            <td class="bank-value bank-value-upi">msunlistedgainadvantagesolutionsprivatelimited.eazypay@icici</td>
                        </tr>
                    </table>
                    <div class="bank-card-actions">
                        <button type="button" class="bank-copy-btn" data-copy-text="Account Name: UnlistedGain Advantage Solutions Pvt. Ltd.&#10;Account Number: 008705011429&#10;IFSC Code: ICIC0000087&#10;Account Type: Current Account&#10;Bank: ICICI Bank&#10;Branch: New Delhi – Janakpuri Branch, Mahatta Towers, 54, B-Block, Community Centre, Janakpuri, New Delhi – 110058&#10;UPI ID: msunlistedgainadvantagesolutionsprivatelimited.eazypay@icici">
                            <i class="fa-regular fa-copy"></i> <span>Copy Details</span>
                        </button>
                        <a href="{{ asset('assets/img/icici-upi-qr.png') }}" download="UnlistedGain-ICICI-UPI-QR.png" class="bank-download-btn">
                            <i class="fa-solid fa-qrcode"></i> <span>Download QR</span>
                        </a>
                    </div>
                </div>

                <div class="bank-card">
                    <div class="bank-card__header">
                        <div class="bank-card__badge">BDBL</div>
                        <div>
                            <h3>Bandhan Bank</h3>
                            <span class="bank-card__type">Current Account</span>
                        </div>
                    </div>
                    <table class="bank-table">
                        <tr>
                            <td>Account Name</td>
                            <td>UnlistedGain Advantage Solutions Pvt. Ltd.</td>
                        </tr>
                        <tr>
                            <td>Account Number</td>
                            <td class="bank-value">20100079496491</td>
                        </tr>
                        <tr>
                            <td>IFSC Code</td>
                            <td class="bank-value">BDBL0002801</td>
                        </tr>
                        <tr>
                            <td>Branch</td>
                            <td>Tilak Nagar Branch, Tilak Nagar, New Delhi – 110018</td>
                        </tr>
                    </table>
                    <div class="bank-card-actions">
                        <button type="button" class="bank-copy-btn" data-copy-text="Account Name: UnlistedGain Advantage Solutions Pvt. Ltd.&#10;Account Number: 20100079496491&#10;IFSC Code: BDBL0002801&#10;Account Type: Current Account&#10;Bank: Bandhan Bank&#10;Branch: Tilak Nagar Branch, Tilak Nagar, New Delhi – 110018">
                            <i class="fa-regular fa-copy"></i> <span>Copy Details</span>
                        </button>
                    </div>
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

@push('styles')
<style>
    .bank-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 10px;
    }

    .bank-card {
        background: #fff;
        border: 1px solid #e8ece5;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(20, 30, 20, .05);
        overflow: hidden;
        transition: box-shadow .2s ease;
    }

    .bank-card:hover {
        box-shadow: 0 8px 24px rgba(20, 30, 20, .08);
    }

    /* Grid items stretch to equal height by default, but the card itself
       needs to be a flex column so the actions block can be pinned to the
       bottom via margin-top:auto — otherwise a shorter card's buttons sit
       right under its table instead of aligning with the taller card's. */
    .bank-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .bank-card__header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #f0faf2 0%, #fafffe 100%);
        border-bottom: 1px solid #e8f5e9;
    }

    .bank-card__badge {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #87b942;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
    }

    .bank-card__header h3 {
        margin: 0 0 2px;
        font-size: 16px;
        font-weight: 700;
        color: #111;
    }

    .bank-card__type {
        font-size: 12px;
        color: #789;
        font-weight: 500;
    }

    .bank-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bank-table td {
        padding: 13px 24px;
        font-size: 13.5px;
        border-bottom: 1px solid #f0f2ee;
        vertical-align: top;
    }

    .bank-table tr:last-child td {
        border-bottom: none;
    }

    .bank-table td:first-child {
        color: #8a9188;
        width: 38%;
        font-weight: 500;
        white-space: nowrap;
    }

    .bank-table td:last-child {
        color: #1a1a1a;
        font-weight: 600;
    }

    .bank-value {
        font-family: 'Courier New', Courier, monospace;
        letter-spacing: .5px;
        font-size: 14px;
    }

    .bank-value-upi {
        font-size: 12.5px;
        word-break: break-all;
        line-height: 1.5;
    }

    .bank-card-actions {
        margin-top: auto;
        padding: 16px 24px 24px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .bank-copy-btn,
    .bank-download-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1.5px solid #87b942;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        text-decoration: none;
        box-sizing: border-box;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
    }

    .bank-copy-btn {
        background: #fff;
        color: #87b942;
    }

    .bank-copy-btn:hover {
        background: #87b942;
        color: #fff;
    }

    .bank-copy-btn.copied {
        background: #1e8e3e;
        border-color: #1e8e3e;
        color: #fff;
    }

    .bank-download-btn {
        background: #87b942;
        border-color: #87b942;
        color: #fff !important;
    }
    .bank-download-btn:hover {
        background: #74a336;
        border-color: #74a336;
        color: #fff !important;
    }
    .bank-download-btn i,
    .bank-download-btn span {
        color: inherit;
    }

    @media (max-width: 480px) {
        .bank-table td:first-child { white-space: normal; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.bank-copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy-text');

            function showCopied() {
                var label = btn.querySelector('span');
                var icon  = btn.querySelector('i');
                var originalLabel = label.textContent;
                var originalIcon  = icon.className;

                btn.classList.add('copied');
                label.textContent = 'Copied!';
                icon.className = 'fa-solid fa-check';

                setTimeout(function () {
                    btn.classList.remove('copied');
                    label.textContent = originalLabel;
                    icon.className = originalIcon;
                }, 1800);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(showCopied);
            } else {
                var textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(textarea);
                showCopied();
            }
        });
    });
});
</script>
@endpush
