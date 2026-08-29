@extends('layouts.admin')

@section('title', $task->title)

@section('content')
<div
    x-data="{
        panelOpen: false,
        form: {
            title: @js($task->title),
            description: @js($task->description ?? ''),
            gdrive_link: @js($task->gdrive_link ?? ''),
            collection_date: @js($task->collection_date ?? ''),
            collection_time: @js($task->collection_time ?? ''),
            collection_place: @js($task->collection_place ?? 'Ruang Laboratorium EPSK')
        },
        openEdit() {
            this.panelOpen = true;
        },
        closeEdit() {
            this.panelOpen = false;
        }
    }"
    class="space-y-6"
>
    <!-- Header Navigation -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.practicum.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Detail Tugas Praktikum</h1>
                <p class="mt-1 text-sm text-slate-500">Profil penugasan, berkas soal GDrive, dan riwayat log perubahan.</p>
            </div>
        </div>

        <!-- Tombol Trigger Edit -->
        <button
            type="button"
            @click="openEdit()"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer"
        >
            <i class="fa-regular fa-pen-to-square text-xs"></i> Edit Data Tugas
        </button>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="mb-1 font-semibold">Periksa kembali isian berikut:</p>
            <ul class="list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2 Kolom Grid -->
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

        <!-- Sidebar Info Penugasan (Kiri) - Sticky -->
        <div class="space-y-6 lg:col-span-1 lg:sticky lg:top-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-soft">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-maroon-50 text-maroon-600 ring-4 ring-maroon-50/60">
                    <i class="fa-solid fa-clipboard-check text-4xl"></i>
                </div>

                <h2 class="mt-4 text-base font-bold text-slate-800 leading-snug">{{ $task->title }}</h2>
                <p class="mt-1 text-xs text-slate-400">
                    <i class="fa-regular fa-calendar-plus me-1 text-maroon-500"></i>
                    Dibuat: {{ \Carbon\Carbon::parse($task->created_at ?? $task->uploaded_at)->translatedFormat('d F Y, H:i') }} WIB
                </p>

                <div class="mt-3">
                    <span class="inline-flex items-center rounded-full bg-maroon-50 px-3 py-1 text-xs font-semibold text-maroon-700 border border-maroon-100">
                        <i class="fa-solid fa-circle-dot text-[8px] mr-1.5 text-maroon-600"></i> Aktif untuk Praktikan
                    </span>
                </div>

                <div class="mt-5 space-y-2.5 border-t border-slate-100 pt-5 text-left text-sm">
                    <div class="rounded-lg bg-slate-50 p-3 border border-slate-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Hari &amp; Tanggal</span>
                        <p class="font-semibold text-slate-700 flex items-center gap-2 mb-0">
                            <i class="fa-regular fa-calendar text-maroon-500"></i>
                            <span>{{ $task->collection_date }}</span>
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 border border-slate-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Waktu Pengumpulan</span>
                        <p class="font-semibold text-slate-700 flex items-center gap-2 mb-0">
                            <i class="fa-regular fa-clock text-maroon-500"></i>
                            <span>{{ $task->collection_time }}</span>
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 border border-slate-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Lokasi Pengumpulan</span>
                        <p class="font-semibold text-slate-700 flex items-center gap-2 mb-0">
                            <i class="fa-solid fa-location-dot text-maroon-500"></i>
                            <span>{{ $task->collection_place }}</span>
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ $task->gdrive_link }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full rounded-lg bg-blue-50 px-4 py-2.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors border border-blue-100">
                            <i class="fa-brands fa-google-drive text-sm"></i> Buka Direktori Google Drive
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Konten & Petunjuk Penugasan (Kanan) -->
        <div class="space-y-6 lg:col-span-2">
            <!-- 1. Deskripsi Tugas -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-align-left mr-2 text-maroon-400"></i>Deskripsi &amp; Ringkasan Tugas
                    </h3>
                </div>
                <div class="p-5 text-sm leading-relaxed text-slate-600 whitespace-pre-line">
                    {{ $task->description }}
                </div>
            </div>

            <!-- 2. Tautan Berkas -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-cloud-arrow-down mr-2 text-maroon-400"></i>Tautan Berkas &amp; Lembar Kerja
                    </h3>
                </div>
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50/70 p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                <i class="fa-brands fa-google-drive text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Google Drive Document / Folder</p>
                                <p class="text-[11px] text-slate-400 truncate max-w-xs sm:max-w-md">{{ $task->gdrive_link }}</p>
                            </div>
                        </div>
                        <a href="{{ $task->gdrive_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white shadow-soft hover:bg-maroon-700">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i> Kunjungi Tautan
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Standar Ketentuan Pengerjaan -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-list-check mr-2 text-maroon-400"></i>Ketentuan Pengerjaan Praktikan
                    </h3>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 rounded-lg border border-red-100 bg-red-50/60 p-3 text-xs text-red-800">
                            <i class="fa-solid fa-ban mt-0.5 text-red-500"></i>
                            <span>Dilarang keras menyalin atau menjiplak tugas praktikan lain (<strong>Plagiarisme / Copas = Nilai 0</strong>).</span>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-slate-100 bg-slate-50 p-3 text-xs text-slate-600">
                            <i class="fa-solid fa-pen-nib mt-0.5 text-maroon-500"></i>
                            <span>Jawaban ditulis tangan secara rapi menggunakan <strong>pulpen tinta biru</strong> sesuai format penugasan.</span>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-slate-100 bg-slate-50 p-3 text-xs text-slate-600">
                            <i class="fa-solid fa-file-lines mt-0.5 text-maroon-500"></i>
                            <span>Jawaban dikerjakan di atas kertas <strong>A4 berkop resmi EPSK</strong> dengan format batas margin <strong>4-3-3-3</strong>.</span>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-slate-100 bg-slate-50 p-3 text-xs text-slate-600">
                            <i class="fa-solid fa-eraser mt-0.5 text-maroon-500"></i>
                            <span>Kerapian tulisan dinilai. Penggunaan correction pen/tipe-x <strong>dibatasi maksimal 3 kali</strong> pada seluruh lembar kerja.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Riwayat Perubahan -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-maroon-400"></i>Riwayat Perubahan
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse ($task->activityLogs as $log)
                        <div class="p-4 text-xs text-slate-600 space-y-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-bold text-slate-800 leading-snug">{{ $log->description }}</span>
                                <span class="text-slate-400 whitespace-nowrap text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-400 text-[11px]">
                                Oleh: <span class="font-medium text-slate-600">{{ $log->user->name ?? 'Admin EPSK' }}</span> &middot; {{ $log->created_at->format('d M Y, H:i') }}
                            </p>

                            @if(isset($log->properties['new']) && is_array($log->properties['new']))
                                @php
                                    $ignored = ['id', 'created_at', 'updated_at', 'uploaded_at'];
                                    $diffs = [];

                                    // 1. Coba filter perbedaan nilai
                                    foreach ($log->properties['new'] as $key => $newVal) {
                                        if (in_array($key, $ignored)) continue;
                                        $oldVal = $log->properties['old'][$key] ?? null;
                                        if ((string)$oldVal !== (string)$newVal) {
                                            $diffs[$key] = ['old' => $oldVal, 'new' => $newVal];
                                        }
                                    }

                                    // 2. Fallback untuk data log lama yang nilainya tercatat sama di old & new
                                    if (empty($diffs)) {
                                        foreach ($log->properties['new'] as $key => $newVal) {
                                            if (in_array($key, $ignored)) continue;
                                            $diffs[$key] = [
                                                'old' => $log->properties['old'][$key] ?? $newVal,
                                                'new' => $newVal
                                            ];
                                        }
                                    }
                                @endphp

                                @if(count($diffs) > 0)
                                    <div class="mt-2.5 rounded-lg bg-slate-50 p-2.5 font-mono text-[11px] space-y-2 border border-slate-100 leading-relaxed">
                                        @foreach($diffs as $key => $diff)
                                            <div>
                                                <div class="text-slate-500">
                                                    {{ $key }}: <span class="text-red-500 line-through">{{ is_array($diff['old']) ? json_encode($diff['old']) : $diff['old'] }}</span>
                                                </div>
                                                <div class="text-emerald-600 font-semibold pl-2">
                                                    &rarr; {{ is_array($diff['new']) ? json_encode($diff['new']) : $diff['new'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="p-5 text-center text-xs text-slate-400">Belum ada riwayat perubahan yang tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: EDIT PRACTICUM TASK ============================= --}}
    <div
        x-cloak
        x-show="panelOpen"
        class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="closeEdit()"
    >
        <!-- Backdrop Overlay -->
        <div
            x-show="panelOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
            @click="closeEdit()"
        ></div>

        <!-- Slide Panel Drawer -->
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div
                x-show="panelOpen"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-xl bg-white shadow-2xl flex flex-col justify-between"
            >
                <!-- Panel Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Edit Tugas Praktikum</h2>
                        <p class="text-xs text-slate-400">Perbarui detail penugasan, berkas soal, atau batas waktu pengumpulan.</p>
                    </div>
                    <button type="button" @click="closeEdit()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="practicumDetailEditForm" method="POST" action="{{ route('admin.practicum.update', $task->id) }}" class="flex-1 space-y-4 overflow-y-auto px-6 py-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Tugas / Topik Praktikum *</label>
                        <input type="text" name="title" x-model="form.title" required
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Deskripsi Singkat Penugasan *</label>
                        <textarea name="description" x-model="form.description" rows="4" required
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Link Google Drive Berkas Soal *</label>
                        <div class="relative">
                            <i class="fa-brands fa-google-drive pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                            <input type="url" name="gdrive_link" x-model="form.gdrive_link" required
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-9 pr-3 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Hari / Tanggal Pengumpulan *</label>
                            <input type="text" name="collection_date" x-model="form.collection_date" required
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Waktu Pengumpulan *</label>
                            <input type="text" name="collection_time" x-model="form.collection_time" required
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tempat Pengumpulan *</label>
                            <input type="text" name="collection_place" x-model="form.collection_place" required
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>
                </form>

                <!-- Panel Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                    <button @click="closeEdit()" type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="practicumDetailEditForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
