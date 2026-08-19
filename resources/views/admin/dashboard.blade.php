@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Dashboard Overview</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan aktivitas Laboratorium EPSK &middot; {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-soft hover:border-maroon-200 hover:text-maroon-600">
            <i class="fa-solid fa-arrows-rotate text-xs"></i> Refresh Data
        </a>
    </div>

    <!-- Quick Metrics: 4 grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Dosen Pengampu</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['lecturers']['total'] }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-maroon-50 text-maroon-600">
                    <i class="fa-solid fa-users text-lg"></i>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                <span class="font-semibold text-maroon-600">{{ $stats['lecturers']['head'] }}</span> Kepala Lab &middot;
                <span class="font-semibold text-slate-600">{{ $stats['lecturers']['members'] }}</span> Anggota
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Berita / Artikel</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['news']['total'] }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-newspaper text-lg"></i>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                <span class="font-semibold text-emerald-600">{{ $stats['news']['published'] }}</span> Published &middot;
                <span class="font-semibold text-amber-500">{{ $stats['news']['draft'] }}</span> Draft
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Slider Banner</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['sliders']['total'] }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <i class="fa-solid fa-images text-lg"></i>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                <span class="font-semibold text-sky-600">{{ $stats['sliders']['active'] }}</span> Aktif &middot;
                <span class="font-semibold text-slate-400">{{ $stats['sliders']['inactive'] }}</span> Nonaktif
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Asisten Lab / EPSIKERS</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['assistants']['total'] }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <i class="fa-solid fa-user-check text-lg"></i>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-500">
                <span class="font-semibold text-violet-600">{{ $stats['assistants']['active'] }}</span> Aktif (Periode {{ $stats['assistants']['latest_period'] }})
            </p>
        </div>
    </div>

    <!-- Main widgets -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Latest Published News Table -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-soft lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-700">Berita Terbaru</h2>
                <a href="{{ route('admin.news.index') }}" class="text-xs font-semibold text-maroon-600 hover:underline">Lihat semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Berita</th>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Penulis</th>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($latestNews as $news)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $news->image }}" alt="{{ $news->title }}" class="h-10 w-14 shrink-0 rounded-md object-cover ring-1 ring-slate-200">
                                    <span class="line-clamp-2 max-w-55 font-medium text-slate-700">{{ $news->title }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-600">{{ $news->category }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $news->author }}</td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $news->date }}</td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('admin.news.toggle', $news->id) }}" class="flex justify-center">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $news->published ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $news->published ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar widgets -->
        <div class="space-y-6">

            <!-- Quick Action Card -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
                <h2 class="text-sm font-bold text-slate-700">Aksi Cepat</h2>
                <div class="mt-4 space-y-2.5">
                    <a href="{{ route('admin.lecturers.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-100 px-3.5 py-3 text-sm font-medium text-slate-600 transition-colors hover:border-maroon-200 hover:bg-maroon-50 hover:text-maroon-600">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-maroon-50 text-maroon-600"><i class="fa-solid fa-user-plus"></i></span>
                        Tambah Dosen Baru
                    </a>
                    <a href="{{ route('admin.assistants.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-100 px-3.5 py-3 text-sm font-medium text-slate-600 transition-colors hover:border-violet-200 hover:bg-violet-50 hover:text-violet-600">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><i class="fa-solid fa-user-check"></i></span>
                        Tambah Asisten Baru
                    </a>
                    <a href="{{ route('admin.news.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-100 px-3.5 py-3 text-sm font-medium text-slate-600 transition-colors hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><i class="fa-solid fa-pen-nib"></i></span>
                        Buat Berita Baru
                    </a>
                    <a href="{{ route('admin.sliders.index') }}" class="flex items-center gap-3 rounded-lg border border-slate-100 px-3.5 py-3 text-sm font-medium text-slate-600 transition-colors hover:border-sky-200 hover:bg-sky-50 hover:text-sky-600">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600"><i class="fa-solid fa-image"></i></span>
                        Upload Slider Baru
                    </a>
                </div>
            </div>

            <!-- System Status / Contact Preview -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-700">Status &amp; Kontak</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Online
                    </span>
                </div>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <i class="fa-solid fa-location-dot mt-0.5 w-4 text-maroon-400"></i>
                        <span>{{ $contact['address'] }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-envelope w-4 text-maroon-400"></i>
                        <span>{{ $contact['email'] }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-phone w-4 text-maroon-400"></i>
                        <span>{{ $contact['phone'] }}</span>
                    </li>
                </ul>
                <a href="#" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-maroon-600 hover:underline">
                    Kelola Pengaturan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
