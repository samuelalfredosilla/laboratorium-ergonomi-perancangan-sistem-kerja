@extends('layouts.admin')

@section('title', 'Laboratory Equipment')

@section('content')
<div
    x-data="{
        panelOpen: false,
        panelMode: 'create',
        editId: null,
        deleteModalOpen: false,
        deleteTarget: null,
        categoryModalOpen: false,
        photoPreview: null,
        categoryList: {{ json_encode($categories ?? []) }},
        form: { name: '', specification: '', category: @js($categories[0] ?? ''), description: '', sort_order: 1 },

        resetForm() {
            this.form = { name: '', specification: '', category: this.categoryList[0] || '', description: '', sort_order: 1 };
            this.photoPreview = null;
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
        },
        openEdit(item) {
            if (item.category && !this.categoryList.includes(item.category)) {
                this.categoryList.unshift(item.category);
            }
            this.form = {
                name: item.name,
                specification: item.specification || '',
                category: item.category || '',
                description: item.description || '',
                sort_order: item.sort_order || 1
            };
            this.photoPreview = item.photo_url || (item.photo ? '/storage/' + item.photo : null);
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
        },
    }"
    class="space-y-6"
>
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Laboratory Equipment</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola inventaris perangkat keras dan alat ukur laboratorium.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="categoryModalOpen = true" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-600 shadow-soft hover:border-maroon-200 hover:text-maroon-600 transition-colors cursor-pointer">
                <i class="fa-solid fa-tags text-xs text-maroon-500"></i> Kelola Kategori
            </button>
            <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Alat
            </button>
        </div>
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
        <form method="GET" action="{{ route('admin.equipment.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative w-full lg:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat, spesifikasi..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select name="category" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-xs font-semibold text-slate-600 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100 cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories ?? [] as $cat)
                        <option value="{{ $cat }}" @selected(request('category') == $cat)>{{ $cat }}</option>
                    @endforeach
                </select>

                <select name="sort" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-xs font-semibold text-slate-600 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100 cursor-pointer">
                    <option value="order" {{ request('sort') != 'newest' ? 'selected' : '' }}>Sesuai Urutan</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                </select>

                <!-- Tombol Reset disamakan persis seperti halaman EPSIKERS -->
                @if (request()->filled('search') || request()->filled('category') || (request()->has('sort') && request('sort') !== 'order'))
                    <a href="{{ route('admin.equipment.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($equipments->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-microscope text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Data peralatan tidak ditemukan</p>
                <p class="max-w-xs text-xs text-slate-400">Pencarian atau filter yang Anda masukkan tidak cocok dengan data peralatan laboratorium apa pun.</p>
                @if (request()->anyFilled(['search', 'category']) || request()->filled('sort'))
                    <a href="{{ route('admin.equipment.index') }}" class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:border-maroon-200 hover:text-maroon-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset Filter
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Foto</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Nama & Spesifikasi</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Kategori</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Urutan</th>
                            <th class="px-5 py-3 text-right font-semibold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($equipments as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if($item->photo)
                                    <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}" class="h-12 w-16 rounded-md object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-12 w-16 items-center justify-center rounded-md bg-slate-100 text-slate-400"><i class="fa-solid fa-image"></i></div>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800 leading-snug">{{ $item->name }}</p>
                                <p class="text-xs text-slate-400">{{ $item->specification ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-600 border border-maroon-100">
                                    {{ $item->category ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500 font-mono text-xs">#{{ $item->sort_order }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <div class="group relative">
                                        <button type="button" @click="openEdit(@js($item))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>
                                    <div class="group relative">
                                        <button type="button" @click="confirmDelete(@js($item->only('id', 'name')))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>
                                    <form id="delete-form-{{ $item->id }}" method="POST" action="{{ route('admin.equipment.destroy', $item) }}" class="hidden">
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

            @if ($equipments instanceof \Illuminate\Pagination\LengthAwarePaginator && $equipments->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $equipments->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================= MODAL KELOLA KATEGORI ============================= --}}
    <div
        x-cloak
        x-show="categoryModalOpen"
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9999;"
        @keydown.escape.window="categoryModalOpen = false"
    >
        <div x-show="categoryModalOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="categoryModalOpen = false"></div>

        <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4">
            <div x-show="categoryModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-100">

                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-white">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-maroon-50 text-maroon-600">
                            <i class="fa-solid fa-tags text-sm"></i>
                        </span>
                        <h3 class="text-base font-bold text-slate-800">Kelola Kategori Alat</h3>
                    </div>
                    <button type="button" @click="categoryModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    <form method="POST" action="{{ route('admin.equipment.categories.store') }}" class="rounded-xl bg-slate-50 p-4 border border-slate-200">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tambah Kategori Baru</label>
                        <div class="flex gap-2">
                            <input type="text" name="category" required placeholder="Contoh: 1. Physiological Measurement"
                                class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <button type="submit" class="shrink-0 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700 shadow-sm transition-colors">
                                <i class="fa-solid fa-plus text-[10px] mr-1"></i> Tambah
                            </button>
                        </div>
                    </form>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Daftar Kategori Tersedia</label>
                        <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden">
                            @forelse ($categoryStats ?? [] as $item)
                                <div class="flex items-center justify-between p-3.5 bg-white hover:bg-slate-50/50" x-data="{ editing: false, newName: @js($item->name) }">
                                    <div x-show="!editing" class="flex items-center gap-3">
                                        <div>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $item->name }}</span>
                                            <span class="block text-[11px] text-slate-400">{{ $item->equipments_count ?? 0 }} alat terdaftar</span>
                                        </div>
                                    </div>

                                    <div x-show="!editing" class="flex items-center gap-1">
                                        <button type="button" @click="editing = true" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600">
                                            <i class="fa-regular fa-pen-to-square text-xs"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.equipment.categories.destroy') }}" onsubmit="return confirm('Hapus kategori {{ addslashes($item->name) }}? Alat yang menggunakan kategori ini akan menjadi Tanpa Kategori.')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="category" value="{{ $item->name }}">
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <form x-show="editing" method="POST" action="{{ route('admin.equipment.categories.rename') }}" class="flex w-full items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="old_category" value="{{ $item->name }}">
                                        <input type="text" name="new_category" x-model="newName" required class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-maroon-400 focus:outline-none">
                                        <button type="submit" class="rounded-lg bg-maroon-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-maroon-700">Simpan</button>
                                        <button type="button" @click="editing = false" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-500 hover:bg-slate-50">Batal</button>
                                    </form>
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400">Belum ada kategori yang ditambahkan.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 text-right">
                    <button @click="categoryModalOpen = false" type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: CREATE & EDIT EQUIPMENT ============================= --}}
    <div
        x-cloak
        x-show="panelOpen"
        class="fixed inset-0 overflow-hidden"
        style="z-index: 9999;"
        @keydown.escape.window="closePanel()"
    >
        <div x-show="panelOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="closePanel()"></div>

        <div class="fixed inset-y-0 right-0 top-0 h-full flex max-w-full pl-10">
            <div x-show="panelOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                class="w-screen max-w-xl h-full bg-white shadow-2xl flex flex-col justify-between">

                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-white">
                    <div>
                        <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Peralatan Baru' : 'Edit Data Peralatan'"></h2>
                        <p class="text-xs text-slate-400">Lengkapi nama alat, spesifikasi, dan kategorinya.</p>
                    </div>
                    <button type="button" @click="closePanel()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="equipmentForm" method="POST" :action="panelMode === 'create' ? '{{ route('admin.equipment.store') }}' : '{{ url('admin/equipment') }}/' + editId" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    @csrf
                    <template x-if="panelMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Gambar Alat</label>
                        <div class="flex items-center gap-4">
                            <div class="flex h-20 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50">
                                <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover">
                                <i x-show="!photoPreview" class="fa-solid fa-camera text-2xl text-slate-300"></i>
                            </div>
                            <label class="flex-1 cursor-pointer rounded-lg border border-dashed border-slate-300 px-4 py-3 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                                <i class="fa-solid fa-cloud-arrow-up mb-1 block text-lg"></i>
                                Klik untuk unggah gambar (Maks 5MB)
                                <input type="file" name="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Alat *</label>
                        <input type="text" name="name" x-model="form.name" required placeholder="Contoh: Digital Goniometer"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Spesifikasi / Merek</label>
                            <input type="text" name="specification" x-model="form.specification" placeholder="Contoh: Baseline 12-1056"
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Kategori Alat</label>
                            <select name="category" x-model="form.category"
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-maroon-700 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                <option value="">-- Tanpa Kategori --</option>
                                @foreach ($categories ?? [] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Deskripsi Singkat</label>
                        <textarea name="description" x-model="form.description" rows="3" placeholder="Fungsi atau keterangan alat..."
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" min="1"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        <p class="text-[11px] text-slate-400 mt-1">Angka terkecil akan tampil paling atas.</p>
                    </div>
                </form>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closePanel()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Batal</button>
                    <button type="submit" form="equipmentForm" class="inline-flex items-center gap-2 rounded-xl bg-maroon-600 px-5 py-2.5 text-xs font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> <span x-text="panelMode === 'create' ? 'Simpan Peralatan' : 'Simpan Perubahan'"></span>
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
        <div x-show="deleteModalOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="deleteModalOpen = false"></div>

        <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4">
            <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl border border-slate-100">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-8 ring-red-50/50">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Peralatan?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.name"></span>? Tindakan ini tidak dapat dibatalkan.
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
