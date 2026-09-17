@extends('layouts.app')

@section('title', 'Asisten Laboratorium (EPSIKERS) - Lab EPSK UTM')
@section('meta_description', 'Profil dan daftar generasi asisten Laboratorium Ergonomi dan Perancangan Sistem Kerja (EPSIKERS) Universitas Trunojoyo Madura.')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HERO BANNER */
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
</style>

<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">EPSIKERS</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container dengan Struktur Achievement -->
    <div class="achievement-container">
        <div class="achievement-card-main">

            <!-- Header Section -->
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <div>
                    <h2 class="section-title">Asisten Laboratorium EPSK (EPSIKERS)</h2>
                    <p class="section-subtitle">
                        Generasi asisten laboratorium yang berdedikasi dalam mendukung kegiatan praktikum, riset ergonomi, dan operasional laboratorium periode <span class="font-bold text-slate-800">{{ $selectedPeriod }}</span>.
                    </p>
                </div>
            </div>

            <!-- Grid Asisten EPSIKERS -->
            <div class="epsikers-grid">
                @forelse($assistants as $assistant)
                    <div class="epsiker-card">
                        <!-- Frame Foto Asisten -->
                        <div class="epsiker-img-wrapper">
                            <img src="{{ Str::startsWith($assistant->photo ?? '', 'http') ? $assistant->photo : ($assistant->photo ? asset('storage/' . $assistant->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($assistant->name) . '&background=6B1C1C&color=fff&size=500') }}"
                                 alt="{{ $assistant->name }}"
                                 class="epsiker-img">

                            @if(!empty($assistant->division))
                                <span class="epsiker-badge">{{ $assistant->division }}</span>
                            @endif
                        </div>

                        <!-- Informasi Data Asisten -->
                        <div class="epsiker-info">
                            <h3 class="epsiker-name" title="{{ $assistant->name }}">
                                {{ $assistant->name }}
                            </h3>
                            <p class="epsiker-nim">NIM. {{ $assistant->nim ?? '-' }}</p>

                            <div class="epsiker-divider"></div>

                            <div class="epsiker-contact-row">
                                <i class="fa-solid fa-envelope"></i>
                                <span title="{{ $assistant->email ?? '-' }}">{{ $assistant->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                        <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                            <i class="fa-solid fa-users fa-2x"></i>
                        </div>
                        <h4 class="text-slate-700 fw-bold">Belum Ada Data Asisten</h4>
                        <p class="text-slate-500">Belum ada data asisten untuk Periode {{ $selectedPeriod }}.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
