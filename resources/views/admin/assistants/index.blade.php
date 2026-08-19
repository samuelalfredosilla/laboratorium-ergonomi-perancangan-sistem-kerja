@extends('layouts.admin')

@section('title', 'EPSIKERS & Assistants')

@section('content')
<div
    x-data="{
        panelOpen: false,
        panelMode: 'create',
        editId: null,
        deleteModalOpen: false,
        deleteTarget: null,
        periodModalOpen: false,
        photoPreview: null,
        periodList: {{ json_encode($periods) }},
        form: { name: '', nim: '', division: '', period: @js($periods[0] ?? ''), email: '', sort_order: 1 },
        
        resetForm() {
            this.form = { name: '', nim: '', division: '', period: this.periodList[0] || '', email: '', sort_order: 1 };
            this.photoPreview = null;
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
        },
        openEdit(assistant) {
            if (assistant.period && !this.periodList.includes(assistant.period)) {
                this.periodList.unshift(assistant.period);
            }
            this.form = {
                name: assistant.name,
                nim: assistant.nim || '',
                division: assistant.division || '',
                period: assistant.period || '',
                email: assistant.email || '',
                sort_order: assistant.sort_order || 1
            };
            this.photoPreview = assistant.photo_url || null;
            this.panelMode = 'edit';
            this.editId = assistant.id;
            this.panelOpen = true;
        },
        confirmDelete(assistant) {
            this.deleteTarget = assistant;
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
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">EPSIKERS &amp; Assistants</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data asisten laboratorium EPSK per periode kepengurusan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="periodModalOpen = true" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-600 shadow-soft hover:border-maroon-200 hover:text-maroon-600">
                <i class="fa-solid fa-calendar-days text-xs text-maroon-500"></i> Kelola Periode
            </button>
            <button @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Asisten
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
        <form method="GET" action="{{ route('admin.assistants.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, atau email..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select name="period" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Periode</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period }}" @selected(request('period') == $period)>Periode {{ $period }}</option>
                    @endforeach
                </select>
                <select name="division" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division }}" @selected(request('division') == $division)>{{ $division }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>
                @if (request()->anyFilled(['search', 'period', 'division']))
                    <a href="{{ route('admin.assistants.index') }}" class="text-xs font-semibold text-slate-400 hover:text-maroon-600">Reset</a>
                @endif
            </div>
        </form>

        @if ($assistants->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-user-check text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Belum ada data asisten</p>
                <p class="max-w-xs text-xs text-slate-400">Data asisten laboratorium EPSK akan tampil di sini setelah ditambahkan.</p>
                <button @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Asisten Pertama
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Foto</th>
                            <th class="px-5 py-3 font-semibold">Nama Lengkap</th>
                            <th class="px-5 py-3 font-semibold">NIM</th>
                            <th class="px-5 py-3 font-semibold">Divisi</th>
                            <th class="px-5 py-3 font-semibold">Periode</th>
                            <th class="px-5 py-3 font-semibold">Urutan</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($assistants as $assistant)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <img src="{{ $assistant->photo_url }}" alt="{{ $assistant->name }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200">
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-700">{{ $assistant->name }}</p>
                                <p class="text-xs text-slate-400">{{ $assistant->email ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $assistant->nim ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-600">
                                    {{ $assistant->division ?? 'Anggota' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-600 font-medium">
                                {{ $assistant->period ?? '—' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500 font-mono text-xs">#{{ $assistant->sort_order }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <div class="group relative">
                                        <a href="{{ route('admin.assistants.show', $assistant) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Detail</span>
                                    </div>
                                    <div class="group relative">
                                        <button @click="openEdit({{ Illuminate\Support\Js::from($assistant) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>
                                    <div class="group relative">
                                        <button @click="confirmDelete({{ Illuminate\Support\Js::from($assistant->only('id', 'name')) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>
                                    <form id="delete-form-{{ $assistant->id }}" method="POST" action="{{ route('admin.assistants.destroy', $assistant) }}" class="hidden">
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

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $assistants->links() }}
            </div>
        @endif
    </div>

    {{-- ============================= MODAL KELOLA PERIODE ============================= --}}
    <div x-cloak x-show="periodModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="periodModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="periodModalOpen = false"></div>

        <div x-show="periodModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md rounded-xl bg-white shadow-2xl overflow-hidden">
            
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-maroon-50 text-maroon-600">
                        <i class="fa-solid fa-calendar-days text-sm"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-800">Kelola Periode Asisten</h3>
                </div>
                <button @click="periodModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                <!-- FORM TAMBAH PERIODE BARU -->
                <form method="POST" action="{{ route('admin.assistants.periods.store') }}" class="rounded-lg bg-slate-50 p-3.5 border border-slate-200">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tambah Periode Baru</label>
                    <div class="flex gap-2">
                        <input type="text" name="period" required placeholder="Contoh: 2026/2027" pattern="[0-9]{4}/[0-9]{4}" title="Format: YYYY/YYYY (Contoh: 2026/2027)"
                            class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        <button type="submit" class="shrink-0 rounded-lg bg-maroon-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-maroon-700 shadow-sm">
                            <i class="fa-solid fa-plus text-[10px] mr-1"></i> Tambah
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Gunakan format tahun akademik, contoh: <span class="font-mono text-slate-600">2026/2027</span>.</p>
                </form>

                <!-- DAFTAR PERIODE & TOGGLE STATUS -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Daftar Periode &amp; Status Tampil Publik</label>
                    <div class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                        @foreach ($periodStats as $item)
                            <div class="flex items-center justify-between p-3" x-data="{ editing: false, newName: @js($item->name) }">
                                <!-- Mode Normal -->
                                <div x-show="!editing" class="flex items-center gap-2.5">
                                    <!-- Switch Toggle Aktif / Nonaktif -->
                                    <form method="POST" action="{{ route('admin.assistants.periods.toggle', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="{{ $item->is_active ? 'Periode Aktif (Tampil di Website)' : 'Klik untuk jadikan periode aktif' }}"
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $item->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                            <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $item->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>

                                    <div>
                                        <span class="font-semibold text-slate-700 text-xs">Periode {{ $item->name }}</span>
                                        @if($item->is_active)
                                            <span class="ml-1 rounded bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-600">Aktif Publik</span>
                                        @endif
                                        <span class="block text-[11px] text-slate-400">
                                            {{ $item->assistants_count }} asisten
                                        </span>
                                    </div>
                                </div>

                                <div x-show="!editing" class="flex items-center gap-1">
                                    <button type="button" @click="editing = true" class="flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-maroon-50 hover:text-maroon-600" title="Ubah Nama Periode">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    <form method="POST" action="{{ route('admin.assistants.periods.destroy') }}" onsubmit="return confirm('Hapus periode {{ addslashes($item->name) }}? Asisten di periode ini tidak akan dihapus, kolom periodenya saja yang akan dikosongkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="period" value="{{ $item->name }}">
                                        <button type="submit" class="flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus Periode Ini">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Mode Edit Nama Periode -->
                                <form x-show="editing" method="POST" action="{{ route('admin.assistants.periods.rename') }}" class="flex w-full items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="old_period" value="{{ $item->name }}">
                                    <input type="text" name="new_period" x-model="newName" required pattern="[0-9]{4}/[0-9]{4}" class="flex-1 rounded border border-slate-300 px-2.5 py-1 text-xs focus:border-maroon-400 focus:outline-none">
                                    <button type="submit" class="rounded bg-maroon-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-maroon-700">Simpan</button>
                                    <button type="button" @click="editing = false" class="rounded border border-slate-200 px-2 py-1 text-xs text-slate-500 hover:bg-slate-50">Batal</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-6 py-3 text-right">
                <button @click="periodModalOpen = false" type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT ASSISTANT ============================= --}}
    <div x-cloak x-show="panelOpen" x-transition.opacity class="fixed inset-0 z-40" @keydown.escape.window="panelOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="panelOpen = false"></div>

        <div
            x-show="panelOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 flex h-full w-full max-w-lg flex-col bg-white shadow-2xl"
        >
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Asisten Baru' : 'Edit Data Asisten'"></h2>
                    <p class="text-xs text-slate-400">Lengkapi data profil, periode, dan divisi asisten.</p>
                </div>
                <button @click="panelOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="assistantForm" method="POST" enctype="multipart/form-data"
                :action="panelMode === 'create' ? '{{ route('admin.assistants.store') }}' : '{{ url('admin/assistants') }}/' + editId"
                class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="panelMode === 'edit' ? 'PUT' : 'POST'">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Foto Asisten</label>
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-dashed border-slate-300 bg-slate-50">
                            <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover">
                            <i x-show="!photoPreview" class="fa-solid fa-user-check text-2xl text-slate-300"></i>
                        </div>
                        <label class="flex-1 cursor-pointer rounded-lg border border-dashed border-slate-300 px-4 py-3 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                            <i class="fa-solid fa-cloud-arrow-up mb-1 block text-lg"></i>
                            Klik untuk unggah foto (Maks 5MB)
                            <input type="file" name="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Lengkap *</label>
                    <input type="text" name="name" x-model="form.name" required placeholder="Contoh: Budi Santoso"
                        class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">NIM</label>
                        <input type="text" name="nim" x-model="form.nim" placeholder="Contoh: 220481100012"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Periode</label>
                        <select name="period" x-model="form.period"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-maroon-700 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <option value="">-- Tanpa Periode --</option>
                            <template x-for="p in periodList" :key="p">
                                <option :value="p" x-text="'Periode ' + p"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Divisi / Jabatan</label>
                        <input type="text" name="division" x-model="form.division" placeholder="Contoh: Divisi Praktikum"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Urutan Tampil</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" min="1" max="100"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Email Mahasiswa</label>
                    <input type="email" name="email" x-model="form.email" placeholder="contoh@student.trunojoyo.ac.id"
                        class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>
            </form>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="panelOpen = false" type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" form="assistantForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Asisten
                </button>
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div x-cloak x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="deleteModalOpen = false"></div>
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-sm rounded-xl bg-white p-6 text-center shadow-2xl">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </span>
            <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Asisten?</h3>
            <p class="mt-1.5 text-sm text-slate-500">
                Anda yakin ingin menghapus asisten <span class="font-semibold text-slate-700" x-text="deleteTarget?.name"></span>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex gap-3">
                <button @click="deleteModalOpen = false" class="flex-1 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button @click="submitDelete()" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection