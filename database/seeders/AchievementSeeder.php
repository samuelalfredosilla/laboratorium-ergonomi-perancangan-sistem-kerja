<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'title'         => 'Juara 1 Lomba Desain Produk Ergonomis Nasional',
                'level'         => 'Nasional',
                'achiever_name' => 'Tim EPSIKERS Alpha (Budi, Siti, Andi)',
                'date_achieved' => Carbon::parse('2025-10-15'),
                'description'   => 'Berhasil marancang inovasi stasiun kerja yang ergonomis untuk pekerja UMKM. Desain ini terbukti mampu menurunkan keluhan Musculoskeletal Disorders (MSDs) sebesar 45%.',
                'photo'         => null,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'title'         => 'Best Paper Award in International Ergonomics Conference',
                'level'         => 'Internasional',
                'achiever_name' => 'Tim Riset EPSK (Rina & Dimas)',
                'date_achieved' => Carbon::parse('2026-02-20'),
                'description'   => 'Publikasi paper ilmiah yang membahas "Analisis Beban Kerja Fisik Menggunakan Metode RULA dan REBA pada Pekerja Konstruksi" memenangkan penghargaan Best Paper di ajang internasional.',
                'photo'         => null,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'title'         => 'Asisten Laboratorium Berprestasi Tingkat Fakultas',
                'level'         => 'Fakultas / Universitas',
                'achiever_name' => 'Muhammad Reza',
                'date_achieved' => Carbon::parse('2025-12-10'),
                'description'   => 'Penghargaan tahunan yang diberikan oleh Fakultas Teknik kepada asisten laboratorium dengan kinerja, dedikasi, dan kontribusi riset terbaik selama satu periode akademik.',
                'photo'         => null,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('achievements')->insert($achievements);
    }
}