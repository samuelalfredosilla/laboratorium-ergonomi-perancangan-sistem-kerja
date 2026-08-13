@csrf

@if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="mb-1 font-semibold">Periksa kembali isian berikut:</p>
        <ul class="list-disc space-y-0.5 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Judul Berita</label>
                <input type="text" name="title" value="{{ old('title', $news->title ?? '') }}" required placeholder="Judul berita atau artikel..."
                    class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-base font-semibold text-slate-800 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Konten</label>
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <div id="quill-editor" style="min-height:280px"></div>
                </div>
                <textarea name="content" id="content-input" class="hidden">{{ old('content', $news->content ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <h2 class="text-sm font-bold text-slate-700">Detail Publikasi</h2>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Kategori</label>
                <select name="category_id" required class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $news->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tanggal Publikasi</label>
                <input type="date" name="published_at"
                    value="{{ old('published_at', isset($news) && $news->published_at ? $news->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
            </div>

            <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3.5 py-3">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Status Publikasi</p>
                    <p class="text-xs text-slate-400">Tampilkan di situs publik</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="is_published" value="1" class="peer sr-only" {{ old('is_published', $news->is_published ?? true) ? 'checked' : '' }}>
                    <div class="h-5 w-9 rounded-full bg-slate-300 transition-colors peer-checked:bg-emerald-500"></div>
                    <div class="absolute left-1 top-1 h-3.5 w-3.5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
                </label>
            </div>
        </div>

        <div class="space-y-3 rounded-xl border border-slate-200 bg-white p-5 shadow-soft">
            <h2 class="text-sm font-bold text-slate-700">Gambar Utama</h2>
            <label id="dropzone" for="image-input"
                class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 border-dashed border-slate-300 px-4 py-6 text-center text-xs text-slate-400 transition-colors hover:border-maroon-300 hover:bg-maroon-50 hover:text-maroon-500">
                @if (isset($news) && $news->image)
                    <img id="image-preview" src="{{ $news->image_url }}" class="mb-1 h-28 w-full rounded-md object-cover">
                @else
                    <img id="image-preview" src="" class="mb-1 hidden h-28 w-full rounded-md object-cover">
                @endif
                <i id="image-placeholder-icon" class="fa-solid fa-cloud-arrow-up text-lg {{ isset($news) && $news->image ? 'hidden' : '' }}"></i>
                <span id="image-placeholder-text" class="{{ isset($news) && $news->image ? 'hidden' : '' }}">Klik atau seret gambar ke sini (JPG/PNG, maks 5MB)</span>
                <span id="image-replace-text" class="{{ isset($news) && $news->image ? '' : 'hidden' }}">Klik atau seret untuk mengganti gambar</span>
                <input type="file" name="image" id="image-input" accept="image/*" class="hidden">
            </label>
        </div>
    </div>
</div>

<div class="flex items-center justify-end gap-3 pt-6">
    <a href="{{ route('admin.news.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-maroon-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
        <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Berita
    </button>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>.ql-editor{min-height:250px;font-size:14px;}</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var hidden = document.getElementById('content-input');
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Tulis isi berita di sini...',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'link', 'image'],
                        ['clean'],
                    ],
                },
            });
            quill.root.innerHTML = hidden.value;
            quill.on('text-change', function () { hidden.value = quill.root.innerHTML; });
            document.querySelector('#quill-editor').closest('form').addEventListener('submit', function () {
                hidden.value = quill.root.innerHTML;
            });

            var dropzone = document.getElementById('dropzone');
            var input = document.getElementById('image-input');
            var preview = document.getElementById('image-preview');
            var icon = document.getElementById('image-placeholder-icon');
            var placeholderText = document.getElementById('image-placeholder-text');
            var replaceText = document.getElementById('image-replace-text');

            function showFile(file) {
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');
                    placeholderText.classList.add('hidden');
                    replaceText.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }

            input.addEventListener('change', function (e) { showFile(e.target.files[0]); });
            dropzone.addEventListener('dragover', function (e) { e.preventDefault(); dropzone.classList.add('border-maroon-300', 'bg-maroon-50'); });
            dropzone.addEventListener('dragleave', function () { dropzone.classList.remove('border-maroon-300', 'bg-maroon-50'); });
            dropzone.addEventListener('drop', function (e) {
                e.preventDefault();
                dropzone.classList.remove('border-maroon-300', 'bg-maroon-50');
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    showFile(e.dataTransfer.files[0]);
                }
            });
        });
    </script>
@endpush
