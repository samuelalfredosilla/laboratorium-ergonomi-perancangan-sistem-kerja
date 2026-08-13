@extends('layouts.admin')

@section('title', 'News & Articles')

@section('content')
<div
    x-data="{
        deleteModalOpen: false,
        deleteTarget: null,
        confirmDelete(news) {
            this.deleteTarget = news;
            this.deleteModalOpen = true;
        },
        submitDelete() {
            document.getElementById('delete-form-' + this.deleteTarget.id).submit();
        },
    }"
    class="space-y-6"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">News &amp; Articles</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola berita dan artikel yang tampil di situs Laboratorium EPSK.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Berita
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-soft">
        <form method="GET" action="{{ route('admin.news.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select name="category" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-600 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Semua Status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
                    <i class="fa-solid fa-filter text-xs"></i> Terapkan
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
                <p class="max-w-xs text-xs text-slate-400">Berita dan artikel yang kamu tulis akan tampil di sini.</p>
                <a href="{{ route('admin.news.create') }}" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-4 py-2 text-xs font-semibold text-white hover:bg-maroon-700">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tulis Berita Pertama
                </a>
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
                                    <span class="line-clamp-2 max-w-[260px] font-medium text-slate-700">{{ $item->title }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-maroon-50 px-2.5 py-1 text-[11px] font-semibold text-maroon-600">{{ $item->category->name ?? 'Umum' }}</span>
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
                                    <a href="{{ route('admin.news.edit', $item) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-maroon-50 hover:text-maroon-600">
                                        <i class="fa-regular fa-pen-to-square text-sm"></i>
                                    </a>
                                    <button @click="confirmDelete({{ Illuminate\Support\Js::from($item->only('id', 'title')) }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
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

    {{-- ============================= DELETE CONFIRM MODAL ============================= --}}
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
