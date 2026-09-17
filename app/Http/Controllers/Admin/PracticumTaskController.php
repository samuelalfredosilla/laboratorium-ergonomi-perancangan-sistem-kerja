<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\PracticumTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PracticumTaskController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $place = $request->input('place');
        $sort = $request->input('sort', 'latest'); // default: terbaru

        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        // Ambil daftar lokasi unik untuk opsi dropdown filter
        $places = PracticumTask::select('collection_place')
            ->whereNotNull('collection_place')
            ->where('collection_place', '!=', '')
            ->distinct()
            ->pluck('collection_place');

        $tasks = PracticumTask::query()
            ->with('rules') // <-- KUNCI: Tambahkan Eager Loading ini di sini
            // 1. Filter Pencarian Teks
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('collection_place', 'like', '%' . $search . '%');
                });
            })
            // 2. Filter Berdasarkan Tempat Pengumpulan
            ->when($place, function ($query) use ($place) {
                $query->where('collection_place', $place);
            })
            // 3. Filter Pengurutan (Sorting)
            ->when($sort === 'oldest', function ($query) {
                $query->oldest('uploaded_at');
            }, function ($query) use ($sort) {
                if ($sort === 'title_asc') {
                    $query->orderBy('title', 'asc');
                } elseif ($sort === 'title_desc') {
                    $query->orderBy('title', 'desc');
                } else {
                    $query->latest('uploaded_at'); // default
                }
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.practicum.index', compact('tasks', 'places'));
    }

    public function show(PracticumTask $practicum)
    {
        $practicum->load(['activityLogs.user']);
        $task = $practicum;

        return view('admin.practicum.show', compact('task'));
    }

    public function store(Request $request)
    {
        // Pastikan validasi Anda juga mengizinkan array 'rules' lolos (jika ada FormRequest)
        $data = $this->validatedFields($request);
        $data['uploaded_at'] = now();

        $task = DB::transaction(function () use ($data, $request) {
            // Hapus array 'rules' dari $data utama agar tidak error "column not found" di tabel practicum_tasks
            $taskData = \Illuminate\Support\Arr::except($data, ['rules']);

            $task = PracticumTask::create($taskData);

            // Jika ada input rules dinamis, simpan ke tabel relasi practicum_task_rules
            if ($request->has('rules') && is_array($request->rules)) {
                $task->rules()->createMany($request->rules);
            }

            return $task;
        });

        // 1. Notifikasi untuk ikon lonceng navbar atas
        Notification::log('Tugas praktikum "' . $task->title . '" ditambahkan.', 'fa-clipboard-check', 'success', route('admin.practicum.index'));

        // 2. Catat riwayat log aktivitas
        ActivityLog::record($task, 'created', 'Menambahkan tugas praktikum baru: ' . $task->title, [
            'attributes' => $task->toArray(),
        ]);

        // 3. Return dengan flash session
        return redirect()->route('admin.practicum.index')
            ->with('success', 'Tugas praktikum "' . $task->title . '" berhasil ditambahkan.');
    }

    public function update(Request $request, PracticumTask $practicum)
    {
        $data = $this->validatedFields($request);

        // Pisahkan 'rules' dari data utama agar tabel practicum_tasks tidak error
        $taskData = \Illuminate\Support\Arr::except($data, ['rules']);
        $rulesData = $request->input('rules', []);

        // 1. Ambil nilai asli sebelum diupdate (hanya dari $taskData)
        $oldValues = $practicum->only(array_keys($taskData));

        // 2. Jalankan update di database (Data utama + Rules)
        DB::transaction(function () use ($practicum, $taskData, $rulesData, $request) {
            // Update tabel utama
            $practicum->update($taskData);

            // KUNCI: Hapus aturan lama, lalu buat ulang jika ada input baru
            $practicum->rules()->delete();
            if ($request->has('rules') && is_array($rulesData)) {
                $practicum->rules()->createMany($rulesData);
            }
        });

        // 3. Bandingkan kolom yang mengalami perubahan nyata
        $changes = [];
        $oldChanges = [];

        foreach ($taskData as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;
            if ((string) $oldValue !== (string) $newValue) {
                $changes[$key] = $newValue;
                $oldChanges[$key] = $oldValue;
            }
        }

        // Cek apakah ada perubahan pada ketentuan (rules)
        $rulesChanged = $request->has('rules');

        Notification::log('Data tugas praktikum "' . $practicum->title . '" diperbarui.', 'fa-clipboard-check', 'maroon', route('admin.practicum.index'));

        // 4. Catat riwayat log jika ada field utama yang diubah ATAU ketentuan diubah
        if (! empty($changes) || $rulesChanged) {
            ActivityLog::record($practicum, 'updated', 'Memperbarui data tugas praktikum: ' . $practicum->title, [
                'old' => $oldChanges,
                'new' => $changes,
                'rules_updated' => $rulesChanged // Tambahkan flag untuk log
            ]);
        }

        return redirect()->back()
            ->with('success', 'Data tugas praktikum "' . $practicum->title . '" berhasil diperbarui.');
    }

    public function destroy(PracticumTask $practicum)
    {
        $title = $practicum->title;
        $backupData = $practicum->toArray();

        DB::transaction(function () use ($practicum) {
            $practicum->delete();
        });

        Notification::log('Tugas praktikum "' . $title . '" dihapus.', 'fa-trash-can', 'danger', route('admin.practicum.index'));

        ActivityLog::record($practicum, 'deleted', 'Menghapus tugas praktikum: ' . $title, [
            'attributes' => $backupData,
        ]);

        return redirect()->route('admin.practicum.index')
            ->with('success', 'Tugas praktikum "' . $title . '" berhasil dihapus.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string'],
            // Regex fleksibel: menerima domain drive/docs dengan atau tanpa path di belakangnya
            'gdrive_link'      => [
                'required',
                'url',
                'max:500',
                'regex:/^https:\/\/(drive|docs)\.google\.com(\/.*)?$/i',
            ],
            'collection_date'  => ['required', 'string', 'max:100'],
            'collection_time'  => ['required', 'string', 'max:100'],
            'collection_place' => ['required', 'string', 'max:150'],
        ], [
            'title.required'            => 'Judul topik praktikum wajib diisi.',
            'description.required'      => 'Deskripsi penugasan wajib diisi.',
            'gdrive_link.required'      => 'Tautan Google Drive berkas soal wajib diisi.',
            'gdrive_link.url'           => 'Format tautan Google Drive tidak valid.',
            'gdrive_link.regex'         => 'Tautan harus berupa tautan Google Drive (https://drive.google.com/...).',
            'collection_date.required'  => 'Hari dan tanggal pengumpulan wajib diisi.',
            'collection_time.required'  => 'Waktu batas pengumpulan wajib diisi.',
            'collection_place.required' => 'Lokasi pengumpulan berkas wajib diisi.',
        ]);

        // Sanitasi teks dari tag HTML dan karakter berbahaya
        $data['title']            = strip_tags(trim($data['title']));
        $data['description']      = strip_tags(trim($data['description']));
        $data['gdrive_link']      = trim($data['gdrive_link']);
        $data['collection_date']  = strip_tags(trim($data['collection_date']));
        $data['collection_time']  = strip_tags(trim($data['collection_time']));
        $data['collection_place'] = strip_tags(trim($data['collection_place']));

        return $data;
    }
}
