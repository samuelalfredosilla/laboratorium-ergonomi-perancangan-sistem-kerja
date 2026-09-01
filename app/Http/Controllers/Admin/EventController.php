<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryEvent;
use App\Models\Notification;
use App\Models\ActivityLog; // Import model ActivityLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Tampilkan daftar acara laboratorium dengan fitur pencarian & filter.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        // Ambil semua lokasi unik dari database untuk dropdown filter
        $places = LaboratoryEvent::select('event_place')
                                 ->distinct()
                                 ->whereNotNull('event_place')
                                 ->where('event_place', '!=', '')
                                 ->pluck('event_place');

        $events = LaboratoryEvent::query()
            // 1. Fitur Pencarian (Judul, Deskripsi, Lokasi)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhere('event_place', 'like', '%' . $search . '%');
                });
            })
            // 2. Fitur Filter Lokasi
            ->when($request->filled('place'), function ($query) use ($request) {
                $query->where('event_place', $request->place);
            })
            // 3. Fitur Filter Tanggal (Menggantikan fitur "Sort")
            ->when($request->filled('date'), function ($query) use ($request) {
                // Mencari acara yang tanggal pelaksanaannya sama dengan input date
                $query->whereDate('event_date', $request->date);
            })
            // Default diurutkan berdasarkan data terbaru (baru ditambahkan)
            ->orderBy('uploaded_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', compact('events', 'places'));
    }

    /**
     * Simpan data acara baru ke database dan catat notifikasi & log.
     */
    public function store(Request $request)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);

            $data['photo'] = $file->storeAs('events', $safeFileName, 'public');
        }

        $data['uploaded_at'] = now();
        $event = LaboratoryEvent::create($data);

        // Tambahkan Notifikasi Penambahan Data
        Notification::log(
            'Kegiatan baru "' . $event->title . '" ditambahkan.',
            'fa-calendar-plus',
            'maroon',
            route('admin.events.index')
        );

        // Catat ke Activity Log
        ActivityLog::record($event, 'created', 'Menambahkan kegiatan baru: ' . $event->title, [
            'attributes' => $event->toArray(),
        ]);

        return redirect()->route('admin.events.index')
                         ->with('success', 'Kegiatan laboratorium berhasil ditambahkan.');
    }

    /**
     * Update data acara di database dan catat notifikasi & riwayat perubahan log.
     */
    public function update(Request $request, LaboratoryEvent $event)
    {
        $data = $this->validatedFields($request);

        // 1. Snapshot nilai lama sebelum diperbarui
        $oldValues = [
            'Judul Kegiatan'      => $event->title,
            'Deskripsi'           => $event->description,
            'Tanggal Pelaksanaan' => $event->event_date,
            'Waktu Acara'         => $event->event_time,
            'Lokasi / Platform'   => $event->event_place,
            'Tautan GDrive'       => $event->gdrive_link ?? '—',
            'Poster'              => $event->photo ? 'Ada Foto' : 'Tanpa Foto',
        ];

        $photoChanged = false;
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($event->photo && Storage::disk('public')->exists($event->photo)) {
                Storage::disk('public')->delete($event->photo);
            }

            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);

            $data['photo'] = $file->storeAs('events', $safeFileName, 'public');
            $photoChanged = true;
        }

        // 2. Eksekusi pembaruan
        $event->update($data);

        // 3. Snapshot nilai baru setelah update
        $newValues = [
            'Judul Kegiatan'      => $event->title,
            'Deskripsi'           => $event->description,
            'Tanggal Pelaksanaan' => $event->event_date,
            'Waktu Acara'         => $event->event_time,
            'Lokasi / Platform'   => $event->event_place,
            'Tautan GDrive'       => $event->gdrive_link ?? '—',
            'Poster'              => $photoChanged ? 'Foto Diperbarui' : ($event->photo ? 'Ada Foto' : 'Tanpa Foto'),
        ];

        // 4. Deteksi field yang berubah untuk Log
        $changes = [];
        $oldChanges = [];

        foreach ($newValues as $key => $newVal) {
            $oldVal = $oldValues[$key] ?? null;
            if ((string)$oldVal !== (string)$newVal) {
                $changes[$key] = $newVal;
                $oldChanges[$key] = $oldVal;
            }
        }

        // Tambahkan Notifikasi Pembaruan Data
        Notification::log(
            'Data kegiatan "' . $event->title . '" diperbarui.',
            'fa-pen-to-square',
            'maroon',
            route('admin.events.index')
        );

        // Catat riwayat perubahan ke Activity Log (hanya jika ada yang berubah)
        if (! empty($changes)) {
            ActivityLog::record($event, 'updated', 'Memperbarui data kegiatan: ' . $event->title, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return redirect()->route('admin.events.index')
                         ->with('success', 'Kegiatan laboratorium berhasil diperbarui.');
    }

    /**
     * Hapus data acara dari database berserta fotonya dan catat log penghapusan.
     */
    public function destroy(LaboratoryEvent $event)
    {
        $title = $event->title;
        $backupData = $event->toArray(); // Backup data untuk direkam di log

        // Menghapus file foto dari storage saat data dihapus
        if ($event->photo && Storage::disk('public')->exists($event->photo)) {
            Storage::disk('public')->delete($event->photo);
        }

        $event->delete();

        // Tambahkan Notifikasi Penghapusan Data
        Notification::log(
            'Kegiatan "' . $title . '" telah dihapus.',
            'fa-trash-can',
            'danger',
            route('admin.events.index')
        );

        // Catat ke Activity Log
        ActivityLog::record($event, 'deleted', 'Menghapus data kegiatan: ' . $title, [
            'attributes' => $backupData,
        ]);

        return redirect()->route('admin.events.index')
                         ->with('success', 'Kegiatan laboratorium berhasil dihapus.');
    }

    /**
     * Helper untuk validasi input dari request
     */
    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date'  => ['required', 'date'],
            'event_time'  => ['required', 'string', 'max:100'],
            'event_place' => ['required', 'string', 'max:255'],
            'gdrive_link' => ['nullable', 'url', 'max:255'],
            'photo'       => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // Maks 5MB
            ],
        ], [
            'title.required'       => 'Judul kegiatan wajib diisi.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'event_date.required'  => 'Tanggal kegiatan wajib diisi.',
            'event_time.required'  => 'Waktu kegiatan wajib diisi.',
            'event_place.required' => 'Lokasi kegiatan wajib diisi.',
            'gdrive_link.url'      => 'Tautan Google Drive tidak valid.',
            'photo.image'          => 'Berkas poster harus berupa gambar.',
            'photo.mimes'          => 'Format foto yang diperbolehkan: JPG, PNG, atau WebP.',
            'photo.max'            => 'Ukuran poster maksimal 5 MB.',
        ]);

        // Sanitasi input (mencegah XSS)
        $data['title']       = strip_tags(trim($data['title']));
        $data['description'] = strip_tags(trim($data['description']));
        $data['event_time']  = strip_tags(trim($data['event_time']));
        $data['event_place'] = strip_tags(trim($data['event_place']));
        $data['gdrive_link'] = $data['gdrive_link'] ? trim($data['gdrive_link']) : null;

        return $data;
    }
}
