<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // 1. Sanitasi & Pembatasan Karakter Input
        $rawQuery = trim((string) $request->input('q', ''));
        $cleanQuery = strip_tags($rawQuery);

        if (mb_strlen($cleanQuery) < 2 || mb_strlen($cleanQuery) > 80) {
            return response()->json(['lecturers' => [], 'news' => [], 'menu' => []]);
        }

        // Escape karakter wildcard SQL (%) dan (_)
        $escapedQuery = addcslashes($cleanQuery, '%_');

        // 2. Query Lecturer Terproteksi
        $lecturers = Lecturer::query()
            ->select(['id', 'name', 'role', 'photo', 'nip', 'expertise'])
            ->where(function ($q) use ($escapedQuery) {
                $q->where('name', 'like', "%{$escapedQuery}%")
                    ->orWhere('nip', 'like', "%{$escapedQuery}%")
                    ->orWhere('expertise', 'like', "%{$escapedQuery}%");
            })
            ->limit(5)
            ->get()
            ->map(fn (Lecturer $lecturer) => [
                'id'       => (int) $lecturer->id,
                'name'     => e($lecturer->name),
                'subtitle' => e($lecturer->role ?? 'Dosen / Staf'),
                'photo'    => $lecturer->photo_url,
                'url'      => route('admin.lecturers.show', $lecturer),
            ]);

        // 3. Query News Terproteksi
        $news = News::query()
            ->select(['id', 'title', 'slug', 'is_published'])
            ->where('title', 'like', "%{$escapedQuery}%")
            ->limit(5)
            ->get()
            ->map(fn (News $item) => [
                'id'       => (int) $item->id,
                'title'    => e($item->title),
                'subtitle' => $item->is_published ? 'Published' : 'Draft',
                'url'      => Route::has('admin.news.show') 
                                ? route('admin.news.show', $item) 
                                : (Route::has('admin.news.edit') ? route('admin.news.edit', $item) : route('admin.news.index')),
            ]);

        // 4. Filter Navigasi Menu
        $menuItems = [
            ['label' => 'Dashboard Overview', 'icon' => 'fa-gauge-high', 'url' => route('admin.dashboard')],
            ['label' => 'Lecturers & Staff', 'icon' => 'fa-users', 'url' => route('admin.lecturers.index')],
            ['label' => 'News & Articles', 'icon' => 'fa-newspaper', 'url' => route('admin.news.index')],
            ['label' => 'Categories', 'icon' => 'fa-tags', 'url' => route('admin.categories.index')],
            ['label' => 'Home Sliders', 'icon' => 'fa-images', 'url' => route('admin.sliders.index')],
            ['label' => 'Organization Structure', 'icon' => 'fa-sitemap', 'url' => route('admin.organization-structure.index')],
        ];

        $menu = collect($menuItems)
            ->filter(fn ($item) => Str::contains($item['label'], $cleanQuery, true))
            ->values();

        return response()->json([
            'lecturers' => $lecturers,
            'news'      => $news,
            'menu'      => $menu,
        ]);
    }
}