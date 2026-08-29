@extends('layouts.app')

@section('content')
<style>
    .lecturer-detail-wrapper {
        max-width: 1200px;
        margin: 2rem auto 0 auto; 
        padding: 0 1.25rem 2.5rem 1.25rem;
        position: relative;
        z-index: 10;
    }
    .lecturer-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 2rem;
        align-items: start;
    }
    @media (max-width: 992px) {
        .lecturer-grid {
            grid-template-columns: 1fr;
        }
    }
    .profile-card-sticky {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 90px;
        overflow: visible; /* Mencegah foto terpotong */
    }
    .profile-card-top-bg {
        height: 90px;
        background: linear-gradient(135deg, #6b1c1c 0%, #3d0c0c 100%);
        border-top-left-radius: 1.25rem;
        border-top-right-radius: 1.25rem;
        position: relative;
    }

    /* Tombol Kembali Panah */
    .btn-back-circle {
        position: absolute;
        top: 14px;
        left: 14px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        color: #881337;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }
    .btn-back-circle:hover {
        background: #881337;
        color: #ffffff;
        transform: translateX(-3px);
    }

    /* Avatar Box - Disesuaikan agar wajah tampil penuh */
    .profile-avatar-box {
        margin-top: -55px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        position: relative;
        z-index: 5;
    }
    .profile-avatar-box img {
        width: 125px;
        height: 125px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center 15%; /* Fokus ke area wajah */
        border: 4px solid #ffffff;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        background: #ffffff;
        display: block;
    }
    .profile-body {
        padding: 1rem 1.5rem 1.5rem 1.5rem;
        text-align: center;
    }
    .badge-role {
        display: inline-block;
        background: #fee2e2;
        color: #991b1b;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.3rem 0.85rem;
        border-radius: 50px;
        margin-bottom: 0.65rem;
    }
    .profile-name-text {
        font-size: 1.18rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.35;
        margin-bottom: 0.25rem;
    }
    .profile-nip-text {
        font-size: 0.82rem;
        color: #64748b;
        font-family: monospace;
        margin-bottom: 1.15rem;
    }
    .info-list-box {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-bottom: 1.15rem;
    }
    .info-box-item {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 0.75rem;
        padding: 0.75rem 0.9rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    .info-box-item i {
        color: #881337;
        font-size: 0.95rem;
        margin-top: 0.2rem;
        flex-shrink: 0;
    }
    .info-box-label {
        font-size: 0.66rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-box-val {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        margin-top: 0.1rem;
        word-break: break-word;
    }
    .social-btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }
    .social-link-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #cbd5e1;
        color: #334155;
    }
    .social-link-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .social-link-btn.linkedin {
        background: #0077b5;
        color: #ffffff;
        border-color: #0077b5;
    }

    /* Konten Kanan */
    .content-card-box {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.25rem;
    }
    .content-card-box:last-child {
        margin-bottom: 0;
    }
    .content-header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1rem;
    }
    .content-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 0.75rem;
        background: #fee2e2;
        color: #881337;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }
    .content-header-title h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }
    .content-header-title p {
        font-size: 0.78rem;
        color: #64748b;
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
        margin-bottom: 1.25rem;
    }
    .edu-item:last-child {
        margin-bottom: 0;
    }
    .edu-dot {
        position: absolute;
        width: 14px;
        height: 14px;
        background: #ffffff;
        border: 3.5px solid #881337;
        border-radius: 50%;
        left: -32px;
        top: 3px;
    }
    .edu-degree {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .edu-badge-year {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.72rem;
        font-family: monospace;
        padding: 0.15rem 0.55rem;
        border-radius: 50px;
    }
    .edu-institution {
        font-size: 0.84rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    /* List Row */
    .item-card-row {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 0.75rem;
        padding: 0.85rem 1rem;
        display: flex;
        gap: 0.85rem;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    .item-card-row:last-child {
        margin-bottom: 0;
    }
    .item-number {
        width: 26px;
        height: 26px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        color: #881337;
        font-size: 0.72rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .item-title {
        font-size: 0.88rem;
        color: #1e293b;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }
    .item-year-badge {
        display: inline-block;
        background: #e2e8f0;
        color: #334155;
        font-size: 0.7rem;
        font-family: monospace;
        font-weight: 600;
        padding: 0.1rem 0.5rem;
        border-radius: 50px;
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.90) 25%, rgba(26, 26, 26, 0.65)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Detail Profil Lecturer</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="lecturer-detail-wrapper">

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
@endsection
