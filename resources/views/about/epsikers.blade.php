@extends('layouts.app')

@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">EPSIKERS</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Section Intro Terpadu dengan Periode Dinamis -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-maroon-800 sm:text-3xl">
                Asisten Laboratorium EPSK (EPSIKERS)
            </h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Generasi asisten laboratorium yang berdedikasi dalam mendukung kegiatan praktikum, riset ergonomi, dan operasional laboratorium periode <span class="font-bold text-slate-800">{{ $selectedPeriod }}</span>.
            </p>
        </div>

        <!-- Grid Asisten EPSIKERS (Layout 4 Kolom x 4 Baris) -->
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
                <div class="text-center py-5 w-100">
                    <p class="text-muted">Belum ada data asisten untuk Periode {{ $selectedPeriod }}.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
