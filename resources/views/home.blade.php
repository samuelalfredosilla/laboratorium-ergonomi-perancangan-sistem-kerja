@extends('layouts.app')

@section('content')

<!-<!-- ==========================================
     HERO SECTION WITH VIDEO BACKGROUND
=========================================== -->
<section class="hero-section">
    <!-- 1. Video Element -->
    <video autoplay loop muted playsinline class="hero-video">
        <source src="{{ asset('videos/bg-hero.mp4') }}" type="video/mp4">
        Browser Anda tidak mendukung tag video.
    </video>

    <!-- 2. Dark Overlay Layer (Agar Teks Terbaca Kerap) -->
    <div class="hero-overlay"></div>

    <!-- 3. Hero Content (Teks & Tombol) -->
    <div class="hero-content">
        <div class="hero-container">
            <!-- Subtitle Badge -->
            <span class="hero-badge">
                INDUSTRIAL ENGINEERING - UNIVERSITAS TRUNOJOYO MADURA
            </span>

            <!-- Main Title -->
            <h1 class="hero-title">
                ERGONOMIC<br />AND WORK SYSTEM DESIGN LABORATORY
            </h1>

            <!-- Description -->
            <p class="hero-desc">
                Integrating human factors, occupational safety, and innovative design thinking to build future industrial solutions.
            </p>

            <!-- CTA Button -->
            <div class="hero-btn-wrapper">
                <a href="#about" class="btn-hero">
                    GET TO KNOW <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     SECTION 3: PILLARS SECTION (3 Utama)
=========================================== -->
<section class="pillars-section">
    <div class="pillars-container">
        <div class="pillars-grid">

            <!-- Pillar 1 -->
            <div class="pillar-card">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-microscope"></i>
                </div>
                <h3 class="pillar-title">RESEARCH</h3>
                <p class="pillar-desc">
                    Developing innovative studies in ergonomics, human integrated systems, and modern product design.
                </p>
            </div>

            <!-- Pillar 2 -->
            <div class="pillar-card">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="pillar-title">ACADEMIC</h3>
                <p class="pillar-desc">
                    Providing top-tier practical education, practicums, and student mentoring in industrial engineering.
                </p>
            </div>

            <!-- Pillar 3 -->
            <div class="pillar-card">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="pillar-title">COMMUNITY SERVICE</h3>
                <p class="pillar-desc">
                    Applying industrial technology and ergonomic solutions directly to SMEs and the wider public.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     SECTION 4: ABOUT US SECTION
=========================================== -->
<section id="about" class="about-section">
    <div class="about-container">
        <div class="about-grid">

            <!-- Left Side: Content Card -->
            <div class="about-card">
                <div>
                    <!-- Badge Accent Baru -->
                    <span class="about-badge">ABOUT OUR LABORATORY</span>

                    <!-- Judul Tebal & Tegas -->
                    <h2 class="about-hero-title">
                        ERGONOMIC AND WORK SYSTEM DESIGN LABORATORY
                    </h2>

                    <!-- Deskripsi Rapi -->
                    <div class="about-desc">
                        <p>
                            Located within the Department of Industrial Engineering, Faculty of Engineering, Universitas Trunojoyo Madura, the <strong>EPSK Laboratory</strong> has been at the forefront of ergonomic research and product development since 2005.
                        </p>
                        <p>
                            We focus on optimizing human well-being and overall system performance by applying ergonomic principles, work methodology engineering, physical simulation, and contemporary product design techniques.
                        </p>
                    </div>
                </div>

                <!-- Accent Line Decorative (Sebagai Pengisi Bagian Bawah yang Kosong) -->
                <div class="about-card-footer">
                    <div class="about-accent-line"></div>
                </div>
            </div>

            <!-- Right Side: Image Slider -->
            <div class="slider-wrapper">
                <div id="imageSlider" class="slider-container">
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1000&q=80" alt="Laboratory Team 1">
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1000&q=80" alt="Laboratory Practicum 2">
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1000&q=80" alt="Research Activity 3">
                    </div>
                </div>

                <!-- Navigation Controls -->
                <button id="prevBtn" class="slider-btn prev">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button id="nextBtn" class="slider-btn next">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

                <!-- Indicator Dots -->
                <div id="sliderDots" class="slider-dots"></div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     SECTION 5: LATEST NEWS SECTION
