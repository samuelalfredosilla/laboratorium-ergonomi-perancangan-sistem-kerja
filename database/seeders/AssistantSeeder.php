<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssistantSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'Koordinator Lab',
            'Sekretaris & Bendahara',
            'Divisi Praktikum',
            'Divisi Riset & Publikasi',
            'Divisi Inventaris & Alat',
            'Divisi Media & Humas'
        ];

        $names = [
            'Ahmad Fauzi', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Lestari',
            'Rizky Pratama', 'Putri Ayu Wandira', 'Dimas Anggara', 'Anisa Rahmawati',
            'Fajar Hidayat', 'Nadia Safitri', 'Bayu Setiawan', 'Mega Utami',
            'Hendra Wijaya', 'Rina Marlina', 'Galih Permana', 'Tiara Andini'
        ];

        // Membersihkan tabel sebelum diisi ulang
        DB::table('assistants')->truncate();

        foreach ($names as $index => $name) {
            $i = $index + 1;

            // Penentuan divisi agar terdistribusi rapi
            if ($i === 1) {
                $div = $divisions[0]; // Koordinator
            } elseif ($i === 2) {
                $div = $divisions[1]; // Sekretaris
            } else {
                $div = $divisions[2 + (($i - 3) % 4)];
            }

            DB::table('assistants')->insert([
                'name' => $name,
                'nim' => '2204811000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'division' => $div,
                'email' => strtolower(str_replace(' ', '', $name)) . '@student.trunojoyo.ac.id',
                'photo' => null, // Akan otomatis memakai avatar initial jika null
                'sort_order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
