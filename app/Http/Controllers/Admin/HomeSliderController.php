<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeSliderController extends Controller
{
    public function index(Request $request)
    {
        $sliders = HomeSlider::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Ketat (MIME sniffing, Dimensi Pixel Flood, Ukuran)
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s\-\/\(\)\.\,\&]+$/'],
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192', // Maksimal 8MB
                'dimensions:max_width=6000,max_height=6000',
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.regex'       => 'Judul mengandung karakter ilegal yang tidak diperbolehkan.',
            'image.required'    => 'File gambar banner wajib diunggah.',
            'image.image'       => 'File harus berupa gambar valid.',
            'image.mimes'       => 'Format file yang diizinkan hanya JPG, PNG, dan WebP.',
            'image.mimetypes'   => 'Tipe konten file tidak valid.',
            'image.max'         => 'Ukuran banner maksimal 8 MB.',
            'image.dimensions'  => 'Dimensi gambar banner terlalu besar (maksimal 6000x6000 px).',
        ]);

        $file = $request->file('image');

        // 2. Verifikasi Integritas File
        if (! $file->isValid()) {
            return back()->withErrors(['image' => 'File gambar rusak atau gagal diunggah.'])->withInput();
        }

        // 3. Penamaan File Acak Aman (Mencegah Directory Traversal & Shell Overwrite)
        $extension = $file->getClientOriginalExtension();
        $safeFileName = Str::random(40) . '.' . strtolower($extension);
        $path = $file->storeAs('sliders', $safeFileName, 'public');

        // 4. Sanitasi Input Title
        $cleanTitle = isset($validated['title']) ? strip_tags(trim($validated['title'])) : null;

        $slider = HomeSlider::create([
            'title'      => $cleanTitle,
            'image_path' => $path,
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => (int) HomeSlider::max('sort_order') + 1,
        ]);

        Notification::log(
            'Slider "' . ($slider->title ?: 'Tanpa judul') . '" ditambahkan.',
            'fa-images',
            'success',
            route('admin.sliders.index')
        );

        return redirect()->route('admin.sliders.index')->with('success', 'Banner slider berhasil ditambahkan secara aman.');
    }

    public function update(Request $request, HomeSlider $slider)
    {
        // 1. Validasi Ketat Update
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s\-\/\(\)\.\,\&]+$/'],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192',
                'dimensions:max_width=6000,max_height=6000',
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.regex'       => 'Judul mengandung karakter ilegal yang tidak diperbolehkan.',
            'image.image'       => 'File harus berupa gambar valid.',
            'image.mimes'       => 'Format file yang diizinkan hanya JPG, PNG, dan WebP.',
            'image.mimetypes'   => 'Tipe konten file tidak valid.',
            'image.max'         => 'Ukuran banner maksimal 8 MB.',
            'image.dimensions'  => 'Dimensi gambar banner terlalu besar (maksimal 6000x6000 px).',
        ]);

        $cleanTitle = isset($validated['title']) ? strip_tags(trim($validated['title'])) : null;

        $data = [
            'title'     => $cleanTitle,
            'is_active' => $request->boolean('is_active'),
        ];

        // 2. Penanganan File Baru & Pembersihan File Lama
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if (! $file->isValid()) {
                return back()->withErrors(['image' => 'File gambar rusak atau gagal diunggah.'])->withInput();
            }

            // Hapus file lama secara aman
            $this->safeDeleteFile($slider->image_path);

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['image_path'] = $file->storeAs('sliders', $safeFileName, 'public');
        }

        $slider->update($data);

        Notification::log(
            'Slider "' . ($slider->title ?: 'Tanpa judul') . '" diperbarui.',
            'fa-images',
            'maroon',
            route('admin.sliders.index')
        );

        return redirect()->route('admin.sliders.index')->with('success', 'Banner slider berhasil diperbarui.');
    }

    public function destroy(HomeSlider $slider)
    {
        // Hapus file fisik dari storage disk
        $this->safeDeleteFile($slider->image_path);

        $title = $slider->title ?: 'Tanpa judul';
        $slider->delete();

        Notification::log('Slider "' . $title . '" dihapus.', 'fa-trash-can', 'danger', route('admin.sliders.index'));

        return redirect()->route('admin.sliders.index')->with('success', 'Banner slider berhasil dihapus.');
    }

    public function toggle(HomeSlider $slider)
    {
        $slider->update(['is_active' => ! $slider->is_active]);

        Notification::log(
            'Slider "' . ($slider->title ?: 'Tanpa judul') . '" ' . ($slider->is_active ? 'diaktifkan.' : 'dinonaktifkan.'),
            $slider->is_active ? 'fa-eye' : 'fa-eye-slash',
            $slider->is_active ? 'success' : 'warning',
            route('admin.sliders.index')
        );

        return back()->with('success', $slider->is_active ? 'Slider diaktifkan.' : 'Slider dinonaktifkan.');
    }

    public function reorder(Request $request)
    {
        // Validasi Payload Reorder (Cegah Injeksi ID non-numerik atau ID fiktif)
        $validated = $request->validate([
            'order'   => ['required', 'array', 'max:50'],
            'order.*' => ['integer', 'exists:home_sliders,id'],
        ]);

        // Eksekusi Pembaruan Posisi Secara Atomik (Database Transaction)
        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $index => $id) {
                HomeSlider::where('id', $id)->update(['sort_order' => $index + 1]);
            }
        });

        Notification::log('Urutan slider banner diperbarui.', 'fa-arrows-up-down', 'maroon', route('admin.sliders.index'));

        return response()->json(['status' => 'ok']);
    }

    /**
     * Mencegah Path Traversal & Arbitrary File Deletion
     */
    private function safeDeleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'sliders/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}