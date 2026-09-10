<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;

class AchievementController extends Controller
{
    public function index()
    {
        // 1. ambil data dari database achievements
        $achievements = Achievement::query()
            ->where('is_active', true) // hanya ambil yang aktif
            ->orderBy('date_achieved', 'desc') // urutkan berdasarkan tanggal pencapaian terbaru
            ->get();

        // 2. tampilkan view achievements.index dengan data achievements
        return view('achievement.index', compact('achievements'));
    } 

    public function show($id){
        // cari data berdasarkan id, jika tidak ada atau (disembunyikan admin) tampilkan 404
        $achievement = Achievement::where('is_active', true)->findOrFail($id);

        return view('achievement.detail-achievement', compact('achievement'));
    }
}
