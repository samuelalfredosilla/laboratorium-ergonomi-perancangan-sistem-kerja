<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\News;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Kata kunci terlarang agar tidak bentrok dengan rute sistem
     */
    private const RESERVED_SLUGS = [
        'admin', 'api', 'login', 'logout', 'dashboard', 'news', 'article',
        'create', 'edit', 'delete', 'update', 'show', 'all', 'public', 'draft'
    ];

    public function index(Request $request)
    {
        // Sanitasi query pencarian dari wildcard injection
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        $news = News::with(['category', 'author'])
            ->when($search, fn ($q) => $q->where('title', 'like', '%' . $search . '%'))
            ->when($request->filled('category') && is_numeric($request->category), fn ($q) => $q->where('category_id', (int) $request->category))
            ->when($request->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($request->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->latest('published_at')
            ->paginate(8)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.news.index', compact('news', 'categories'));
    }

    public function show(News $news)
    {
        $news->load(['category', 'author', 'activityLogs.user']);
        $categories = Category::orderBy('name')->get();

        return view('admin.news.show', compact('news', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (! $file->isValid()) {
                return back()->withErrors(['image' => 'File gambar tidak valid atau rusak.'])->withInput();
            }

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['image'] = $file->storeAs('news', $safeFileName, 'public');
        }

        $news = News::create($data);

        Notification::log(
            'Berita baru "' . $news->title . '" ditambahkan.',
            'fa-newspaper',
            'success',
            route('admin.news.index')
        );

        ActivityLog::record($news, 'created', 'Menambahkan berita baru: ' . $news->title, [
            'attributes' => $news->toArray(),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Berita "' . $news->title . '" berhasil disimpan secara aman.');
    }

    public function update(Request $request, News $news)
    {
        $data = $this->validatedFields($request);

        if ($data['title'] !== $news->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $news->id);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (! $file->isValid()) {
                return back()->withErrors(['image' => 'File gambar tidak valid atau rusak.'])->withInput();
            }

            // Hapus file lama secara aman
            $this->safeDeleteFile($news->image);

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['image'] = $file->storeAs('news', $safeFileName, 'public');
        }

        $oldValues = $news->getOriginal();
        $news->update($data);
        $changes = $news->getChanges();
        $oldChanges = array_intersect_key($oldValues, $changes);

        Notification::log(
            'Berita "' . $news->title . '" diperbarui.',
            'fa-newspaper',
            'maroon',
            route('admin.news.index')
        );

        if (! empty($changes)) {
            ActivityLog::record($news, 'updated', 'Memperbarui data berita: ' . $news->title, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return redirect()->back()->with('success', 'Berita "' . $news->title . '" berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        $title = $news->title;
        $backupData = $news->toArray();

        // Hapus banner lama secara aman
        $this->safeDeleteFile($news->image);

        $news->delete();

        Notification::log('Berita "' . $title . '" dihapus.', 'fa-trash-can', 'danger', route('admin.news.index'));

        ActivityLog::record($news, 'deleted', 'Menghapus berita: ' . $title, [
            'attributes' => $backupData,
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Berita "' . $title . '" berhasil dihapus.');
    }

    public function toggle(News $news)
    {
        $news->update(['is_published' => ! $news->is_published]);

        $statusText = $news->is_published ? 'dipublikasikan.' : 'dijadikan draft.';

        Notification::log(
            'Berita "' . $news->title . '" ' . $statusText,
            $news->is_published ? 'fa-eye' : 'fa-eye-slash',
            $news->is_published ? 'success' : 'warning',
            route('admin.news.index')
        );

        ActivityLog::record($news, 'updated', 'Mengubah status publikasi berita: ' . $news->title, [
            'status' => $news->is_published ? 'published' : 'draft',
        ]);

        return back()->with('success', $news->is_published ? 'Berita dipublikasikan.' : 'Berita disimpan sebagai draft.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'content'      => ['required', 'string', 'max:65000'],
            'image'        => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192', // Maksimal 8MB
                'dimensions:max_width=6000,max_height=6000',
            ],
            'published_at' => ['nullable', 'date'],
        ], [
            'title.required'        => 'Judul berita wajib diisi.',
            'category_id.required'  => 'Pilih kategori berita.',
            'category_id.exists'    => 'Kategori yang dipilih tidak valid.',
            'content.required'      => 'Konten berita wajib diisi.',
            'image.image'           => 'File banner harus berupa gambar.',
            'image.mimes'           => 'Format gambar yang diperbolehkan: JPG, PNG, atau WebP.',
            'image.max'             => 'Ukuran banner maksimal 8 MB.',
            'image.dimensions'      => 'Dimensi gambar banner maksimal 6000x6000 px.',
            'published_at.date'     => 'Format tanggal publikasi tidak valid.',
        ]);

        // Sanitasi teks judul dan konten HTML
        $data['title'] = strip_tags(trim($data['title']));
        $data['content'] = $this->cleanHtmlContent($data['content']);

        unset($data['image']);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->filled('published_at') ? $request->input('published_at') : now();

        return $data;
    }

    /**
     * Sanitasi Konten HTML untuk Mencegah Stored XSS
     */
    private function cleanHtmlContent(string $html): string
    {
        // Daftar tag yang diizinkan untuk formatting artikel
        $allowedTags = '<p><br><b><strong><i><em><u><strike><s><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><img><table><thead><tbody><tr><th><td><hr><code><pre>';
        
        $cleaned = strip_tags($html, $allowedTags);

        // Hapus atribut berbahaya (on*, javascript:, data:)
        $cleaned = preg_replace('/(<[^>]+?)([\s\r\n\t]+on\w+=\s*(["\'][^"\']*["\']|[^\s>]+))/i', '$1', $cleaned);
        $cleaned = preg_replace('/href=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $cleaned);
        $cleaned = preg_replace('/src=\s*["\']\s*javascript:[^"\']*["\']/i', 'src=""', $cleaned);

        return $cleaned;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);

        if (empty($base)) {
            $base = 'berita-' . Str::random(6);
        }

        if (in_array($base, self::RESERVED_SLUGS, true)) {
            $base = $base . '-post';
        }

        $slug = $base;
        $attempt = 1;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$attempt);
        }

        return $slug;
    }

    private function safeDeleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'news/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}