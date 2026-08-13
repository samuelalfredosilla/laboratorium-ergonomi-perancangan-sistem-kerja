<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'lecturers' => ['total' => 8, 'head' => 1, 'members' => 7],
            'news' => ['total' => 24, 'published' => 19, 'draft' => 5],
            'sliders' => ['total' => 6, 'active' => 4, 'inactive' => 2],
            'assistants' => ['total' => 15, 'active' => 12],
        ];

        $latestNews = [
            (object) [
                'title' => 'Seminar Nasional Ergonomi & K3 2026 Resmi Dibuka',
                'image' => asset('images/1.jpeg'),
                'category' => 'Kegiatan',
                'author' => 'Admin EPSK',
                'date' => '12 Agu 2026',
                'published' => true,
            ],
            (object) [
                'title' => 'Kunjungan Industri Mahasiswa ke PT Semen Indonesia',
                'image' => asset('images/2.jpeg'),
                'category' => 'Kunjungan',
                'author' => 'Dr. Anisa R.',
                'date' => '08 Agu 2026',
                'published' => true,
            ],
            (object) [
                'title' => 'Publikasi Riset Ergonomi Kognitif Tembus Jurnal Q1',
                'image' => asset('images/1.jpeg'),
                'category' => 'Riset',
                'author' => 'Prof. Bambang S.',
                'date' => '02 Agu 2026',
                'published' => false,
            ],
            (object) [
                'title' => 'Pelatihan Postur Kerja untuk UMKM Bangkalan',
                'image' => asset('images/2.jpeg'),
                'category' => 'Pengabdian',
                'author' => 'Admin EPSK',
                'date' => '28 Jul 2026',
                'published' => true,
            ],
        ];

        $contact = [
            'address' => 'Fakultas Teknik, Universitas Trunojoyo Madura, Bangkalan, Jawa Timur',
            'email' => 'epsk.lab@trunojoyo.ac.id',
            'phone' => '(031) 3011146',
        ];

        return view('admin.dashboard', compact('stats', 'latestNews', 'contact'));
    }
}
