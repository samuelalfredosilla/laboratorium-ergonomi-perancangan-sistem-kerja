<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doc1 = DB::table('lecturers')->insertGetId([
            'name' => 'Nama Dosen Utama, S.T., M.T.',
            'nip' => '19850101 201012 1 001',
            'role' => 'Head of Lab',
            'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
            'expertise' => 'Physical Ergonomics & Biomechanics',
            'email' => 'dosen.utama@trunojoyo.ac.id',
            'scholar_link' => 'https://scholar.google.com',
            'linkedin_link' => 'https://linkedin.com',
            'sort_order' => 1,
            'created_at' => now(), 'updated_at' => now()
        ]);

        DB::table('lecturer_educations')->insert([
            ['lecturer_id' => $doc1, 'degree' => 'S1 - Teknik Industri', 'institution' => 'Universitas Brawijaya', 'year_range' => '2003 - 2007', 'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $doc1, 'degree' => 'S2 - Teknik Industri', 'institution' => 'Institut Teknologi Sepuluh Nopember (ITS)', 'year_range' => '2008 - 2010', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('lecturer_researches')->insert([
            ['lecturer_id' => $doc1, 'title' => 'Analisis Beban Kerja Fisik dan Postur Kerja Operator Industri Batik Madura Menggunakan Metode REBA.', 'year' => '2023', 'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $doc1, 'title' => 'Perancangan Alat Bantu Ergonomis Berbasis Potensi Lokal untuk Mengurangi Risk Of Musculoskeletal Disorders.', 'year' => '2024', 'created_at' => now(), 'updated_at' => now()]
        ]);

        DB::table('lecturer_community_services')->insert([
            ['lecturer_id' => $doc1, 'title' => 'Penerapan Prinsip Ergonomi pada UKM Pengrajin Gerabah di Kabupaten Bangkalan.', 'year' => '2023', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
