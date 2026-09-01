@extends('layouts.app')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HEADER */
    /* ========================================================================= */
    .practicum-container {
        max-width: 1200px;
        margin: -2.5rem auto 4rem auto; /* Margin negatif ditarik ke atas menimpa banner */
        padding: 0 1.25rem;
        position: relative;
        z-index: 10;
    }
    .practicum-card-main {
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
    /* KARTU TUGAS PRAKTIKUM (GRID) */
    /* ========================================================================= */
    .task-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }
    .task-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        height: 100%;
    }
    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px -5px rgba(107, 28, 28, 0.1);
        border-color: #cbd5e1;
        background: #ffffff;
    }
    .task-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.6rem;
        line-height: 1.4;
        letter-spacing: -0.2px;
    }
    .task-desc {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex-grow: 1; /* Mendorong tombol ke paling bawah */
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .task-meta-date {
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

    <!-- Header Hero Banner (Tinggi disamakan dengan halaman event) -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 9rem 0;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Practicum Activities</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="practicum-container">

        <!-- DAFTAR TUGAS PRAKTIKUM (MENGGANTIKAN POSISI DESKRIPSI) -->
        <div class="practicum-card-main">
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h2 class="section-title">Daftar Tugas Praktikum</h2>
                    <p class="section-subtitle">Pilih tugas untuk melihat instruksi, berkas soal, dan ketentuan pengumpulan</p>
                </div>
            </div>

            <div class="task-grid">
                @forelse($tasks as $task)
                    <div class="task-card">
                        
                        <h3 class="task-title">{{ $task->title }}</h3>

                        <!-- Ringkasan Deskripsi Singkat -->
                        <p class="task-desc">
                            {{ \Illuminate\Support\Str::limit($task->description, 130, '...') }}
                        </p>

                        <div class="task-meta-date">
                            <i class="fa-regular fa-clock text-maroon"></i> Diupload: {{ \Carbon\Carbon::parse($task->uploaded_at)->translatedFormat('d F Y, H:i') }} WIB
                        </div>

                        <!-- Link ke Halaman Detail Baru -->
                        <a href="{{ route('activities.practicum.detail', $task->id) }}" class="btn-read-more">
                            Lihat Detail Tugas <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                        <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                            <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                        </div>
                        <h4 class="text-slate-700 fw-bold">Belum ada tugas praktikum</h4>
                        <p class="text-slate-500">Tugas praktikum yang diterbitkan oleh asisten akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection