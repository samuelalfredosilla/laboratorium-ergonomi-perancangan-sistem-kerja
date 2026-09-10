<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\ActivityLog; // Asumsi Anda menggunakan model ini seperti di News
use App\Models\Notification; // Asumsi Anda menggunakan model ini seperti di News
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        $achievements = Achievement::query()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('achiever_name', 'like', '%' . $search . '%');
            })
            // Tambahkan logika pencarian berdasarkan Tingkat (level)
            ->when($request->filled('level'), fn ($q) => $q->where('level', $request->level))
            ->when($request->status === 'published', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'draft', fn ($q) => $q->where('is_active', false))
            ->orderBy('date_achieved', 'desc')
            ->paginate(8)
            ->withQueryString();

        // Ambil daftar tingkat yang unik dari database untuk opsi dropdown
        $levels = Achievement::select('level')
            ->distinct()
            ->whereNotNull('level')
            ->orderBy('level')
            ->pluck('level');

        // Pastikan variabel $levels juga dikirimkan (compact) ke view
        return view('admin.achievements.index', compact('achievements', 'levels'));
    }

    public function show(Achievement $achievement)
    {
        // Load relasi logs jika Anda menggunakan ActivityLog pada model ini
        $achievement->load(['activityLogs.user']); 
        
        return view('admin.achievements.show', compact('achievement'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File gambar tidak valid atau rusak.'])->withInput();
            }

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('achievements', $safeFileName, 'public');
        }

        $achievement = DB::transaction(function () use ($data) {
            return Achievement::create($data);
        });

        if (class_exists(Notification::class)) {
            Notification::log(
                'Data prestasi baru "' . $achievement->title . '" ditambahkan.',
                'fa-trophy',
                'success',
                route('admin.achievements.show', $achievement)
            );
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record($achievement, 'created', 'Menambahkan prestasi baru: ' . $achievement->title, [
                'attributes' => $achievement->only(['title', 'achiever_name', 'level', 'date_achieved', 'is_active']),
            ]);
        }

        return redirect()->route('admin.achievements.index')->with('success', 'Data prestasi "' . $achievement->title . '" berhasil disimpan.');
    }

    public function update(Request $request, Achievement $achievement)
    {
        $data = $this->validatedFields($request);

        // Snapshot data lama
        $oldDate = $achievement->date_achieved ? \Carbon\Carbon::parse($achievement->date_achieved)->format('Y-m-d') : '—';
        $oldStatus = $achievement->is_active ? 'Ditampilkan' : 'Disembunyikan';
        $oldContentText = trim(preg_replace('/\s+/', ' ', strip_tags($achievement->description)));

        $oldValues = [
            'Judul Prestasi'   => $achievement->title,
            'Nama Peraih'      => $achievement->achiever_name,
            'Tingkat'          => $achievement->level,
            'Tanggal'          => $oldDate,
            'Status Tampil'    => $oldStatus,
            'Foto'             => $achievement->photo ? 'Ada Foto' : 'Tanpa Foto',
            'Deskripsi'        => Str::limit($oldContentText, 70),
        ];

        $imageChanged = false;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File gambar tidak valid atau rusak.'])->withInput();
            }

            $this->safeDeleteFile($achievement->photo);

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('achievements', $safeFileName, 'public');
            $imageChanged = true;
        }

        DB::transaction(function () use ($achievement, $data) {
            $achievement->update($data);
        });

        // Snapshot data baru
        $newDate = $achievement->date_achieved ? \Carbon\Carbon::parse($achievement->date_achieved)->format('Y-m-d') : '—';
        $newStatus = $achievement->is_active ? 'Ditampilkan' : 'Disembunyikan';
        $newContentText = trim(preg_replace('/\s+/', ' ', strip_tags($achievement->description)));

        $newValues = [
            'Judul Prestasi'   => $achievement->title,
            'Nama Peraih'      => $achievement->achiever_name,
            'Tingkat'          => $achievement->level,
            'Tanggal'          => $newDate,
            'Status Tampil'    => $newStatus,
            'Foto'             => $imageChanged ? 'Foto Diperbarui' : ($achievement->photo ? 'Ada Foto' : 'Tanpa Foto'),
            'Deskripsi'        => Str::limit($newContentText, 70),
        ];

        $changes = [];
        $oldChanges = [];
        foreach ($newValues as $key => $newVal) {
            $oldVal = $oldValues[$key] ?? null;
            if ((string)$oldVal !== (string)$newVal) {
                $changes[$key] = $newVal;
                $oldChanges[$key] = $oldVal;
            }
        }

        if (class_exists(Notification::class)) {
            Notification::log(
                'Prestasi "' . $achievement->title . '" diperbarui.',
                'fa-trophy',
                'maroon',
                route('admin.achievements.show', $achievement)
            );
        }

        if (! empty($changes) && class_exists(ActivityLog::class)) {
            ActivityLog::record($achievement, 'updated', 'Memperbarui data prestasi: ' . $achievement->title, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return redirect()->back()->with('success', 'Data prestasi "' . $achievement->title . '" berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement)
    {
        $title = $achievement->title;
        $backupData = $achievement->toArray();

        $this->safeDeleteFile($achievement->photo);

        DB::transaction(function () use ($achievement) {
            $achievement->delete();
        });

        if (class_exists(Notification::class)) {
            Notification::log('Data prestasi "' . $title . '" dihapus.', 'fa-trash-can', 'danger', route('admin.achievements.index'));
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record($achievement, 'deleted', 'Menghapus data prestasi: ' . $title, [
                'attributes' => $backupData,
            ]);
        }

        return redirect()->route('admin.achievements.index')->with('success', 'Data prestasi "' . $title . '" berhasil dihapus.');
    }

    public function toggle(Achievement $achievement)
    {
        $oldStatus = $achievement->is_active ? 'Ditampilkan' : 'Disembunyikan';
        $achievement->update(['is_active' => ! $achievement->is_active]);
        $newStatus = $achievement->is_active ? 'Ditampilkan' : 'Disembunyikan';

        $statusText = $achievement->is_active ? 'ditampilkan di web.' : 'disembunyikan dari web.';

        if (class_exists(Notification::class)) {
            Notification::log(
                'Prestasi "' . $achievement->title . '" ' . $statusText,
                $achievement->is_active ? 'fa-eye' : 'fa-eye-slash',
                $achievement->is_active ? 'success' : 'warning',
                route('admin.achievements.show', $achievement)
            );
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record($achievement, 'updated', 'Mengubah status tampil prestasi: ' . $achievement->title, [
                'old' => ['Status Tampil' => $oldStatus],
                'new' => ['Status Tampil' => $newStatus],
            ]);
        }

        return back()->with('success', $achievement->is_active ? 'Prestasi berhasil ditampilkan.' : 'Prestasi berhasil disembunyikan.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'achiever_name' => ['required', 'string', 'max:255'],
            'level'         => ['required', 'string', 'max:150'],
            'date_achieved' => ['required', 'date'],
            'description'   => ['nullable', 'string'],
            'photo'         => [
                'nullable', 'file', 'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192',
            ],
        ], [
            'title.required'         => 'Judul prestasi wajib diisi.',
            'achiever_name.required' => 'Nama peraih prestasi wajib diisi.',
            'level.required'         => 'Tingkat/Kategori prestasi wajib diisi.',
            'date_achieved.required' => 'Tanggal pencapaian wajib diisi.',
            'photo.image'            => 'File foto harus berupa gambar.',
            'photo.max'              => 'Ukuran foto maksimal 8 MB.',
        ]);

        $data['title'] = strip_tags(trim($data['title']));
        $data['achiever_name'] = strip_tags(trim($data['achiever_name']));
        $data['level'] = strip_tags(trim($data['level']));
        
        if (isset($data['description'])) {
            $data['description'] = $this->cleanHtmlContent($data['description']);
        }

        unset($data['photo']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function cleanHtmlContent(string $html): string
    {
        $allowedTags = '<p><br><b><strong><i><em><u><strike><s><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><img><table><thead><tbody><tr><th><td><hr><code><pre>';
        $cleaned = strip_tags($html, $allowedTags);
        $cleaned = preg_replace('/(<[^>]+?)([\s\r\n\t]+on\w+=\s*(["\'][^"\']*["\']|[^\s>]+))/i', '$1', $cleaned);
        $cleaned = preg_replace('/href=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $cleaned);
        $cleaned = preg_replace('/src=\s*["\']\s*javascript:[^"\']*["\']/i', 'src=""', $cleaned);

        return $cleaned;
    }

    private function safeDeleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'achievements/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}