@extends('layouts.app')

@section('title', 'Visi & Misi Laboratorium - Lab EPSK UTM')
@section('meta_description', 'Landasan utama, arah strategis, visi, dan misi Laboratorium Ergonomi dan Perancangan Sistem Kerja (EPSK) Universitas Trunojoyo Madura.')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HEADER BERSAMA */
    /* ========================================================================= */
    body, .overview-page-wrapper {
        background-color: #f8fafc;
        min-height: 100vh;
    }
    .overview-hero-header {
        background-size: cover;
        background-position: center;
        text-align: center;
    }
    .overview-main-title {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    /* ========================================================================= */
    /* CONTAINER & KARTU UTAMA (PERSIS SEPERTI STRUKTUR ACHIEVEMENT) */
    /* ========================================================================= */
    .achievement-container {
        max-width: 1200px;
        margin: -2.5rem auto 4rem auto;
        padding: 0 1.25rem;
        position: relative;
        z-index: 10;
    }
    .achievement-card-main {
        background: #ffffff;
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
        padding: 2.5rem;
    }

    /* ========================================================================= */
    /* HEADER KONTEN (IKON KOTAK TUMPUL) */
    /* ========================================================================= */
    .section-header-box {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .section-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        background: #fff1f2;
        color: #9f1239;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .section-title {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.5px;
    }
    .section-subtitle {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
        font-weight: 400;
    }

    /* ========================================================================= */
    /* KOTAK KONTEN VISI & MISI (GRID) */
    /* ========================================================================= */
    .card-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media(min-width: 768px) {
        .card-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .content-box {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease;
    }
    .content-box:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .box-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 1rem 0;
        line-height: 1.4;
    }
    .box-desc {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.7;
        margin: 0;
    }
    .box-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .box-list li {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
    }
    .box-list li i {
        color: #9f1239;
        margin-top: 0.3rem;
        font-size: 0.75rem;
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 9rem 0;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Overview</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Container menggunakan struktur achievement-container presisi -->
    <div class="achievement-container">
        <div class="achievement-card-main">

            <!-- Header Visi & Misi -->
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <h2 class="section-title">Visi & Misi Laboratorium</h2>
                    <p class="section-subtitle">Landasan utama dan arah strategis Laboratorium Ergonomi dan Perancangan Sistem Kerja dalam mewujudkan keunggulan akademik dan riset.</p>
                </div>
            </div>

            <!-- Isi Visi & Misi -->
            <div class="card-grid">
                <!-- KARTU 1: VISI -->
                <div class="content-box">
                    <h3 class="box-title">Visi Utama Laboratorium</h3>
                    <p class="box-desc">
                        Menjadi laboratorium yang unggul dalam pendidikan, penelitian, dan penerapan ergonomi serta perancangan sistem kerja yang berbasis potensi lokal Madura, guna mendukung terciptanya lulusan yang cerdas, kompetitif, dan berakhlakul karimah pada tahun 2030.
                    </p>
                </div>

                <!-- KARTU 2: MISI -->
                <div class="content-box">
                    <h3 class="box-title">Misi Pelaksanaan</h3>
                    <ul class="box-list">
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span>Menyelenggarakan pendidikan dan praktikum integratif di bidang ergonomi untuk kompetensi mahasiswa.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span>Melaksanakan penelitian dan pengabdian berkelanjutan berbasis potensi lokal Madura.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span>Menjalin kolaborasi dengan industri, pemerintah, dan institusi pendidikan.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span>Membangun sistem pengelolaan laboratorium yang efisien, aman, terbuka, dan profesional.</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
