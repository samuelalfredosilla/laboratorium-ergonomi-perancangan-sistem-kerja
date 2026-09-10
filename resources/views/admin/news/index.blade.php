@extends('layouts.admin')

@section('title', 'News & Articles')

@section('content')
<div
    x-data="{
        panelOpen: false,
        panelMode: 'create',
        editId: null,
        deleteModalOpen: false,
        deleteTarget: null,
        categoryModalOpen: false,
        imagePreview: null,
        form: {
            title: '',
            category_id: '',
            published_at: '{{ now()->format('Y-m-d') }}',
            is_published: true,
            content: ''
        },
        quill: null,
        initQuill() {
            if (!this.quill) {
                this.quill = new Quill('#quill-editor-index', {
                    theme: 'snow',
                    placeholder: 'Tulis isi artikel atau berita...',
                    modules: {
                        toolbar: [
                            [{ header: [2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['blockquote', 'link', 'image'],
                            ['clean']
                        ]
                    }
                });
                this.quill.on('text-change', () => {
                    this.form.content = this.quill.root.innerHTML;
                    document.getElementById('hidden-content-index').value = this.quill.root.innerHTML;
                });
            }
        },
        resetForm() {
            this.form = {
                title: '',
                category_id: '',
                published_at: '{{ now()->format('Y-m-d') }}',
                is_published: true,
                content: ''
            };
            this.imagePreview = null;
            if (this.quill) this.quill.root.innerHTML = '';
            document.getElementById('hidden-content-index').value = '';
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
            this.$nextTick(() => {
                this.initQuill();
                if(this.quill) this.quill.root.innerHTML = '';
            });
        },
        openEdit(item) {
            let pubDate = '';
            if (item.published_date_formatted) {
                pubDate = item.published_date_formatted;
            } else if (item.published_at) {
                pubDate = item.published_at.toString().substring(0, 10);
            } else {
                pubDate = '{{ now()->format('Y-m-d') }}';
            }

            this.form = {
                title: item.title,
                category_id: item.category_id,
                published_at: pubDate,
                is_published: !!item.is_published,
                content: item.content || ''
            };
            this.imagePreview = item.image_url || (item.image ? '/storage/' + item.image : null);
            this.panelMode = 'edit';
            this.editId = item.id;
            this.panelOpen = true;
            this.$nextTick(() => {
                this.initQuill();
                if (this.quill) {
                    this.quill.root.innerHTML = this.form.content;
                }
                document.getElementById('hidden-content-index').value = this.form.content;
            });
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
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">News & Articles</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola publikasi berita, artikel, dan pengumuman laboratorium.</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Tombol Buka Modal Kategori -->
            <button type="button" @click="categoryModalOpen = true" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-600 shadow-soft hover:border-maroon-200 hover:text-maroon-600 transition-colors cursor-pointer">
                <i class="fa-solid fa-tags text-xs text-maroon-500"></i> Kelola Kategori
            </button>
            <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i> Tulis Berita
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.news.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative w-full lg:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select name="category" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-xs font-semibold text-slate-600 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100 cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-xs font-semibold text-slate-600 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100 cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>

                @if (request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('admin.news.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 hover:border-red-200 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($news->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-newspaper text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Belum ada data berita</p>
                <p class="max-w-xs text-xs text-slate-400">Tulis dan publikasikan berita pertama Anda ke portal web lab.</p>
                @if (request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('admin.news.index') }}" class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:border-maroon-200 hover:text-maroon-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset Filter
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Berita</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Kategori</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Tanggal</th>
                            <th class="px-5 py-3 text-center font-semibold whitespace-nowrap">Status</th>
                            <th class="px-5 py-3 text-right font-semibold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($news as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="h-10 w-14 shrink-0 rounded-md object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-10 w-14 items-center justify-center rounded-md bg-slate-100 text-slate-400"><i class="fa-regular fa-image text-xs"></i></div>
                                    @endif
                                    <span class="line-clamp-2 max-w-xs font-medium text-slate-800 leading-snug">{{ $item->title }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-700 border border-maroon-100">
                                    {{ $item->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-xs text-slate-500">
                                {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-5 py-3 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.news.toggle', $item) }}" class="flex justify-center">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors cursor-pointer {{ $item->is_published ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $item->is_published ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">

                                    <!-- Ikon Mata (Show/Detail) Dikembalikan -->
                                    <div class="group relative">
                                        <a href="{{ route('admin.news.show', $item) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Detail</span>
                                    </div>

                                    <!-- Ikon Edit -->
                                    <div class="group relative">
                                        <button type="button" @click="openEdit(@js($item))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600 transition-colors">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>

                                    <!-- Ikon Delete -->
                                    <div class="group relative">
                                        <button type="button" @click="confirmDelete(@js($item->only('id', 'title')))" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Hapus</span>
                                    </div>

                                    <form id="delete-form-{{ $item->id }}" method="POST" action="{{ route('admin.news.destroy', $item) }}" class="hidden">
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

            @if ($news instanceof \Illuminate\Pagination\LengthAwarePaginator && $news->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $news->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================= MODAL KELOLA KATEGORI (BERITA) ============================= --}}
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
                        <h3 class="text-base font-bold text-slate-800">Kelola Kategori Berita</h3>
                    </div>
                    <button type="button" @click="categoryModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    <!-- Form Tambah Kategori -->
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="rounded-xl bg-slate-50 p-4 border border-slate-200">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tambah Kategori Baru</label>
                        <div class="flex gap-2">
                            <input type="text" name="name" required placeholder="Contoh: Pengumuman"
                                class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <button type="submit" class="shrink-0 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700 shadow-sm transition-colors">
                                <i class="fa-solid fa-plus text-[10px] mr-1"></i> Tambah
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Kategori List -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Daftar Kategori Tersedia</label>
                        <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden">
                            @forelse ($categories ?? [] as $category)
                                <div class="flex items-center justify-between p-3.5 bg-white hover:bg-slate-50/50" x-data="{ editing: false, newName: @js($category->name) }">

                                    <!-- Mode Lihat -->
                                    <div x-show="!editing" class="flex items-center gap-3">
                                        <div>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $category->name }}</span>
                                            <span class="block text-[11px] text-slate-400">Slug: {{ $category->slug }}</span>
                                            <!-- Menampilkan jumlah relasi (pastikan controller memakai ->withCount('news')) -->
                                            <span class="block text-[10px] font-medium text-maroon-600 mt-0.5">{{ $category->news_count ?? 0 }} berita terhubung</span>
                                        </div>
                                    </div>

                                    <!-- Tombol Aksi Kategori -->
                                    <div x-show="!editing" class="flex items-center gap-1">
                                        <button type="button" @click="editing = true" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600" title="Edit Kategori">
                                            <i class="fa-regular fa-pen-to-square text-xs"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Berita yang terkait akan otomatis kehilangan kategori.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus Kategori">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Mode Edit Inline -->
                                    <form x-show="editing" method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="flex w-full items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" x-model="newName" required class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs focus:border-maroon-400 focus:outline-none">
                                        <button type="submit" class="rounded-lg bg-maroon-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-maroon-700">Simpan</button>
                                        <button type="button" @click="editing = false; newName = @js($category->name)" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-500 hover:bg-slate-50">Batal</button>
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

    {{-- ============================= SLIDE-OVER: CREATE & EDIT NEWS ============================= --}}
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
                class="w-screen max-w-2xl h-full bg-white shadow-2xl flex flex-col justify-between">

                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-white">
                    <div>
                        <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tulis Berita Baru' : 'Edit Berita'"></h2>
                        <p class="text-xs text-slate-400">Lengkapi gambar, judul, dan isi berita.</p>
                    </div>
                    <button type="button" @click="closePanel()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="newsForm" method="POST" :action="panelMode === 'create' ? '{{ route('admin.news.store') }}' : '{{ url('admin/news') }}/' + editId" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    @csrf
                    <template x-if="panelMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Textarea disembunyikan untuk Quill Content -->
                    <textarea name="content" id="hidden-content-index" class="hidden"></textarea>

                    <!-- Gambar Banner Upload -->
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Gambar Banner Utama</label>
                        <label class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 border-dashed border-slate-300 px-4 py-4 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                            <img x-show="imagePreview" :src="imagePreview" class="h-32 w-full rounded-md object-cover">
                            <i x-show="!imagePreview" class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>
                            <span x-show="!imagePreview">Klik untuk unggah gambar banner (Maks 8MB)</span>
                            <span x-show="imagePreview" class="text-maroon-600 font-semibold">Klik untuk mengganti gambar</span>
                            <input type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Berita *</label>
                        <input type="text" name="title" x-model="form.title" required placeholder="Judul artikel atau berita..."
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Kategori *</label>
                            <select name="category_id" x-model="form.category_id" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-maroon-700 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Publikasi</label>
                            <input type="date" name="published_at" x-model="form.published_at"
                                class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>

                    <!-- Quill Editor -->
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Isi Konten Berita *</label>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <div id="quill-editor-index" style="min-height: 250px"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-3.5">
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Publikasikan Langsung</p>
                            <p class="text-[11px] text-slate-400">Tampilkan berita ini di halaman utama dan berita publik.</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_published" value="1" x-model="form.is_published" class="peer sr-only">
                            <div class="h-5 w-9 rounded-full bg-slate-300 transition-colors peer-checked:bg-emerald-500"></div>
                            <div class="absolute left-1 top-1 h-3.5 w-3.5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
                        </label>
                    </div>
                </form>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 bg-white">
                    <button type="button" @click="closePanel()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Batal</button>
                    <button type="submit" form="newsForm" class="inline-flex items-center gap-2 rounded-xl bg-maroon-600 px-5 py-2.5 text-xs font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> <span x-text="panelMode === 'create' ? 'Simpan Berita' : 'Simpan Perubahan'"></span>
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

                <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Berita?</h3>
                <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                    Anda yakin ingin menghapus <span class="font-semibold text-slate-700" x-text="deleteTarget?.title"></span>? Tindakan ini tidak dapat dibatalkan.
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

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
@endpush
