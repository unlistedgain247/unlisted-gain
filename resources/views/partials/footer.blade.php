<footer class="main-footer">
    <div class="footer-inner">

        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-brand-badge">
                    <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" alt="UnlistedGain" class="footer-brand-logo">
                </div>
                <p class="footer-tagline">India's trusted marketplace to buy &amp; sell unlisted and pre-IPO shares.</p>

                <div class="social-icons">
                    <a href="https://whatsapp.com/channel/0029Vb7maZ63bbV2NEoSxw28" class="social-btn sb-whatsapp" target="_blank" rel="noopener" aria-label="WhatsApp">
                        <img src="{{ asset('assets/img/whatsapp-white.png') }}" alt="">
                    </a>
                    <a href="https://x.com/Unlistedgain" class="social-btn sb-x" target="_blank" rel="noopener" aria-label="X (formerly Twitter)">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=61587793225842" class="social-btn sb-facebook" target="_blank" rel="noopener" aria-label="Facebook">
                        <img src="{{ asset('assets/img/facebook.png') }}" alt="">
                    </a>
                    <a href="https://www.instagram.com/unlistedgain?igsh=MWU2ZXo1OGVuOHhvcQ==" class="social-btn sb-instagram" target="_blank" rel="noopener" aria-label="Instagram">
                        <img src="{{ asset('assets/img/instagram-pink.png') }}" alt="">
                    </a>
                    <a href="https://www.linkedin.com/company/unlistedgain-advantage-solutions-pvt-ltd/" class="social-btn sb-linkedin" target="_blank" rel="noopener" aria-label="LinkedIn">
                        <img src="{{ asset('assets/img/linkedin-white.png') }}" alt="">
                    </a>
                    <a href="https://youtube.com/@unlistedgain?si=VsojxJIjQXecf2Bj" class="social-btn sb-youtube" target="_blank" rel="noopener" aria-label="YouTube">
                        <img src="{{ asset('assets/img/youtube.png') }}" alt="">
                    </a>
                    <a href="tel:+919891881886" class="social-btn sb-phone" aria-label="Call us">
                        <img src="{{ asset('assets/img/phone-white.png') }}" alt="">
                    </a>
                </div>
            </div>

            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="{{ url('/off-market-annexure') }}">Off Market Annexure</a></li>
                    <li><a href="{{ url('/pan-unlisted-shares') }}">PAN of unlisted shares</a></li>
                    <li><a href="{{ url('/sebi-guidelines') }}">SEBI guidelines</a></li>
                    <li><a href="{{ url('/bank-account-details') }}">Bank Account Details</a></li>
                    <li><a href="{{ url('/knowledge-centre') }}">Knowledge centre</a></li>
                    <li><a href="{{ url('/faq') }}">Frequently Asked Questions</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>UnlistedGain</h3>
                <ul class="footer-links">
                    <li><a href="{{ url('/about') }}">About Us</a></li>
                    <li><a href="{{ url('/contact-us') }}">Contact Us</a></li>
                    <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ url('/terms-of-use') }}">Terms of Use</a></li>
                </ul>
            </div>

            <div class="footer-column footer-office">
                <h3>Our Office</h3>
                <div class="address-box">
                    <h4><svg class="pin-icon" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>Head office</h4>
                    <p>113/2, 1st floor, Meenakshi Garden,<br>Tilak Nagar, New Delhi - 110018</p>
                </div>
                <div class="address-box">
                    <h4><svg class="pin-icon" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>Branch office</h4>
                    <p>UG-10, Somdutt Chamber - 2,<br>Bhikaji Cama Place, New Delhi - 110066</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="copyright">&copy; Copyright {{ date('Y') }} UnlistedGain Pvt. Ltd. All Rights Reserved.</p>
            <p class="disclaimer"><strong>Disclaimer:</strong> UnlistedGain is an information portal owned and operated by
                UnlistedGain Pvt. Ltd. UnlistedGain is not
                an investment advisory portal and does not make any investment recommendations and therefore does
                not require any license from SEBI/RBI or regulatory authority in India. Investors are advised to
                use the data at their own risks.</p>
        </div>

    </div>
</footer>
