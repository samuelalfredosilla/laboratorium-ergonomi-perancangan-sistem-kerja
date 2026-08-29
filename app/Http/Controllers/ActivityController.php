<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Menampilkan Halaman Utama Activities (Hub/Overview)
     */
    public function practicum()
    {
        $tasks = \App\Models\PracticumTask::latest('uploaded_at')->get();
        return view('activities.practicum', compact('tasks'));
    }

    /**
     * Menampilkan Halaman Utama events (Hub/Overview)
     */
    public function events()
    {
        return view('activities.events');
    }
    
}