=========================================== -->
<section class="news-section">
    <div class="news-container">

        <!-- Banner Header -->
        <div class="news-header-banner">
            <h2 class="news-header-title">LATEST NEWS</h2>
            <p class="news-header-desc">Stay updated with our latest activities, workshops, and achievements</p>
        </div>

        <!-- News Cards Grid (3 Columns) -->
        <div class="news-grid">
            @foreach($latestNews as $news)
            <div class="news-card">
                <div>
                    <!-- Thumbnail -->
                    <div class="news-image-wrapper">
                        <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}" class="news-image">
                        <span class="news-category-badge">
                            {{ $news['category'] }}
                        </span>
                    </div>

                    <!-- Meta & Content -->
                    <div class="news-card-body">
                        <div class="news-meta">
                            <span><i class="fa-regular fa-calendar"></i> {{ $news['date'] }}</span>
                            <span><i class="fa-regular fa-user"></i> {{ $news['author'] }}</span>
                        </div>
                        <h3 class="news-title">
                            {{ $news['title'] }}
                        </h3>
                    </div>
                </div>

                <!-- Read More Button -->
                <div class="news-card-footer">
                    <a href="#" class="btn-read-more">
                        READ MORE
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- See More Button (Inside Maroon Area) -->
        <div class="news-see-more-wrapper">
            <a href="#" class="btn-see-more">
                SEE MORE
                <i class="fa-solid fa-angles-right"></i>
            </a>
        </div>

    </div>
</section>


<!-- ==========================================
     SECTION 6: CONTACT US & GOOGLE MAPS SECTION
=========================================== -->
<section id="contact" class="contact-section">
    <div class="contact-container">
        <div class="contact-grid">

            <!-- Left Side: Address & Social Media -->
            <div class="contact-info-wrapper">
                <div>
                    <h2 class="contact-title">Contact Us</h2>
                    <p class="contact-desc">
                        Have any questions regarding research collaborations, laboratory testing, or academic activities? Get in touch with us!
                    </p>

                    <!-- Address Info -->
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon-box">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="contact-item-title">Address</h4>
                                <p class="contact-item-text">
                                    Perumahan Telang Inda, Telang, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162
                                </p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-box">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="contact-item-title">Email</h4>
                                <p class="contact-item-text">epsk.trunojoyo@gmail.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Buttons -->
                <div>
                    <h4 class="social-media-title">Follow Our Social Media</h4>
                    <div class="social-links">
                        <a href="https://www.linkedin.com/company/laboratorium-ergonomi-dan-perancangan-sistem-kerja/" target="_blank" class="social-btn li">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="https://www.instagram.com/epsk.trunojoyo?igsh=MW80Ymx4MDg3dnNtdQ==" target="_blank" class="social-btn ig">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <!-- TikTok Button -->
                        <a href="https://www.tiktok.com/@epsk.trunojoyo?_r=1&_t=ZS-98eIrGucmqj" target="_blank" class="social-btn tt">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="https://youtube.com/@lab.epsktrunojoyo6071?si=Oc7izSAmZenv-5pw" target="_blank" class="social-btn yt">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Google Maps Embed -->
            <div class="maps-wrapper">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.974962303046!2d112.72557479999999!3d-7.1288926!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd80362972678a7%3A0xe507ebcb91dd8b90!2sLab%20EPSK%20(Ergonomi%20dan%20Sistem%20Perancangan%20Kerja)!5e0!3m2!1sid!2sid!4v1785978902105!5m2!1sid!2sid"
                    class="maps-iframe"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin">
                </iframe>
            </div>
        </div>
    </div>
</section>

@endsection
