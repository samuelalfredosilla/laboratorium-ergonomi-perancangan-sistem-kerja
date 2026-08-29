@extends('layouts.app')

@section('content')

<!-- ==========================================
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
                INDUSTRIAL ENGINEERING - UNIVERSITAS TRUNODJOYO MADURA
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
                <h3 class="pillar-title">PENELITIAN</h3>
                <p class="pillar-desc">
                    Mengembangkan studi inovatif dalam bidang ergonomi, sistem terintegrasi manusia, dan perancangan produk modern.
                </p>
            </div>

            <!-- Pillar 2 -->
            <div class="pillar-card">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="pillar-title">AKADEMIK</h3>
                <p class="pillar-desc">
                    Menyelenggarakan pendidikan praktis berkualitas tinggi, praktikum, serta pembimbingan mahasiswa di bidang teknik industri.
                </p>
            </div>

            <!-- Pillar 3 -->
            <div class="pillar-card">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="pillar-title">PENGABDIAN MASYARAKAT</h3>
                <p class="pillar-desc">
                    Menerapkan teknologi industri dan solusi ergonomi secara langsung kepada UMKM serta masyarakat luas.
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
                        LABORATORIUM ERGONOMI DAN PERANCANGAN SISTEM KERJA
                    </h2>

                    <!-- Deskripsi Rapi -->
                    <div class="about-desc">
                        <p>
                            Laboratorium Ergonomi dan Perancangan Sistem Kerja Universitas Trunodjoyo Madura merupakan fasilitas akademik dan riset di bawah Program Studi Teknik Industri yang berfokus pada penerapan prinsip ergonomi, keselamatan kerja, dan desain sistem kerja yang efisien dan manusiawi. Laboratorium ini mendukung praktikum, penelitian, dan pengembangan teknologi berbasis sistem untuk meningkatkan produktivitas, efisiensi, kenyamanan, serta keselamatan dan kesehatan kerja (K3).
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
                    @forelse($sliders as $slider)
                        <div class="slide">
                            <img src="{{ Str::startsWith($slider->image_path, 'http') ? $slider->image_path : asset('storage/' . $slider->image_path) }}" alt="{{ $slider->title }}">
                        </div>
                    @empty
                        <div class="slide">
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1000&q=80" alt="Default Image">
                        </div>
                    @endforelse
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
                        <img src="{{ Str::startsWith($news->image, 'http') ? $news->image : asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="news-image">
                        <span class="news-category-badge">
                            {{ $news->category_name }}
                        </span>
                    </div>

                    <!-- Meta & Content -->
                    <div class="news-card-body">
                        <div class="news-meta">
                            <span><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($news->published_at)->format('d M Y') }}</span>
                            <span><i class="fa-regular fa-user"></i> Admin</span>
                        </div>
                        <h3 class="news-title">
                            {{ $news->title }}
                        </h3>
                    </div>
                </div>

                <!-- Read More Button -->
                <div class="news-card-footer">
                    <a href="{{ route('news.show', $news->slug) }}" class="btn-read-more">
                        READ MORE
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- See More Button (Inside Maroon Area) -->
        <div class="news-see-more-wrapper">
            <a href="{{ route('news.index') }}" class="btn-see-more">
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
                                    {{ $settings['contact_address'] ?? 'Perumahan Telang Indah, Bangkalan' }}
                                </p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-box">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="contact-item-title">Email</h4>
                                <p class="contact-item-text">{{ $settings['contact_email'] ?? 'epsk.trunojoyo@gmail.com' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Buttons -->
                    <div>
                        <h4 class="social-media-title">Follow Our Social Media</h4>
                        <div class="social-links">
                            <a href="{{ $settings['social_linkedin'] ?? '#' }}" target="_blank" class="social-btn li">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <a href="{{ $settings['social_instagram'] ?? '#' }}" target="_blank" class="social-btn ig">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <a href="{{ $settings['social_tiktok'] ?? '#' }}" target="_blank" class="social-btn tt">
                                <i class="fa-brands fa-tiktok"></i>
                            </a>
                            <a href="{{ $settings['social_youtube'] ?? '#' }}" target="_blank" class="social-btn yt">
                                <i class="fa-brands fa-youtube"></i>
                            </a>
                        </div>
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
