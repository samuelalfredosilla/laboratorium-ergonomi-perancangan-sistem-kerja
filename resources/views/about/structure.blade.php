@extends('layouts.app')

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
            <h1 class="overview-main-title text-white mb-3">Structure Organization</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container dengan Struktur Achievement -->
    <div class="achievement-container">
        <div class="achievement-card-main">
            
            <!-- Header Section: Struktur Organisasi -->
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <div>
                    <h2 class="section-title">{{ $structure->title ?? 'Struktur Organisasi Laboratorium' }}</h2>
                    <p class="section-subtitle">
                        Bagan susunan kepengurusan dan tata kelola Laboratorium Ergonomi dan Perancangan Sistem Kerja guna mendukung kegiatan akademik, penelitian, dan operasional laboratorium.
                    </p>
                </div>
            </div>

            <!-- 1. BAGAN GAMBAR STRUKTUR ORGANISASI -->
            <div class="overview-card p-4 text-center mb-5 border rounded-3 bg-slate-50">
                @if($structure && $structure->image_path)
                    <div>
                        <img src="{{ asset('storage/' . $structure->image_path) }}" 
                             alt="{{ $structure->title }}" 
                             class="img-fluid rounded-3 shadow-sm" 
                             style="max-width: 100%; height: auto; max-height: 800px; object-fit: contain;">
                    </div>
                @else
                    <div class="py-5 text-muted">
                        <i class="fa-solid fa-sitemap fa-3x mb-3 text-secondary"></i>
                        <p class="overview-text mb-0">Bagan struktur organisasi saat ini sedang dalam proses pembaruan oleh administrator.</p>
                    </div>
                @endif
            </div>

            <!-- Section Header: Tugas & Fungsi -->
            <div class="section-header-box mt-5">
                <div class="section-icon">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <h2 class="section-title">Tugas & Fungsi Struktur Organisasi</h2>
                    <p class="section-subtitle">
                        Uraian tanggung jawab dan fungsi kerja dari setiap lini kepengurusan di Laboratorium Ergonomi dan Perancangan Sistem Kerja.
                    </p>
                </div>
            </div>

            <!-- 2. GRID DESKRIPSI STRUKTUR (Level Pimpinan & Dosen) -->
            <div class="vm-grid mb-4">
                <!-- Laboratory Chief -->
                <div class="overview-card vision-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-vision mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-user-tie"></i> LEADERSHIP
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Laboratory Chief (Kepala Laboratorium)</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Memimpin, mengarahkan, dan bertanggung jawab penuh atas seluruh kebijakan strategis, pengelolaan sarana prasarana, standar operasional prosedur, serta pengembangan riset dan kegiatan praktikum di Laboratorium EPSK.
                    </p>
                </div>

                <!-- Lecturer of Interest -->
                <div class="overview-card mission-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-mission mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-chalkboard-user"></i> ACADEMIC & RESEARCH
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Lecturer of Interest (Dosen KBK Ergonomi)</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Kelompok dosen bidang keahlian yang bertindak sebagai pembina akademik, pengarah roadmap riset laboratorium, pembimbing praktikum, serta pengembang keilmuan ergonomi dan perancangan sistem kerja.
                    </p>
                </div>
            </div>

            <!-- 3. GRID KOORDINATOR ASISTEN, SEKRETARIS, BENDAHARA -->
            <div class="vm-grid mb-4">
                <!-- Coordinator of Lab Assistant -->
                <div class="overview-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-vision mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-users-gear"></i> ASSISTANT COORDINATION
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Coordinator of Laboratory Assistant</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Mengoordinasikan seluruh asisten laboratorium dalam menjalankan kegiatan operasional harian, program praktikum mahasiswa, serta menjembatani komunikasi antara dosen pembina dan seluruh staf asisten.
                    </p>
                </div>

                <!-- Secretary & Treasurer -->
                <div class="overview-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-mission mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-file-invoice-dollar"></i> ADMINISTRATION & FINANCE
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Secretary & Treasurer of Laboratory</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        <strong>Secretary:</strong> Bertanggung jawab atas pengelolaan surat-menyurat resmi, dokumentasi notula, pengarsipan berkas laboratorium, dan inventarisasi data administrasi praktikum.<br>
                        <strong>Treasurer:</strong> Mengelola perputaran arus kas, pencatatan anggaran operasional, serta penyusunan laporan keuangan kegiatan praktikum dan laboratorium.
                    </p>
                </div>
            </div>

            <!-- 4. GRID DIVISI OPERASIONAL ASISTEN -->
            <div class="vm-grid">
                <!-- Maintenance Division -->
                <div class="overview-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-vision mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-screwdriver-wrench"></i> DIVISION
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Division of Maintenance</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Bertanggung jawab atas pemeliharaan rutin, kalibrasi, pengawasan kondisi fisik alat laboratorium, serta pengelolaan ketersediaan fasilitas penunjang praktikum ergonomi.
                    </p>
                </div>

                <!-- HRD Division -->
                <div class="overview-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-mission mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-user-group"></i> DIVISION
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Division of Human Resources Development (HRD)</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Mengelola program kaderisasi asisten baru, pengembangan kapasitas dan kompetensi internal tim, evaluasi kinerja asisten, serta menjaga keharmonisan internal EPSIKERS.
                    </p>
                </div>

                <!-- RnD Division -->
                <div class="overview-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-vision mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-flask-vial"></i> DIVISION
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Division of Research and Development (RnD)</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Fokus pada pembaruan modul praktikum, penelitian terapan ergonomi, eksperimen rancang bangun sistem kerja, dan pendampingan publikasi ilmiah mahasiswa maupun asisten.
                    </p>
                </div>

                <!-- Infocom Division -->
                <div class="overview-card p-4 border rounded-3 mb-3">
                    <div class="card-tag tag-mission mb-2 text-maroon-700 font-bold">
                        <i class="fa-solid fa-bullhorn"></i> DIVISION
                    </div>
                    <h3 class="overview-card-title text-lg font-bold text-slate-800 mb-2">Division of Information and Communication (Infocom)</h3>
                    <p class="overview-text text-slate-600 text-sm">
                        Mengelola publikasi media sosial, dokumentasi visual kegiatan, hubungan masyarakat, serta pemeliharaan konten informasi website resmi laboratorium EPSK.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection