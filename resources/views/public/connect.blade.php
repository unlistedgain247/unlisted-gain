@extends('layout.app')

@section('title', 'Contact UnlistedGain | Reach Our Experts via Call, Email or WhatsApp')
@section('meta_description', 'Have questions about unlisted shares? Connect with our dedicated team of experts. We are available via Phone, Email, and WhatsApp to assist you with your investments.')
@section('meta_keywords', 'contact unlistedgain, unlisted share support, pre-ipo investment help, unlisted share advisors India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/connect.css') }}?v={{ filemtime(public_path('assets/css/pagecss/connect.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Contact Us']]])
@endsection

@section('content')
<main>

    {{-- Hero --}}
    <section class="cn-hero">
        <div class="cn-container">
            <span class="cn-tag">GET IN TOUCH</span>
            <h1>Connect With <span>Our Team</span></h1>
            <p>Have questions about unlisted shares or Pre-IPO investments? Our experts are available via Call, Email, and WhatsApp — reach out anytime.</p>

            <div class="cn-info-row">
                <div class="cn-info-item">
                    <div class="cn-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <strong>Office Address</strong>
                        <span>113/2, Meenakshi Garden, Tilak Nagar, New Delhi</span>
                    </div>
                </div>
                <div class="cn-info-item">
                    <div class="cn-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <strong>Email Us</strong>
                        <span><a href="mailto:support@unlistedgain.com">support@unlistedgain.com</a></span>
                    </div>
                </div>
                <div class="cn-info-item">
                    <div class="cn-info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <strong>Working Hours</strong>
                        <span>Mon – Sat, 10:00 AM – 7:00 PM</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Team Cards --}}
    <section class="cn-team">
        <div class="cn-container">
            <div class="cn-section-head">
                <h2>Speak Directly With <span>Our Experts</span></h2>
                <p>Choose a team member and connect instantly via your preferred channel.</p>
            </div>

            <div class="cn-team-grid">
                <div class="cn-team-card">
                    <div class="cn-avatar">PS</div>
                    <h3>Pawan Singh</h3>
                    <span class="cn-designation">Director</span>
                    <div class="cn-contact-btns">
                        <a href="tel:+919718881886" class="cn-btn cn-call">
                            <i class="fas fa-phone-alt"></i> Call
                        </a>
                        <a href="mailto:support@unlistedgain.com" class="cn-btn cn-email">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                        <a href="https://wa.me/919718881886" class="cn-btn cn-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <div class="cn-team-card">
                    <div class="cn-avatar">VSR</div>
                    <h3>Virendra Singh Rautela</h3>
                    <span class="cn-designation">CEO</span>
                    <div class="cn-contact-btns">
                        <a href="tel:+918506894923" class="cn-btn cn-call">
                            <i class="fas fa-phone-alt"></i> Call
                        </a>
                        <a href="mailto:support@unlistedgain.com" class="cn-btn cn-email">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                        <a href="https://wa.me/918506894923" class="cn-btn cn-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <div class="cn-team-card">
                    <div class="cn-avatar">SB</div>
                    <h3>Sarthak Bhatia</h3>
                    <span class="cn-designation">Investment Advisor</span>
                    <div class="cn-contact-btns">
                        <a href="tel:+918826632646" class="cn-btn cn-call">
                            <i class="fas fa-phone-alt"></i> Call
                        </a>
                        <a href="mailto:support@unlistedgain.com" class="cn-btn cn-email">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                        <a href="https://wa.me/918826632646" class="cn-btn cn-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Social Links --}}
    <section class="cn-social">
        <div class="cn-container">
            <div class="cn-section-head">
                <h2>Follow Us <span>Online</span></h2>
                <p>Stay updated with market insights, new listings and investment tips.</p>
            </div>

            <div class="cn-social-grid">
                <a href="https://x.com/Unlistedgain" target="_blank" class="cn-social-card cn-twitter">
                    <img src="{{ asset('assets/img/twitter.png') }}" alt="Twitter">
                    <span>Twitter / X</span>
                </a>
                <a href="https://www.facebook.com/profile.php?id=61587793225842" target="_blank" class="cn-social-card cn-facebook">
                    <img src="{{ asset('assets/img/facebook.png') }}" alt="Facebook">
                    <span>Facebook</span>
                </a>
                <a href="https://www.instagram.com/unlistedgain?igsh=MWU2ZXo1OGVuOHhvcQ==" target="_blank" class="cn-social-card cn-instagram">
                    <img src="{{ asset('assets/img/instagram-pink.png') }}" alt="Instagram">
                    <span>Instagram</span>
                </a>
                <a href="#" class="cn-social-card cn-telegram">
                    <img src="{{ asset('assets/img/telegram.png') }}" alt="Telegram">
                    <span>Telegram</span>
                </a>
                <a href="https://www.linkedin.com/company/unlistedgain-advantage-solutions-pvt-ltd/" target="_blank" class="cn-social-card cn-linkedin">
                    <img src="{{ asset('assets/img/linkedin-white.png') }}" alt="LinkedIn">
                    <span>LinkedIn</span>
                </a>
                <a href="https://youtube.com/@unlistedgain?si=VsojxJIjQXecf2Bj" target="_blank" class="cn-social-card cn-youtube">
                    <img src="{{ asset('assets/img/youtube.png') }}" alt="YouTube">
                    <span>YouTube</span>
                </a>
            </div>
        </div>
    </section>

</main>
@endsection
