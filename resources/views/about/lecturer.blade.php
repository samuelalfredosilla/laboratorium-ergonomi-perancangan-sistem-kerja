@extends('layouts.app')

@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Profile Lecturers</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Section Intro -->
        <div class="section-intro text-center">
            <h2 class="section-heading">Dosen Pengampu & Peneliti Laboratorium</h2>
            <p class="section-subtext">
                Tim dosen ahli di bidang Ergonomi dan Perancangan Sistem Kerja yang berfokus pada pengembangan keilmuan melalui pendidikan, penelitian, dan pengabdian kepada masyarakat.
            </p>
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
                        <h3 class="lecturer-name">
                            <a href="{{ route('about.lecturer-detail', $lecturer->id) }}">{{ $lecturer->name }}</a>
                        </h3>
                        <p class="lecturer-nip">NIP. {{ $lecturer->nip ?? '-' }}</p>
                        <div class="lecturer-divider"></div>
                        <p class="lecturer-expertise">
                            <i class="fa-solid fa-microscope"></i> {{ $lecturer->expertise ?? 'Ergonomics & Work System' }}
                        </p>

                        <div class="lecturer-action-btn">
                            <a href="{{ route('about.lecturer-detail', $lecturer->id) }}" class="btn-detail-profile">
                                Lihat Profil Lengkap <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback Tampilan jika Data di Database Masih Kosong -->
                <div class="text-center py-5 w-100">
                    <p class="text-muted">Belum ada data dosen yang ditambahkan.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
