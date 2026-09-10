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
    .practicum-card-main {
        background: #ffffff;
        border-radius: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        padding: 2.5rem 3.5rem;
    }

    /* =========================================
       2. TOMBOL KEMBALI & POSTER
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
        max-height: 450px;
        object-fit: cover;
        border-radius: 1rem;
        margin-bottom: 2rem;
        border: 1px solid #f1f5f9;
    }

    /* =========================================
       3. TIPOGRAFI (JUDUL & META)
       ========================================= */
    .event-meta-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .event-meta-badge {
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
    }
    .event-meta-badge.location {
        background: #f1f5f9;
        color: #475569;
    }
    .event-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 2.5rem;
        letter-spacing: -0.5px;
    }

    /* =========================================
       4. SEKSI KONTEN & PANEL INFORMASI
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
        white-space: pre-line;
    }

    /* Panel Jadwal (Clean UI) */
    .info-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.75rem;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }
    .info-item .label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 0.3rem;
    }
    .info-item .val {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    /* =========================================
       5. TATA TERTIB (4 BARIS KE BAWAH / 1 KOLOM)
       ========================================= */
    .rules-grid {
        display: grid;
        grid-template-columns: 1fr; /* MEMAKSA SEMUA ITEM KE BAWAH DALAM 1 KOLOM */
        gap: 1rem;
    }
    .rule-item {
        display: flex;
        gap: 1.25rem; /* Jarak antara icon dan teks dibuat lebih lega */
        align-items: flex-start;
        padding: 1.25rem; /* Padding dikembalikan ke ukuran normal */
        border-radius: 0.85rem;
        border: 1px solid #f1f5f9;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .rule-item.warning {
        background: #fff0f2;
        border-color: #ffe4e6;
    }
    .rule-icon {
        width: 42px; 
        height: 42px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .rule-item.warning .rule-icon {
        background: #e11d48;
        color: #ffffff;
    }
    .rule-title {
        font-size: 0.95rem; /* Ukuran font dikembalikan normal */
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
    .rule-item.warning .rule-title {
        color: #e11d48;
    }
    .rule-desc {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }

    /* =========================================
       6. TOMBOL UNDUH
       ========================================= */
    .btn-gdrive-action {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background: #881337;
        color: #ffffff !important;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 0.85rem 1.75rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(136, 19, 55, 0.25);
    }
    .btn-gdrive-action:hover {
        background: #6b1426;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(136, 19, 55, 0.35);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .practicum-card-main { padding: 1.5rem; }
        .event-title { font-size: 1.8rem; }
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Detail Tugas Praktikum</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Detail Container -->
    <div class="detail-container">

        <div class="practicum-card-main">

            <!-- Tombol Kembali -->
            <a href="{{ route('activities.practicum') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tugas
            </a>

            <!-- Judul & Meta Data -->
            <div class="event-meta-wrapper">
                <span class="event-meta-badge">
                    <i class="fa-regular fa-clock"></i> Diupload: {{ \Carbon\Carbon::parse($task->uploaded_at)->translatedFormat('d M Y') }}
                </span>
                <span class="event-meta-badge location">
                    <i class="fa-solid fa-location-dot"></i> {{ $task->collection_place }}
                </span>
            </div>

            <h1 class="event-title">{{ $task->title }}</h1>

            <!-- 1. Deskripsi / Instruksi Tugas -->
            <div class="content-section">
                <h3 class="section-heading"><i class="fa-solid fa-bullhorn text-maroon"></i> Instruksi Tugas</h3>
                <p class="section-text">{{ $task->description }}</p>
            </div>

            <!-- 2. Jadwal & Tempat Pengumpulan -->
            <div class="content-section">
                <h3 class="section-heading"><i class="fa-solid fa-calendar-check text-maroon"></i> Jadwal & Tempat Pengumpulan</h3>
                <div class="info-panel">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="label">Hari / Tanggal</div>
                            <div class="val">{{ $task->collection_date }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Batas Waktu (Deadline)</div>
                            <div class="val">{{ $task->collection_time }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Lokasi Pengumpulan</div>
                            <div class="val">{{ $task->collection_place }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Tautan Berkas Soal -->
            <div class="content-section">
                <h3 class="section-heading"><i class="fa-solid fa-cloud-arrow-down text-maroon"></i> Berkas Soal & Template</h3>
                <p class="text-slate-500 mb-3" style="font-size: 0.95rem;">Unduh template dan lembar soal tugas melalui direktori Google Drive resmi berikut:</p>

                @if($task->gdrive_link)
                    <a href="{{ $task->gdrive_link }}" target="_blank" rel="noopener noreferrer" class="btn-gdrive-action">
                        <i class="fa-brands fa-google-drive"></i> Unduh Berkas Soal
                    </a>
                @else
                    <div class="alert alert-light border text-slate-500 d-inline-flex align-items-center gap-2" style="font-size: 0.85rem; border-radius: 8px;">
                        <i class="fa-solid fa-link-slash"></i> Tautan belum tersedia untuk tugas ini.
                    </div>
                @endif
            </div>

            <!-- 4. Ketentuan Pengerjaan -->
            <div class="content-section mb-0">
                <h3 class="section-heading"><i class="fa-solid fa-list-check text-maroon"></i> Ketentuan & Format Pengerjaan</h3>

                <div class="rules-grid">
                    <div class="rule-item warning">
                        <div class="rule-icon"><i class="fa-solid fa-ban"></i></div>
                        <div>
                            <div class="rule-title">Bebas Plagiarisme (Anti-Copas)</div>
                            <p class="rule-desc text-danger">Dilarang keras menyalin tugas milik praktikan lain. Tindakan copy-paste otomatis <strong>Nilai = 0</strong>.</p>
                        </div>
                    </div>

                    <div class="rule-item">
                        <div class="rule-icon"><i class="fa-solid fa-pen-nib"></i></div>
                        <div>
                            <div class="rule-title">Tulis Tangan & Tinta</div>
                            <p class="rule-desc">Wajib tulis tangan secara rapi menggunakan <strong>pulpen biru</strong>.</p>
                        </div>
                    </div>

                    <div class="rule-item">
                        <div class="rule-icon"><i class="fa-solid fa-file-lines"></i></div>
                        <div>
                            <div class="rule-title">Kertas & Margin</div>
                            <p class="rule-desc">Kertas <strong>A4 berkop resmi EPSK</strong> dengan format margin <strong>4-3-3-3</strong>.</p>
                        </div>
                    </div>

                    <div class="rule-item">
                        <div class="rule-icon"><i class="fa-solid fa-eraser"></i></div>
                        <div>
                            <div class="rule-title">Standar Kerapian</div>
                            <p class="rule-desc">Penggunaan correction pen (tipe-x) <strong>maksimal 3 kali</strong> di seluruh lembar pengerjaan.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
