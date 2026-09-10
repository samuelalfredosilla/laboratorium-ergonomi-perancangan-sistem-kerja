@extends('layouts.app')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HERO BANNER (DARI EPSIKERS) */
    /* ========================================================================= */
    body, .overview-page-wrapper {
        background-color: #f8fafc;
        min-height: 100vh;
    }
    .overview-hero-header {
        background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}');
        background-size: cover;
        background-position: center;
        padding: 5rem 0 9rem 0;
        text-align: center;
    }
    .overview-main-title {
        color: #ffffff;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }
    .overview-title-line {
        width: 60px;
        height: 4px;
        background-color: #ffffff;
        border-radius: 50px;
        margin: 0 auto;
    }

    /* ========================================================================= */
    /* CONTAINER & KARTU UTAMA (STRUKTUR ACHIEVEMENT) */
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
    /* GRID KARTU DOSEN SPESIFIK */
    /* ========================================================================= */
    .lecturer-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.75rem;
    }
    @media (min-width: 768px) {
        .lecturer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1024px) {
        .lecturer-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .lecturer-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .lecturer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(136, 19, 55, 0.1);
        border-color: #cbd5e1;
    }

    .lecturer-img-wrapper {
        width: 100%;
        height: 280px;
        position: relative;
        background: #f1f5f9;
        overflow: hidden;
    }
    .lecturer-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        transition: transform 0.5s ease;
    }
    .lecturer-card:hover .lecturer-img {
        transform: scale(1.05);
    }

    .lecturer-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #9f1239;
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(159, 18, 57, 0.3);
    }

    .lecturer-info {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex-grow: 1;
        justify-content: space-between;
    }

    .lecturer-info-top {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .lecturer-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 0.25rem 0;
        line-height: 1.4;
    }
    .lecturer-name a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }
    .lecturer-name a:hover {
        color: #9f1239;
    }

    .lecturer-nip {
        font-size: 0.8rem;
        color: #64748b;
        margin: 0 0 1rem 0;
        font-weight: 500;
    }

    .lecturer-divider {
        height: 1px;
        background: #f1f5f9;
        margin-bottom: 1rem;
        width: 100%;
    }

    .lecturer-expertise {
        font-size: 0.85rem;
        color: #475569;
        line-height: 1.5;
        margin-bottom: 0;
        display: inline-flex;
        align-items: flex-start;
        text-align: left;
        gap: 0.5rem;
        max-width: 100%;
    }
    .lecturer-expertise i {
        color: #9f1239;
        flex-shrink: 0;
        margin-top: 0.2rem;
    }

    .lecturer-action-btn {
        width: 100%;
        margin-top: 1.5rem;
    }

    .btn-detail-profile {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.65rem 1rem;
        border: 1px solid #9f1239;
        color: #9f1239;
        background: transparent;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-detail-profile:hover {
        background: #9f1239;
        color: #ffffff;
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Profile Lecturers</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container dengan Struktur Achievement -->
    <div class="achievement-container">
        <div class="achievement-card-main">

            <!-- Header Section -->
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h2 class="section-title">Dosen Pengampu & Peneliti Laboratorium</h2>
                    <p class="section-subtitle">
                        Tim dosen ahli di bidang Ergonomi dan Perancangan Sistem Kerja yang berfokus pada pengembangan keilmuan melalui pendidikan, penelitian, dan pengabdian.
                    </p>
                </div>
            </div>

            <!-- Grid Kartu Dosen Dinamis -->
            <div class="lecturer-grid">
                @forelse($lecturers as $lecturer)
                    <div class="lecturer-card">
                        <a href="{{ route('about.lecturer-detail', $lecturer->id) }}" class="lecturer-card-link">
                            <div class="lecturer-img-wrapper">
                                <img src="{{ Str::startsWith($lecturer->photo, 'http') ? $lecturer->photo : asset('storage/' . $lecturer->photo) }}"
                                     alt="{{ $lecturer->name }}"
                                     class="lecturer-img">

                                @if($lecturer->role && Str::contains(Str::lower($lecturer->role), 'head'))
                                    <div class="lecturer-badge">{{ strtoupper($lecturer->role) }}</div>
                                @endif
                            </div>
                        </a>

                        <div class="lecturer-info">
                            <div class="lecturer-info-top">
                                <h3 class="lecturer-name">
                                    <a href="{{ route('about.lecturer-detail', $lecturer->id) }}">{{ $lecturer->name }}</a>
                                </h3>
                                <p class="lecturer-nip">NIP. {{ $lecturer->nip ?? '-' }}</p>
                                <div class="lecturer-divider"></div>
                                <p class="lecturer-expertise">
                                    <i class="fa-solid fa-microscope"></i>
                                    <span>{{ $lecturer->expertise ?? 'Ergonomics & Work System' }}</span>
                                </p>
                            </div>

                            <div class="lecturer-action-btn">
                                <a href="{{ route('about.lecturer-detail', $lecturer->id) }}" class="btn-detail-profile">
                                    Lihat Profil Lengkap <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback Tampilan jika Data Kosong -->
                    <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                        <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                            <i class="fa-solid fa-user-graduate fa-2x"></i>
                        </div>
                        <h4 class="text-slate-700 fw-bold">Belum Ada Data Dosen</h4>
                        <p class="text-slate-500">Data dosen pengampu dan staf laboratorium akan segera diperbarui di sini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
