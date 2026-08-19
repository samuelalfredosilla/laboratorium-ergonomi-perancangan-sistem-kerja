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
    public function index(Request $request)
    {
        $news = News::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
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
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news = News::create($data);

        Notification::log('Berita baru "' . $news->title . '" ditambahkan.', 'fa-newspaper', 'success', route('admin.news.index'));

        ActivityLog::record($news, 'created', 'Menambahkan berita baru: ' . $news->title, [
            'attributes' => $news->toArray(),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Berita "' . $news->title . '" berhasil disimpan.');
    }

    public function update(Request $request, News $news)
    {
        $data = $this->validatedFields($request);

        if ($data['title'] !== $news->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $news->id);
        }

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $oldValues = $news->getOriginal();
        $news->update($data);
        $changes = $news->getChanges();
        $oldChanges = array_intersect_key($oldValues, $changes);

        Notification::log('Berita "' . $news->title . '" diperbarui.', 'fa-newspaper', 'maroon', route('admin.news.index'));

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

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

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
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'published_at' => ['nullable', 'date'],
        ]);

        unset($data['image']);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->filled('published_at') ? $request->input('published_at') : now();

        return $data;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $attempt = 1;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . ++$attempt;
        }

        return $slug;
    }
}
