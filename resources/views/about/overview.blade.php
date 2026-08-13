@extends('layouts.app')

@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner dengan Gambar 2.jpeg -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Overview</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Section Header Intro (Sesuai Format Gambar Referensi) -->
        <div class="section-intro text-center">
            <h2 class="section-heading">Visi & Misi Laboratorium</h2>
            <p class="section-subtext">
                Landasan utama dan arah strategis Laboratorium Ergonomi dan Perancangan Sistem Kerja dalam mewujudkan keunggulan akademik, riset, serta pengabdian berbasis potensi lokal.
            </p>
        </div>

        <!-- 1. VISION & MISSION SECTION -->
        <div class="vm-grid">
            <!-- Vision -->
            <div class="overview-card vision-card">
                <div class="card-tag tag-vision">
                    <i class="fa-solid fa-eye"></i> VISION
                </div>
                <h3 class="overview-card-title">Visi Kami</h3>
                <p class="overview-text highlight-quote">
                    "Menjadi laboratorium yang unggul dalam pendidikan, penelitian, dan penerapan ergonomi serta perancangan sistem kerja yang berbasis potensi lokal Madura, guna mendukung terciptanya lulusan yang cerdas, kompetitif, dan berakhlakul karimah pada tahun 2030."
                </p>
            </div>

            <!-- Mission -->
            <div class="overview-card mission-card">
                <div class="card-tag tag-mission">
                    <i class="fa-solid fa-bullseye"></i> MISSION
                </div>
                <h3 class="overview-card-title">Misi Kami</h3>
                <ul class="mission-list">
                    <li>
                        <i class="fa-solid fa-circle-check mission-icon"></i>
                        <span>Menyelenggarakan kegiatan pendidikan dan praktikum yang integratif dalam bidang ergonomi dan perancangan sistem kerja untuk meningkatkan kompetensi analitis dan aplikatif mahasiswa.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check mission-icon"></i>
                        <span>Melaksanakan penelitian dan pengabdian kepada masyarakat di bidang ergonomi dan sistem kerja yang berkelanjutan dan berbasis potensi lokal Madura, untuk mendukung pembelajaran dan publikasi ilmiah.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check mission-icon"></i>
                        <span>Menjalin kolaborasi fungsional dengan mitra industri, pemerintah daerah, komunitas lokal, serta institusi pendidikan lainnya dalam pengembangan keilmuan dan teknologi tepat guna di bidang ergonomi.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check mission-icon"></i>
                        <span>Membangun sistem pengelolaan laboratorium yang efisien, aman, dan terbuka, guna menciptakan lingkungan kerja yang profesional dan mendukung kegiatan akademik maupun penelitian mahasiswa dan dosen.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
