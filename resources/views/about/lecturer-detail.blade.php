@extends('layouts.app')

@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Detail Profil Lecturer</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Tombol Kembali -->
        <div class="back-navigation">
            <a href="{{ route('about.lecturer') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Dosen
            </a>
        </div>

        <!-- Grid Profil 2 Kolom -->
        <div class="profile-detail-grid">

            <!-- Sidebar Informasi Dosen (Kiri) -->
            <div class="profile-sidebar-card">
                <div class="profile-avatar-wrapper">
                    <img src="{{ Str::startsWith($lecturer->photo, 'http') ? $lecturer->photo : asset('storage/' . $lecturer->photo) }}"
                         alt="{{ $lecturer->name }}"
                         class="profile-avatar-img">
                </div>

                @if($lecturer->role)
                    <span class="profile-role-badge">{{ $lecturer->role }}</span>
                @endif

                <h2 class="profile-name">{{ $lecturer->name }}</h2>
                <p class="profile-nip">NIP. {{ $lecturer->nip ?? '-' }}</p>

                <div class="profile-contact-list">
                    <div class="contact-item">
                        <i class="fa-solid fa-microscope icon-maroon"></i>
                        <span><strong>Keahlian Utama:</strong><br>{{ $lecturer->expertise ?? 'Ergonomics & Work System' }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fa-solid fa-envelope icon-maroon"></i>
                        <span><strong>Email Resmi:</strong><br>{{ $lecturer->email ?? '-' }}</span>
                    </div>
                </div>

                <div class="profile-social-buttons">
                    @if($lecturer->scholar_link)
                        <a href="{{ $lecturer->scholar_link }}" target="_blank" class="social-btn">
                            <i class="fa-solid fa-graduation-cap"></i> Google Scholar
                        </a>
                    @endif

                    @if($lecturer->linkedin_link)
                        <a href="{{ $lecturer->linkedin_link }}" target="_blank" class="social-btn">
                            <i class="fa-brands fa-linkedin"></i> LinkedIn
                        </a>
                    @endif
                </div>
            </div>

            <!-- Konten Utama Dosen (Kanan) -->
            <div class="profile-main-content">

                <!-- 1. RIWAYAT PENDIDIKAN -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="fa-solid fa-user-graduate card-icon"></i>
                        <h3>Riwayat Pendidikan</h3>
                    </div>
                    <div class="timeline-list">
                        @forelse($educations as $edu)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <h4>{{ $edu->degree }}</h4>
                                    <p class="institution">{{ $edu->institution }}</p>
                                    <span class="year-badge">{{ $edu->year_range }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted fs-6">Belum ada data riwayat pendidikan.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 2. JUDUL PENELITIAN -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="fa-solid fa-flask card-icon"></i>
                        <h3>Judul Penelitian</h3>
                    </div>
                    <ul class="data-bullet-list">
                        @forelse($researches as $research)
                            <li>
                                <i class="fa-solid fa-circle-check bullet-icon"></i>
                                <span>
                                    {{ $research->title }}
                                    @if($research->year)
                                        <strong>({{ $research->year }})</strong>
                                    @endif
                                </span>
                            </li>
                        @empty
                            <li class="text-muted">Belum ada data penelitian.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- 3. PENGABDIAN KEPADA MASYARAKAT -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="fa-solid fa-hand-holding-heart card-icon"></i>
                        <h3>Pengabdian Kepada Masyarakat</h3>
                    </div>
                    <ul class="data-bullet-list">
                        @forelse($communityServices as $service)
                            <li>
                                <i class="fa-solid fa-circle-check bullet-icon"></i>
                                <span>
                                    {{ $service->title }}
                                    @if($service->year)
                                        <strong>({{ $service->year }})</strong>
                                    @endif
                                </span>
                            </li>
                        @empty
                            <li class="text-muted">Belum ada data pengabdian masyarakat.</li>
                        @endforelse
                    </ul>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
