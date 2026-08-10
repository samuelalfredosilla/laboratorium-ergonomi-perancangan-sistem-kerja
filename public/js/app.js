// public/js/app.js

document.addEventListener('DOMContentLoaded', function () {

    /* ==========================================================================
       1. LOGIKA NAVBAR (SCROLL & MOBILE MENU)
       ========================================================================== */
    const navbar = document.getElementById('navbar');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');

    if (navbar) {
        // Fungsi cek scroll
        const checkScroll = () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };

        // Jalankan saat pertama dimuat & saat di-scroll
        checkScroll();
        window.addEventListener('scroll', checkScroll);
    }

    // Toggle menu mobile
    if (hamburgerBtn && mobileDrawer) {
        hamburgerBtn.addEventListener('click', function () {
            mobileDrawer.classList.toggle('open');
        });
    }


    /* ==========================================================================
       2. LOGIKA IMAGE SLIDER (ABOUT US SECTION)
       ========================================================================== */
    const slider = document.getElementById('imageSlider');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dotsContainer = document.getElementById('sliderDots');

    if (slider && slides.length > 0) {
        let currentIndex = 0;
        const totalSlides = slides.length;

        // Generate titik indikator (dots) otomatis
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            if (dotsContainer) dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.dot');

        const updateSlider = () => {
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        };

        const goToSlide = (index) => {
            currentIndex = index;
            updateSlider();
        };

        const nextSlide = () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider();
        };

        const prevSlide = () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSlider();
        };

        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);

        // Autoplay slide setiap 4 detik
        setInterval(nextSlide, 4000);
    }

});

