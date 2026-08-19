<?php

namespace App\Http\Controllers;

use App\Models\AssistantPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Assistant;

class AboutController extends Controller
{
    /**
     * Menampilkan Halaman Overview (History, Visi, Misi, dll)
     */
    public function overview()
    {
        return view('about.overview');
    }

    /**
     * Menampilkan Halaman Lecturer
     */
    public function lecturers()
    {
        // Menampilkan daftar dosen dari database
        $lecturers = DB::table('lecturers')
            ->orderBy('name', 'asc')
            ->get();

        return view('about.lecturer', compact('lecturers'));
    }

    // Menampilkan Detail Dosen beserta Riwayat Pendidikan, Penelitian, & Pengabdian
    public function lecturerDetail($id)
    {
        $lecturer = DB::table('lecturers')->where('id', $id)->first();

        if (!$lecturer) {
            abort(404);
        }

        $educations = DB::table('lecturer_educations')
            ->where('lecturer_id', $id)
            ->get();

        $researches = DB::table('lecturer_researches')
            ->where('lecturer_id', $id)
            ->get();

        $communityServices = DB::table('lecturer_community_services')
            ->where('lecturer_id', $id)
            ->get();

        return view('about.lecturer-detail', compact('lecturer', 'educations', 'researches', 'communityServices'));
    }

    /**
     * Menampilkan Halaman Asisten Lab (EPSIKERS) dari Database
     */
    public function epsikers()
    {
        // Ambil periode yang diaktifkan oleh admin di panel kelola periode
        $activePeriod = AssistantPeriod::where('is_active', true)->first()
            ?? AssistantPeriod::orderBy('name', 'desc')->first();

        $selectedPeriod = $activePeriod ? $activePeriod->name : '2025/2026';

        // Ambil asisten pada periode aktif tersebut
        $assistants = Assistant::where('period', $selectedPeriod)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('about.epsikers', compact('assistants', 'selectedPeriod'));
    }
}
