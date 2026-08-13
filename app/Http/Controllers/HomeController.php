<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // 1. Ambil data slider yang aktif
        $sliders = DB::table('home_sliders')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        // 2. Ambil berita terbaru (join dengan tabel categories)
        $latestNews = DB::table('news')
            ->join('categories', 'news.category_id', '=', 'categories.id')
            ->select('news.*', 'categories.name as category_name')
            ->where('news.is_published', true)
            ->orderBy('news.published_at', 'desc')
            ->take(3)
            ->get();

        // 3. Ambil data settings kontak & sosial media
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        return view('home', compact('sliders', 'latestNews', 'settings'));
    }
}
