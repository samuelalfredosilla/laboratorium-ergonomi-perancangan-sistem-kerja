@extends('layouts.app')

@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner dengan Gambar 2.jpeg -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Structure Organization</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Section Header Intro -->
        <div class="section-intro text-center">
            <h2 class="section-heading">{{ $structure->title ?? 'Struktur Organisasi Laboratorium' }}</h2>
            <p class="section-subtext">
                Bagan susunan kepengurusan dan tata kelola Laboratorium Ergonomi dan Perancangan Sistem Kerja guna mendukung kegiatan akademik, penelitian, dan operasional laboratorium.
            </p>
        </div>

        <!-- 1. BAGAN GAMBAR STRUKTUR ORGANISASI -->
        <div class="overview-card p-4 text-center mb-5">
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

        <!-- Section Penjelasan Peran & Tugas -->
        <div class="section-intro text-center mt-5">
            <h2 class="section-heading">Tugas & Fungsi Struktur Organisasi</h2>
            <p class="section-subtext">
                Uraian tanggung jawab dan fungsi kerja dari setiap lini kepengurusan di Laboratorium Ergonomi dan Perancangan Sistem Kerja.
            </p>
        </div>

        <!-- 2. GRID DESKRIPSI STRUKTUR (Level Pimpinan & Dosen) -->
        <div class="vm-grid mb-4">
            <!-- Laboratory Chief -->
            <div class="overview-card vision-card">
                <div class="card-tag tag-vision">
                    <i class="fa-solid fa-user-tie"></i> LEADERSHIP
                </div>
                <h3 class="overview-card-title">Laboratory Chief (Kepala Laboratorium)</h3>
                <p class="overview-text">
                    Memimpin, mengarahkan, dan bertanggung jawab penuh atas seluruh kebijakan strategis, pengelolaan sarana prasarana, standar operasional prosedur, serta pengembangan riset dan kegiatan praktikum di Laboratorium EPSK.
                </p>
            </div>

            <!-- Lecturer of Interest -->
            <div class="overview-card mission-card">
                <div class="card-tag tag-mission">
                    <i class="fa-solid fa-chalkboard-user"></i> ACADEMIC & RESEARCH
                </div>
                <h3 class="overview-card-title">Lecturer of Interest (Dosen KBK Ergonomi)</h3>
                <p class="overview-text">
                    Kelompok dosen bidang keahlian yang bertindak sebagai pembina akademik, pengarah roadmap riset laboratorium, pembimbing praktikum, serta pengembang keilmuan ergonomi dan perancangan sistem kerja.
                </p>
            </div>
        </div>

        <!-- 3. GRID KOORDINATOR ASISTEN, SEKRETARIS, BENDAHARA -->
        <div class="vm-grid mb-4">
            <!-- Coordinator of Lab Assistant -->
            <div class="overview-card">
                <div class="card-tag tag-vision">
                    <i class="fa-solid fa-users-gear"></i> ASSISTANT COORDINATION
                </div>
                <h3 class="overview-card-title">Coordinator of Laboratory Assistant</h3>
                <p class="overview-text">
                    Mengoordinasikan seluruh asisten laboratorium dalam menjalankan kegiatan operasional harian, program praktikum mahasiswa, serta menjembatani komunikasi antara dosen pembina dan seluruh staf asisten.
                </p>
            </div>

            <!-- Secretary & Treasurer -->
            <div class="overview-card">
                <div class="card-tag tag-mission">
                    <i class="fa-solid fa-file-invoice-dollar"></i> ADMINISTRATION & FINANCE
                </div>
                <h3 class="overview-card-title">Secretary & Treasurer of Laboratory</h3>
                <p class="overview-text">
                    <strong>Secretary:</strong> Bertanggung jawab atas pengelolaan surat-menyurat resmi, dokumentasi notula, pengarsipan berkas laboratorium, dan inventarisasi data administrasi praktikum.<br>
                    <strong>Treasurer:</strong> Mengelola perputaran arus kas, pencatatan anggaran operasional, serta penyusunan laporan keuangan kegiatan praktikum dan laboratorium.
                </p>
            </div>
        </div>

        <!-- 4. GRID DIVISI OPERASIONAL ASISTEN -->
        <div class="vm-grid">
            <!-- Maintenance Division -->
            <div class="overview-card">
                <div class="card-tag tag-vision">
                    <i class="fa-solid fa-screwdriver-wrench"></i> DIVISION
                </div>
                <h3 class="overview-card-title">Division of Maintenance</h3>
                <p class="overview-text">
                    Bertanggung jawab atas pemeliharaan rutin, kalibrasi, pengawasan kondisi fisik alat laboratorium, serta pengelolaan ketersediaan fasilitas penunjang praktikum ergonomi.
                </p>
            </div>

            <!-- HRD Division -->
            <div class="overview-card">
                <div class="card-tag tag-mission">
                    <i class="fa-solid fa-user-group"></i> DIVISION
                </div>
                <h3 class="overview-card-title">Division of Human Resources Development (HRD)</h3>
                <p class="overview-text">
                    Mengelola program kaderisasi asisten baru, pengembangan kapasitas dan kompetensi internal tim, evaluasi kinerja asisten, serta menjaga keharmonisan internal EPSIKERS.
                </p>
            </div>

            <!-- RnD Division -->
            <div class="overview-card">
                <div class="card-tag tag-vision">
                    <i class="fa-solid fa-flask-vial"></i> DIVISION
                </div>
                <h3 class="overview-card-title">Division of Research and Development (RnD)</h3>
                <p class="overview-text">
                    Fokus pada pembaruan modul praktikum, penelitian terapan ergonomi, eksperimen rancang bangun sistem kerja, dan pendampingan publikasi ilmiah mahasiswa maupun asisten.
                </p>
            </div>

            <!-- Infocom Division -->
            <div class="overview-card">
                <div class="card-tag tag-mission">
                    <i class="fa-solid fa-bullhorn"></i> DIVISION
                </div>
                <h3 class="overview-card-title">Division of Information and Communication (Infocom)</h3>
                <p class="overview-text">
                    Mengelola publikasi media sosial, dokumentasi visual kegiatan, hubungan masyarakat, serta pemeliharaan konten informasi website resmi laboratorium EPSK.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection