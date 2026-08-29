@extends('layouts.admin')

@section('title', $news->title)

@section('content')
<div x-data="newsDetailData()" class="space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.news.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Detail Berita</h1>
                <p class="mt-1 text-sm text-slate-500">Pratinjau artikel lengkap dan riwayat log perubahan.</p>
            </div>
        </div>
        <button type="button" @click="openEdit()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors cursor-pointer">
            <i class="fa-regular fa-pen-to-square text-xs"></i> Edit Berita
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

    <!-- 2 Kolom Grid: items-start untuk sticky behavior -->
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
        
        <!-- ================= KOLOM KIRI (Konten Berita Tetap/Sticky Aman di Bawah Navbar) ================= -->
        <div class="space-y-6 lg:col-span-2 lg:sticky lg:top-24">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-soft">
                @if ($news->image)
                    <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="mb-5 h-72 w-full rounded-lg object-cover ring-1 ring-slate-100">
                @endif
                <div class="mb-3 flex items-center gap-2">
                    <span class="rounded-full bg-maroon-50 px-3 py-1 text-xs font-semibold text-maroon-700 border border-maroon-100">{{ $news->category->name ?? 'Umum' }}</span>
                    <span class="text-xs text-slate-400">&middot;</span>
                    <span class="text-xs text-slate-400">{{ $news->published_at?->translatedFormat('d F Y') ?? 'Draft' }}</span>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 leading-snug">{{ $news->title }}</h2>
                <div class="prose prose-slate mt-5 max-w-none border-t border-slate-100 pt-5 text-sm leading-relaxed text-slate-600 max-h-105 overflow-y-auto pr-2">
                    {!! $news->content !!}
                </div>
            </div>
        </div>

        <!-- ================= KOLOM KANAN (Status Publikasi & Riwayat Perubahan) ================= -->
        <div class="space-y-6 lg:col-span-1">
            <!-- 1. Publication Info Card -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
                <h3 class="mb-3 text-sm font-bold text-slate-700"><i class="fa-solid fa-circle-info mr-2 text-maroon-400"></i>Status Publikasi</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                        <span class="text-xs text-slate-400">Status</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $news->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $news->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                        <span class="text-xs text-slate-400">Penulis</span>
                        <span class="font-semibold text-slate-700">{{ $news->author->name ?? 'Admin EPSK' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                        <span class="text-xs text-slate-400">Kategori</span>
                        <span class="font-semibold text-slate-700">{{ $news->category->name ?? 'Tanpa Kategori' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Slug URL</span>
                        <span class="font-mono text-xs text-slate-600 truncate max-w-36" title="{{ $news->slug }}">{{ $news->slug }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Activity Logs Card (Scrollable) -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-maroon-400"></i>Riwayat Perubahan
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse ($news->activityLogs as $log)
                        <div class="p-4 text-xs text-slate-600 space-y-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-bold text-slate-800 leading-snug">{{ $log->description }}</span>
                                <span class="text-slate-400 whitespace-nowrap text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-400 text-[11px]">
                                Oleh: <span class="font-medium text-slate-600">{{ $log->user->name ?? 'Admin EPSK' }}</span> &middot; {{ $log->created_at->format('d M Y, H:i') }}
                            </p>

                            @if(isset($log->properties['new']) && is_array($log->properties['new']))
                                @php
                                    $ignored = ['id', 'created_at', 'updated_at'];
                                    $diffs = [];

                                    foreach ($log->properties['new'] as $key => $newVal) {
                                        if (in_array($key, $ignored)) continue;
                                        $oldVal = $log->properties['old'][$key] ?? null;
                                        if ((string)$oldVal !== (string)$newVal) {
                                            $diffs[$key] = [
                                                'old' => $oldVal,
                                                'new' => $newVal
                                            ];
                                        }
                                    }

                                    if (empty($diffs)) {
                                        foreach ($log->properties['new'] as $key => $newVal) {
                                            if (in_array($key, $ignored)) continue;
                                            $diffs[$key] = [
                                                'old' => $log->properties['old'][$key] ?? $newVal,
                                                'new' => $newVal
                                            ];
                                        }
                                    }
                                @endphp

                                @if(count($diffs) > 0)
                                    <div class="mt-2.5 rounded-lg bg-slate-50 p-2.5 font-mono text-[11px] space-y-2 border border-slate-100 leading-relaxed">
                                        @foreach($diffs as $key => $diff)
                                            <div>
                                                <div class="text-slate-500">
                                                    {{ $key }}:
                                                    @if(!is_null($diff['old']) && $diff['old'] !== '')
                                                        <span class="text-red-500 line-through">{{ is_array($diff['old']) ? json_encode($diff['old']) : $diff['old'] }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-emerald-600 font-semibold pl-2">
                                                    &rarr; {{ is_array($diff['new']) ? json_encode($diff['new']) : $diff['new'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="p-5 text-center text-xs text-slate-400">Belum ada riwayat perubahan yang tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============================= SLIDE-OVER: EDIT NEWS (FULL SCREEN) ============================= --}}
    <div 
        x-cloak 
        x-show="panelOpen" 
        class="fixed inset-0 overflow-hidden" 
        style="z-index: 9999;"
        @keydown.escape.window="closeEdit()"
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
            @click="closeEdit()"
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
                class="w-screen max-w-2xl h-full bg-white shadow-2xl flex flex-col justify-between"
            >
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-white">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Edit Berita</h2>
                        <p class="text-xs text-slate-400">Perbarui informasi artikel dan status publikasi.</p>
                    </div>
                    <button type="button" @click="closeEdit()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="newsEditForm" method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <textarea name="content" id="hidden-content-show" class="hidden"></textarea>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Berita *</label>
                        <input type="text" name="title" x-model="form.title" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Kategori *</label>
                            <select name="category_id" x-model="form.category_id" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Publikasi</label>
                            <input type="date" name="published_at" x-model="form.published_at" class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Gambar Banner Utama</label>
                        <label class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 border-dashed border-slate-300 px-4 py-4 text-center text-xs text-slate-400 hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                            <img x-show="imagePreview" :src="imagePreview" class="h-28 w-full rounded-md object-cover">
                            <i x-show="!imagePreview" class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>
                            <span class="text-maroon-600 font-semibold">Klik untuk mengganti gambar banner (Maks 8MB)</span>
                            <input type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Isi Konten Berita *</label>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <div id="quill-editor-show" style="min-height:220px"></div>
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
                    <button type="button" @click="closeEdit()" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" form="newsEditForm" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Raw content carrier -->
<div id="raw-news-content" class="hidden">{!! $news->content !!}</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function newsDetailData() {
            return {
                panelOpen: false,
                imagePreview: '{{ $news->image_url }}',
                form: {
                    title: @js($news->title),
                    category_id: '{{ $news->category_id }}',
                    published_at: '{{ $news->published_at ? $news->published_at->format('Y-m-d') : '' }}',
                    is_published: {{ $news->is_published ? 'true' : 'false' }},
                    content: ''
                },
                quill: null,
                initQuill() {
                    if (!this.quill) {
                        this.form.content = document.getElementById('raw-news-content').innerHTML;
                        this.quill = new Quill('#quill-editor-show', {
                            theme: 'snow',
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
                        this.quill.root.innerHTML = this.form.content;
                        document.getElementById('hidden-content-show').value = this.form.content;
                        this.quill.on('text-change', () => {
                            this.form.content = this.quill.root.innerHTML;
                            document.getElementById('hidden-content-show').value = this.quill.root.innerHTML;
                        });
                    }
                },
                openEdit() {
                    this.panelOpen = true;
                    this.$nextTick(() => { this.initQuill(); });
                },
                closeEdit() {
                    this.panelOpen = false;
                }
            };
        }
    </script>
@endpush