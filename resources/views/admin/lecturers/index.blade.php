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
        form: { name: '', nip: '', role: 'Lecturer of Interest', expertise: '', email: '', scholar_link: '', linkedin_link: '' },
        educations: [ { degree: 'S1', institution: '', year_range: '' } ],
        researches: [ { title: '', year: '' } ],
        services: [ { title: '', year: '' } ],
        resetForm() {
            this.form = { name: '', nip: '', role: 'Lecturer of Interest', expertise: '', email: '', scholar_link: '', linkedin_link: '' };
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
            this.form = { name: lecturer.name, nip: lecturer.nip, role: lecturer.role, expertise: lecturer.expertise, email: lecturer.email, scholar_link: lecturer.scholar_link || '', linkedin_link: lecturer.linkedin_link || '' };
            this.educations = lecturer.educations && lecturer.educations.length ? lecturer.educations : [ { degree: 'S1', institution: '', year_range: '' } ];
            this.researches = lecturer.researches && lecturer.researches.length ? lecturer.researches : [ { title: '', year: '' } ];
            this.services = lecturer.community_services && lecturer.community_services.length ? lecturer.community_services : [ { title: '', year: '' } ];
            this.photoPreview = lecturer.photo_url || null;
            this.panelMode = 'edit';
            this.editId = lecturer.id;
            this.activeTab = 'profile';
            this.panelOpen = true;
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
            <p class="mt-1 text-sm text-slate-500">Kelola data dosen pengampu, kepala lab, dan anggota Laboratorium EPSK.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
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

    <!-- Table Card -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.lecturers.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            <div class="flex items-center gap-2">
                <select name="role" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Peran</option>
                    <option value="chief" @selected(request('role') === 'chief')>Kepala Lab</option>
                    <option value="member" @selected(request('role') === 'member')>Dosen Anggota</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>
                @if (request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.lecturers.index') }}" class="text-xs font-semibold text-slate-400 hover:text-maroon-600">Reset</a>
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
                <p class="max-w-xs text-xs text-slate-400">Data dosen dan staf pengampu laboratorium akan tampil di sini setelah ditambahkan.</p>
                <button @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Dosen Pertama
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Foto</th>
                            <th class="px-5 py-3 font-semibold">Nama Lengkap &amp; Gelar</th>
                            <th class="px-5 py-3 font-semibold">NIP</th>
                            <th class="px-5 py-3 font-semibold">Peran</th>
                            <th class="px-5 py-3 font-semibold">Keahlian Utama</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($lecturers as $lecturer)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <img src="{{ $lecturer->photo_url }}" alt="{{ $lecturer->name }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200">
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-700">{{ $lecturer->name }}</p>
                                <p class="text-xs text-slate-400">{{ $lecturer->email }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $lecturer->nip ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if (Illuminate\Support\Str::contains($lecturer->role, ['Head', 'Kepala', 'Chief'], true))
                                    <span class="inline-flex items-center rounded-full bg-maroon-600 px-2.5 py-1 text-[11px] font-semibold text-white">{{ $lecturer->role }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">{{ $lecturer->role }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 max-w-55 truncate text-slate-500">{{ $lecturer->expertise }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <div class="group relative">
                                        <a href="{{ route('admin.lecturers.show', $lecturer) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Detail</span>
                                    </div>
                                    <div class="group relative">
                                        <button @click="openEdit({{ Illuminate\Support\Js::from($lecturer) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>
                                    <div class="group relative">
                                        <button @click="confirmDelete({{ Illuminate\Support\Js::from($lecturer) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
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
            <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row">
                <p class="text-xs text-slate-400">Menampilkan <span class="font-semibold text-slate-600">1–{{ count($lecturers) }}</span> dari <span class="font-semibold text-slate-600">{{ count($lecturers) }}</span> dosen</p>
                <div class="flex items-center gap-1">
                    <button class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:border-maroon-200 hover:text-maroon-600" disabled>
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button class="flex h-8 w-8 items-center justify-center rounded-lg bg-maroon-600 text-xs font-semibold text-white">1</button>
                    <button class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-xs font-semibold text-slate-500 hover:border-maroon-200 hover:text-maroon-600">2</button>
                    <button class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:border-maroon-200 hover:text-maroon-600">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT LECTURER ============================= --}}
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
            class="absolute right-0 top-0 flex h-full w-full max-w-xl flex-col bg-white shadow-2xl"
        >
            <!-- Panel Header -->
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Dosen Baru' : 'Edit Data Dosen'"></h2>
                    <p class="text-xs text-slate-400">Lengkapi informasi profil, pendidikan, dan rekam jejak dosen.</p>
                </div>
                <button @click="panelOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex gap-1 overflow-x-auto border-b border-slate-200 px-6 pt-3">
                <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">1. Profil</button>
                <button @click="activeTab = 'education'" :class="activeTab === 'education' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">2. Pendidikan</button>
                <button @click="activeTab = 'research'" :class="activeTab === 'research' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">3. Penelitian</button>
                <button @click="activeTab = 'service'" :class="activeTab === 'service' ? 'border-maroon-600 text-maroon-600' : 'border-transparent text-slate-400 hover:text-slate-600'" class="whitespace-nowrap border-b-2 px-3 pb-3 text-sm font-semibold">4. Pengabdian</button>
            </div>

            <!-- Tab Content -->
            <form id="lecturerForm" method="POST" enctype="multipart/form-data"
                :action="panelMode === 'create' ? '{{ route('admin.lecturers.store') }}' : '{{ url('admin/lecturers') }}/' + editId"
                class="flex-1 overflow-y-auto px-6 py-5">
                @csrf
                <input type="hidden" name="_method" :value="panelMode === 'edit' ? 'PUT' : 'POST'">

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
                                Klik atau seret foto ke sini (JPG/PNG)
                                <input type="file" name="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama Lengkap &amp; Gelar</label>
                            <input type="text" name="name" x-model="form.name" placeholder="Dr. Nama Dosen, S.T., M.T." class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">NIP</label>
                            <input type="text" name="nip" x-model="form.nip" placeholder="19850101 201012 1 001" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Peran</label>
                            <select name="role" x-model="form.role" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                <template x-for="opt in (['Laboratory Chief', 'Lecturer of Interest'].includes(form.role) ? ['Laboratory Chief', 'Lecturer of Interest'] : ['Laboratory Chief', 'Lecturer of Interest', form.role])" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Keahlian Utama</label>
                            <input type="text" name="expertise" x-model="form.expertise" placeholder="Ergonomi Kognitif, Perancangan Sistem Kerja" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Email</label>
                            <input type="email" name="email" x-model="form.email" placeholder="nama@trunojoyo.ac.id" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Google Scholar</label>
                            <input type="url" name="scholar_link" x-model="form.scholar_link" placeholder="https://scholar.google.com/..." class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">LinkedIn</label>
                            <input type="url" name="linkedin_link" x-model="form.linkedin_link" placeholder="https://linkedin.com/in/..." class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Riwayat Pendidikan (Repeater) -->
                <div x-show="activeTab === 'education'" class="space-y-3">
                    <template x-for="(edu, idx) in educations" :key="idx">
                        <div class="relative grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-3">
                            <button type="button" @click="educations.splice(idx, 1)" x-show="educations.length > 1" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Jenjang</label>
                                <select x-model="edu.degree" :name="'educations[' + idx + '][degree]'" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                    <option>S1</option><option>S2</option><option>S3</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Institusi</label>
                                <input type="text" x-model="edu.institution" :name="'educations[' + idx + '][institution]'" placeholder="Nama Institusi" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Tahun</label>
                                <input type="text" x-model="edu.year_range" :name="'educations[' + idx + '][year_range]'" placeholder="2008 - 2012" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="educations.push({ degree: 'S1', institution: '', year_range: '' })" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-maroon-300 px-3.5 py-2 text-xs font-semibold text-maroon-600 hover:bg-maroon-50">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Riwayat Pendidikan
                    </button>
                </div>

                <!-- TAB 3: Judul Penelitian (Repeater) -->
                <div x-show="activeTab === 'research'" class="space-y-3">
                    <template x-for="(item, idx) in researches" :key="idx">
                        <div class="relative grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-4">
                            <button type="button" @click="researches.splice(idx, 1)" x-show="researches.length > 1" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                            <div class="sm:col-span-3">
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Judul Penelitian</label>
                                <textarea x-model="item.title" :name="'researches[' + idx + '][title]'" rows="2" placeholder="Judul penelitian..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Tahun</label>
                                <input type="text" x-model="item.year" :name="'researches[' + idx + '][year]'" placeholder="2024" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="researches.push({ title: '', year: '' })" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-maroon-300 px-3.5 py-2 text-xs font-semibold text-maroon-600 hover:bg-maroon-50">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Judul Penelitian
                    </button>
                </div>

                <!-- TAB 4: Pengabdian Masyarakat (Repeater) -->
                <div x-show="activeTab === 'service'" class="space-y-3">
                    <template x-for="(item, idx) in services" :key="idx">
                        <div class="relative grid grid-cols-1 gap-3 rounded-lg border border-slate-200 p-4 sm:grid-cols-4">
                            <button type="button" @click="services.splice(idx, 1)" x-show="services.length > 1" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow hover:bg-red-600">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                            <div class="sm:col-span-3">
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Judul Pengabdian</label>
                                <textarea x-model="item.title" :name="'services[' + idx + '][title]'" rows="2" placeholder="Judul pengabdian masyarakat..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100"></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-500">Tahun</label>
                                <input type="text" x-model="item.year" :name="'services[' + idx + '][year]'" placeholder="2024" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="services.push({ title: '', year: '' })" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-maroon-300 px-3.5 py-2 text-xs font-semibold text-maroon-600 hover:bg-maroon-50">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Pengabdian Masyarakat
                    </button>
                </div>
            </form>

            <!-- Panel Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="panelOpen = false" type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" form="lecturerForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>

    {{-- ============================= DELETE CONFIRMATION MODAL ============================= --}}
    <div x-cloak x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="deleteModalOpen = false"></div>
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-sm rounded-xl bg-white p-6 text-center shadow-2xl">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </span>
            <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Data Dosen?</h3>
            <p class="mt-1.5 text-sm text-slate-500">
                Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.name"></span>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex gap-3">
                <button @click="deleteModalOpen = false" class="flex-1 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button @click="submitDelete()" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection
