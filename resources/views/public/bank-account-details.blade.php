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
                    <button type="button" class="bank-copy-btn" data-copy-text="Account Name: UnlistedGain Advantage Solutions Pvt. Ltd.&#10;Account Number: 008705011429&#10;IFSC Code: ICIC0000087&#10;Account Type: Current Account&#10;Bank: ICICI Bank&#10;Branch: New Delhi – Janakpuri Branch, Mahatta Towers, 54, B-Block, Community Centre, Janakpuri, New Delhi – 110058">
                        <i class="fa-regular fa-copy"></i> <span>Copy Details</span>
                    </button>
                </div>
                <div class="info-card">
                    <h3>Bandhan Bank</h3>
                    <p><strong>Account Name:</strong> UnlistedGain Advantage Solutions Pvt. Ltd.</p>
                    <p><strong>Account Number:</strong> 20100079496491</p>
                    <p><strong>IFSC Code:</strong> BDBL0002801</p>
                    <p><strong>Account Type:</strong> Current Account</p>
                    <p><strong>Branch:</strong> Tilak Nagar Branch, Tilak Nagar, New Delhi – 110018</p>
                    <button type="button" class="bank-copy-btn" data-copy-text="Account Name: UnlistedGain Advantage Solutions Pvt. Ltd.&#10;Account Number: 20100079496491&#10;IFSC Code: BDBL0002801&#10;Account Type: Current Account&#10;Bank: Bandhan Bank&#10;Branch: Tilak Nagar Branch, Tilak Nagar, New Delhi – 110018">
                        <i class="fa-regular fa-copy"></i> <span>Copy Details</span>
                    </button>
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
    .bank-copy-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 16px;
        padding: 8px 18px;
        border-radius: 999px;
        border: 1.5px solid #87b942;
        background: #fff;
        color: #87b942;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
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
