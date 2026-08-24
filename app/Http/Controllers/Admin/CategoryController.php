<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Daftar kata slug yang dilindungi sistem agar tidak bentrok dengan rute Laravel
     */
    private const RESERVED_SLUGS = [
        'admin', 'api', 'login', 'logout', 'dashboard', 'category', 'categories',
        'create', 'edit', 'delete', 'update', 'show', 'all', 'public'
    ];

    public function index(Request $request)
    {
        $categories = Category::withCount('news')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Sanitasi Awal Input String
        $sanitizedName = strip_tags(trim($request->input('name', '')));
        $request->merge(['name' => $sanitizedName]);

        // 2. Validasi Ketat Karakter & Keunikan
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:60',
                'regex:/^[a-zA-Z0-9\s\-\&\/\,\(\)\.]+$/',
                'unique:categories,name',
            ],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.min'      => 'Nama kategori minimal 2 karakter.',
            'name.max'      => 'Nama kategori maksimal 60 karakter.',
            'name.regex'    => 'Nama kategori hanya boleh memuat huruf, angka, spasi, dan tanda baca umum.',
            'name.unique'   => 'Kategori dengan nama tersebut sudah ada.',
        ]);

        $slug = $this->uniqueSlug($validated['name']);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        Notification::log(
            'Kategori "' . $category->name . '" ditambahkan.',
            'fa-tags',
            'success',
            route('admin.categories.index')
        );

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        // 1. Sanitasi Awal Input String
        $sanitizedName = strip_tags(trim($request->input('name', '')));
        $request->merge(['name' => $sanitizedName]);

        // 2. Validasi Ketat dengan Pengecualian ID Kategori Saat Ini
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:60',
                'regex:/^[a-zA-Z0-9\s\-\&\/\,\(\)\.]+$/',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.min'      => 'Nama kategori minimal 2 karakter.',
            'name.max'      => 'Nama kategori maksimal 60 karakter.',
            'name.regex'    => 'Nama kategori hanya boleh memuat huruf, angka, spasi, dan tanda baca umum.',
            'name.unique'   => 'Kategori dengan nama tersebut sudah digunakan.',
        ]);

        $newSlug = $validated['name'] === $category->name
            ? $category->slug
            : $this->uniqueSlug($validated['name'], $category->id);

        $category->update([
            'name' => $validated['name'],
            'slug' => $newSlug,
        ]);

        Notification::log(
            'Kategori "' . $category->name . '" diperbarui.',
            'fa-tags',
            'maroon',
            route('admin.categories.index')
        );

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $newsCount = $category->news()->count();

        // 3. Penghapusan Atomik Aman Melalui DB Transaction
        DB::transaction(function () use ($category) {
            $category->delete();
        });

        $message = $newsCount > 0
            ? "Kategori \"{$name}\" beserta {$newsCount} berita di dalamnya berhasil dihapus."
            : "Kategori \"{$name}\" berhasil dihapus.";

        Notification::log($message, 'fa-trash-can', 'danger', route('admin.categories.index'));

        return redirect()->route('admin.categories.index')->with('success', $message);
    }

    /**
     * Membangun slug URL unik yang aman dari collision dan kata kunci sistem
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);

        // Fallback jika nama kategori hanya berisi karakter non-alfanumerik
        if (empty($base)) {
            $base = 'kategori-' . Str::random(6);
        }

        // Hindari bentrok dengan reserved keyword routing
        if (in_array($base, self::RESERVED_SLUGS, true)) {
            $base = $base . '-lab';
        }

        $slug = $base;
        $attempt = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$attempt);
        }

        return $slug;
    }
}