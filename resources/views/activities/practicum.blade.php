@extends('layouts.app')

@section('content')
<style>
    .practicum-container {
        max-width: 1200px;
        margin: -2.5rem auto 0 auto;
        padding: 0 1.25rem 4rem 1.25rem;
        position: relative;
        z-index: 10;
    }
    .practicum-card-main {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        padding: 2.25rem;
        margin-bottom: 2rem;
    }
    .section-header-box {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .section-icon {
        width: 42px;
        height: 42px;
        border-radius: 0.75rem;
        background: #fee2e2;
        color: #881337;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }
    .section-subtitle {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
    }

    /* Grid Kartu Tugas */
    .task-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
    }
    .task-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
    }
    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(107, 28, 28, 0.08);
        border-color: #cbd5e1;
        background: #ffffff;
    }
    .task-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.6rem;
        line-height: 1.4;
    }
    .task-desc {
        font-size: 0.88rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Membatasi tinggi tampilan teks deskripsi kartu */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .task-meta-date {
        font-size: 0.76rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1rem;
        padding-top: 0.75rem;
        border-top: 1px dashed #e2e8f0;
        font-weight: 600;
    }

    /* Tombol Read More */
    .btn-read-more {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #ffffff;
        border: 1.5px solid #881337;
        color: #881337;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.55rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        width: 100%;
    }
    .btn-read-more:hover {
        background: #881337;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(136, 19, 55, 0.25);
    }

    /* Modal Styling */
    .task-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    .task-modal-box {
        background: #ffffff;
        border-radius: 1.5rem;
        max-width: 680px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(226, 232, 240, 0.8);
        position: relative;
        animation: modalScaleUp 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalScaleUp {
        from { opacity: 0; transform: scale(0.93) translateY(12px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .task-modal-header {
        position: relative;
        padding: 1.5rem 1.75rem 1.25rem 1.75rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        border-top-left-radius: 1.5rem;
        border-top-right-radius: 1.5rem;
    }
    .btn-modal-close {
        position: absolute;
        top: 1.25rem;
        right: 1.25rem;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-modal-close:hover {
        background: #fee2e2;
        color: #881337;
        border-color: #fca5a5;
        transform: rotate(90deg);
    }
    .task-modal-body {
        padding: 1.75rem 2rem 2rem 2rem;
    }
    .modal-announcement-card {
        background: #fff5f5;
        border: 1px dashed #fca5a5;
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    .modal-info-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.03);
    }
    .modal-card-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-gdrive-action {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background: #881337;
        color: #ffffff !important;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 0.65rem 1.25rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(136, 19, 55, 0.2);
    }
    .btn-gdrive-action:hover {
        background: #6b1426;
        transform: translateY(-2px);
    }
    .schedule-pill-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
    }
    .schedule-item {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }
    .schedule-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }
    .schedule-val {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-top: 0.2rem;
    }

    /* Bento Grid Ketentuan */
    .rules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 0.75rem;
    }
    .rule-item-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        padding: 0.9rem 1rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    .rule-item-box.rule-warning {
        background: #fff5f5;
        border: 1px solid #fed7d7;
    }
    .rule-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fee2e2;
        color: #881337;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .rule-warning .rule-icon-box {
        background: #e11d48;
        color: #ffffff;
    }
    .rule-text-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.15rem;
    }
    .rule-text-desc {
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.45;
        margin: 0;
    }
</style>

