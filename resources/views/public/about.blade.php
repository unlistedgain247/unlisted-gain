@extends('layout.app')

@section('title', 'About UnlistedGain | Our Mission & Expertise in Unlisted Market')
@section('meta_description', 'Learn about UnlistedGain Advantage Solutions Pvt. Ltd., India\'s premier gateway to Pre-IPO and Unlisted Shares. Meet our leadership team.')
@section('meta_keywords', 'about unlistedgain, unlisted share experts, pre-ipo marketplace India, unlisted investment platform')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/about.css') }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'About Us']]])
@endsection

@section('content')
<main>

    {{-- WHO WE ARE --}}
    <section class="ab-who">
        <div class="ab-container">
            <div class="ab-who-text">
                <span class="ab-tag">WHO WE ARE</span>
                <h1>About <span>Unlistedgain Advantage Solutions Pvt. Ltd.</span></h1>
                <p>Welcome to Unlistedgain Advantage Solutions Pvt. Ltd., your premier gateway to the evolving world of Pre-IPO and Unlisted Shares. We specialize in bridge-building between astute investors and high-growth opportunities that exist beyond the traditional stock exchange.</p>
                <div class="ab-who-pills">
                    <span class="ab-pill">Simple</span>
                    <span class="ab-pill">Effective</span>
                    <span class="ab-pill">Affordable</span>
                </div>
            </div>
            <div class="ab-who-stats">
                <div class="ab-stat">
                    <strong>15+</strong>
                    <span>Years Combined Expertise</span>
                </div>
                <div class="ab-stat">
                    <strong>5,000+</strong>
                    <span>Families Served</span>
                </div>
                <div class="ab-stat">
                    <strong>500+</strong>
                    <span>Companies Tracked</span>
                </div>
                <div class="ab-stat">
                    <strong>&#8377;100Cr+</strong>
                    <span>Transaction Volume</span>
                </div>
            </div>
        </div>
    </section>

    {{-- LEADERSHIP TEAM --}}
    <section class="ab-team">
        <div class="ab-container">
            <div class="ab-section-head">
                <span class="ab-tag">OUR LEADERSHIP</span>
                <h2>The Team Behind <span>UnlistedGain</span></h2>
            </div>

            {{-- CEO Card --}}
            <div class="ab-ceo-card">
                <div class="ab-ceo-avatar">
                    <div class="ab-avatar-placeholder ceo">VSR</div>
                </div>
                <div class="ab-ceo-info">
                    <span class="ab-role-tag">Chief Executive Officer</span>
                    <h3>Virendra Singh Rautela</h3>
                    <p>Virendra Singh Rautela serves as the Chief Executive Officer of Unlistedgain, bringing an unparalleled depth of expertise to the private equity and unlisted share markets. With a career spanning over <strong>15 years</strong>, Mr. Rautela is a seasoned veteran in the financial services industry. His journey is defined by a profound understanding of the complexities, risks, and immense opportunities within the unlisted space — a sector that requires not just data, but the intuition that only decades of experience can provide.</p>
                </div>
            </div>

            {{-- Directors Grid --}}
            <div class="ab-directors-grid">
                <div class="ab-director-card">
                    <div class="ab-avatar-placeholder dir">MA</div>
                    <div class="ab-director-info">
                        <span class="ab-role-tag">Director</span>
                        <h4>Manish Arora</h4>
                        <p>A key leader at Dhanlabh Capital Services LLP, instrumental in delivering data-driven wealth strategies to over <strong>5,000 families</strong>. With deep expertise in Indian financial markets — from SIPs to high-growth opportunities — he ensures every client experience is rooted in transparency and our "Right Planning" philosophy.</p>
                    </div>
                </div>
                <div class="ab-director-card">
                    <div class="ab-avatar-placeholder dir">PKS</div>
                    <div class="ab-director-info">
                        <span class="ab-role-tag">Director</span>
                        <h4>Pawan Kumar Singh</h4>
                        <p>Leading our franchise network's strategic expansion with over <strong>12 years</strong> of experience. He excels in building high-value partnerships and specializing in Unlisted Shares and Pre-IPO placements, driving revenue growth and providing expert training support to business associates.</p>
                    </div>
                </div>
                <div class="ab-director-card">
                    <div class="ab-avatar-placeholder dir">ASD</div>
                    <div class="ab-director-info">
                        <span class="ab-role-tag">Director</span>
                        <h4>Amrinder Singh Dhillon</h4>
                        <p>Providing critical strategic oversight with a distinguished career spanning more than <strong>25 years</strong> in the financial and real estate sectors. His extensive background allows him to provide a holistic approach to portfolio building, ensuring every asset class works in synergy to achieve long-term financial goals.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- WHAT WE DO --}}
    <section class="ab-whatwedo">
        <div class="ab-container">
            <div class="ab-section-head">
                <span class="ab-tag">WHAT WE DO</span>
                <h2>Our <span>Core Services</span></h2>
                <p>We facilitate the seamless buying and selling of unlisted shares, providing clients with early access to companies poised for future public listings.</p>
            </div>
            <div class="ab-services-grid">
                <div class="ab-service-card">
                    <div class="ab-service-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#87b942" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <h4>Strategic Value</h4>
                    <p>We don't just facilitate transactions; we provide the strategic insight necessary to navigate the complexities of the Pre-IPO market.</p>
                </div>
                <div class="ab-service-card">
                    <div class="ab-service-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#87b942" d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
                    </div>
                    <h4>Wealth Creation</h4>
                    <p>Our primary goal is to help clients and associates unlock long-term value and build wealth through diversified investment portfolios.</p>
                </div>
                <div class="ab-service-card">
                    <div class="ab-service-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#87b942" d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                    </div>
                    <h4>Expansion &amp; Growth</h4>
                    <p>Leveraging our extensive network, we focus on identifying private equity opportunities that offer significant growth potential.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY CHOOSE US --}}
    <section class="ab-why">
        <div class="ab-container">
            <div class="ab-why-inner">
                <div class="ab-why-text">
                    <span class="ab-tag light">WHY CHOOSE US</span>
                    <h2>Our <span>Philosophy</span></h2>
                    <p>At Unlistedgain, we believe that access to high-potential investments should be backed by transparency and expertise. We are committed to fostering a culture of strategic planning and excellence, ensuring that every associate we work with has the tools and opportunities to succeed in the dynamic financial landscape of India.</p>
                    <p class="ab-why-tagline"><strong>Simple. Effective. Affordable.</strong><br>Empowering your portfolio with the high-growth potential of Pre-IPO shares through a streamlined, cost-efficient, and results-driven investment experience.</p>
                </div>
                <div class="ab-why-pills">
                    <div class="ab-why-pill">
                        <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#87b942" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Wealth-Centric Philosophy
                    </div>
                    <div class="ab-why-pill">
                        <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#87b942" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Extensive Ecosystem
                    </div>
                    <div class="ab-why-pill">
                        <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#87b942" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Results-Driven Approach
                    </div>
                    <div class="ab-why-pill">
                        <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#87b942" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Strategic Transparency
                    </div>
                    <div class="ab-why-pill">
                        <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#87b942" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Experienced Team
                    </div>
                    <div class="ab-why-pill">
                        <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#87b942" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Proven Network
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- OUR PROCESS --}}
    <section class="ab-process">
        <div class="ab-container">
            <div class="ab-section-head">
                <span class="ab-tag">OUR PROCESS</span>
                <h2>From Interest to <span>Ownership</span></h2>
                <p>Navigating the unlisted market requires precision. We've distilled 12+ years of expertise into a streamlined six-step workflow designed to move you from interest to ownership with total confidence.</p>
            </div>
            <div class="ab-process-grid">
                <div class="ab-process-step">
                    <div class="ab-step-num">01</div>
                    <h4>Rigorous Pre-Vetting &amp; Due Diligence</h4>
                    <p>Every asset is thoroughly vetted before it reaches you.</p>
                </div>
                <div class="ab-process-step">
                    <div class="ab-step-num">02</div>
                    <h4>Personalized Strategic Consultation</h4>
                    <p>We align every opportunity with your unique wealth goals.</p>
                </div>
                <div class="ab-process-step">
                    <div class="ab-step-num">03</div>
                    <h4>Curated Opportunity Selection</h4>
                    <p>Handpicked investments that match your risk profile.</p>
                </div>
                <div class="ab-process-step">
                    <div class="ab-step-num">04</div>
                    <h4>Secure &amp; Seamless Execution</h4>
                    <p>Digital-first, secure share transfer with full documentation.</p>
                </div>
                <div class="ab-process-step">
                    <div class="ab-step-num">05</div>
                    <h4>Active Portfolio Diversification</h4>
                    <p>Ongoing risk management across asset classes.</p>
                </div>
                <div class="ab-process-step">
                    <div class="ab-step-num">06</div>
                    <h4>Strategic Exit Intelligence</h4>
                    <p>Expert guidance until your investment realizes its full potential.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="ab-cta">
        <div class="ab-container">
            <div class="ab-cta-inner">
                <h2>Ready to Take Your Portfolio to the Next Level?</h2>
                <p>At Unlistedgain Advantage Solutions, we believe that the most significant wealth-building opportunities shouldn't be reserved for a select few.</p>
                <div class="ab-cta-actions">
                    <a href="/connect" class="ab-cta-btn primary">Get in Touch</a>
                    <a href="/unlisted" class="ab-cta-btn outline">Browse Stocks</a>
                </div>
                <div class="ab-cta-contact">
                    <span>+91 85068 94923</span>
                    <span>www.Unlistedgain.com</span>
                    <span>113/2, Meenakshi Garden, Tilak Nagar, New Delhi</span>
                </div>
            </div>
        </div>
    </section>

    {{-- PARTNERS & REGISTRATION --}}
    <section class="ab-partners">
        <div class="ab-container">
            <div class="ab-partners-grid">
                <div class="ab-partner-card">
                    <h3>Bank / Payment Partners</h3>
                    <div class="ab-bank-logos">
                        <img src="{{ asset('assets/img/bandhan.jpeg') }}" alt="Bandhan Bank">
                        <img src="{{ asset('assets/img/icici.png') }}" alt="ICICI Bank">
                    </div>
                </div>
                <div class="ab-partner-card">
                    <h3>Registration Details</h3>
                    <div class="ab-reg-details">
                        <p class="ab-reg-name">Unlistedgain Advantage Solutions Pvt. Ltd.</p>
                        <p><strong>CIN :</strong> U66190DL2026PTC463467</p>
                        <p><strong>TAN :</strong> DELU10559D</p>
                        <p><strong>PAN :</strong> AAECU0789R</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
