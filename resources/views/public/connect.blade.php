@extends('layout.app')

@section('title', 'Contact UnlistedGain | Reach Our Experts via Call, Email or WhatsApp')
@section('meta_description', 'Have questions about unlisted shares? Connect with our dedicated team of experts. We are available via Phone, Email, and WhatsApp to assist you with your investments.')
@section('meta_keywords', 'contact unlistedgain, unlisted share support, pre-ipo investment help, unlisted share advisors India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/connect.css') }}">
@endpush

@section('content')
<main>
    {{-- Team Section --}}
    <section class="ug-connect-section">
        <h2 class="section-title">Connect With <span>The Team</span></h2>

        <div class="team-grid">
            <div class="team-card">
                <div class="image-wrap">
                    <img src="{{ asset('assets/img/profile.jpeg') }}" alt="Pawan Singh">
                </div>
                <div class="member-info">
                    <h3>Pawan Singh</h3>
                </div>
                <div class="cta-buttons">
                    <a href="tel:+919718881886" class="cta-btn cta-call"><i class="fas fa-phone-alt"></i> Call</a>
                    <a href="mailto:support@unlistedgain.com" class="cta-btn cta-email"><i class="fas fa-envelope"></i> Email</a>
                    <a href="https://wa.me/919718881886" class="cta-btn cta-whatsapp"><i class="fab fa-whatsapp"></i> Whatsapp</a>
                </div>
            </div>

            <div class="team-card">
                <div class="image-wrap">
                    <img src="{{ asset('assets/img/profile.jpeg') }}" alt="Virendra Singh Rautela">
                </div>
                <div class="member-info">
                    <h3>Virendra Singh Rautela</h3>
                </div>
                <div class="cta-buttons">
                    <a href="tel:+918506894923" class="cta-btn cta-call"><i class="fas fa-phone-alt"></i> Call</a>
                    <a href="mailto:support@unlistedgain.com" class="cta-btn cta-email"><i class="fas fa-envelope"></i> Email</a>
                    <a href="https://wa.me/918506894923" class="cta-btn cta-whatsapp"><i class="fab fa-whatsapp"></i> Whatsapp</a>
                </div>
            </div>

            <div class="team-card">
                <div class="image-wrap">
                    <img src="{{ asset('assets/img/profile.jpeg') }}" alt="Sarthak Bhatia">
                </div>
                <div class="member-info">
                    <h3>Sarthak Bhatia</h3>
                </div>
                <div class="cta-buttons">
                    <a href="tel:+918826632646" class="cta-btn cta-call"><i class="fas fa-phone-alt"></i> Call</a>
                    <a href="mailto:support@unlistedgain.com" class="cta-btn cta-email"><i class="fas fa-envelope"></i> Email</a>
                    <a href="https://wa.me/918826632646" class="cta-btn cta-whatsapp"><i class="fab fa-whatsapp"></i> Whatsapp</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Form --}}
    <section class="ug-contact-form-section">
        <div class="contact-form-container">
            <div class="form-header">
                <h3>Send Us A <span>Message</span></h3>
                <p>Have a specific inquiry? Fill out the form below and our team will get back to you within 24 hours.</p>
            </div>

            <form id="ugContactForm" class="contact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="emailAddr">Email Address</label>
                        <input type="email" id="emailAddr" name="emailAddr" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phoneNum">Phone Number</label>
                        <input type="tel" id="phoneNum" name="phoneNum" placeholder="Enter your mobile number" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="" disabled selected>Select a subject</option>
                            <option value="buy">Buy Unlisted Shares</option>
                            <option value="sell">Sell Unlisted Shares</option>
                            <option value="valuation">Portfolio Valuation</option>
                            <option value="general">General Inquiry</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="How can we help you?" required></textarea>
                </div>

                <div class="form-footer">
                    <button type="submit" class="submit-btn" id="contactSubmitBtn">Send Message</button>
                </div>
            </form>

            <div id="formSuccessMessage" class="form-success-message" style="display:none;">
                <div class="success-icon">&#10003;</div>
                <h4>Thank You!</h4>
                <p>Your message has been sent successfully. Our team will contact you shortly.</p>
                <button type="button" class="reset-btn" id="resetFormBtn">Send Another Message</button>
            </div>
        </div>
    </section>

    {{-- Social Box --}}
    <div class="contact-box">
        <h4>Like Share <span>Follow</span></h4>
        <div class="social-row">
            <a href="https://x.com/Unlistedgain" target="_blank"><img src="{{ asset('assets/img/twitter.png') }}" alt="Twitter"></a>
            <a href="https://www.facebook.com/profile.php?id=61587793225842" target="_blank"><img src="{{ asset('assets/img/facebook.png') }}" alt="Facebook"></a>
            <a href="https://www.instagram.com/unlistedgain?igsh=MWU2ZXo1OGVuOHhvcQ==" target="_blank"><img src="{{ asset('assets/img/instagram-pink.png') }}" alt="Instagram"></a>
            <a href="#"><img src="{{ asset('assets/img/telegram.png') }}" alt="Telegram"></a>
            <a href="https://www.linkedin.com/company/unlistedgain-advantage-solutions-pvt-ltd/" target="_blank"><img src="{{ asset('assets/img/linkedin-white.png') }}" alt="LinkedIn"></a>
            <a href="https://youtube.com/@unlistedgain?si=VsojxJIjQXecf2Bj" target="_blank"><img src="{{ asset('assets/img/youtube.png') }}" alt="YouTube"></a>
            <a href="#"><img src="{{ asset('assets/img/pinterest.png') }}" alt="Pinterest"></a>
            <a href="#"><img src="{{ asset('assets/img/reddit.png') }}" alt="Reddit"></a>
            <a href="tel:+919718881886"><img src="{{ asset('assets/img/phone-white.png') }}" alt="Phone"></a>
        </div>
    </div>
</main>
@endsection

