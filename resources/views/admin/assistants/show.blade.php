@extends('layouts.admin')

@section('title', $assistant->name)

@section('content')
<div
    x-data="{
        panelOpen: false,
        photoPreview: '{{ $assistant->photo_url }}',
        form: {
            name: '{{ addslashes($assistant->name) }}',
            nim: '{{ addslashes($assistant->nim ?? '') }}',
            division: '{{ addslashes($assistant->division ?? '') }}',
            period: '{{ addslashes($assistant->period ?? '2025/2026') }}',
            email: '{{ addslashes($assistant->email ?? '') }}',
            sort_order: {{ $assistant->sort_order ?? 1 }}
        }
    }"
    class="space-y-6"
>
    <!-- Header Navigation -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.assistants.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Detail Asisten (EPSIKERS)</h1>
                <p class="mt-1 text-sm text-slate-500">Profil lengkap dan informasi kepengurusan asisten laboratorium.</p>
            </div>
        </div>
        <button @click="panelOpen = true" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
            <i class="fa-regular fa-pen-to-square text-xs"></i> Edit Asisten
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Card -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-soft lg:col-span-1">
            <img src="{{ $assistant->photo_url }}" alt="{{ $assistant->name }}" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-maroon-50">
            <h2 class="mt-4 text-base font-bold text-slate-800">{{ $assistant->name }}</h2>
            <p class="mt-1 text-xs text-slate-400">NIM. {{ $assistant->nim ?? '—' }}</p>

            <div class="mt-3">
                <span class="inline-flex items-center rounded-full bg-maroon-600 px-3 py-1 text-xs font-semibold text-white">
                    {{ $assistant->division ?? 'Anggota Asisten' }}
                </span>
            </div>

            <div class="mt-5 space-y-3 border-t border-slate-100 pt-5 text-left text-sm">
                <p class="flex items-center gap-2.5 text-slate-600">
                    <i class="fa-solid fa-calendar-days w-4 text-maroon-400"></i>
                    <span>Periode <strong>{{ $assistant->period }}</strong></span>
                </p>
                <p class="flex items-center gap-2.5 text-slate-600">
                    <i class="fa-solid fa-arrow-down-1-9 w-4 text-maroon-400"></i>
                    <span>Urutan Tampil: <strong>#{{ $assistant->sort_order }}</strong></span>
                </p>
                <p class="flex items-center gap-2.5 text-slate-600">
                    <i class="fa-solid fa-envelope w-4 text-maroon-400"></i>
                    <span class="truncate">{{ $assistant->email ?? '—' }}</span>
                </p>
            </div>
        </div>

        <!-- Detail Information Card -->
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
                <h3 class="text-sm font-bold text-slate-700 mb-3"><i class="fa-solid fa-circle-info mr-2 text-maroon-400"></i>Ringkasan Informasi</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <span class="text-xs font-medium text-slate-400">Nama Lengkap</span>
                        <p class="font-semibold text-slate-700 mt-0.5">{{ $assistant->name }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <span class="text-xs font-medium text-slate-400">Nomor Induk Mahasiswa</span>
                        <p class="font-semibold text-slate-700 mt-0.5">{{ $assistant->nim ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <span class="text-xs font-medium text-slate-400">Divisi Penugasan</span>
                        <p class="font-semibold text-slate-700 mt-0.5">{{ $assistant->division ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3.5 border border-slate-100">
                        <span class="text-xs font-medium text-slate-400">Status Periode</span>
                        <p class="font-semibold text-slate-700 mt-0.5">Periode {{ $assistant->period }}</p>
                    </div>
                </div>
            </div>

            <!-- Riwayat Perubahan Data Card -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-maroon-400"></i>Riwayat Perubahan Data
                    </h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($assistant->activityLogs as $log)
                        <div class="p-4 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">{{ $log->description }}</span>
                                <span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-400">
                                Oleh: <span class="font-medium text-slate-600">{{ $log->user->name ?? 'Admin' }}</span> &middot; {{ $log->created_at->format('d M Y, H:i') }}
                            </p>

                            @if(isset($log->properties['new']))
                                <div class="mt-2 rounded bg-slate-50 p-2 font-mono text-[11px]">
                                    @foreach($log->properties['new'] as $key => $val)
                                        @if($key !== 'updated_at')
                                            <div>
                                                <span class="text-slate-500">{{ $key }}:</span> 
                                                <span class="text-red-500 line-through">{{ $log->properties['old'][$key] ?? 'null' }}</span> &rarr; 
                                                <span class="text-emerald-600 font-semibold">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="p-5 text-center text-xs text-slate-400">Belum ada riwayat perubahan yang tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: EDIT ASSISTANT ============================= --}}
    <div x-cloak x-show="panelOpen" x-transition.opacity class="fixed inset-0 z-40" @keydown.escape.window="panelOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="panelOpen = false"></div>

        <div
            x-show="panelOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 flex h-full w-full max-w-lg flex-col bg-white shadow-2xl"
        >

            <!-- Panel Header -->
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Edit Data Asisten</h2>
                    <p class="text-xs text-slate-400">Perbarui data profil dan divisi asisten.</p>
                </div>
                <button @click="panelOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="assistantEditForm" method="POST" action="{{ route('admin.assistants.update', $assistant) }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Foto Asisten</label>
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-dashed border-slate-300 bg-slate-50">
                            <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover">
                            <i x-show="!photoPreview" class="fa-solid fa-user-check text-2xl text-slate-300"></i>
                        </div>
                        <label class="flex-1 cursor-pointer rounded-lg border border-dashed border-slate-300 px-4 py-3 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                            <i class="fa-solid fa-cloud-arrow-up mb-1 block text-lg"></i>
                            Ganti foto profil (Maks 5MB)
                            <input type="file" name="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Lengkap *</label>
                    <input type="text" name="name" x-model="form.name" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">NIM</label>
                        <input type="text" name="nim" x-model="form.nim" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Periode *</label>
                        <input type="text" name="period" x-model="form.period" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-maroon-700 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Divisi / Jabatan</label>
                        <input type="text" name="division" x-model="form.division" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Urutan Tampil</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" min="1" max="100" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Email Mahasiswa</label>
                    <input type="email" name="email" x-model="form.email" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>
            </form>

            <!-- Panel Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="panelOpen = false" type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" form="assistantEditForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
