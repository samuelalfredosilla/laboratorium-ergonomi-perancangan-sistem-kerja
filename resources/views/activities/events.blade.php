@extends('layouts.app')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HEADER */
    /* ========================================================================= */
    .event-container {
        max-width: 1200px;
        margin: -2.5rem auto 4rem auto;
        padding: 0 1.25rem;
        position: relative;
        z-index: 10;
    }
    .event-card-main {
        background: #ffffff;
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }
    .section-header-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
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
    /* KARTU KEGIATAN EVENT (GRID) */
    /* ========================================================================= */
    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }
    .event-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        height: 100%;
    }
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px -5px rgba(107, 28, 28, 0.1);
        border-color: #cbd5e1;
        background: #ffffff;
    }

    /* Gambar Poster di dalam Kartu */
    .event-image-wrapper {
        width: 100%;
        height: 190px;
        border-radius: 0.85rem;
        overflow: hidden;
        margin-bottom: 1rem;
        background: #f1f5f9;
        flex-shrink: 0;
    }
    .event-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .event-card:hover .event-image-wrapper img {
        transform: scale(1.05);
    }

    /* Teks dalam Kartu */
    .event-card-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-top: 0;
        margin-bottom: 0.6rem;
        line-height: 1.4;
        letter-spacing: -0.2px;
    }
    .event-desc {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .event-meta-date {
        font-size: 0.8rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1.25rem;
        padding-top: 0.85rem;
        border-top: 1px dashed #e2e8f0;
        font-weight: 600;
    }

    /* Tombol Detail */
    .btn-read-more {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #ffffff;
        border: 1.5px solid #881337;
        color: #881337;
        font-size: 0.88rem;
        font-weight: 700;
        padding: 0.65rem 1rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
        width: 100%;
        margin-top: auto;
    }
    .btn-read-more:hover {
        background: #881337;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(136, 19, 55, 0.25);
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 9rem 0;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Laboratory Events</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="event-container">

        <!-- DAFTAR KEGIATAN LABORATORIUM -->
        <div class="event-card-main">
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <h2 class="section-title">Daftar Kegiatan & Acara</h2>
                    <p class="section-subtitle">Pilih kegiatan untuk melihat informasi lengkap, jadwal pelaksanaan, dan tautan terkait</p>
                </div>
            </div>

            <div class="event-grid">
                @forelse($events as $event)
                    <div class="event-card">

                        <!-- Poster -->
                        @if($event->photo)
                            <div class="event-image-wrapper">
                                <img src="{{ asset('storage/' . $event->photo) }}" alt="Poster {{ $event->title }}">
                            </div>
                        @endif

                        <h3 class="event-card-title">{{ $event->title }}</h3>

                        <!-- Deskripsi Singkat -->
                        <p class="event-desc">
                            {{ \Illuminate\Support\Str::limit($event->description, 130, '...') }}
                        </p>

                        <!-- Tanggal Publikasi -->
                        <div class="event-meta-date">
                            <i class="fa-regular fa-clock text-maroon"></i> Dipublikasi: {{ \Carbon\Carbon::parse($event->uploaded_at)->translatedFormat('d F Y, H:i') }} WIB
                        </div>

                        <!-- Link ke Halaman Detail -->
                        <a href="{{ route('activities.events.detail', $event->id) }}" class="btn-read-more">
                            Lihat Detail Acara <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                        <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                            <i class="fa-solid fa-calendar-xmark fa-2x"></i>
                        </div>
                        <h4 class="text-slate-700 fw-bold">Belum ada agenda tersedia</h4>
                        <p class="text-slate-500">Kegiatan laboratorium yang diterbitkan oleh admin akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
