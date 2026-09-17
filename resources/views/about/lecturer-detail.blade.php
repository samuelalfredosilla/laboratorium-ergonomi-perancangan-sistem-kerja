@extends('layouts.app')

@section('title', $lecturer->name . ' - Profil Dosen Lab EPSK UTM')
@section('meta_description', 'Profil lengkap, bidang keahlian, riwayat pendidikan, dan rekam jejak riset ' . $lecturer->name . ' di Laboratorium EPSK Universitas Trunojoyo Madura.')

@section('content')
<style>
    /* ========================================================================= */
    /* MODERN DESIGN SYSTEM & VARIABLES (RINGAN) */
    /* ========================================================================= */
    :root {
        --primary-maroon: #881337;
        --primary-maroon-dark: #6b1c1c;
        --primary-maroon-light: #fff1f2;
        --accent-rose: #ffe4e6;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --bg-body: #f8fafc;
        --border-color: #e2e8f0;
        --card-radius: 1.5rem;
        --transition-smooth: all 0.3s ease;
    }

    body, .overview-page-wrapper {
        background-color: var(--bg-body);
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* ========================================================================= */
    /* HERO BANNER (DIKEMBALIKAN KE WARNA ASLI) */
    /* ========================================================================= */
    .overview-hero-header {
        background-image: linear-gradient(to right, rgba(107, 28, 28, 0.90) 25%, rgba(26, 26, 26, 0.65)), url('{{ asset('images/2.jpeg') }}');
        background-size: cover;
        background-position: center;
        padding: 5rem 0 9rem 0;
        text-align: center;
    }
    .overview-main-title {
        color: #ffffff;
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -1px;
    }
    .overview-title-line {
        width: 60px;
        height: 4px;
        background-color: #ffffff;
        border-radius: 50px;
        margin: 0 auto;
    }

    /* ========================================================================= */
    /* CONTAINER UTAMA (SOLID, TANPA BLUR AGAR RINGAN) */
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
        border-radius: var(--card-radius);
        border: 1px solid var(--border-color);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
        padding: 2.5rem;
    }

    /* ========================================================================= */
    /* GRID HALAMAN DETAIL DOSEN */
    /* ========================================================================= */
    .lecturer-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 2.5rem;
        align-items: start;
    }

    /* ========================================================================= */
    /* SIDEBAR KIRI (PROFIL DOSEN) */
    /* ========================================================================= */
    .profile-card-sticky {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
        position: sticky;
        top: 90px;
        overflow: visible;
    }
    .profile-card-top-bg {
        height: 110px;
        background: linear-gradient(135deg, var(--primary-maroon-dark) 0%, #3d0c0c 100%);
        border-top-left-radius: 1.25rem;
        border-top-right-radius: 1.25rem;
        position: relative;
    }

    /* Tombol Kembali Panah (Solid, Tanpa Blur) */
    .btn-back-circle {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        color: var(--primary-maroon);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        text-decoration: none;
        transition: var(--transition-smooth);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }
    .btn-back-circle:hover {
        background: var(--primary-maroon);
        color: #ffffff;
        transform: translateX(-4px);
    }

    /* Avatar Box */
    .profile-avatar-box {
        margin-top: -65px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        position: relative;
        z-index: 5;
    }
    .profile-avatar-box img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center 15%;
        border: 5px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        background: #ffffff;
        display: block;
    }

    .profile-body {
        padding: 1.25rem 1.75rem 1.75rem 1.75rem;
        text-align: center;
    }
    .badge-role {
        display: inline-block;
        background: var(--primary-maroon-light);
        color: var(--primary-maroon);
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        margin-bottom: 0.75rem;
    }
    .profile-name-text {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.35;
        margin-bottom: 0.25rem;
    }
    .profile-nip-text {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-family: monospace;
        margin-bottom: 1.25rem;
    }

    /* Kotak Informasi Mini (Email & Keahlian) */
    .info-list-box {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .info-box-item {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 0.85rem 1rem;
        display: flex;
        gap: 0.85rem;
        align-items: flex-start;
        transition: var(--transition-smooth);
    }
    .info-box-item:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.05);
    }
    .info-box-item i {
        color: var(--primary-maroon);
        font-size: 1rem;
        margin-top: 0.15rem;
        flex-shrink: 0;
    }
    .info-box-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-box-val {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-main);
        margin-top: 0.15rem;
        word-break: break-word;
    }

    /* Tombol Sosial Media */
    .social-btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .social-link-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: var(--transition-smooth);
        border: 1px solid #cbd5e1;
        color: #334155;
        background: #ffffff;
    }
    .social-link-btn:hover {
        background: #f1f5f9;
        color: var(--text-main);
    }
    .social-link-btn.linkedin {
        background: #0077b5;
        color: #ffffff;
        border-color: #0077b5;
    }
    .social-link-btn.linkedin:hover {
        background: #006097;
    }

    /* ========================================================================= */
    /* KONTEN UTAMA DOSEN (KANAN) */
    /* ========================================================================= */
    .content-card-box {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid var(--border-color);
        padding: 2rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
    }
    .content-card-box:last-child {
        margin-bottom: 0;
    }
    .content-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    .content-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 1rem;
        background: var(--primary-maroon-light);
        color: var(--primary-maroon);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .content-header-title h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 0.15rem 0;
    }
    .content-header-title p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Timeline Pendidikan */
    .edu-timeline {
        position: relative;
        padding-left: 1.5rem;
        border-left: 2px solid #e2e8f0;
        margin-left: 0.75rem;
    }
    .edu-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .edu-item:last-child {
        margin-bottom: 0;
    }
    .edu-dot {
        position: absolute;
        width: 16px;
        height: 16px;
        background: #ffffff;
        border: 4px solid var(--primary-maroon);
        border-radius: 50%;
        left: -33px;
        top: 2px;
    }
    .edu-degree {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .edu-badge-year {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid var(--border-color);
        font-size: 0.75rem;
        font-weight: 600;
        font-family: monospace;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
    }
    .edu-institution {
        font-size: 0.88rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    /* List Row (Riset & Pengabdian) */
    .item-card-row {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 0.85rem;
        padding: 1rem 1.25rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 0.85rem;
        transition: var(--transition-smooth);
    }
    .item-card-row:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }
    .item-card-row:last-child {
        margin-bottom: 0;
    }
    .item-number {
        width: 32px;
        height: 32px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 50%;
        color: var(--primary-maroon);
        font-size: 0.85rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .item-title {
        font-size: 0.95rem;
        color: var(--text-main);
        font-weight: 600;
        line-height: 1.5;
        margin-bottom: 0.35rem;
    }
    .item-year-badge {
        display: inline-block;
        background: #e2e8f0;
        color: #334155;
        font-size: 0.75rem;
        font-family: monospace;
        font-weight: 700;
        padding: 0.15rem 0.6rem;
        border-radius: 50px;
    }

    /* ========================================================================= */
    /* RESPONSIVE FIXES UNTUK MOBILE */
    /* ========================================================================= */
    @media (max-width: 992px) {
        .lecturer-grid {
            grid-template-columns: 1fr;
        }
        .profile-card-sticky {
            position: relative; /* Matikan sticky di HP agar tidak tertabrak konten bawah */
            top: 0;
            z-index: 1;
        }
    }

    @media (max-width: 768px) {
        .achievement-card-main {
            padding: 1.25rem; /* Perkecil padding utama agar tidak sempit di HP */
        }
        .content-card-box {
            padding: 1.25rem; /* Perkecil padding card dalam di HP */
        }
        .profile-body {
            padding: 1rem 1.25rem 1.25rem 1.25rem;
        }
        .overview-main-title {
            font-size: 2rem;
        }
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Banner dengan Warna Asli -->
    <div class="overview-hero-header">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Detail Profil Lecturer</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Container (Solid White tanpa Blur) -->
    <div class="achievement-container">
        <div class="achievement-card-main">

            <!-- 2 Kolom Grid Profil -->
            <div class="lecturer-grid">

                <!-- Sidebar Informasi Dosen (Kiri) -->
                <div class="profile-card-sticky">
                    <div class="profile-card-top-bg">
                        <a href="{{ route('about.lecturer') }}" class="btn-back-circle" title="Kembali ke Daftar Dosen">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>

                    <div class="profile-avatar-box">
                        <img src="{{ Str::startsWith($lecturer->photo, 'http') ? $lecturer->photo : ($lecturer->photo ? asset('storage/' . $lecturer->photo) : asset('images/default-avatar.png')) }}"
                             alt="{{ $lecturer->name }}">
                    </div>

                    <div class="profile-body">
                        <span class="badge-role">{{ $lecturer->role ?? 'Lecturer of Interest' }}</span>
                        <h2 class="profile-name-text">{{ $lecturer->name }}</h2>
                        <p class="profile-nip-text">NIP. {{ $lecturer->nip ?? '-' }}</p>

                        <div class="info-list-box">
                            <div class="info-box-item">
                                <i class="fa-solid fa-microscope"></i>
                                <div>
                                    <div class="info-box-label">Fokus Bidang Keahlian</div>
                                    <div class="info-box-val">{{ $lecturer->expertise ?? 'Ergonomi & Perancangan Sistem Kerja' }}</div>
                                </div>
                            </div>

                            <div class="info-box-item">
                                <i class="fa-solid fa-envelope"></i>
                                <div>
                                    <div class="info-box-label">Alamat Email Resmi</div>
                                    <div class="info-box-val">
                                        @if($lecturer->email)
                                            <a href="mailto:{{ $lecturer->email }}" style="color: inherit; text-decoration: none;">
                                                {{ $lecturer->email }}
                                            </a>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($lecturer->scholar_link || $lecturer->linkedin_link)
                            <div class="social-btn-group">
                                @if($lecturer->scholar_link)
                                    <a href="{{ $lecturer->scholar_link }}" target="_blank" rel="noopener noreferrer" class="social-link-btn">
                                        <i class="fa-solid fa-graduation-cap" style="color: #2563eb;"></i> Google Scholar
                                    </a>
                                @endif

                                @if($lecturer->linkedin_link)
                                    <a href="{{ $lecturer->linkedin_link }}" target="_blank" rel="noopener noreferrer" class="social-link-btn linkedin">
                                        <i class="fa-brands fa-linkedin"></i> LinkedIn
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Konten Utama Dosen (Kanan) -->
                <div>

                    <!-- 1. RIWAYAT PENDIDIKAN -->
                    <div class="content-card-box">
                        <div class="content-header">
                            <div class="content-header-icon">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div class="content-header-title">
                                <h3>Riwayat Pendidikan Formal</h3>
                                <p>Jenjang pendidikan tinggi dan institusi kelulusan</p>
                            </div>
                        </div>

                        <div class="edu-timeline">
                            @forelse($educations as $edu)
                                <div class="edu-item">
                                    <span class="edu-dot"></span>
                                    <div class="edu-degree">
                                        <span>{{ $edu->degree }}</span>
                                        @if($edu->year_range)
                                            <span class="edu-badge-year">{{ $edu->year_range }}</span>
                                        @endif
                                    </div>
                                    <div class="edu-institution">{{ $edu->institution }}</div>
                                </div>
                            @empty
                                <p style="color: #94a3b8; font-size: 0.88rem; margin: 0;">Belum ada data riwayat pendidikan.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- 2. JUDUL PENELITIAN -->
                    <div class="content-card-box">
                        <div class="content-header">
                            <div class="content-header-icon">
                                <i class="fa-solid fa-flask"></i>
                            </div>
                            <div class="content-header-title">
                                <h3>Riset & Rekam Jejak Penelitian</h3>
                                <p>Publikasi dan topik penelitian yang dikembangkan</p>
                            </div>
                        </div>

                        <div>
                            @forelse($researches as $index => $research)
                                <div class="item-card-row">
                                    <div class="item-number">{{ $index + 1 }}</div>
                                    <div style="flex: 1;">
                                        <div class="item-title">{{ $research->title }}</div>
                                        @if($research->year)
                                            <span class="item-year-badge">Tahun {{ $research->year }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p style="color: #94a3b8; font-size: 0.88rem; margin: 0;">Belum ada data rekam jejak penelitian.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- 3. PENGABDIAN KEPADA MASYARAKAT -->
                    <div class="content-card-box">
                        <div class="content-header">
                            <div class="content-header-icon">
                                <i class="fa-solid fa-hand-holding-heart"></i>
                            </div>
                            <div class="content-header-title">
                                <h3>Pengabdian Kepada Masyarakat</h3>
                                <p>Implementasi keilmuan ke masyarakat dan sektor industri</p>
                            </div>
                        </div>

                        <div>
                            @forelse($communityServices as $index => $service)
                                <div class="item-card-row">
                                    <div class="item-number">{{ $index + 1 }}</div>
                                    <div style="flex: 1;">
                                        <div class="item-title">{{ $service->title }}</div>
                                        @if($service->year)
                                            <span class="item-year-badge">Tahun {{ $service->year }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p style="color: #94a3b8; font-size: 0.88rem; margin: 0;">Belum ada data pengabdian kepada masyarakat.</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection
