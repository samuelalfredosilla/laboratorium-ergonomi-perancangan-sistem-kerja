<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\PracticumTask;
use App\Models\LaboratoryEvent;
use App\Models\News;
use App\Models\Achievement;
use App\Models\Lecturer;

class SitemapController extends Controller
{
    public function index()
    {
        // 1. Halaman Statis Utama
        $urls = [
            url('/'),
            url('/activities/practicum'),
            url('/activities/events'),
            url('/facilities/equipment'),
            url('/about/overview'),
            url('/about/lecturer'),
            url('/about/epsikers'),
            url('/about/structure'),
            url('/achievement'),
            url('/lab-procedures'),
            url('/news'),
        ];

        // 2. Ambil Halaman Dinamis dari Database (Tugas Praktikum)
        $practicums = PracticumTask::all();
        foreach ($practicums as $item) {
            $urls[] = url('/activities/practicum/' . $item->id);
        }

        // 3. Ambil Halaman Dinamis dari Database (Events)
        $events = LaboratoryEvent::all();
        foreach ($events as $item) {
            $urls[] = url('/activities/events/' . $item->id);
        }

        // 4. Ambil Halaman Dinamis dari Database (News)
        $news = News::all();
        foreach ($news as $item) {
            $urls[] = url('/news/' . $item->slug ?? $item->id);
        }

        // 5. Ambil Halaman Dinamis dari Database (Prestasi & Dosen)
        $achievements = Achievement::all();
        foreach ($achievements as $item) {
            $urls[] = url('/achievement/' . $item->id);
        }

        $lecturers = Lecturer::all();
        foreach ($lecturers as $item) {
            $urls[] = url('/about/lecturer/' . $item->id);
        }

        // Render ke format XML
        return response()->view('sitemap', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }
}