<div class="overview-page-wrapper">

    <!-- Header Hero Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.90) 25%, rgba(26, 26, 26, 0.7)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Practicum Activities</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="practicum-container">

        <!-- 1. PENGANTAR PRAKTIKUM -->
        <div class="practicum-card-main">
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div>
                    <h2 class="section-title">Deskripsi Mata Praktikum</h2>
                    <p class="section-subtitle">Tujuan dan capaian pembelajaran praktikum laboratorium EPSK</p>
                </div>
            </div>
            <p style="color: #475569; font-size: 0.95rem; line-height: 1.7; margin: 0;">
                Praktikum Perancangan Sistem Kerja dan Ergonomi (PSKE) bertujuan membekali mahasiswa Teknik Industri dengan keahlian praktis dalam mengukur, menganalisis, dan merancang sistem kerja yang efektif, nyaman, aman, sehat, dan efisien (ENASE). Mahasiswa melakukan pengukuran langsung mulai dari antropometri, fisiologi kerja, biomekanika, beban kerja mental, hingga evaluasi postur menggunakan standar ergonomi internasional.
            </p>
        </div>

        <!-- 2. DAFTAR TUGAS PRAKTIKUM -->
        <div class="practicum-card-main">
            <div class="section-header-box">
                <div class="section-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h2 class="section-title">Daftar Tugas Praktikum</h2>
                    <p class="section-subtitle">Instruksi penugasan, berkas soal, dan ketentuan pengumpulan praktikan</p>
                </div>
            </div>

            <div class="task-grid">
                @forelse($tasks as $task)
                    <div class="task-card">
                        <h3 class="task-title">{{ $task->title }}</h3>
                        
                        <!-- Ringkasan Deskripsi Singkat (Potongan Teks) -->
                        <p class="task-desc">
                            {{ \Illuminate\Support\Str::limit($task->description, 130, '...') }}
                        </p>
                        
                        <div class="task-meta-date">
                            <i class="fa-regular fa-clock text-maroon"></i> Diupload: {{ \Carbon\Carbon::parse($task->uploaded_at)->translatedFormat('d F Y, H:i') }} WIB
                        </div>

                        <button type="button" class="btn-read-more" 
                            onclick="openTaskModal(
                                @js($task->title), 
                                @js($task->description),
                                @js(\Carbon\Carbon::parse($task->uploaded_at)->translatedFormat('d F Y, H:i') . ' WIB'), 
                                @js($task->collection_date), 
                                @js($task->collection_time), 
                                @js($task->collection_place), 
                                @js($task->gdrive_link)
                            )">
                            Read More <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-5 w-100" style="grid-column: 1 / -1;">
                        <p class="text-muted">Belum ada tugas praktikum yang diterbitkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL PENGUMUMAN TUGAS (MODERN DESIGN) -->
