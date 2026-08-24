<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganizationStructureController extends Controller
{
    public function index()
    {
        $structure = OrganizationStructure::first();
        return view('admin.organization-structure.index', compact('structure'));
    }

    public function update(Request $request)
    {
        $structure = OrganizationStructure::first();

        // 1. Sanitasi & Validasi Ketat
        $request->validate([
            'title' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s\-\/\(\)\.\,\&]+$/'],
            'image' => [
                $structure ? 'nullable' : 'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192', // Maksimal 8MB
                'dimensions:max_width=6000,max_height=6000',
            ],
        ], [
            'title.required'     => 'Judul struktur organisasi wajib diisi.',
            'title.regex'        => 'Judul mengandung karakter ilegal yang tidak diperbolehkan.',
            'image.required'     => 'File gambar bagan struktur wajib diunggah.',
            'image.image'        => 'File harus berupa gambar valid.',
            'image.mimes'        => 'Format yang diizinkan hanya JPG, PNG, dan WebP.',
            'image.mimetypes'    => 'Tipe konten file tidak valid.',
            'image.max'          => 'Ukuran file gambar maksimal 8 MB.',
            'image.dimensions'   => 'Dimensi gambar terlalu besar (maksimal 6000x6000 px).',
        ]);

        $sanitizedTitle = strip_tags(trim($request->input('title')));

        $data = [
            'title' => $sanitizedTitle,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if (! $file->isValid()) {
                return back()->withErrors(['image' => 'File unggahan rusak atau tidak valid.']);
            }

            // Hapus file lama secara aman
            if ($structure && $structure->image_path) {
                $this->safeDeleteFile($structure->image_path);
            }

            // Generate nama file acak
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            
            $path = $file->storeAs('organization', $safeFileName, 'public');
            $data['image_path'] = $path;
        }

        if ($structure) {
            $structure->update($data);
        } else {
            OrganizationStructure::create($data);
        }

        // 2. Bersihkan Cache Publik agar update gambar langsung tampil
        Cache::forget('lab_org_structure');

        return back()->with('success', 'Bagan struktur organisasi berhasil diperbarui secara aman.');
    }

    public function destroy()
    {
        $structure = OrganizationStructure::first();

        if ($structure) {
            if ($structure->image_path) {
                $this->safeDeleteFile($structure->image_path);
            }
            $structure->delete();
        }

        // Bersihkan Cache Publik
        Cache::forget('lab_org_structure');

        return back()->with('success', 'Bagan struktur organisasi berhasil dihapus.');
    }

    /**
     * Mencegah Path Traversal saat penghapusan file
     */
    private function safeDeleteFile(string $path): void
    {
        if (str_starts_with($path, 'organization/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}