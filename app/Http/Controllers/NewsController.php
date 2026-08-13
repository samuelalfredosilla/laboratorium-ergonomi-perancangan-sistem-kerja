<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $newsList = News::with('category')
            ->where('is_published', true)
            ->latest('published_at')
            ->paginate(9);

        return view('news.index', compact('newsList'));
    }

    public function show(News $news)
    {
        abort_unless($news->is_published, 404);

        $news->load(['category', 'author']);

        return view('news.show', compact('news'));
    }
}
