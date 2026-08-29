<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\News;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $news = DB::transaction(function () use ($data) {
            return News::create($data);
        });

        Notification::log(
            'Berita baru "' . $news->title . '" ditambahkan.',
            'fa-newspaper',
            'success',
            route('admin.news.show', $news)
        );

        ActivityLog::record($news, 'created', 'Menambahkan berita baru: ' . $news->title, [
            'attributes' => $news->only(['title', 'category_id', 'published_at', 'is_published']),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Berita "' . $news->title . '" berhasil disimpan secara aman.');
    }

    public function update(Request $request, News $news)
    {
        $data = $this->validatedFields($request, $news);

        // 1. Ambil snapshot data lama
        $news->loadMissing('category');
        $oldCategoryName = $news->category?->name ?? '—';
        $oldDate = $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('Y-m-d') : 'Draft';
        $oldStatus = $news->is_published ? 'Published' : 'Draft';
        $oldContentText = trim(preg_replace('/\s+/', ' ', strip_tags($news->content)));

        $oldValues = [
            'Judul Berita'      => $news->title,
            'Kategori'          => $oldCategoryName,
            'Tanggal Publikasi' => $oldDate,
            'Status Publikasi'  => $oldStatus,
            'Gambar Banner'     => $news->image ? 'Ada Banner' : 'Tanpa Banner',
            'Isi Konten'        => Str::limit($oldContentText, 70),
        ];

        if ($data['title'] !== $news->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $news->id);
        }

        $imageChanged = false;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (! $file->isValid()) {
                return back()->withErrors(['image' => 'File gambar tidak valid atau rusak.'])->withInput();
            }

            $this->safeDeleteFile($news->image);

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['image'] = $file->storeAs('news', $safeFileName, 'public');
            $imageChanged = true;
        }

        // 2. Eksekusi update
        DB::transaction(function () use ($news, $data) {
            $news->update($data);
        });

        // 3. Ambil snapshot data baru
        $news->load('category');
        $newCategoryName = $news->category?->name ?? '—';
        $newDate = $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('Y-m-d') : 'Draft';
        $newStatus = $news->is_published ? 'Published' : 'Draft';
        $newContentText = trim(preg_replace('/\s+/', ' ', strip_tags($news->content)));

        $newValues = [
            'Judul Berita'      => $news->title,
            'Kategori'          => $newCategoryName,
            'Tanggal Publikasi' => $newDate,
            'Status Publikasi'  => $newStatus,
            'Gambar Banner'     => $imageChanged ? 'Banner Diperbarui' : ($news->image ? 'Ada Banner' : 'Tanpa Banner'),
            'Isi Konten'        => Str::limit($newContentText, 70),
        ];

        // 4. Bandingkan semua field yang berubah
        $changes = [];
        $oldChanges = [];

        foreach ($newValues as $key => $newVal) {
            $oldVal = $oldValues[$key] ?? null;
            if ((string)$oldVal !== (string)$newVal) {
                $changes[$key] = $newVal;
                $oldChanges[$key] = $oldVal;
            }
        }

        // 5. Catat ke notifikasi lonceng navbar & database activity logs
        Notification::log(
            'Berita "' . $news->title . '" diperbarui.',
            'fa-newspaper',
            'maroon',
            route('admin.news.show', $news)
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

        $this->safeDeleteFile($news->image);

        DB::transaction(function () use ($news) {
            $news->delete();
        });

        Notification::log('Berita "' . $title . '" dihapus.', 'fa-trash-can', 'danger', route('admin.news.index'));

        ActivityLog::record($news, 'deleted', 'Menghapus berita: ' . $title, [
            'attributes' => $backupData,
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Berita "' . $title . '" berhasil dihapus.');
    }

    public function toggle(News $news)
    {
        $oldStatus = $news->is_published ? 'Published' : 'Draft';
        $news->update(['is_published' => ! $news->is_published]);
        $newStatus = $news->is_published ? 'Published' : 'Draft';

        $statusText = $news->is_published ? 'dipublikasikan.' : 'dijadikan draft.';

        Notification::log(
            'Berita "' . $news->title . '" ' . $statusText,
            $news->is_published ? 'fa-eye' : 'fa-eye-slash',
            $news->is_published ? 'success' : 'warning',
            route('admin.news.show', $news)
        );

        ActivityLog::record($news, 'updated', 'Mengubah status publikasi berita: ' . $news->title, [
            'old' => ['Status Publikasi' => $oldStatus],
            'new' => ['Status Publikasi' => $newStatus],
        ]);

        return back()->with('success', $news->is_published ? 'Berita dipublikasikan.' : 'Berita disimpan sebagai draft.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'content'      => ['required', 'string'],
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