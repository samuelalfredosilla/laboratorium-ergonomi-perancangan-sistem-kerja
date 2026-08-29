@extends('layouts.admin')

@section('title', 'Practicum Activities')

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
            description: '',
            gdrive_link: '',
            collection_date: '',
            collection_time: '',
            collection_place: 'Ruang Laboratorium EPSK'
        },
        resetForm() {
            this.form = {
                title: '',
                description: '',
                gdrive_link: '',
                collection_date: '',
                collection_time: '',
                collection_place: 'Ruang Laboratorium EPSK'
            };
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
        },
        openEdit(task) {
            this.form = {
                title: task.title,
                description: task.description,
                gdrive_link: task.gdrive_link,
                collection_date: task.collection_date,
                collection_time: task.collection_time,
                collection_place: task.collection_place || 'Ruang Laboratorium EPSK'
            };
            this.panelMode = 'edit';
            this.editId = task.id;
            this.panelOpen = true;
        },
        closePanel() {
            this.panelOpen = false;
        },
        confirmDelete(task) {
            this.deleteTarget = task;
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
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Practicum Activities</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola rilis tugas praktikum, jadwal pengumpulan, dan berkas soal Google Drive.</p>
        </div>
        <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Tugas Baru
        </button>
    </div>

    <!-- Table Card with Multi-Filter Toolbar -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">

        <!-- Toolbar Filter Form -->
        <form method="GET" action="{{ route('admin.practicum.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            
            <!-- 1. Search Box -->
            <div class="relative w-full lg:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, topik, lokasi..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>

            <!-- 2. Dropdown Filters & Reset -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Filter Tempat/Lokasi Pengumpulan -->
                <div class="w-full sm:w-auto">
                    <select name="place" onchange="this.form.submit()"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        <option value="">Semua Lokasi</option>
                        @foreach ($places as $p)
                            <option value="{{ $p }}" {{ request('place') == $p ? 'selected' : '' }}>
                                {{ $p }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Urutan (Sorting) -->
                <div class="w-full sm:w-auto">
                    <select name="sort" onchange="this.form.submit()"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru Dirilis</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Dirilis</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul (A &rarr; Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul (Z &rarr; A)</option>
                    </select>
                </div>

                <!-- Tombol Reset Filter -->
                @if (request()->hasAny(['search', 'place', 'sort']))
                    <a href="{{ route('admin.practicum.index') }}" 
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if (count($tasks) === 0)
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-clipboard-list text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Tidak ada data tugas praktikum</p>
                <p class="max-w-xs text-xs text-slate-400">
                    @if (request()->hasAny(['search', 'place', 'sort']))
                        Tidak ditemukan tugas yang sesuai dengan kriteria pencarian/filter Anda.
                    @else
                        Tugas praktikum yang diterbitkan akan tampil di sini dan diakses oleh mahasiswa.
                    @endif
                </p>
                @if (request()->hasAny(['search', 'place', 'sort']))
                    <a href="{{ route('admin.practicum.index') }}" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i> Tampilkan Semua Data
                    </a>
                @else
                    <button type="button" @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Tugas Pertama
                    </button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold whitespace-nowrap" style="width: 50px;">No</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Judul Tugas</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Dibuat Pada</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Jadwal Pengumpulan</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Lokasi</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Berkas Soal</th>
                            <th class="px-5 py-3 text-right font-semibold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($tasks as $index => $task)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-semibold text-slate-400">
                                {{ $tasks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tasks->firstItem() + $index : $index + 1 }}
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800">{{ $task->title }}</p>
                                <p class="text-xs text-slate-400 truncate max-w-xs">{{ \Illuminate\Support\Str::limit($task->description, 60) }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <p class="text-xs font-semibold text-slate-700">
                                    {{ \Carbon\Carbon::parse($task->created_at ?? $task->uploaded_at)->translatedFormat('d M Y') }}
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($task->created_at ?? $task->uploaded_at)->format('H:i') }} WIB
                                </p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <p class="text-xs font-semibold text-slate-700">{{ $task->collection_date }}</p>
                                <p class="text-xs text-slate-400">{{ $task->collection_time }}</p>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $task->collection_place }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ $task->gdrive_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700 hover:bg-blue-100 transition-colors border border-blue-100">
                                    <i class="fa-brands fa-google-drive"></i> GDrive
                                </a>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Tombol Show / Detail -->
                                    <div class="group relative">
                                        <a href="{{ route('admin.practicum.show', $task->id) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Detail</span>
                                    </div>

                                    <!-- Tombol Edit -->
                                    <div class="group relative">
                                        <button type="button" @click="openEdit(@js($task))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>

                                    <!-- Tombol Hapus -->
                                    <div class="group relative">
                                        <button type="button" @click="confirmDelete(@js($task))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>
                                    <form id="delete-form-{{ $task->id }}" method="POST" action="{{ route('admin.practicum.destroy', $task->id) }}" class="hidden">
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

            @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $tasks->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $tasks->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT PRACTICUM TASK ============================= --}}
    <div
        x-cloak
        x-show="panelOpen"
        class="fixed inset-0 z-50 overflow-hidden"
        style="z-index: 9999;"
        @keydown.escape.window="closePanel()"
    >
        <!-- Backdrop Gelap Full Viewport -->
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

        <!-- Panel Drawer Full Tinggi Layar (top-0 h-full) -->
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
                        <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Tugas Praktikum' : 'Edit Tugas Praktikum'"></h2>
                        <p class="text-xs text-slate-400">Lengkapi detail instruksi penugasan dan tautan berkas resmi lab.</p>
                    </div>
                    <button type="button" @click="closePanel()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form id="practicumTaskForm" method="POST"
                    :action="panelMode === 'create' ? '{{ route('admin.practicum.store') }}' : '{{ url('admin/practicum') }}/' + editId"
                    class="flex-1 space-y-4 overflow-y-auto px-6 py-5">
                    @csrf

                    <template x-if="panelMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

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
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closePanel()" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="practicumTaskForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= DELETE CONFIRMATION MODAL (FULL SCREEN) ============================= --}}
    <div 
        x-cloak 
        x-show="deleteModalOpen" 
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9999;"
        @keydown.escape.window="deleteModalOpen = false"
    >
        <!-- Backdrop Overlay Full Viewport -->
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

        <!-- Modal Box Wrapper di Tengah Layar Penuh -->
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

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Tugas Praktikum?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.title"></span>? Penugasan ini akan dihapus permanen dan tidak dapat diakses lagi oleh praktikan.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <button 
                        type="button" 
                        @click="deleteModalOpen = false" 
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="submitDelete()" 
                        class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-semibold text-white shadow-soft hover:bg-red-700 transition-colors cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection