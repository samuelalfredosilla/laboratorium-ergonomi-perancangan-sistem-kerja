@extends('layouts.admin')

@section('title', $lecturer->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.lecturers.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Detail Dosen</h1>
            <p class="mt-1 text-sm text-slate-500">Profil lengkap, riwayat pendidikan, dan rekam jejak dosen.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Card -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-soft lg:col-span-1">
            <img src="{{ $lecturer->photo_url }}" alt="{{ $lecturer->name }}" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-maroon-50">
            <h2 class="mt-4 text-base font-bold text-slate-800">{{ $lecturer->name }}</h2>
            <p class="mt-1 text-xs text-slate-400">{{ $lecturer->nip ?? 'NIP belum diisi' }}</p>

            <div class="mt-3">
                @if (Illuminate\Support\Str::contains($lecturer->role, ['Head', 'Kepala', 'Chief'], true))
                    <span class="inline-flex items-center rounded-full bg-maroon-600 px-3 py-1 text-xs font-semibold text-white">{{ $lecturer->role }}</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $lecturer->role }}</span>
                @endif
            </div>

            <div class="mt-5 space-y-2.5 border-t border-slate-100 pt-5 text-left text-sm">
                <p class="flex items-start gap-2.5 text-slate-600">
                    <i class="fa-solid fa-flask mt-0.5 w-4 text-maroon-400"></i>
                    <span>{{ $lecturer->expertise ?? '—' }}</span>
                </p>
                <p class="flex items-center gap-2.5 text-slate-600">
                    <i class="fa-solid fa-envelope w-4 text-maroon-400"></i>
                    <span class="truncate">{{ $lecturer->email ?? '—' }}</span>
                </p>
                @if ($lecturer->scholar_link)
                    <a href="{{ $lecturer->scholar_link }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 text-maroon-600 hover:underline">
                        <i class="fa-solid fa-graduation-cap w-4"></i>
                        <span class="truncate">Google Scholar</span>
                    </a>
                @endif
                @if ($lecturer->linkedin_link)
                    <a href="{{ $lecturer->linkedin_link }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 text-maroon-600 hover:underline">
                        <i class="fa-brands fa-linkedin w-4"></i>
                        <span class="truncate">LinkedIn</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-graduation-cap mr-2 text-maroon-400"></i>Riwayat Pendidikan</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->educations as $edu)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $edu->degree }}</p>
                                <p class="text-xs text-slate-500">{{ $edu->institution }}</p>
                            </div>
                            <span class="text-xs font-medium text-slate-400">{{ $edu->year_range }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data riwayat pendidikan.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-microscope mr-2 text-maroon-400"></i>Judul Penelitian</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->researches as $research)
                        <div class="flex items-start justify-between gap-4 px-5 py-3.5">
                            <p class="text-sm text-slate-600">{{ $research->title }}</p>
                            <span class="shrink-0 text-xs font-medium text-slate-400">{{ $research->year }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data judul penelitian.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-hand-holding-heart mr-2 text-maroon-400"></i>Pengabdian Masyarakat</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->communityServices as $service)
                        <div class="flex items-start justify-between gap-4 px-5 py-3.5">
                            <p class="text-sm text-slate-600">{{ $service->title }}</p>
                            <span class="shrink-0 text-xs font-medium text-slate-400">{{ $service->year }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data pengabdian masyarakat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
