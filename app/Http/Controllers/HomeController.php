<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(): View
    {
        // Mock Data untuk Latest News (Dapat diganti Eloquent Query nantinya)
        $latestNews = [
            [
                'title' => 'OHS Training 2024: Occupational Health & Safety Standard in Ergonomics',
                'category' => 'Training',
                'author' => 'Admin EWDPI',
                'date' => '12 Oct 2024',
                'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=600&q=80',
                'slug' => 'ohs-training-2024'
            ],
            [
                'title' => 'Sketchup & 3D Modeling Workshop for Product Innovation',
                'category' => 'Workshop',
                'author' => 'Admin EWDPI',
                'date' => '05 Nov 2024',
                'image' => 'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?auto=format&fit=crop&w=600&q=80',
                'slug' => 'sketchup-training'
            ],
            [
                'title' => 'My Magnum Opus: Industrial Design Exhibition & Final Showcase',
                'category' => 'Exhibition',
                'author' => 'Admin EWDPI',
                'date' => '20 Dec 2024',
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=600&q=80',
                'slug' => 'my-magnum-opus'
            ],
        ];

        return view('home', compact('latestNews'));
    }
}
