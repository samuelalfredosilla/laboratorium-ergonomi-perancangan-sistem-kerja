@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Site Settings</h1>
            <p class="mt-1 text-sm text-slate-500">Konfigurasi informasi kontak dan tautan sosial media Laboratorium EPSK.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 font-semibold">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('admin.site-settings.update') }}" class="space-y-6 pb-10">
        @csrf
        @method('PUT')

        <!-- SECTION: Kontak & Alamat -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-soft overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h2 class="text-base font-bold text-slate-800"><i class="fa-solid fa-address-book mr-2 text-maroon-600"></i> Kontak & Alamat</h2>
                <p class="mt-1 text-xs text-slate-500">Informasi agar mahasiswa atau pihak luar dapat menghubungi laboratorium.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Email Resmi</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}"
                            placeholder="epsk@trunojoyo.ac.id"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <div class="relative">
                        <i class="fa-solid fa-phone pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}"
                            placeholder="+62 812-3456-7890"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot pointer-events-none absolute left-3.5 top-3 text-sm text-slate-400"></i>
                        <textarea name="address" rows="3"
                            placeholder="Gedung Laboratorium Terpadu..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">{{ old('address', $setting->address) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: Sosial Media -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-soft overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h2 class="text-base font-bold text-slate-800"><i class="fa-solid fa-share-nodes mr-2 text-maroon-600"></i> Tautan Sosial Media</h2>
                <p class="mt-1 text-xs text-slate-500">Tautan ini akan mengaktifkan ikon sosial media di website publik jika diisi.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Instagram</label>
                    <div class="relative">
                        <i class="fa-brands fa-instagram pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="url" name="instagram_link" value="{{ old('instagram_link', $setting->instagram_link) }}"
                            placeholder="https://instagram.com/lab.epsk"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">YouTube</label>
                    <div class="relative">
                        <i class="fa-brands fa-youtube pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="url" name="youtube_link" value="{{ old('youtube_link', $setting->youtube_link) }}"
                            placeholder="https://youtube.com/@LabEPSK"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">LinkedIn</label>
                    <div class="relative">
                        <i class="fa-brands fa-linkedin-in pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="url" name="linkedin_link" value="{{ old('linkedin_link', $setting->linkedin_link) }}"
                            placeholder="https://linkedin.com/company/..."
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">TikTok</label>
                    <div class="relative">
                        <i class="fa-brands fa-tiktok pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input type="url" name="tiktok_link" value="{{ old('tiktok_link', $setting->tiktok_link) }}"
                            placeholder="https://tiktok.com/@lab.epsk"
                            class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>
            </div>
        </div>

        <!-- Float Action Button Bottom -->
        <div class="sticky bottom-4 z-10 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-maroon-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-maroon-600/30 hover:bg-maroon-700 hover:shadow-maroon-600/50 transition-all cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection