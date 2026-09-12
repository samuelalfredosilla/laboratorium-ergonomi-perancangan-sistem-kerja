<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\HomeSlider;
use App\Models\Lecturer;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Agregasi Efisien & Terproteksi (Lecturers)
        $lecturerStats = Lecturer::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN LOWER(role) LIKE '%chief%' OR LOWER(role) LIKE '%head%' OR LOWER(role) LIKE '%kepala%' THEN 1 ELSE 0 END) as heads
        ")->first();

        $lecturerTotal = (int) ($lecturerStats->total ?? 0);
        $lecturerHeads = (int) ($lecturerStats->heads ?? 0);

        // 2. Agregasi Efisien & Terproteksi (News)
        $newsStats = News::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published
        ")->first();

        $newsTotal = (int) ($newsStats->total ?? 0);
        $newsPublished = (int) ($newsStats->published ?? 0);

        // 3. Agregasi Efisien & Terproteksi (Sliders)
        $sliderStats = HomeSlider::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
        ")->first();

        $sliderTotal = (int) ($sliderStats->total ?? 0);
        $sliderActive = (int) ($sliderStats->active ?? 0);

        // 4. Asisten Lab & Periode Aktif
        $latestPeriod = Assistant::max('period') ?? '2025/2026';
        $assistantTotal = Assistant::count();
        $assistantActive = Assistant::where('period', $latestPeriod)->count();

        $stats = [
            'lecturers' => [
                'total'   => $lecturerTotal,
                'head'    => $lecturerHeads,
                'members' => max(0, $lecturerTotal - $lecturerHeads),
            ],
            'news' => [
                'total'     => $newsTotal,
                'published' => $newsPublished,
                'draft'     => max(0, $newsTotal - $newsPublished),
            ],
            'sliders' => [
                'total'    => $sliderTotal,
                'active'   => $sliderActive,
                'inactive' => max(0, $sliderTotal - $sliderActive),
            ],
            'assistants' => [
                'total'         => $assistantTotal,
                'active'        => $assistantActive,
                'latest_period' => e($latestPeriod),
            ],
        ];

        // 5. Berita Terbaru dengan Sanitasi Output
        $latestNews = News::with(['category:id,name', 'author:id,name'])
            ->select(['id', 'title', 'slug', 'image', 'category_id', 'user_id', 'is_published', 'published_at'])
            ->latest('published_at')
            ->take(4)
            ->get()
            ->map(function (News $item) {
                return (object) [
                    'id'        => (int) $item->id,
                    'title'     => e($item->title),
                    'image'     => $item->image_url,
                    'category'  => e($item->category->name ?? 'Umum'),
                    'author'    => e($item->author->name ?? 'Admin EPSK'),
                    'date'      => $item->published_at ? $item->published_at->translatedFormat('d M Y') : '—',
                    'published' => (bool) $item->is_published,
                ];
            });

        // 6. Ambil data dari model SiteSetting yang konsisten
        $setting = SiteSetting::first();

        $contact = [
            'address' => $setting && $setting->address ? strip_tags($setting->address) : '—',
            'email'   => $setting && $setting->contact_email ? filter_var($setting->contact_email, FILTER_SANITIZE_EMAIL) : '—',
            'phone'   => $setting && $setting->contact_phone ? strip_tags($setting->contact_phone) : 'Belum diatur',
            'status'  => $setting && isset($setting->lab_status) ? strip_tags($setting->lab_status) : 'online',
        ];

        return view('admin.dashboard', compact('stats', 'latestNews', 'contact'));
    }
}