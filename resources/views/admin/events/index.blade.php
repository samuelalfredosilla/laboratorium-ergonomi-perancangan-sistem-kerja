@extends('layouts.admin')

@section('title', 'Laboratory Events')

@section('content')
<div
    x-data="{
        panelOpen: false,
        panelMode: 'create',
        editId: null,
        deleteModalOpen: false,
        deleteTarget: null,
        photoPreview: null,
        form: {
            title: '',
            description: '',
            gdrive_link: '',
            event_date: '',
            event_time: '',
            event_place: 'Lab EPSK'
        },
        resetForm() {
            this.form = {
                title: '',
                description: '',
                gdrive_link: '',
                event_date: '',
                event_time: '',
                event_place: 'Lab EPSK'
            };
            this.photoPreview = null;
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
        },
        openEdit(eventItem) {
            this.form = {
                title: eventItem.title,
                description: eventItem.description,
                gdrive_link: eventItem.gdrive_link,
                event_date: eventItem.event_date ? eventItem.event_date.split('T')[0] : '',
                event_time: eventItem.event_time,
                event_place: eventItem.event_place || 'Lab EPSK'
            };
            this.photoPreview = eventItem.photo ? '/storage/' + eventItem.photo : null;
            this.panelMode = 'edit';
            this.editId = eventItem.id;
            this.panelOpen = true;
        },
        closePanel() {
            this.panelOpen = false;
        },
        confirmDelete(eventItem) {
            this.deleteTarget = eventItem;
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
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Laboratory Events</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data kegiatan, agenda, dan acara laboratorium EPSK.</p>
        </div>
        <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Kegiatan
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

    <!-- Table Card with Multi-Filter Toolbar -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">

        <!-- Toolbar Filter Form -->
        <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">

            <!-- 1. Search Box -->
            <div class="relative w-full lg:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, topik, lokasi..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>

            <!-- 2. Dropdown Filters & Reset -->
            <div class="flex flex-wrap items-center gap-3">

                <!-- Filter Tempat/Lokasi Pelaksanaan -->
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
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru Dirilis</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Dirilis</option>
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div class="w-full sm:w-auto">
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" title="Filter Tanggal Pelaksanaan"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100 cursor-pointer">
                </div>

                <!-- Tombol Reset Filter -->
                @if (request()->hasAny(['search', 'place', 'date']) || (request()->filled('sort') && request('sort') !== 'latest'))
                    <a href="{{ route('admin.events.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if (count($events) === 0)
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-calendar-xmark text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Tidak ada data kegiatan</p>
                <p class="max-w-xs text-xs text-slate-400">
                    @if (request()->hasAny(['search', 'place', 'sort']))
                        Tidak ditemukan kegiatan yang sesuai dengan kriteria pencarian/filter Anda.
                    @else
                        Agenda dan acara laboratorium akan tampil di sini setelah ditambahkan.
                    @endif
                </p>
                @if (request()->hasAny(['search', 'place', 'sort']))
                    <a href="{{ route('admin.events.index') }}" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i> Tampilkan Semua Data
                    </a>
                @else
                    <button type="button" @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kegiatan Pertama
                    </button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold whitespace-nowrap" style="width: 50px;">No</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Judul Kegiatan</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Jadwal Pelaksanaan</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Lokasi</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Tautan Berkas</th>
                            <th class="px-5 py-3 text-right font-semibold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($events as $index => $eventItem)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-4 font-semibold text-slate-400">
                                {{ $events instanceof \Illuminate\Pagination\LengthAwarePaginator ? $events->firstItem() + $index : $index + 1 }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-800 leading-snug">{{ $eventItem->title }}</p>
                                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1 max-w-xs">{{ \Illuminate\Support\Str::limit($eventItem->description, 60) }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                        <i class="fa-regular fa-calendar text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-700 text-xs">{{ \Carbon\Carbon::parse($eventItem->event_date)->translatedFormat('d F Y') }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $eventItem->event_time }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200">
                                    <i class="fa-solid fa-location-dot text-slate-400"></i> {{ $eventItem->event_place }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($eventItem->gdrive_link)
                                    <a href="{{ $eventItem->gdrive_link }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-600 border border-blue-200 hover:bg-blue-100 hover:text-blue-700 transition-colors">
                                        <i class="fa-brands fa-google-drive"></i> GDrive
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak ada tautan</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Edit Action -->
                                    <div class="group relative">
                                        <button type="button" @click="openEdit(@js($eventItem))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>

                                    <!-- Delete Action -->
                                    <div class="group relative">
                                        <button type="button" @click="confirmDelete(@js($eventItem))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>
                                    <form id="delete-form-{{ $eventItem->id }}" method="POST" action="{{ route('admin.events.destroy', $eventItem->id) }}" class="hidden">
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

            <!-- Pagination -->
            @if ($events instanceof \Illuminate\Pagination\LengthAwarePaginator && $events->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $events->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT EVENT ============================= --}}
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
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Kegiatan Baru' : 'Edit Data Kegiatan'"></h2>
                        <p class="text-xs text-slate-400">Lengkapi informasi jadwal, lokasi, poster, dan detail acara.</p>
                    </div>
                    <button type="button" @click="closePanel()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form id="eventForm" method="POST" enctype="multipart/form-data"
                    :action="panelMode === 'create' ? '{{ route('admin.events.store') }}' : '{{ url('admin/events') }}/' + editId"
                    class="flex-1 space-y-4 overflow-y-auto px-6 py-5">
                    @csrf

                    <template x-if="panelMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Poster / Banner Kegiatan (Opsional)</label>
                        <div class="flex items-center gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50">
                                <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover">
                                <i x-show="!photoPreview" class="fa-regular fa-image text-2xl text-slate-300"></i>
                            </div>
                            <label class="flex-1 cursor-pointer rounded-lg border border-dashed border-slate-300 px-4 py-3 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                                <i class="fa-solid fa-cloud-arrow-up mb-1 block text-lg"></i>
                                Klik untuk unggah poster (JPG/PNG/WEBP, Maks 5MB)
                                <input type="file" name="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Kegiatan *</label>
                        <input type="text" name="title" x-model="form.title" required placeholder="Contoh: Pelatihan Software Ergonomi"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Deskripsi & Instruksi Kegiatan *</label>
                        <textarea name="description" x-model="form.description" rows="4" required placeholder="Tuliskan detail kegiatan, pemateri, atau instruksi bagi peserta..."
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tautan Berkas (Opsional)</label>
                        <div class="relative">
                            <i class="fa-brands fa-google-drive pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                            <input type="url" name="gdrive_link" x-model="form.gdrive_link" placeholder="https://drive.google.com/..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-9 pr-3 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-400">Tautan untuk materi, modul, atau formulir pendaftaran.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Pelaksanaan *</label>
                            <input type="date" name="event_date" x-model="form.event_date" required
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Waktu Acara *</label>
                            <input type="text" name="event_time" x-model="form.event_time" required placeholder="Contoh: 08:00 - Selesai WIB"
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Lokasi / Platform *</label>
                            <input type="text" name="event_place" x-model="form.event_place" required placeholder="Contoh: Lab. EPSK atau Zoom Meeting"
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>
                </form>

                <!-- Panel Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closePanel()" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="eventForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
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

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Kegiatan?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.title"></span>? Data kegiatan ini akan dihapus permanen.
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
