<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('news')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['name'] === $category->name
                ? $category->slug
                : $this->uniqueSlug($validated['name'], $category->id),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $validated['name'] . '" berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $newsCount = $category->news()->count();
        $category->delete();

        $message = $newsCount > 0
            ? "Kategori \"{$name}\" beserta {$newsCount} berita di dalamnya berhasil dihapus."
            : "Kategori \"{$name}\" berhasil dihapus.";

        return redirect()->route('admin.categories.index')->with('success', $message);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $attempt = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . ++$attempt;
        }

        return $slug;
    }
}
