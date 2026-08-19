@extends('layouts.admin')

@section('title', $lecturer->name)

@section('content')
<div
    x-data="{
        panelOpen: false,
        activeTab: 'profile',
        photoPreview: '{{ $lecturer->photo_url }}',
        form: {
            name: @json($lecturer->name),
            nip: @json($lecturer->nip ?? ''),
            role: @json($lecturer->role ?? 'Lecturer of Interest'),
            expertise: @json($lecturer->expertise ?? ''),
            email: @json($lecturer->email ?? ''),
            scholar_link: @json($lecturer->scholar_link ?? ''),
            linkedin_link: @json($lecturer->linkedin_link ?? '')
        },
        educations: {{ $lecturer->educations->isNotEmpty() ? $lecturer->educations->toJson() : '[{ degree: \'S1\', institution: \'\', year_range: \'\' }]' }},
        researches: {{ $lecturer->researches->isNotEmpty() ? $lecturer->researches->toJson() : '[{ title: \'\', year: \'\' }]' }},
        services: {{ $lecturer->communityServices->isNotEmpty() ? $lecturer->communityServices->toJson() : '[{ title: \'\', year: \'\' }]' }},
        openEdit() {
            this.activeTab = 'profile';
            this.panelOpen = true;
        }
    }"
    class="space-y-6"
>
    <!-- Header Navigation -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.lecturers.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Detail Dosen</h1>
                <p class="mt-1 text-sm text-slate-500">Profil lengkap, riwayat pendidikan, rekam jejak, dan aktivitas dosen.</p>
            </div>
        </div>
        <button @click="openEdit()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
            <i class="fa-regular fa-pen-to-square text-xs"></i> Edit Data Dosen
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profile Card & Activity Logs -->
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-soft">
                <img src="{{ $lecturer->photo_url }}" alt="{{ $lecturer->name }}" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-maroon-50">
                <h2 class="mt-4 text-base font-bold text-slate-800">{{ $lecturer->name }}</h2>
                <p class="mt-1 text-xs text-slate-400">{{ $lecturer->nip ? 'NIP. ' . $lecturer->nip : 'NIP belum diisi' }}</p>

                <div class="mt-3">
                    @if (Illuminate\Support\Str::contains($lecturer->role, ['Head', 'Kepala', 'Chief'], true))
                        <span class="inline-flex items-center rounded-full bg-maroon-600 px-3 py-1 text-xs font-semibold text-white">{{ $lecturer->role }}</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $lecturer->role }}</span>
                    @endif
                </div>

                <div class="mt-5 space-y-2.5 border-t border-slate-100 pt-5 text-left text-sm">
                    <p class="flex items-start gap-2.5 text-slate-600">
                        <i class="fa-solid fa-flask mt-0.5 w-4 text-maroon-400"></i>
                        <span>{{ $lecturer->expertise ?? '—' }}</span>
                    </p>
                    <p class="flex items-center gap-2.5 text-slate-600">
                        <i class="fa-solid fa-envelope w-4 text-maroon-400"></i>
                        <span class="truncate">{{ $lecturer->email ?? '—' }}</span>
                    </p>
                    @if ($lecturer->scholar_link)
                        <a href="{{ $lecturer->scholar_link }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 text-maroon-600 hover:underline">
                            <i class="fa-solid fa-graduation-cap w-4"></i>
                            <span class="truncate">Google Scholar</span>
                        </a>
                    @endif
                    @if ($lecturer->linkedin_link)
                        <a href="{{ $lecturer->linkedin_link }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 text-maroon-600 hover:underline">
                            <i class="fa-brands fa-linkedin w-4"></i>
                            <span class="truncate">LinkedIn</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Activity Logs Card -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-maroon-400"></i>Riwayat Perubahan
                    </h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->activityLogs as $log)
                        <div class="p-4 text-xs text-slate-600 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">{{ $log->description }}</span>
                                <span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-400">
                                Oleh: <span class="font-medium text-slate-600">{{ $log->user->name ?? 'Admin' }}</span> &middot; {{ $log->created_at->format('d M Y, H:i') }}
                            </p>

                            @if(isset($log->properties['new']))
                                <div class="mt-2 rounded bg-slate-50 p-2 font-mono text-[11px]">
                                    @foreach($log->properties['new'] as $key => $val)
                                        @if($key !== 'updated_at')
                                            <div>
                                                <span class="text-slate-500">{{ $key }}:</span> 
                                                <span class="text-red-500 line-through">{{ $log->properties['old'][$key] ?? 'null' }}</span> &rarr; 
                                                <span class="text-emerald-600 font-semibold">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="p-5 text-center text-xs text-slate-400">Belum ada riwayat perubahan yang tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Academic & Research Details -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Pendidikan -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-graduation-cap mr-2 text-maroon-400"></i>Riwayat Pendidikan</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->educations as $edu)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $edu->degree }}</p>
                                <p class="text-xs text-slate-500">{{ $edu->institution }}</p>
                            </div>
                            <span class="text-xs font-medium text-slate-400">{{ $edu->year_range }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data riwayat pendidikan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Penelitian -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-microscope mr-2 text-maroon-400"></i>Judul Penelitian</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->researches as $research)
                        <div class="flex items-start justify-between gap-4 px-5 py-3.5">
                            <p class="text-sm text-slate-600">{{ $research->title }}</p>
                            <span class="shrink-0 text-xs font-medium text-slate-400">{{ $research->year }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data judul penelitian.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pengabdian Masyarakat -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-hand-holding-heart mr-2 text-maroon-400"></i>Pengabdian Masyarakat</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($lecturer->communityServices as $service)
                        <div class="flex items-start justify-between gap-4 px-5 py-3.5">
                            <p class="text-sm text-slate-600">{{ $service->title }}</p>
                            <span class="shrink-0 text-xs font-medium text-slate-400">{{ $service->year }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data pengabdian masyarakat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: EDIT LECTURER ============================= --}}
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
                    <h2 class="text-base font-bold text-slate-800">Edit Data Dosen</h2>
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
            <form id="lecturerDetailEditForm" method="POST" action="{{ route('admin.lecturers.update', $lecturer) }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5">
                @csrf
                @method('PUT')

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
                                    <option>S1</option><option>S2</option><option>S3</option>
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
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="panelOpen = false" type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" form="lecturerDetailEditForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection