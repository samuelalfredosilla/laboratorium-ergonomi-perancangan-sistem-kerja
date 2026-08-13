@extends('layouts.admin')

@section('title', 'Home Sliders')

@section('content')
<div
    x-data="{
        modalOpen: false,
        mode: 'create',
        editId: null,
        titleValue: '',
        activeValue: true,
        imagePreview: null,
        imageRequired: true,
        deleteModalOpen: false,
        deleteTarget: null,
        openCreate() {
            this.mode = 'create';
            this.editId = null;
            this.titleValue = '';
            this.activeValue = true;
            this.imagePreview = null;
            this.imageRequired = true;
            this.modalOpen = true;
        },
        openEdit(slider) {
            this.mode = 'edit';
            this.editId = slider.id;
            this.titleValue = slider.title || '';
            this.activeValue = slider.is_active;
            this.imagePreview = slider.image_url;
            this.imageRequired = false;
            this.modalOpen = true;
        },
        confirmDelete(slider) {
            this.deleteTarget = slider;
            this.deleteModalOpen = true;
        },
        submitDelete() {
            document.getElementById('delete-form-' + this.deleteTarget.id).submit();
        },
        previewImage(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => { this.imagePreview = ev.target.result; };
            reader.readAsDataURL(file);
        },
    }"
    class="space-y-6"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Home Sliders</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola banner slider di halaman utama. Seret kartu untuk mengubah urutan tampil.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Slider
        </button>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ $errors->first() }}
        </div>
    @endif

    @if ($sliders->isEmpty())
        <div class="flex flex-col items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-6 py-16 text-center shadow-soft">
            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                <i class="fa-solid fa-images text-2xl"></i>
            </span>
            <p class="text-sm font-semibold text-slate-600">Belum ada slider</p>
            <p class="max-w-xs text-xs text-slate-400">Tambahkan banner untuk ditampilkan di halaman utama situs.</p>
            <button @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Slider Pertama
            </button>
        </div>
    @else
        <div id="sliderGrid" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($sliders as $slider)
            <div class="slider-card group relative cursor-grab overflow-hidden rounded-xl border border-slate-200 bg-white shadow-soft active:cursor-grabbing" draggable="true" data-id="{{ $slider->id }}">
                <div class="absolute left-2 top-2 z-10 flex h-7 w-7 items-center justify-center rounded-md bg-slate-900/50 text-white opacity-0 backdrop-blur transition-opacity group-hover:opacity-100">
                    <i class="fa-solid fa-grip-vertical text-xs"></i>
                </div>
                <span class="absolute right-2 top-2 z-10 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $slider->is_active ? 'bg-emerald-500 text-white' : 'bg-slate-500 text-white' }}">
                    {{ $slider->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                <div class="aspect-video w-full overflow-hidden bg-slate-100">
                    <img src="{{ \Illuminate\Support\Str::startsWith($slider->image_path, 'http') ? $slider->image_path : asset('storage/' . $slider->image_path) }}" alt="{{ $slider->title }}" class="h-full w-full object-cover">
                </div>
                <div class="flex items-center justify-between gap-2 p-3.5">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-700">{{ $slider->title ?: 'Tanpa judul' }}</p>
                        <p class="text-[11px] text-slate-400">Urutan #{{ $slider->sort_order }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-1">
                        <button @click="openEdit({{ Illuminate\Support\Js::from([
                            'id' => $slider->id,
                            'title' => $slider->title,
                            'is_active' => $slider->is_active,
                            'image_url' => \Illuminate\Support\Str::startsWith($slider->image_path, 'http') ? $slider->image_path : asset('storage/' . $slider->image_path),
                        ]) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600">
                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                        </button>
                        <button @click="confirmDelete({{ Illuminate\Support\Js::from(['id' => $slider->id, 'title' => $slider->title ?: 'Slider ini']) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
                            <i class="fa-regular fa-trash-can text-sm"></i>
                        </button>
                        <form id="delete-form-{{ $slider->id }}" method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ============================= CREATE / EDIT MODAL ============================= --}}
    <div x-cloak x-show="modalOpen" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center p-4" @keydown.escape.window="modalOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="modalOpen = false"></div>
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md rounded-xl bg-white shadow-2xl">
            <form method="POST" :action="mode === 'create' ? '{{ route('admin.sliders.store') }}' : '{{ url('admin/sliders') }}/' + editId" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-bold text-slate-800" x-text="mode === 'create' ? 'Tambah Slider' : 'Edit Slider'"></h2>
                    <button type="button" @click="modalOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Gambar Banner (rasio 16:9)</label>
                        <label class="flex aspect-video cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-lg border-2 border-dashed border-slate-300 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="flex flex-col items-center gap-1.5 p-4">
                                    <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                    <span>Klik atau seret gambar ke sini (JPG/PNG)</span>
                                </div>
                            </template>
                            <input type="file" name="image" accept="image/*" class="hidden" @change="previewImage($event)" :required="imageRequired">
                        </label>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul (opsional)</label>
                        <input type="text" name="title" x-model="titleValue" placeholder="Contoh: Kegiatan Laboratorium 2026"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3.5 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Status</p>
                            <p class="text-xs text-slate-400">Tampilkan di halaman utama</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_active" value="1" x-model="activeValue" class="peer sr-only">
                            <div class="h-5 w-9 rounded-full bg-slate-300 transition-colors peer-checked:bg-emerald-500"></div>
                            <div class="absolute left-1 top-1 h-3.5 w-3.5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-5 py-4">
                    <button type="button" @click="modalOpen = false" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan
                    </button>
                </div>
            </form>
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
            <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Slider?</h3>
            <p class="mt-1.5 text-sm text-slate-500">
                Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.title"></span>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex gap-3">
                <button @click="deleteModalOpen = false" class="flex-1 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button @click="submitDelete()" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var grid = document.getElementById('sliderGrid');
        if (!grid) return;
        var dragEl = null;

        grid.querySelectorAll('.slider-card').forEach(function (card) {
            card.addEventListener('dragstart', function () {
                dragEl = card;
                card.classList.add('opacity-40');
            });
            card.addEventListener('dragend', function () {
                card.classList.remove('opacity-40');
                dragEl = null;
                persistOrder();
            });
            card.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (!dragEl || dragEl === card) return;
                var rect = card.getBoundingClientRect();
                var after = (e.clientY - rect.top) / rect.height > 0.5;
                grid.insertBefore(dragEl, after ? card.nextSibling : card);
            });
        });

        function persistOrder() {
            var order = Array.prototype.map.call(grid.querySelectorAll('.slider-card'), function (c) { return c.dataset.id; });
            fetch('{{ route('admin.sliders.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ order: order }),
            }).then(function (res) {
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Urutan slider berhasil disimpan.' } }));
                }
            });
        }
    });
</script>
@endpush
@endsection
