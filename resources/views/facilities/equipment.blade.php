@extends('layouts.app')

@section('title', 'Daftar Peralatan & Fasilitas - Lab EPSK UTM')
@section('meta_description', 'Katalog inventaris alat dan fasilitas riset ergonomi resmi yang tersedia di Laboratorium EPSK Universitas Trunojoyo Madura.')

@section('content')
<style>
    /* ========================================================================= */
    /* LAYOUT UTAMA & HEADER */
    /* ========================================================================= */
    .equipment-container {
        max-width: 1200px;
        margin: -2.5rem auto 4rem auto;
        padding: 0 1.25rem;
        position: relative;
        z-index: 10;
    }
    .equipment-card-main {
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
        margin-bottom: 2rem;
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
    /* KATEGORI SECTIONS */
    /* ========================================================================= */
    .category-group {
        margin-bottom: 3rem;
    }
    .category-group:last-child {
        margin-bottom: 0;
    }
    .category-heading-box {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 0.4rem;
    }
    .category-group-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.2px;
        margin: 0;
        white-space: nowrap;
        text-transform: uppercase;
    }
    .category-group-line {
        flex-grow: 1;
        height: 1px;
        background-color: #e2e8f0;
    }
    .category-group-desc {
        font-size: 0.88rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    /* ========================================================================= */
    /* KARTU EQUIPMENT (GRID) */
    /* ========================================================================= */
    .equipment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }
    .equipment-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        height: 100%;
    }
    .equipment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px -5px rgba(107, 28, 28, 0.1);
        border-color: #cbd5e1;
        background: #ffffff;
    }

    .equipment-image-wrapper {
        width: 100%;
        height: 220px;
        border-radius: 0.85rem;
        overflow: hidden;
        margin-bottom: 1.25rem;
        background: #f1f5f9;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .equipment-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .equipment-card:hover .equipment-image-wrapper img {
        transform: scale(1.05);
    }
    .equipment-image-placeholder {
        color: #94a3b8;
        font-size: 3rem;
    }

    .equipment-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        margin-top: 0;
        margin-bottom: 0.4rem;
        line-height: 1.4;
    }
    .equipment-spec {
        font-size: 0.85rem;
        font-weight: 600;
        color: #9f1239;
        margin-bottom: 0.8rem;
        background: #fff0f2;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
        width: fit-content;
    }
    .equipment-desc {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex-grow: 1;
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.95) 10%, rgba(26, 26, 26, 0.8)), url('{{ asset('images/2.jpeg') }}'); padding: 5rem 0 9rem 0;">
        <div class="overview-hero-content d-flex flex-column align-items-center text-center">
            <h1 class="overview-main-title text-white mb-3">Laboratory Equipment</h1>
            <div class="overview-title-line" style="width: 60px; height: 4px; background-color: #ffffff; border-radius: 50px;"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="equipment-container">
        <div class="equipment-card-main">

            <!-- Pengelompokan Berdasarkan Kategori (Tanpa Filter Kategori di Atas) -->
            @forelse($categories as $category)
                @php
                    $itemsInCat = $equipments->get($category->name);
                @endphp

                @if($itemsInCat && $itemsInCat->isNotEmpty())
                    <div class="category-group">
                        <div class="category-heading-box">
                            <!-- Menampilkan nama kategori langsung tanpa tambahan angka ganda -->
                            <h3 class="category-group-title">{{ $category->name }}</h3>
                            <div class="category-group-line"></div>
                        </div>
                        <p class="category-group-desc">Daftar instrumen dan perangkat keras untuk mendukung pengujian pada bidang ini.</p>

                        <div class="equipment-grid">
                            @foreach($itemsInCat as $item)
                                <div class="equipment-card">
                                    <div class="equipment-image-wrapper">
                                        @if($item->photo)
                                            <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}">
                                        @else
                                            <i class="fa-solid fa-image equipment-image-placeholder"></i>
                                        @endif
                                    </div>

                                    <h3 class="equipment-title">{{ $item->name }}</h3>

                                    @if($item->specification)
                                        <div class="equipment-spec">{{ $item->specification }}</div>
                                    @endif

                                    <p class="equipment-desc">
                                        {{ $item->description ?: 'Belum ada deskripsi untuk peralatan ini.' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                    <div class="d-inline-flex align-items-center justify-content-center bg-slate-50 rounded-circle mb-3" style="width: 80px; height: 80px; color: #cbd5e1;">
                        <i class="fa-solid fa-box-open fa-2x"></i>
                    </div>
                    <h4 class="text-slate-700 fw-bold">Data Peralatan Belum Tersedia</h4>
                    <p class="text-slate-500">Daftar peralatan laboratorium akan segera ditambahkan.</p>
                </div>
            @endforelse

            <!-- Menampilkan Alat Tanpa Kategori (Jika ada) -->
            @if($equipments->has('') && $equipments->get('')->isNotEmpty())
                <div class="category-group" style="margin-top: 3rem;">
                    <div class="category-heading-box">
                        <h3 class="category-group-title" style="color: #64748b;">LAINNYA / TANPA KATEGORI</h3>
                        <div class="category-group-line"></div>
                    </div>
                    <div class="equipment-grid" style="margin-top: 1.5rem;">
                        @foreach($equipments->get('') as $item)
                            <div class="equipment-card">
                                <div class="equipment-image-wrapper">
                                    @if($item->photo)
                                        <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}">
                                    @else
                                        <i class="fa-solid fa-image equipment-image-placeholder"></i>
                                    @endif
                                </div>
                                <h3 class="equipment-title">{{ $item->name }}</h3>
                                @if($item->specification)
                                    <div class="equipment-spec">{{ $item->specification }}</div>
                                @endif
                                <p class="equipment-desc">
                                    {{ $item->description ?: 'Belum ada deskripsi untuk peralatan ini.' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
