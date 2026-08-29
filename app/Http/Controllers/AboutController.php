<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\AssistantPeriod;
use App\Models\OrganizationStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $lecturers = DB::table('lecturers')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('about.lecturer', compact('lecturers'));
    }

    /**
     * Menampilkan Detail Dosen beserta Riwayat Pendidikan, Penelitian, & Pengabdian
     */
    public function lecturerDetail($id)
    {
        $lecturer = DB::table('lecturers')->where('id', $id)->first();

        if (! $lecturer) {
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
        $activePeriod = AssistantPeriod::where('is_active', true)->first()
            ?? AssistantPeriod::orderBy('name', 'desc')->first();

        $selectedPeriod = $activePeriod ? $activePeriod->name : '2025/2026';

        $assistants = Assistant::where('period', $selectedPeriod)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('about.epsikers', compact('assistants', 'selectedPeriod'));
    }

    /**
     * Menampilkan Halaman Struktur Organisasi Publik (Cached 24 Jam)
     */
    public function structure()
    {
        $structure = Cache::remember('lab_org_structure', 86400, function () {
            return OrganizationStructure::first();
        });

        return view('about.structure', compact('structure'));
    }
}