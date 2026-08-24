@extends('layouts.admin')

@section('title', 'Organization Structure')

@section('content')
<div
    x-data="{
        titleValue: '{{ old('title', $structure->title ?? 'Struktur Organisasi Laboratorium EPSK') }}',
        imagePreview: {{ $structure && $structure->image_path ? "'" . asset('storage/' . $structure->image_path) . "'" : 'null' }},
        selectedFileName: null,
        selectedFileSize: null,
        isNewFileSelected: false,
        deleteModalOpen: false,
        previewImage(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            this.selectedFileName = file.name;
            this.selectedFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            this.isNewFileSelected = true;

            const reader = new FileReader();
            reader.onload = (ev) => { this.imagePreview = ev.target.result; };
            reader.readAsDataURL(file);
        },
        resetFileInput() {
            this.$refs.fileInput.value = '';
            this.selectedFileName = null;
            this.selectedFileSize = null;
            this.isNewFileSelected = false;
            this.imagePreview = {{ $structure && $structure->image_path ? "'" . asset('storage/' . $structure->image_path) . "'" : 'null' }};
        }
    }"
    class="space-y-6"
>
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Organization Structure</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola bagan struktur organisasi laboratorium yang ditampilkan pada halaman publik.</p>
        </div>
        @if($structure && $structure->image_path)
            <button @click="deleteModalOpen = true" type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100">
                <i class="fa-regular fa-trash-can text-xs"></i> Hapus Bagan Saat Ini
            </button>
        @endif
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <!-- Kolom Preview Bagan -->
        <div class="lg:col-span-7">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-slate-800">Preview Bagan</h2>
                        <template x-if="isNewFileSelected">
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Belum Disimpan</span>
                        </template>
                    </div>
                    @if($structure)
                        <span class="text-[11px] text-slate-400">
                            Terakhir diubah: {{ $structure->updated_at->format('d M Y, H:i') }} WIB
                        </span>
                    @endif
                </div>

                <div class="flex min-h-[340px] flex-col items-center justify-center overflow-hidden rounded-lg bg-slate-50 p-2">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Preview Bagan" class="max-h-[500px] w-auto rounded-lg object-contain shadow-sm transition-all duration-200">
                    </template>
                    <template x-if="!imagePreview">
                        <div class="flex flex-col items-center gap-2 py-12 text-slate-400">
                            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-300">
                                <i class="fa-solid fa-sitemap text-2xl"></i>
                            </span>
                            <p class="text-xs font-medium">Belum ada bagan struktur organisasi yang diunggah</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Kolom Form Upload & Perbarui -->
        <div class="lg:col-span-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
                <div class="mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-800">Form Perbarui Bagan</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Unggah bagan baru lalu klik simpan perubahan.</p>
                </div>

                <form method="POST" action="{{ route('admin.organization-structure.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul / Periode Bagan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="titleValue" required placeholder="Contoh: Struktur Organisasi Lab EPSK 2026/2027"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                            File Gambar Bagan @if(!$structure) <span class="text-red-500">*</span> @endif
                        </label>
                        
                        <!-- Area Dropzone -->
                        <label 
                            :class="isNewFileSelected ? 'border-emerald-400 bg-emerald-50/40' : 'border-slate-300 hover:border-maroon-300 hover:bg-maroon-50'"
                            class="flex aspect-video cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-lg border-2 border-dashed text-center text-xs transition-colors"
                        >
                            <!-- Tampilan Saat File Dipilih -->
                            <template x-if="isNewFileSelected">
                                <div class="flex flex-col items-center gap-1.5 p-4 text-emerald-700">
                                    <i class="fa-solid fa-circle-check text-2xl text-emerald-600"></i>
                                    <span class="max-w-[220px] truncate font-semibold" x-text="selectedFileName"></span>
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold" x-text="selectedFileSize"></span>
                                    <span class="mt-1 text-[11px] text-slate-500 underline">Klik untuk ganti file lain</span>
                                </div>
                            </template>

                            <!-- Tampilan Default Belum Pilih File Baru -->
                            <template x-if="!isNewFileSelected">
                                <div class="flex flex-col items-center gap-1.5 p-4 text-slate-400">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>
                                    <span class="font-medium text-slate-600">Klik untuk memilih gambar bagan</span>
                                    <span class="text-[10px] text-slate-400">JPG, PNG, atau WEBP (Maks. 8 MB)</span>
                                </div>
                            </template>

                            <input type="file" x-ref="fileInput" name="image" accept="image/*" class="hidden" @change="previewImage($event)" @if(!$structure) required @endif>
                        </label>

                        <!-- Indikator Keterangan File Siap Disimpan -->
                        <template x-if="isNewFileSelected">
                            <div class="mt-2 flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-file-image"></i> Gambar baru siap disimpan
                                </span>
                                <button type="button" @click="resetFileInput()" class="text-xs font-semibold text-red-600 hover:underline">
                                    Batal
                                </button>
                            </div>
                        </template>

                        <p class="mt-1.5 text-[11px] text-slate-400">
                            <i class="fa-solid fa-circle-info mr-1"></i>Menyimpan gambar baru akan otomatis menggantikan file lama di server.
                        </p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                            <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================= DELETE CONFIRM MODAL ============================= --}}
    <div x-cloak x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="deleteModalOpen = false"></div>
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-sm rounded-xl bg-white p-6 text-center shadow-2xl">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </span>
            <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Bagan Organisasi?</h3>
            <p class="mt-1.5 text-sm text-slate-500">
                Bagan yang sedang aktif akan dihapus dari server dan halaman publik. Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex gap-3">
                <button @click="deleteModalOpen = false" type="button" class="flex-1 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <form method="POST" action="{{ route('admin.organization-structure.destroy') }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection