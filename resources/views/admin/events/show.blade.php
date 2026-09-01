@extends('layouts.admin')

@section('title', $event->title)

@section('content')
<div
    x-data="{
        panelOpen: false,
        form: {
            title: @js($event->title),
            description: @js($event->description),
            event_date: @js($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : ''),
            event_time: @js($event->event_time),
            event_place: @js($event->event_place),
            gdrive_link: @js($event->gdrive_link ?? '')
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
            <a href="{{ route('admin.events.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Detail Kegiatan</h1>
                <p class="mt-1 text-sm text-slate-500">Informasi lengkap, deskripsi acara, dan riwayat perubahan data.</p>
            </div>
        </div>

        <!-- Tombol Trigger Edit -->
        <button
            type="button"
            @click="openEdit()"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer"
        >
            <i class="fa-regular fa-pen-to-square text-xs"></i> Edit Kegiatan
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

        <!-- Sidebar Info Acara (Kiri) - Sticky -->
        <div class="space-y-6 lg:col-span-1 lg:sticky lg:top-24">
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-soft">
                <!-- Ikon Pengganti Foto -->
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-4 ring-indigo-50/50">
                    <i class="fa-solid fa-users-viewfinder text-4xl"></i>
                </div>
                
                <h2 class="mt-5 text-base font-bold text-slate-800 leading-snug">{{ $event->title }}</h2>
                <p class="mt-1.5 text-xs font-semibold text-slate-500">Dipublikasi: {{ \Carbon\Carbon::parse($event->uploaded_at)->translatedFormat('d M Y') }}</p>

                <div class="mt-5 space-y-3 border-t border-slate-100 pt-5 text-left text-sm">
                    <div class="flex items-start gap-3 text-slate-600">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-50 text-slate-400">
                            <i class="fa-regular fa-calendar-days text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tanggal & Waktu</p>
                            <p class="font-medium text-slate-700 mt-0.5">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}</p>
                            <p class="text-xs text-slate-500">{{ $event->event_time }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 text-slate-600">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-50 text-slate-400">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Lokasi / Platform</p>
                            <p class="font-medium text-slate-700 mt-0.5">{{ $event->event_place }}</p>
                        </div>
                    </div>

                    <!-- Tombol Eksternal: Google Drive -->
                    <div class="pt-4 space-y-2">
                        @if ($event->gdrive_link)
                            <a href="{{ $event->gdrive_link }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full rounded-lg bg-blue-50 px-4 py-2.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors border border-blue-100">
                                <i class="fa-brands fa-google-drive text-sm"></i> Buka Tautan Berkas
                            </a>
                        @else
                            <button disabled class="flex items-center justify-center gap-2 w-full rounded-lg bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-400 border border-slate-200 cursor-not-allowed">
                                <i class="fa-brands fa-google-drive text-sm"></i> Tidak Ada Tautan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Konten (Kanan) -->
        <div class="space-y-6 lg:col-span-2">
            
            <!-- 1. Deskripsi Acara -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-align-left mr-2 text-maroon-400"></i>Deskripsi & Instruksi Kegiatan</h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
                </div>
            </div>

            <!-- 2. Riwayat Perubahan -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-maroon-400"></i>Riwayat Perubahan Data
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    {{-- Pastikan ActivityLog package terpasang dan ActivityLogs direlasi pada Model LaboratoryEvent --}}
                    @if(method_exists($event, 'activityLogs') && $event->activityLogs->count() > 0)
                        @foreach ($event->activityLogs as $log)
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
                                        $ignored = ['id', 'created_at', 'updated_at'];
                                        $diffs = [];

                                        foreach ($log->properties['new'] as $key => $newVal) {
                                            if (in_array($key, $ignored)) continue;
                                            $oldVal = $log->properties['old'][$key] ?? null;
                                            if ((string)$oldVal !== (string)$newVal) {
                                                $diffs[$key] = [
                                                    'old' => $oldVal,
                                                    'new' => $newVal
                                                ];
                                            }
                                        }

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
                                                        {{ $key }}:
                                                        @if(!is_null($diff['old']) && $diff['old'] !== '')
                                                            <span class="text-red-500 line-through">{{ is_array($diff['old']) ? json_encode($diff['old']) : $diff['old'] }}</span>
                                                        @endif
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
                        @endforeach
                    @else
                        <p class="p-5 text-center text-xs text-slate-400">Belum ada riwayat perubahan yang tercatat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: EDIT EVENT (FULL HEIGHT) ============================= --}}
    <div
        x-cloak
        x-show="panelOpen"
        class="fixed inset-0 overflow-hidden"
        style="z-index: 9999;"
        @keydown.escape.window="closeEdit()"
    >
        <!-- Backdrop Overlay Full Viewport -->
        <div
            x-show="panelOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
            @click="closeEdit()"
        ></div>

        <!-- Slide Panel Drawer Full Height (top-0 h-full) -->
        <div class="fixed inset-y-0 right-0 top-0 h-full flex max-w-full pl-10">
            <div
                x-show="panelOpen"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-xl h-full bg-white shadow-2xl flex flex-col justify-between"
            >
                <!-- Panel Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Edit Data Kegiatan</h2>
                        <p class="text-xs text-slate-400">Perbarui informasi jadwal, lokasi, dan detail acara.</p>
                    </div>
                    <button type="button" @click="closeEdit()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="eventDetailEditForm" method="POST" action="{{ route('admin.events.update', $event) }}" class="flex-1 overflow-y-auto px-6 py-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Kegiatan *</label>
                            <input type="text" name="title" x-model="form.title" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Deskripsi & Instruksi Kegiatan *</label>
                            <textarea name="description" x-model="form.description" required rows="5" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Pelaksanaan *</label>
                                <input type="date" name="event_date" x-model="form.event_date" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Waktu *</label>
                                <input type="text" name="event_time" x-model="form.event_time" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Lokasi / Platform *</label>
                            <input type="text" name="event_place" x-model="form.event_place" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tautan Berkas (Opsional)</label>
                            <input type="url" name="gdrive_link" x-model="form.gdrive_link" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>
                </form>

                <!-- Panel Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closeEdit()" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="eventDetailEditForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection