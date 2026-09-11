<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        // Ambil baris data pertama. Jika tabel masih kosong, inisiasi object kosong agar view tidak error.
        $setting = SiteSetting::first() ?? new SiteSetting();

        return view('admin.site-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'contact_email'    => 'nullable|email|max:255',
            'contact_phone'    => 'nullable|string|max:50',
            'address'          => 'nullable|string|max:500',
            'instagram_link'   => 'nullable|url|max:255',
            'youtube_link'     => 'nullable|url|max:255',
            'linkedin_link'    => 'nullable|url|max:255',
            'tiktok_link'      => 'nullable|url|max:255',
        ]);

        // Menggunakan updateOrCreate jauh lebih aman dan efisien untuk data tunggal (singleton)
        // Method ini akan mencari data yang ada (berdasarkan kondisi array pertama), 
        // jika ada di-update, jika belum ada otomatis dibuatkan baris baru.
        SiteSetting::updateOrCreate(
            ['id' => 1], // Cari record dengan ID 1 (atau biarkan array kosong [] untuk mengambil baris pertama)
            $validated
        );

        return back()->with('success', 'Pengaturan situs berhasil diperbarui.');
    }
}