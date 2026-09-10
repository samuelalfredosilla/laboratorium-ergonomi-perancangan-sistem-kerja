@extends('layouts.app')

@section('content')
<style>
    /* =========================================
       1. CONTAINER & KARTU UTAMA
       ========================================= */
    .detail-container {
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
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        padding: 3rem;
    }

    /* =========================================
       2. TOMBOL KEMBALI & GAMBAR POSTER
       ========================================= */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #475569;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        margin-bottom: 1.5rem;
        padding: 0.65rem 1.25rem;
        border-radius: 50px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        transition: all 0.25s ease;
    }
    .btn-back:hover {
        background: #881337;
        color: #ffffff !important;
        border-color: #881337;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(136, 19, 55, 0.25);
    }
    .detail-poster {
        width: 100%;
        max-height: 550px;
        object-fit: cover;
        border-radius: 1rem;
        margin-bottom: 2.5rem;
        border: 1px solid #f1f5f9;
        background-color: #f8fafc;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    /* =========================================
       3. TIPOGRAFI (JUDUL & META)
       ========================================= */
    .achievement-meta-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .achievement-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #fff0f2;
        color: #9f1239;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid #ffe4e6;
    }
    .achievement-meta-badge.secondary {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
    }
    .achievement-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 2.5rem;
        letter-spacing: -0.5px;
    }

    /* =========================================
       4. PANEL INFORMASI (GRID MODERN)
       ========================================= */
    .info-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.75rem;
        margin-bottom: 2.5rem;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .info-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .info-icon {
        width: 40px;
        height: 40px;
        background: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9f1239;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }
    .info-item .label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 0.2rem;
    }
    .info-item .val {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
    }

    /* =========================================
       5. SEKSI KONTEN & DESKRIPSI
       ========================================= */
    .content-section {
        margin-bottom: 2.75rem;
    }
    .section-heading {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.75rem;
    }
    .section-text {
        color: #475569;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    
    /* Styling tambahan agar output dari Quill Editor terlihat rapi */
    .section-text p { margin-bottom: 1rem; }
    .section-text ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
    .section-text ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
    .section-text a { color: #9f1239; text-decoration: underline; }
    .section-text strong, .section-text b { font-weight: 700; color: #1e293b; }

    /* Responsive */
    @media (max-width: 768px) {
        .achievement-card-main { padding: 1.5rem; }
        .achievement-title { font-size: 1.8rem; }
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 8rem 0;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Detail Pencapaian Prestasi</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Detail Container -->
    <div class="detail-container">

        <div class="achievement-card-main">

            <!-- Tombol Kembali -->
            <a href="{{ route('achievement.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Prestasi
            </a>

            <!-- Judul & Meta Data -->
            <div class="achievement-meta-wrapper">
                <span class="achievement-meta-badge">
                    <i class="fa-solid fa-medal"></i> Tingkat {{ $achievement->level }}
                </span>
                <span class="achievement-meta-badge secondary">
                    <i class="fa-regular fa-calendar-check"></i> {{ \Carbon\Carbon::parse($achievement->date_achieved)->translatedFormat('d F Y') }}
                </span>
            </div>

            <h1 class="achievement-title">{{ $achievement->title }}</h1>

            <!-- Foto Prestasi -->
            @if($achievement->photo)
                <img src="{{ asset('storage/' . $achievement->photo) }}" alt="{{ $achievement->title }}" class="detail-poster">
            @endif

            <!-- Panel Informasi Peraih Prestasi -->
            <div class="info-panel">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-user-graduate"></i></div>
                        <div>
                            <div class="label">Peraih Prestasi</div>
                            <div class="val">{{ $achievement->achiever_name }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-trophy"></i></div>
                        <div>
                            <div class="label">Kategori / Tingkat</div>
                            <div class="val">{{ $achievement->level }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fa-solid fa-calendar-day"></i></div>
                        <div>
                            <div class="label">Tanggal Pencapaian</div>
                            <div class="val">{{ \Carbon\Carbon::parse($achievement->date_achieved)->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Lengkap -->
            <div class="content-section mb-0">
                <h3 class="section-heading"><i class="fa-solid fa-align-left text-maroon-600"></i> Deskripsi Pencapaian</h3>
                <div class="section-text">
                    {!! $achievement->description ?: '<p class="text-slate-400 italic">Tidak ada deskripsi spesifik yang dilampirkan untuk pencapaian ini.</p>' !!}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection