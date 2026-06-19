@extends('layout.app')

@section('title', 'About UnlistedGain | Our Mission & Expertise in Unlisted Market')
@section('meta_description', 'Learn about UnlistedGain, India\'s leading platform for unlisted shares. Our mission is to democratize access to pre-IPO investments with transparency and trust.')
@section('meta_keywords', 'about unlistedgain, unlisted share experts, pre-ipo marketplace India, unlisted investment platform')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/about.css') }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'About Us']]])
@endsection

@section('content')
<main>
    <section class="ug-about-main">
        <div class="about-container">
            <div class="about-content">
                <h1 class="about-title">About <span>Unlistedgain Advantage Solutions Pvt. Ltd.</span></h1>
                <p class="about-intro">
                    Welcome to Unlistedgain Advantage Solutions Pvt. Ltd., your premier gateway to the evolving
                    world of Pre-IPO and Unlisted Shares. We specialize in bridge-building between astute investors
                    and high-growth opportunities that exist beyond the traditional stock exchange.
                </p>

                <div class="about-section">
                    <h3>Our Leadership</h3>
                    <p>Driven by the vision of our Director, <strong>Pawan Kumar Singh</strong>, Unlistedgain is
                        built on a foundation of over 12 years of professional expertise. With a deep-rooted
                        background in franchisee acquisition and strategic development, Mr. Singh brings a unique
                        perspective to the private equity sector. His leadership is focused on sustainable business
                        growth and creating a robust ecosystem where business associates and clients can thrive.</p>
                </div>

                <div class="about-section">
                    <h3>What We Do</h3>
                    <p>We facilitate the seamless buying and selling of unlisted shares, providing our clients with
                        early access to companies poised for future public listings. In a market where timing and
                        information are everything, we act as a trusted partner, offering:</p>
                    <ul class="about-list">
                        <li><strong>Strategic Value:</strong> We don't just facilitate transactions; we provide the
                            strategic insight necessary to navigate the complexities of the Pre-IPO market.</li>
                        <li><strong>Wealth Creation:</strong> Our primary goal is to help our clients and associates
                            unlock long-term value and build wealth through diversified investment portfolios.</li>
                        <li><strong>Expansion &amp; Growth:</strong> Leveraging our extensive network, we focus on
                            identifying private equity opportunities that offer significant growth potential.</li>
                    </ul>
                </div>

                <div class="about-section">
                    <h3>Our Philosophy</h3>
                    <p>At Unlistedgain, we believe that access to high-potential investments should be backed by
                        transparency and expertise. We are committed to fostering a culture of strategic planning
                        and excellence, ensuring that every associate we work with has the tools and opportunities
                        to succeed in the dynamic financial landscape of India.</p>
                </div>
            </div>

            <div class="about-image">
                <img src="{{ asset('assets/img/about-leadership.png') }}" alt="Unlistedgain Leadership">
            </div>
        </div>
    </section>

    <section class="ug-about">
        <div class="about-mission-grid">
            <div class="mission-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#39b54a" d="M12,2L4.5,20.29L5.21,21L12,18L18.79,21L19.5,20.29L12,2Z" />
                    </svg>
                </div>
                <h3>Our Mission</h3>
                <p>We believe every investor should have access to the unlisted market. Secure stakes in high-growth
                    unicorns before their IPO, diversify into private equity, and trade with confidence via our
                    secure, transparent platform.</p>
            </div>
            <div class="mission-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#39b54a"
                            d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" />
                    </svg>
                </div>
                <h3>Our Vision</h3>
                <p>Our vision is to democratize the unlisted market by providing transparent, secure access to
                    private equity, empowering every investor to back future unicorns and diversify portfolios
                    before they go public.</p>
            </div>
        </div>

        <div class="about-values">
            <div class="value-item">
                <span class="value-number">01</span>
                <h4>Invest Early</h4>
                <p>Secure stakes in "Unicorns" and established leaders long before they hit the NSE or BSE.</p>
            </div>
            <div class="value-item">
                <span class="value-number">02</span>
                <h4>Diversify Beyond the Sensex</h4>
                <p>Move beyond standard mutual funds and listed stocks into the high-potential private equity space.</p>
            </div>
            <div class="value-item">
                <span class="value-number">03</span>
                <h4>Trade with Confidence</h4>
                <p>Benefit from a platform built on price transparency, verified data, and secure digital settlements.</p>
            </div>
        </div>

        <div class="about-stats">
            <div class="stat-box">
                <span class="stat-num">500+</span>
                <span class="stat-label">Companies Tracked</span>
            </div>
            <div class="stat-box">
                <span class="stat-num">10k+</span>
                <span class="stat-label">Happy Investors</span>
            </div>
            <div class="stat-box">
                <span class="stat-num">&#8377;100Cr+</span>
                <span class="stat-label">Transaction Volume</span>
            </div>
        </div>
    </section>

    <section class="ug-partners-licenses">
        <div class="partners-grid">
            <div class="partner-card">
                <h3>Bank/Payment Partner</h3>
                <div class="logo-cluster bank-logos">
                    <img src="{{ asset('assets/img/bandhan.jpeg') }}" alt="Bandhan Bank">
                    <img src="{{ asset('assets/img/icici.png') }}" alt="ICICI Bank">
                </div>
            </div>

            <div class="partner-card">
                <h3>Registration Details</h3>
                <div class="partner-details" style="text-align:left;">
                    <p class="partner-name">Unlistedgain Advantage Solutions Pvt. Ltd.</p>
                    <p style="margin-bottom:5px;"><strong>CIN :</strong> U66190DL2026PTC463467</p>
                    <p style="margin-bottom:5px;"><strong>TAN :</strong> DELU10559D</p>
                    <p style="margin-bottom:5px;"><strong>PAN :</strong> AAECU0789R</p>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