<!-- ========================================================================= -->
<div id="taskDetailModal" class="task-modal-overlay" onclick="closeTaskModal(event)">
    <div class="task-modal-box" onclick="event.stopPropagation()">

        <!-- Header Modal -->
        <div class="task-modal-header">
            <button type="button" class="btn-modal-close" onclick="closeModalDirectly()" title="Tutup Modal">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="pe-4">
                <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #fee2e2; color: #881337; font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 50px; margin-bottom: 0.65rem;">
                    <i class="fa-regular fa-clock"></i>
                    <span id="modalUploadDate">Diupload: -</span>
                </div>

                <h3 id="modalTaskTitle" style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1.35; letter-spacing: -0.3px;">
                    Judul Tugas Praktikum
                </h3>
            </div>
        </div>

        <!-- Body Modal -->
        <div class="task-modal-body">

            <!-- Banner Deskripsi Lengkap -->
            <div class="modal-announcement-card">
                <div class="d-flex gap-2.5 align-items-center mb-1">
                    <i class="fa-solid fa-bullhorn text-maroon"></i>
                    <strong class="text-maroon small">Deskripsi &amp; Instruksi Tugas</strong>
                </div>
                <p id="modalTaskFullDesc" class="small text-slate-600 mb-0" style="line-height: 1.6; white-space: pre-line;"></p>
            </div>

            <!-- 1. Berkas Soal Google Drive -->
            <div class="modal-info-card">
                <div class="modal-card-title">
                    <i class="fa-solid fa-cloud-arrow-down text-maroon"></i>
                    <span>Berkas Soal Tugas</span>
                </div>
                <p class="small text-muted mb-3">Unduh template dan lembar soal tugas melalui direktori Google Drive resmi:</p>
                <a id="modalDownloadLink" href="#" target="_blank" rel="noopener noreferrer" class="btn-gdrive-action">
                    <i class="fa-brands fa-google-drive"></i> Unduh Berkas Soal (GDrive)
                </a>
            </div>

            <!-- 2. Jadwal & Tempat Pengumpulan -->
            <div class="modal-info-card">
                <div class="modal-card-title">
                    <i class="fa-solid fa-calendar-check text-maroon"></i>
                    <span>Jadwal & Tempat Pengumpulan</span>
                </div>
                <div class="schedule-pill-box">
                    <div class="schedule-item">
                        <div class="schedule-label">Hari / Tanggal</div>
                        <div id="modalTaskDate" class="schedule-val">-</div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">Waktu Pengumpulan</div>
                        <div id="modalTaskTime" class="schedule-val">-</div>
                    </div>
                    <div class="schedule-item" style="grid-column: 1 / -1;">
                        <div class="schedule-label">Lokasi / Tempat</div>
                        <div id="modalTaskPlace" class="schedule-val">-</div>
                    </div>
                </div>
            </div>

            <!-- 3. Ketentuan Pengerjaan (Bento Grid) -->
            <div class="modal-info-card mb-0">
                <div class="modal-card-title mb-3">
                    <i class="fa-solid fa-list-check text-maroon"></i>
                    <span>Ketentuan & Format Pengerjaan</span>
                </div>

                <div class="rules-grid">
                    <div class="rule-item-box rule-warning" style="grid-column: 1 / -1;">
                        <div class="rule-icon-box">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                        <div>
                            <div class="rule-text-title text-danger">Bebas Plagiarisme (Anti-Copas)</div>
                            <p class="rule-text-desc text-danger-emphasis">Dilarang keras menyalin tugas milik praktikan lain. Tindakan copy-paste otomatis <strong>Nilai = 0</strong>.</p>
                        </div>
                    </div>

                    <div class="rule-item-box">
                        <div class="rule-icon-box">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <div>
                            <div class="rule-text-title">Tulis Tangan & Tinta</div>
                            <p class="rule-text-desc">Wajib tulis tangan secara rapi menggunakan <strong>pulpen biru</strong>.</p>
                        </div>
                    </div>

                    <div class="rule-item-box">
                        <div class="rule-icon-box">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div>
                            <div class="rule-text-title">Kertas & Margin</div>
                            <p class="rule-text-desc">Kertas <strong>A4 berkop resmi EPSK</strong> dengan format margin <strong>4-3-3-3</strong>.</p>
                        </div>
                    </div>

                    <div class="rule-item-box" style="grid-column: 1 / -1;">
                        <div class="rule-icon-box">
                            <i class="fa-solid fa-eraser"></i>
                        </div>
                        <div>
                            <div class="rule-text-title">Standar Kerapian</div>
                            <p class="rule-text-desc">Penggunaan correction pen (tipe-x) <strong>maksimal 3 kali</strong> di seluruh lembar pengerjaan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivational Footer Badge -->
            <div class="text-center mt-4">
                <span class="badge rounded-pill bg-slate-100 text-slate-700 border border-slate-200 px-3.5 py-2 font-monospace fw-bold small">
                    PRAKTIKAN EPSK SEMANGAT & MAMPU!
                </span>
            </div>

        </div>

    </div>
</div>

<script>
    function openTaskModal(title, fullDescription, uploadDate, date, time, place, gdriveUrl) {
        document.getElementById('modalTaskTitle').innerText = title;
        document.getElementById('modalTaskFullDesc').innerText = fullDescription;
        document.getElementById('modalUploadDate').innerText = 'Diupload: ' + uploadDate;
        document.getElementById('modalTaskDate').innerText = date;
        document.getElementById('modalTaskTime').innerText = time;
        document.getElementById('modalTaskPlace').innerText = place;
        document.getElementById('modalDownloadLink').href = gdriveUrl;

        document.getElementById('taskDetailModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModalDirectly() {
        document.getElementById('taskDetailModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function closeTaskModal(event) {
        if (event.target.id === 'taskDetailModal') {
            closeModalDirectly();
        }
    }
</script>
@endsection