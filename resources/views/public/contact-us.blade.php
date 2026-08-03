@extends('layout.app')

@section('title', 'Contact UnlistedGain | Reach Our Experts via Call, Email or WhatsApp')
@section('meta_description', 'Have questions about unlisted shares? Connect with our dedicated team of experts. We are available via Phone, Email, and WhatsApp to assist you with your investments.')
@section('meta_keywords', 'contact unlistedgain, unlisted share support, pre-ipo investment help, unlisted share advisors India')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/contact-us.css') }}?v={{ filemtime(public_path('assets/css/pagecss/contact-us.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Contact Us']]])
@endsection

@section('content')
<main>

    {{-- Hero --}}
    <section class="cn-hero">
        <div class="cn-container cn-hero-grid">
            <div class="cn-hero-copy">
                <span class="cn-tag">GET IN TOUCH</span>
                <h1>Connect With <span>Our Team</span></h1>
                <p>Have questions about unlisted shares or Pre-IPO investments? Our experts are available via Call, Email, and WhatsApp — reach out anytime.</p>

                <div class="cn-info-list">
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
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <strong>Call Us</strong>
                            <span><a href="tel:+919891881886">+91 98918 81886</a></span>
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

            @php
                $officeAddress = '113/2, Meenakshi Garden, Tilak Nagar, New Delhi';
            @endphp
            <div class="cn-hero-map">
                <div class="cn-map-card">
                    <iframe
                        src="https://www.google.com/maps?q={{ urlencode($officeAddress) }}&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="UnlistedGain Office Location">
                    </iframe>
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($officeAddress) }}"
                   target="_blank" rel="noopener" class="cn-map-directions">
                    <i class="fas fa-location-arrow"></i> Get Directions
                </a>
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
