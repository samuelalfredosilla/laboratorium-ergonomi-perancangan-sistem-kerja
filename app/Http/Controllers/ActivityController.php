<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaboratoryEvent;
use App\Models\PracticumTask;

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
        // Mengambil data dari database, diurutkan dari yang terbaru berdasarkan tanggal upload
        $events = \App\Models\LaboratoryEvent::latest('uploaded_at')->get();

        return view('activities.events', compact('events'));
    }

    public function eventDetail($id)
    {
        // Cari event berdasarkan ID, jika tidak ada akan muncul error 404
        $event = LaboratoryEvent::findOrFail($id);
        
        // Return ke file view baru
        return view('activities.detail-event', compact('event'));
    }

    public function practicumDetail($id)
    {
        // Cari event berdasarkan ID, jika tidak ada akan muncul error 404
        $task = PracticumTask::findOrFail($id);
        
        // Return ke file view baru
        return view('activities.detail-practicum', compact('task'));
    }
}
