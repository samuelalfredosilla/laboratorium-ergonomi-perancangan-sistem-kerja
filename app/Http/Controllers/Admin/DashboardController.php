<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Models\Lecturer;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lecturerTotal = Lecturer::count();
        $lecturerHeads = Lecturer::where(function ($query) {
            $query->where('role', 'like', '%chief%')
                ->orWhere('role', 'like', '%head%')
                ->orWhere('role', 'like', '%kepala%');
        })->count();

        $newsTotal = News::count();
        $newsPublished = News::where('is_published', true)->count();

        $sliderTotal = HomeSlider::count();
        $sliderActive = HomeSlider::where('is_active', true)->count();

        $stats = [
            'lecturers' => [
                'total' => $lecturerTotal,
                'head' => $lecturerHeads,
                'members' => $lecturerTotal - $lecturerHeads,
            ],
            'news' => [
                'total' => $newsTotal,
                'published' => $newsPublished,
                'draft' => $newsTotal - $newsPublished,
            ],
            'sliders' => [
                'total' => $sliderTotal,
                'active' => $sliderActive,
                'inactive' => $sliderTotal - $sliderActive,
            ],
            // EPSIKERS / Assistants module isn't built yet, so this stat has no table to count from.
            'assistants' => ['total' => 15, 'active' => 12],
        ];

        $latestNews = News::with(['category', 'author'])
            ->latest('published_at')
            ->take(4)
            ->get()
            ->map(fn (News $news) => (object) [
                'title' => $news->title,
                'image' => $news->image_url,
                'category' => $news->category->name ?? 'Umum',
                'author' => $news->author->name ?? 'Admin EPSK',
                'date' => $news->published_at?->translatedFormat('d M Y') ?? '—',
                'published' => $news->is_published,
            ]);

        $settings = DB::table('settings')->pluck('value', 'key');
        $contact = [
            'address' => $settings['contact_address'] ?? '—',
            'email' => $settings['contact_email'] ?? '—',
            'phone' => $settings['contact_phone'] ?? 'Belum diatur',
        ];

        return view('admin.dashboard', compact('stats', 'latestNews', 'contact'));
    }
}
