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
        imagePreview: null,
        form: { title: '', category_id: '', published_at: '{{ now()->format('Y-m-d') }}', is_published: true, content: '' },
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
            this.form = { title: '', category_id: '', published_at: '{{ now()->format('Y-m-d') }}', is_published: true, content: '' };
            this.imagePreview = null;
            if (this.quill) this.quill.root.innerHTML = '';
        },
        openCreate() {
            this.resetForm();
            this.panelMode = 'create';
            this.editId = null;
            this.panelOpen = true;
            this.$nextTick(() => { this.initQuill(); });
        },
        openEdit(item) {
            this.form = {
                title: item.title,
                category_id: item.category_id,
                published_at: item.published_at ? item.published_at.substring(0, 10) : '{{ now()->format('Y-m-d') }}',
                is_published: !!item.is_published,
                content: item.content || ''
            };
            this.imagePreview = item.image_url || null;
            this.panelMode = 'edit';
            this.editId = item.id;
            this.panelOpen = true;
            this.$nextTick(() => {
                this.initQuill();
                this.quill.root.innerHTML = this.form.content;
                document.getElementById('hidden-content-index').value = this.form.content;
            });
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
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">News &amp; Articles</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola publikasi berita, pengumuman, dan artikel lab.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Berita
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
        <form method="GET" action="{{ route('admin.news.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select name="category" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>
                @if (request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('admin.news.index') }}" class="text-xs font-semibold text-slate-400 hover:text-maroon-600">Reset</a>
                @endif
            </div>
        </form>

        @if ($news->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-maroon-50 text-maroon-300">
                    <i class="fa-solid fa-newspaper text-2xl"></i>
                </span>
                <p class="text-sm font-semibold text-slate-600">Belum ada berita</p>
                <p class="max-w-xs text-xs text-slate-400">Tulis dan publikasikan berita pertama Anda ke situs.</p>
                <button @click="openCreate()" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tulis Berita Pertama
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Berita</th>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 text-center font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($news as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-10 w-14 shrink-0 rounded-md object-cover ring-1 ring-slate-200">
                                    <span class="line-clamp-2 max-w-70 font-medium text-slate-700">{{ $item->title }}</span>                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-600">
                                    {{ $item->category->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $item->published_at?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('admin.news.toggle', $item) }}" class="flex justify-center">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $item->is_published ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $item->is_published ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Detail Action -->
                                    <div class="group relative">
                                        <a href="{{ route('admin.news.show', $item) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Detail</span>
                                    </div>
                                    <!-- Edit Action -->
                                    <div class="group relative">
                                        <button @click="openEdit({{ Illuminate\Support\Js::from($item) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </button>
                                        <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-[11px] text-white opacity-0 transition-opacity group-hover:opacity-100">Edit</span>
                                    </div>
                                    <!-- Delete Action -->
                                    <div class="group relative">
                                        <button @click="confirmDelete({{ Illuminate\Support\Js::from($item->only('id', 'title')) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
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

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $news->links() }}
            </div>
        @endif
    </div>

    {{-- ============================= SLIDE-OVER: CREATE / EDIT NEWS ============================= --}}
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
            class="absolute right-0 top-0 flex h-full w-full max-w-2xl flex-col bg-white shadow-2xl"
        >
            <!-- Panel Header -->
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800" x-text="panelMode === 'create' ? 'Tambah Berita Baru' : 'Edit Berita'"></h2>
                    <p class="text-xs text-slate-400">Lengkapi konten berita, gambar, dan kategori.</p>
                </div>
                <button @click="panelOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Panel Content / Form -->
            <form id="newsForm" method="POST" enctype="multipart/form-data"
                :action="panelMode === 'create' ? '{{ route('admin.news.store') }}' : '{{ url('admin/news') }}/' + editId"
                class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="panelMode === 'edit' ? 'PUT' : 'POST'">
                <textarea name="content" id="hidden-content-index" class="hidden"></textarea>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Berita *</label>
                    <input type="text" name="title" x-model="form.title" required placeholder="Judul artikel atau berita..."
                        class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Kategori *</label>
                        <select name="category_id" x-model="form.category_id" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Publikasi</label>
                        <input type="date" name="published_at" x-model="form.published_at"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>
                </div>

                <!-- Gambar Banner Upload -->
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Gambar Banner Utama</label>
                    <label class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 border-dashed border-slate-300 px-4 py-4 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                        <img x-show="imagePreview" :src="imagePreview" class="h-28 w-full rounded-md object-cover">
                        <i x-show="!imagePreview" class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>
                        <span x-show="!imagePreview">Klik atau seret gambar (Maks 5MB)</span>
                        <span x-show="imagePreview" class="text-maroon-600 font-semibold">Klik untuk mengganti gambar</span>
                        <input type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                    </label>
                </div>

                <!-- Quill Editor -->
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Isi Konten *</label>
                    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div id="quill-editor-index" style="min-height:220px"></div>
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

            <!-- Panel Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button @click="panelOpen = false" type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" form="newsForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Berita
                </button>
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRM MODAL --}}
    <div x-cloak x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/50" @click="deleteModalOpen = false"></div>
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-sm rounded-xl bg-white p-6 text-center shadow-2xl">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </span>
            <h3 class="mt-4 text-base font-bold text-slate-800">Hapus Berita?</h3>
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
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
@endpush
