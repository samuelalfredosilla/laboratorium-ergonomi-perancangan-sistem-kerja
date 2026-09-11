@extends('layouts.admin')

@section('title', 'Lab Procedures')

@section('content')
<div
    x-data="{
        panelOpen: false,
        panelMode: 'create',
        editId: null,
        deleteModalOpen: false,
        deleteTarget: null,
        form: { 
            title: '', 
            file_url: '', 
            sort_order: 1, 
            description: '' 
        },
        resetForm() {
            this.form = { 
                title: '', 
                file_url: '', 
                sort_order: 1, 
                description: '' 
            };
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
        },
        openEdit(item) {
            this.form = {
                title: item.title,
                file_url: item.file_url,
                sort_order: item.sort_order,
                description: item.description || ''
            };
            
            this.panelMode = 'edit';
            this.editId = item.id;
            this.panelOpen = true;
        },
        closePanel() {
            this.panelOpen = false;
        },
        confirmDelete(item) {
            this.deleteTarget = item;
            this.deleteModalOpen = true;
        },
        submitDelete() {
            document.getElementById('delete-form-' + this.deleteTarget.id).submit();
        }
    }"
    class="space-y-6"
>
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Lab Procedures</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola panduan layanan dan tautan dokumen prosedur laboratorium.</p>
        </div>
        <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Prosedur
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

    <!-- Table Card -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.procedures.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative w-full lg:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau deskripsi..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Reset Filter -->
                @if (request()->anyFilled(['search']))
                    <a href="{{ route('admin.procedures.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($procedures->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-file-lines text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Belum ada data prosedur</p>
                <p class="max-w-xs text-xs text-slate-400">
                    @if (request()->anyFilled(['search']))
                        Tidak ditemukan prosedur dengan kata kunci yang dipilih.
                    @else
                        Tambahkan panduan layanan dan prosedur laboratorium pertama Anda.
                    @endif
                </p>
                @if (request()->anyFilled(['search']))
                    <a href="{{ route('admin.procedures.index') }}" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                        Lihat Semua Data
                    </a>
                @else
                    <button type="button" @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Prosedur Pertama
                    </button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold text-center" style="width: 60px;">#</th>
                            <th class="px-5 py-3 font-semibold">Judul Prosedur</th>
                            <th class="px-5 py-3 font-semibold">Tautan Dokumen</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($procedures as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-semibold text-slate-400 text-center">
                                {{ $item->sort_order }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="line-clamp-1 max-w-xs font-medium text-slate-800 leading-snug">{{ $item->title }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ $item->file_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700 hover:bg-blue-100 transition-colors border border-blue-100">
                                    <i class="fa-brands fa-google-drive"></i> Lihat File
                                </a>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Edit Action -->
                                    <div class="group relative">
                                        <button type="button" @click="openEdit(@js($item))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>
                                    <!-- Delete Action -->
                                    <div class="group relative">
                                        <button type="button" @click="confirmDelete(@js($item->only('id', 'title')))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>
                                    <form id="delete-form-{{ $item->id }}" method="POST" action="{{ route('admin.procedures.destroy', $item) }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($procedures instanceof \Illuminate\Pagination\LengthAwarePaginator && $procedures->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $procedures->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT PROCEDURE ============================= --}}
    <div 
        x-cloak 
        x-show="panelOpen" 
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9999;"
        @keydown.escape.window="closePanel()"
    >
        <div 
            x-show="panelOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" 
            @click="closePanel()"
        ></div>

        <!-- Panel Drawer Full Tinggi Layar -->
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
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-white">
                    <div>
                        <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Prosedur Baru' : 'Edit Prosedur'"></h2>
                        <p class="text-xs text-slate-400">Lengkapi informasi prosedur dan tautan dokumen resmi lab.</p>
                    </div>
                    <button type="button" @click="closePanel()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form id="procedureForm" method="POST"
                    :action="panelMode === 'create' ? '{{ route('admin.procedures.store') }}' : '{{ url('admin/lab-procedures') }}/' + editId"
                    class="flex-1 space-y-4 overflow-y-auto px-6 py-5">
                    @csrf
                    
                    <template x-if="panelMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Prosedur *</label>
                        <input type="text" name="title" x-model="form.title" required 
                            placeholder="Contoh: Prosedur Peminjaman Ruangan dan Alat"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Deskripsi Singkat Prosedur *</label>
                        <textarea name="description" x-model="form.description" rows="4" required 
                            placeholder="Tuliskan penjelasan atau alur singkat mengenai prosedur ini..."
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Link Google Drive Dokumen *</label>
                        <div class="relative">
                            <i class="fa-brands fa-google-drive pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                            <input type="url" name="file_url" x-model="form.file_url" required 
                                placeholder="https://drive.google.com/file/d/..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-9 pr-3 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Urutan Tampil *</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" required min="1" 
                            placeholder="1"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </form>

                <!-- Panel Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closePanel()" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="procedureForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= DELETE CONFIRMATION MODAL ============================= --}}
    <div 
        x-cloak 
        x-show="deleteModalOpen" 
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9999;"
        @keydown.escape.window="deleteModalOpen = false"
    >
        <div 
            x-show="deleteModalOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" 
            @click="deleteModalOpen = false"
        ></div>

        <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4">
            <div 
                x-show="deleteModalOpen" 
                x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="opacity-0 scale-95" 
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl border border-slate-100"
            >
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-8 ring-red-50/50">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Prosedur?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus data <span class="font-semibold text-slate-700" x-text="deleteTarget?.title"></span>? Data ini akan dihapus permanen.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Batal</button>
                    <button type="button" @click="submitDelete()" class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-semibold text-white shadow-soft hover:bg-red-700 transition-colors cursor-pointer">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection