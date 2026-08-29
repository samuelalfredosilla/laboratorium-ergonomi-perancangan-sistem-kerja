@extends('layouts.admin')

@section('title', 'Lecturers & Staff')

@section('content')
<div
    x-data="{
        panelOpen: false,
        panelMode: 'create',
        editId: null,
        activeTab: 'profile',
        deleteModalOpen: false,
        deleteTarget: null,
        photoPreview: null,
        form: { name: '', nip: '', role: 'Lecturer of Interest', expertise: '', email: '', scholar_link: '', linkedin_link: '', sort_order: 1 },
        educations: [ { degree: 'S1', institution: '', year_range: '' } ],
        researches: [ { title: '', year: '' } ],
        services: [ { title: '', year: '' } ],
        resetForm() {
            this.form = { name: '', nip: '', role: 'Lecturer of Interest', expertise: '', email: '', scholar_link: '', linkedin_link: '', sort_order: 1 };
            this.educations = [ { degree: 'S1', institution: '', year_range: '' } ];
            this.researches = [ { title: '', year: '' } ];
            this.services = [ { title: '', year: '' } ];
            this.photoPreview = null;
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.activeTab = 'profile';
            this.panelOpen = true;
        },
        openEdit(lecturer) {
            this.form = { 
                name: lecturer.name, 
                nip: lecturer.nip || '', 
                role: lecturer.role || 'Lecturer of Interest', 
                expertise: lecturer.expertise || '', 
                email: lecturer.email || '', 
                scholar_link: lecturer.scholar_link || '', 
                linkedin_link: lecturer.linkedin_link || '',
                sort_order: lecturer.sort_order || 1
            };
            this.educations = lecturer.educations && lecturer.educations.length ? lecturer.educations : [ { degree: 'S1', institution: '', year_range: '' } ];
            this.researches = lecturer.researches && lecturer.researches.length ? lecturer.researches : [ { title: '', year: '' } ];
            this.services = lecturer.community_services && lecturer.community_services.length ? lecturer.community_services : [ { title: '', year: '' } ];
            this.photoPreview = lecturer.photo_url || null;
            this.panelMode = 'edit';
            this.editId = lecturer.id;
            this.activeTab = 'profile';
            this.panelOpen = true;
        },
        closePanel() {
            this.panelOpen = false;
        },
        confirmDelete(lecturer) {
            this.deleteTarget = lecturer;
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
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Lecturers &amp; Staff</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data dosen pengampu, kepala lab, urutan posisi, dan anggota laboratorium.</p>
        </div>
        <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Dosen
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

    <!-- Table Card with Filter Toolbar -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">

        <!-- Toolbar Filter Form -->
        <form method="GET" action="{{ route('admin.lecturers.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative w-full lg:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, atau keahlian..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <select name="role" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-xs font-semibold text-slate-600 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Peran</option>
                    <option value="chief" @selected(request('role') === 'chief')>Kepala Lab</option>
                    <option value="member" @selected(request('role') === 'member')>Dosen Anggota</option>
                </select>

                @if (request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.lecturers.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if (count($lecturers) === 0)
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-user-graduate text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Belum ada data dosen</p>
                <p class="max-w-xs text-xs text-slate-400">
                    @if (request()->anyFilled(['search', 'role']))
                        Tidak ditemukan data dosen dengan kata kunci pencarian ini.
                    @else
                        Data dosen dan staf pengampu laboratorium akan tampil di sini setelah ditambahkan.
                    @endif
                </p>
                @if (request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.lecturers.index') }}" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                        Lihat Semua Data
                    </a>
                @else
                    <button type="button" @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Dosen Pertama
                    </button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold whitespace-nowrap text-center" style="width: 60px;">Urutan</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Foto</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Nama Lengkap &amp; Gelar</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">NIP</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Peran</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Keahlian Utama</th>
                            <th class="px-5 py-3 text-right font-semibold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($lecturers as $lecturer)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Badge Nomor Urutan -->
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-700 border border-slate-200">
                                    {{ $lecturer->sort_order ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <img src="{{ $lecturer->photo_url }}" alt="{{ $lecturer->name }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200">
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800 leading-snug">{{ $lecturer->name }}</p>
                                <p class="text-xs text-slate-400">{{ $lecturer->email }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500 text-xs">{{ $lecturer->nip ?? '—' }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if (Illuminate\Support\Str::contains($lecturer->role, ['Head', 'Kepala', 'Chief'], true))
                                    <span class="inline-flex items-center rounded-full bg-maroon-600 px-2.5 py-1 text-[11px] font-semibold text-white">{{ $lecturer->role }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $lecturer->role }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 max-w-xs truncate text-slate-500 text-xs">{{ $lecturer->expertise ?? '—' }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Detail Action -->
                                    <div class="group relative">
                                        <a href="{{ route('admin.lecturers.show', $lecturer) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Detail</span>
                                    </div>

                                    <!-- Edit Action -->
                                    <div class="group relative">
                                        <button type="button" @click="openEdit(@js($lecturer))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>

                                    <!-- Delete Action -->
                                    <div class="group relative">
                                        <button type="button" @click="confirmDelete(@js($lecturer))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>
                                    <form id="delete-form-{{ $lecturer->id }}" method="POST" action="{{ route('admin.lecturers.destroy', $lecturer) }}" class="hidden">
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
            @if ($lecturers instanceof \Illuminate\Pagination\LengthAwarePaginator && $lecturers->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $lecturers->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT LECTURER (FULL HEIGHT) ============================= --}}
    <div
        x-cloak
        x-show="panelOpen"
        class="fixed inset-0 overflow-hidden"
        style="z-index: 9999;"
        @keydown.escape.window="closePanel()"
    >
        <!-- Backdrop Overlay Full Viewport -->
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

        <!-- Slide Panel Drawer Full Height (top-0 h-full) -->
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
                        <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Dosen Baru' : 'Edit Data Dosen'"></h2>
                        <p class="text-xs text-slate-400">Lengkapi informasi profil, posisi urutan, dan rekam jejak dosen.</p>
                    </div>
                    <button type="button" @click="closePanel()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Tabs Navigation -->
                <div class="flex gap-1 overflow-x-auto border-b border-slate-200 px-6 pt-3 bg-white">
                    <button type="button" @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">1. Profil</button>
                    <button type="button" @click="activeTab = 'education'" :class="activeTab === 'education' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">2. Pendidikan</button>
                    <button type="button" @click="activeTab = 'research'" :class="activeTab === 'research' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">3. Penelitian</button>
                    <button type="button" @click="activeTab = 'service'" :class="activeTab === 'service' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">4. Pengabdian</button>
                </div>

                <!-- Form Content -->
                <form id="lecturerForm" method="POST"
                    :action="panelMode === 'create' ? '{{ route('admin.lecturers.store') }}' : '{{ url('admin/lecturers') }}/' + editId"
                    enctype="multipart/form-data"
                    class="flex-1 overflow-y-auto px-6 py-5">
                    @csrf

                    <template x-if="panelMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- TAB 1: Profile Info -->
                    <div x-show="activeTab === 'profile'" class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Foto Profil</label>
                            <div class="flex items-center gap-4">
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-dashed border-slate-300 bg-slate-50">
                                    <img x-show="photoPreview" :src="photoPreview" class="h-full w-full object-cover">
                                    <i x-show="!photoPreview" class="fa-solid fa-user text-2xl text-slate-300"></i>
                                </div>
                                <label class="flex-1 cursor-pointer rounded-lg border border-dashed border-slate-300 px-4 py-3 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                                    <i class="fa-solid fa-cloud-arrow-up mb-1 block text-lg"></i>
                                    Klik untuk mengganti foto (JPG/PNG, Maks 5MB)
                                    <input type="file" name="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Lengkap &amp; Gelar *</label>
                                <input type="text" name="name" x-model="form.name" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">NIP</label>
                                <input type="text" name="nip" x-model="form.nip" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Peran *</label>
                                <select name="role" x-model="form.role" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                    <option value="Laboratory Chief">Laboratory Chief</option>
                                    <option value="Lecturer of Interest">Lecturer of Interest</option>
                                </select>
                            </div>

                            <!-- Input Urutan Tampil (Sort Order) -->
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Urutan Tampil (Posisi)</label>
                                <input type="number" name="sort_order" x-model="form.sort_order" min="1" placeholder="1, 2, 3..." class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                <p class="mt-1 text-[11px] text-slate-400">Angka lebih kecil (contoh: 1) akan ditampilkan paling atas (misal Kepala Lab).</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Keahlian Utama</label>
                                <input type="text" name="expertise" x-model="form.expertise" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Email</label>
                                <input type="email" name="email" x-model="form.email" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Google Scholar</label>
                                <input type="url" name="scholar_link" x-model="form.scholar_link" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-600">LinkedIn</label>
                                <input type="url" name="linkedin_link" x-model="form.linkedin_link" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Pendidikan Repeater -->
                    <div x-show="activeTab === 'education'" class="space-y-3">
                        <template x-for="(edu, idx) in educations" :key="idx">
                            <div class="relative grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-3">
                                <button type="button" @click="educations.splice(idx, 1)" x-show="educations.length > 1" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Jenjang</label>
                                    <select x-model="edu.degree" :name="'educations[' + idx + '][degree]'" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Institusi</label>
                                    <input type="text" x-model="edu.institution" :name="'educations[' + idx + '][institution]'" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Tahun</label>
                                    <input type="text" x-model="edu.year_range" :name="'educations[' + idx + '][year_range]'" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="educations.push({ degree: 'S1', institution: '', year_range: '' })" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-maroon-300 px-3.5 py-2 text-xs font-semibold text-maroon-600 hover:bg-maroon-50">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Riwayat Pendidikan
                        </button>
                    </div>

                    <!-- TAB 3: Penelitian Repeater -->
                    <div x-show="activeTab === 'research'" class="space-y-3">
                        <template x-for="(item, idx) in researches" :key="idx">
                            <div class="relative grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-4">
                                <button type="button" @click="researches.splice(idx, 1)" x-show="researches.length > 1" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                                <div class="sm:col-span-3">
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Judul Penelitian</label>
                                    <textarea x-model="item.title" :name="'researches[' + idx + '][title]'" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Tahun</label>
                                    <input type="text" x-model="item.year" :name="'researches[' + idx + '][year]'" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="researches.push({ title: '', year: '' })" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-maroon-300 px-3.5 py-2 text-xs font-semibold text-maroon-600 hover:bg-maroon-50">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Judul Penelitian
                        </button>
                    </div>

                    <!-- TAB 4: Pengabdian Repeater -->
                    <div x-show="activeTab === 'service'" class="space-y-3">
                        <template x-for="(item, idx) in services" :key="idx">
                            <div class="relative grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-4">
                                <button type="button" @click="services.splice(idx, 1)" x-show="services.length > 1" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                                <div class="sm:col-span-3">
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Judul Pengabdian</label>
                                    <textarea x-model="item.title" :name="'services[' + idx + '][title]'" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Tahun</label>
                                    <input type="text" x-model="item.year" :name="'services[' + idx + '][year]'" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="services.push({ title: '', year: '' })" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-maroon-300 px-3.5 py-2 text-xs font-semibold text-maroon-600 hover:bg-maroon-50">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Pengabdian Masyarakat
                        </button>
                    </div>
                </form>

                <!-- Panel Footer -->
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closePanel()" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="lecturerForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
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

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Data Dosen?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.name"></span>? Seluruh data riwayat pendidikan, penelitian, dan pengabdian terkait akan dihapus permanen.
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