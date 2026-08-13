<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['lecturers' => [], 'news' => [], 'menu' => []]);
        }

        $lecturers = Lecturer::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('nip', 'like', "%{$query}%")
                    ->orWhere('expertise', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(fn (Lecturer $lecturer) => [
                'id' => $lecturer->id,
                'name' => $lecturer->name,
                'subtitle' => $lecturer->role,
                'photo' => $lecturer->photo_url,
                'url' => route('admin.lecturers.show', $lecturer),
            ]);

        $news = News::query()
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(fn (News $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->is_published ? 'Published' : 'Draft',
                'url' => route('admin.news.edit', $item),
            ]);

        $menuItems = [
            ['label' => 'Dashboard Overview', 'icon' => 'fa-gauge-high', 'url' => route('admin.dashboard')],
            ['label' => 'Lecturers & Staff', 'icon' => 'fa-users', 'url' => route('admin.lecturers.index')],
            ['label' => 'News & Articles', 'icon' => 'fa-newspaper', 'url' => route('admin.news.index')],
            ['label' => 'Categories', 'icon' => 'fa-tags', 'url' => route('admin.categories.index')],
            ['label' => 'Home Sliders', 'icon' => 'fa-images', 'url' => route('admin.sliders.index')],
        ];

        $menu = collect($menuItems)
            ->filter(fn ($item) => Str::contains($item['label'], $query, true))
            ->values();

        return response()->json([
            'lecturers' => $lecturers,
            'news' => $news,
            'menu' => $menu,
        ]);
    }
}
