@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div
    x-data="{
        modalOpen: false,
        mode: 'create',
        editId: null,
        form: { name: '' },
        deleteModalOpen: false,
        deleteTarget: null,
        openCreate() {
            this.mode = 'create';
            this.editId = null;
            this.form = { name: '' };
            this.modalOpen = true;
        },
        openEdit(category) {
            this.mode = 'edit';
            this.editId = category.id;
            this.form = { name: category.name };
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },
        confirmDelete(category) {
            this.deleteTarget = category;
            this.deleteModalOpen = true;
        },
        submitDelete() {
            document.getElementById('delete-form-' + this.deleteTarget.id).submit();
        },
    }"
    class="space-y-6"
>
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Categories</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola kategori untuk mengelompokkan berita &amp; artikel.</p>
        </div>
        <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Kategori
        </button>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ $errors->first() }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
        @if ($categories->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-tags text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Belum ada kategori</p>
                <p class="max-w-xs text-xs text-slate-400">Buat kategori untuk mulai mengelompokkan berita &amp; artikel laboratorium.</p>
                <button type="button" @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kategori Pertama
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Nama Kategori</th>
                            <th class="px-5 py-3 font-semibold">Slug</th>
                            <th class="px-5 py-3 font-semibold">Jumlah Berita</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($categories as $category)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $category->name }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-400">{{ $category->slug }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-600 border border-maroon-100">
                                    {{ $category->news_count }} berita
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" @click="openEdit(@js($category->only('id', 'name')))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                        <i class="fa-regular fa-pen-to-square text-sm"></i>
                                    </button>
                                    <button type="button" @click="confirmDelete(@js($category->only('id', 'name')))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
                                    <form id="delete-form-{{ $category->id }}" method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="hidden">
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
        @endif
    </div>

    {{-- ============================= CREATE / EDIT MODAL (FULL SCREEN) ============================= --}}
    <div 
        x-cloak 
        x-show="modalOpen" 
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9999;"
        @keydown.escape.window="closeModal()"
    >
        <!-- Backdrop Full Viewport -->
        <div 
            x-show="modalOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" 
            @click="closeModal()"
        ></div>

        <!-- Modal Box Wrapper -->
        <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4">
            <div 
                x-show="modalOpen" 
                x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="opacity-0 scale-95" 
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-slate-100"
            >
                <form method="POST" :action="mode === 'create' ? '{{ route('admin.categories.store') }}' : '{{ url('admin/categories') }}/' + editId">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <h2 class="text-base font-bold text-slate-800" x-text="mode === 'create' ? 'Tambah Kategori' : 'Edit Kategori'"></h2>
                        <button type="button" @click="closeModal()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="px-6 py-5">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Kategori *</label>
                        <input type="text" name="name" x-model="form.name" required placeholder="Contoh: Riset, Pengabdian, Workshop"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4">
                        <button type="button" @click="closeModal()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-maroon-600 px-5 py-2.5 text-xs font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
                            <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================= DELETE CONFIRM MODAL (FULL SCREEN) ============================= --}}
    <div 
        x-cloak 
        x-show="deleteModalOpen" 
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9999;"
        @keydown.escape.window="deleteModalOpen = false"
    >
        <!-- Backdrop Full Viewport -->
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

        <!-- Modal Box Wrapper di Titik Tengah Layar Penuh -->
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

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Kategori?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.name"></span>? Berita di dalam kategori ini tidak akan terhapus, melainkan diubah menjadi <strong>Tanpa Kategori (None)</strong>.
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