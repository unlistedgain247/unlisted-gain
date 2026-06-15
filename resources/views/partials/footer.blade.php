<footer class="main-footer">
    <div class="footer-top">
        <div class="footer-logo">
            <img src="{{ asset('assets/img/unlisted-head.jpeg') }}" alt="UnlistedGain">
        </div>
    </div>

    <div class="footer-content">
        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="{{ url('/off-market-annexure') }}">Off Market Annexure</a></li>
                <li><a href="{{ url('/pan-unlisted-shares') }}">PAN of unlisted shares</a></li>
                <li><a href="{{ url('/sebi-guidelines') }}">SEBI guidelines</a></li>
                <li><a href="{{ url('/knowledge-centre') }}">Knowledge centre</a></li>
                <li><a href="{{ url('/faq') }}">Frequently Asked Questions</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>UnlistedGain</h3>
            <ul class="footer-links">
                <li><a href="{{ url('/about') }}">About Us</a></li>
                <li><a href="{{ url('/connect') }}">Contact Us</a></li>
                <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ url('/terms-of-use') }}">Terms of Use</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Our Office</h3>
            <div class="address-box">
                <h4>Head office</h4>
                <p>113/2, 1st floor, Meenakshi Garden,<br>Tilak Nagar, New Delhi - 110018</p>
            </div>
            <div class="address-box">
                <h4>Branch office</h4>
                <p>UG-10, Somdutt Chamber - 2,<br>Bhikaji Cama Place, New Delhi - 110066</p>
            </div>
        </div>
    </div>

    <div class="footer-social">
        <div class="social-icons">
            <a href="https://whatsapp.com/channel/0029Vb7maZ63bbV2NEoSxw28" class="social-btn" target="_blank">
                <img src="{{ asset('assets/img/whatsapp-white.png') }}" alt="WhatsApp">
            </a>
            <a href="https://x.com/Unlistedgain" class="social-btn" target="_blank">
                <img src="{{ asset('assets/img/twitter.png') }}" alt="Twitter">
            </a>
            <a href="https://www.facebook.com/profile.php?id=61587793225842" class="social-btn" target="_blank">
                <img src="{{ asset('assets/img/facebook.png') }}" alt="Facebook">
            </a>
            <a href="https://www.instagram.com/unlistedgain?igsh=MWU2ZXo1OGVuOHhvcQ==" class="social-btn" target="_blank">
                <img src="{{ asset('assets/img/instagram-pink.png') }}" alt="Instagram">
            </a>
            <a href="#" class="social-btn">
                <img src="{{ asset('assets/img/telegram.png') }}" alt="Telegram">
            </a>
            <a href="https://www.linkedin.com/company/unlistedgain-advantage-solutions-pvt-ltd/" class="social-btn" target="_blank">
                <img src="{{ asset('assets/img/linkedin-white.png') }}" alt="LinkedIn">
            </a>
            <a href="https://youtube.com/@unlistedgain?si=VsojxJIjQXecf2Bj" class="social-btn" target="_blank">
                <img src="{{ asset('assets/img/youtube.png') }}" alt="YouTube">
            </a>
            <a href="#" class="social-btn">
                <img src="{{ asset('assets/img/phone-white.png') }}" alt="Phone">
            </a>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="copyright">
            <p>&copy; Copyright {{ date('Y') }} UnlistedGain Pvt. Ltd. All Rights Reserved.</p>
        </div>
        <div class="disclaimer">
            <p><strong>Disclaimer:</strong> UnlistedGain is an information portal owned and operated by
                UnlistedGain Pvt. Ltd. The data feed is provided by Accord Fintech Pvt. Ltd. UnlistedGain is not
                an investment advisory portal and does not make any investment recommendations and therefore does
                not require any license from SEBI/RBI or regulatory authority in India. Investors are advised to
                use the data at their own risks.</p>
        </div>
    </div>
</footer>
