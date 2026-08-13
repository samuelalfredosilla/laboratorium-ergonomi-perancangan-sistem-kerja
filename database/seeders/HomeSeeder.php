<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories
        $cat1 = DB::table('categories')->insertGetId([
            'name' => 'Research',
            'slug' => 'research',
            'created_at' => now(), 'updated_at' => now()
        ]);
        $cat2 = DB::table('categories')->insertGetId([
            'name' => 'Workshop',
            'slug' => 'workshop',
            'created_at' => now(), 'updated_at' => now()
        ]);

        // 2. Seed Home Sliders
        DB::table('home_sliders')->insert([
            [
                'title' => 'Laboratory Team 1',
                'image_path' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1000&q=80',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'title' => 'Laboratory Practicum 2',
                'image_path' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1000&q=80',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'title' => 'Research Activity 3',
                'image_path' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1000&q=80',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 3. Seed News
        DB::table('news')->insert([
            [
                'category_id' => $cat1,
                'title' => 'Riset Postur Kerja Ergonomis Industri Batik Madura',
                'slug' => Str::slug('Riset Postur Kerja Ergonomis Industri Batik Madura'),
                'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80',
                'content' => 'Tim laboratorium melakukan analisis biomekanika pada perajin batik lokal di Bangkalan.',
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'category_id' => $cat2,
                'title' => 'Workshop Software Biomekanika & Simulasi Ergonomi',
                'slug' => Str::slug('Workshop Software Biomekanika Simulasi Ergonomi'),
                'image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80',
                'content' => 'Pelatihan penggunaan software simulasi ergonomi untuk mahasiswa Teknik Industri UTM.',
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 4. Seed Settings
        DB::table('settings')->insert([
            ['key' => 'contact_address', 'value' => 'Perumahan Telang Indah, Telang, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'epsk.trunojoyo@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_linkedin', 'value' => 'https://www.linkedin.com/company/laboratorium-ergonomi-dan-perancangan-sistem-kerja/', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_instagram', 'value' => 'https://www.instagram.com/epsk.trunojoyo', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_tiktok', 'value' => 'https://www.tiktok.com/@epsk.trunojoyo', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@lab.epsktrunojoyo6071', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
