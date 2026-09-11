@extends('layouts.app')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HEADER */
    /* ========================================================================= */
    body, .overview-page-wrapper {
        background-color: #f8fafc;
        min-height: 100vh;
    }
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
    /* DAFTAR PROSEDUR (LIST CARD) */
    /* ========================================================================= */
    .procedure-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .procedure-item {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }
    .procedure-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px -5px rgba(107, 28, 28, 0.08);
        border-color: #cbd5e1;
    }

    .procedure-header-row {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }

    .procedure-number {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #fff0f2;
        color: #9f1239;
        border: 1px solid #ffe4e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .procedure-body {
        flex-grow: 1;
    }

    .procedure-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.4;
        margin: 0 0 0.5rem 0;
    }

    .procedure-desc {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 1.25rem 0;
    }

    .btn-download-doc {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff0f2;
        color: #9f1239 !important;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.55rem 1.25rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #ffe4e6;
        width: fit-content;
    }
    .btn-download-doc:hover {
        background: #9f1239;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(159, 18, 57, 0.2);
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 9rem 0; background-size: cover; background-position: center; text-align: center;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3" style="font-size: 2.5rem; font-weight: 800;">Lab Procedures</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="achievement-container">
        <div class="achievement-card-main">
            
            <!-- Section Header Box -->
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div>
                    <h2 class="section-title">Prosedur & Ketentuan Layanan Laboratorium</h2>
                    <p class="section-subtitle">Panduan resmi alur peminjaman serta pengembalian fasilitas Laboratorium Ergonomi dan Perancangan Sistem Kerja (EPSK).</p>
                </div>
            </div>

            <!-- Daftar Prosedur (Dinamis dari Database) -->
            <div class="procedure-list">
                @forelse($procedures as $index => $item)
                    <div class="procedure-item">
                        <div class="procedure-header-row">
                            <div class="procedure-number">{{ $item->sort_order }}</div>
                            <div class="procedure-body">
                                <h3 class="procedure-title">{{ $item->title }}</h3>
                                
                                @if($item->description)
                                    <p class="procedure-desc">{{ $item->description }}</p>
                                @endif
                                
                                @if($item->file_url)
                                    <a href="{{ $item->file_url }}" target="_blank" rel="noopener noreferrer" class="btn-download-doc">
                                        <i class="fa-solid fa-eye"></i> Lihat Dokumen Prosedur
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic"><i class="fa-solid fa-link-slash mr-1"></i> Dokumen belum tersedia</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Tampilan jika admin belum memasukkan data sama sekali -->
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                            <i class="fa-solid fa-file-lines fa-2x"></i>
                        </div>
                        <h4 class="text-slate-700 fw-bold" style="font-size: 1.1rem; margin-bottom: 0.5rem;">Prosedur Belum Tersedia</h4>
                        <p class="text-slate-500" style="font-size: 0.9rem;">Daftar layanan dan prosedur laboratorium belum ditambahkan oleh admin.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection