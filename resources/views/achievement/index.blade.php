@extends('layouts.app')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HEADER */
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
    .section-header-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #fff0f2;
        color: #9f1239;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .section-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.2rem 0;
        letter-spacing: -0.3px;
    }
    .section-subtitle {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
    }

    /* ========================================================================= */
    /* DAFTAR PRESTASI (HORIZONTAL CARDS) */
    /* ========================================================================= */
    .achievement-list {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .achievement-item {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .achievement-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(107, 28, 28, 0.1);
        border-color: #cbd5e1;
    }

    /* Foto di Kiri */
    .achievement-image-box {
        width: 100%;
        height: 250px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        position: relative;
    }
    .achievement-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .achievement-item:hover .achievement-image-box img {
        transform: scale(1.05);
    }
    .achievement-placeholder {
        font-size: 4rem;
        color: #cbd5e1;
    }

    /* Konten Penjelasan di Kanan */
    .achievement-content {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .achievement-badge {
        align-self: flex-start;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        margin-bottom: 1rem;
        background: #fff0f2;
        color: #9f1239;
        border: 1px solid #ffe4e6;
    }

    .achievement-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.3;
        margin: 0 0 1rem 0;
    }

    .achievement-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px dashed #e2e8f0;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
    }
    .meta-icon {
        color: #9f1239;
    }

    .achievement-desc {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.7;
        margin: 0;
    }

    /* ========================================================================= */
    /* TOMBOL BACA SELENGKAPNYA */
    /* ========================================================================= */
    .btn-read-more {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #9f1239;
        color: #ffffff !important;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 0.65rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(159, 18, 57, 0.2);
        width: fit-content;
    }
    .btn-read-more:hover {
        background: #7e0e2d; /* Warna maroon lebih gelap saat dihover */
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(159, 18, 57, 0.3);
    }

    /* Responsif untuk Layar Besar (Desktop) -> Foto kiri, teks kanan */
    @media (min-width: 768px) {
        .achievement-item {
            flex-direction: row;
            height: auto;
        }
        .achievement-image-box {
            width: 35%; /* Lebar gambar 35% dari card */
            height: auto;
            min-height: 250px;
        }
        .achievement-content {
            width: 65%; /* Lebar teks 65% */
        }
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 9rem 0;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Prestasi & Penghargaan</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="achievement-container">
        <div class="achievement-card-main">
            
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <h2 class="section-title">Rekam Jejak Prestasi EPSIKERS</h2>
                    <p class="section-subtitle">Daftar pencapaian, karya, dan penghargaan yang diraih oleh asisten laboratorium EPSK.</p>
                </div>
            </div>

            <div class="achievement-list">
                @forelse($achievements as $item)
                    <div class="achievement-item">
                        
                        <!-- SEBELAH KIRI: FOTO -->
                        <div class="achievement-image-box">
                            @if($item->photo)
                                <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->title }}">
                            @else
                                <i class="fa-solid fa-award achievement-placeholder"></i>
                            @endif
                        </div>

                        <!-- SEBELAH KANAN: PENJELASAN HEADER & DESKRIPSI -->
                        <div class="achievement-content">
                            <!-- Badge Tingkat Prestasi -->
                            <div class="achievement-badge">
                                <i class="fa-solid fa-medal mr-1"></i> Tingkat {{ $item->level }}
                            </div>

                            <!-- Header / Judul Prestasi -->
                            <h3 class="achievement-title">{{ $item->title }}</h3>

                            <!-- Meta Data (Nama Peraih & Tanggal) -->
                            <div class="achievement-meta">
                                <div class="meta-item">
                                    <i class="fa-solid fa-user-graduate meta-icon"></i>
                                    <span>{{ $item->achiever_name }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fa-solid fa-calendar-check meta-icon"></i>
                                    <span>{{ \Carbon\Carbon::parse($item->date_achieved)->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>

                            <!-- Penjelasan Singkat (Deskripsi dibatasi 120 karakter) -->
                            <p class="achievement-desc">
                                {{ Str::limit(strip_tags($item->description ?: 'Tidak ada deskripsi spesifik untuk pencapaian ini.'), 120) }}
                            </p>

                            <!-- Tombol Baca Selengkapnya -->
                            <div style="margin-top: 1.5rem;">
                                <a href="{{ route('achievement.show', $item->id) }}" class="btn-read-more">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- TAMPILAN KOSONG -->
                    <div class="text-center py-5 w-100">
                        <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                            <i class="fa-solid fa-medal fa-2x"></i>
                        </div>
                        <h4 class="text-slate-700 fw-bold">Belum Ada Catatan Prestasi</h4>
                        <p class="text-slate-500">Daftar penghargaan asisten laboratorium akan segera diperbarui di sini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection