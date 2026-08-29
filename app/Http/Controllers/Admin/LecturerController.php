<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Lecturer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        $lecturers = Lecturer::with(['educations', 'researches', 'communityServices'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nip', 'like', '%' . $search . '%')
                      ->orWhere('expertise', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                if ($request->role === 'chief') {
                    $query->where('role', 'Laboratory Chief');
                } elseif ($request->role === 'member') {
                    $query->where('role', 'Lecturer of Interest');
                }
            })
            // Urutkan berdasarkan sort_order terkecil ke terbesar (1, 2, 3...)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.lecturers.index', compact('lecturers'));
    }

    public function show(Lecturer $lecturer)
    {
        $lecturer->load(['educations', 'researches', 'communityServices', 'activityLogs.user']);

        return view('admin.lecturers.show', compact('lecturer'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);
        $this->validateRepeaters($request);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File foto rusak atau tidak valid.'])->withInput();
            }

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('lecturers', $safeFileName, 'public');
        }

        $lecturer = DB::transaction(function () use ($data, $request) {
            $createdLecturer = Lecturer::create($data);
            $this->syncRepeaters($createdLecturer, $request);
            return $createdLecturer;
        });

        Notification::log('Dosen baru "' . $lecturer->name . '" ditambahkan.', 'fa-user-plus', 'success', route('admin.lecturers.index'));

        ActivityLog::record($lecturer, 'created', 'Menambahkan dosen baru: ' . $lecturer->name, [
            'attributes' => $lecturer->toArray(),
        ]);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Dosen "' . $lecturer->name . '" berhasil ditambahkan secara aman.');
    }

    public function update(Request $request, Lecturer $lecturer)
    {
        $data = $this->validatedFields($request, $lecturer);

        // 1. Snapshot nilai lama sebelum diperbarui
        $oldValues = [
            'Nama Lengkap'         => $lecturer->name,
            'NIP'                  => $lecturer->nip ?? '—',
            'Peran'                => $lecturer->role,
            'Urutan Tampil'        => (string) ($lecturer->sort_order ?? 1),
            'Keahlian Utama'       => $lecturer->expertise ?? '—',
            'Email'                => $lecturer->email ?? '—',
            'Google Scholar'       => $lecturer->scholar_link ?? '—',
            'LinkedIn'             => $lecturer->linkedin_link ?? '—',
            'Foto Profil'          => $lecturer->photo ? 'Ada Foto' : 'Tanpa Foto',
            'Riwayat Pendidikan'   => $lecturer->educations->count() . ' data',
            'Judul Penelitian'     => $lecturer->researches->count() . ' data',
            'Pengabdian Masyarakat'=> $lecturer->communityServices->count() . ' data',
        ];

        $photoChanged = false;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File foto tidak valid atau rusak.'])->withInput();
            }

            if ($lecturer->photo && Storage::disk('public')->exists($lecturer->photo)) {
                Storage::disk('public')->delete($lecturer->photo);
            }

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('lecturers', $safeFileName, 'public');
            $photoChanged = true;
        }

        // 2. Eksekusi pembaruan profil dan relasi repeater
        DB::transaction(function () use ($lecturer, $data, $request) {
            $lecturer->update($data);

            // Sync Pendidikan
            $lecturer->educations()->delete();
            if ($request->has('educations')) {
                foreach ($request->input('educations', []) as $edu) {
                    if (! empty($edu['institution'])) {
                        $lecturer->educations()->create([
                            'degree'      => strip_tags(trim($edu['degree'] ?? 'S1')),
                            'institution' => strip_tags(trim($edu['institution'])),
                            'year_range'  => strip_tags(trim($edu['year_range'] ?? '')),
                        ]);
                    }
                }
            }

            // Sync Penelitian
            $lecturer->researches()->delete();
            if ($request->has('researches')) {
                foreach ($request->input('researches', []) as $res) {
                    if (! empty($res['title'])) {
                        $lecturer->researches()->create([
                            'title' => strip_tags(trim($res['title'])),
                            'year'  => strip_tags(trim($res['year'] ?? '')),
                        ]);
                    }
                }
            }

            // Sync Pengabdian
            $lecturer->communityServices()->delete();
            if ($request->has('services')) {
                foreach ($request->input('services', []) as $srv) {
                    if (! empty($srv['title'])) {
                        $lecturer->communityServices()->create([
                            'title' => strip_tags(trim($srv['title'])),
                            'year'  => strip_tags(trim($srv['year'] ?? '')),
                        ]);
                    }
                }
            }
        });

        // 3. Snapshot nilai baru setelah update
        $lecturer->load(['educations', 'researches', 'communityServices']);
        $newValues = [
            'Nama Lengkap'         => $lecturer->name,
            'NIP'                  => $lecturer->nip ?? '—',
            'Peran'                => $lecturer->role,
            'Urutan Tampil'        => (string) ($lecturer->sort_order ?? 1),
            'Keahlian Utama'       => $lecturer->expertise ?? '—',
            'Email'                => $lecturer->email ?? '—',
            'Google Scholar'       => $lecturer->scholar_link ?? '—',
            'LinkedIn'             => $lecturer->linkedin_link ?? '—',
            'Foto Profil'          => $photoChanged ? 'Foto Diperbarui' : ($lecturer->photo ? 'Ada Foto' : 'Tanpa Foto'),
            'Riwayat Pendidikan'   => $lecturer->educations->count() . ' data',
            'Judul Penelitian'     => $lecturer->researches->count() . ' data',
            'Pengabdian Masyarakat'=> $lecturer->communityServices->count() . ' data',
        ];

        // 4. Deteksi field yang berubah
        $changes = [];
        $oldChanges = [];

        foreach ($newValues as $key => $newVal) {
            $oldVal = $oldValues[$key] ?? null;
            if ((string)$oldVal !== (string)$newVal) {
                $changes[$key] = $newVal;
                $oldChanges[$key] = $oldVal;
            }
        }

        Notification::log(
            'Data dosen "' . $lecturer->name . '" diperbarui.',
            'fa-user-gear',
            'maroon',
            route('admin.lecturers.show', $lecturer)
        );

        if (! empty($changes)) {
            ActivityLog::record($lecturer, 'updated', 'Memperbarui profil dosen: ' . $lecturer->name, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return redirect()->back()->with('success', 'Data dosen "' . $lecturer->name . '" berhasil diperbarui.');
    }

    public function destroy(Lecturer $lecturer)
    {
        $name = $lecturer->name;
        $backupData = $lecturer->toArray();

        $this->safeDeleteFile($lecturer->photo);

        DB::transaction(function () use ($lecturer) {
            $lecturer->educations()->delete();
            $lecturer->researches()->delete();
            $lecturer->communityServices()->delete();
            $lecturer->delete();
        });

        Notification::log('Data dosen "' . $name . '" dihapus.', 'fa-trash-can', 'danger', route('admin.lecturers.index'));

        ActivityLog::record($lecturer, 'deleted', 'Menghapus data dosen: ' . $name, [
            'attributes' => $backupData,
        ]);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Data dosen "' . $name . '" berhasil dihapus.');
    }

    private function validatedFields(Request $request, ?Lecturer $lecturer = null): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'nip'           => ['nullable', 'string', 'max:50'],
            'role'          => ['required', 'string', 'max:100'],
            'expertise'     => ['nullable', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:150'],
            'scholar_link'  => ['nullable', 'url', 'max:255'],
            'linkedin_link' => ['nullable', 'url', 'max:255'],
            'sort_order'    => ['nullable', 'integer', 'min:1'], // Validasi nomor urutan
            'photo'         => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:5120',
            ],
        ], [
            'name.required'       => 'Nama lengkap dan gelar dosen wajib diisi.',
            'role.required'       => 'Peran dosen wajib dipilih.',
            'email.email'         => 'Format email tidak valid.',
            'scholar_link.url'    => 'Format URL Google Scholar tidak valid.',
            'linkedin_link.url'   => 'Format URL LinkedIn tidak valid.',
            'sort_order.integer'  => 'Urutan tampil harus berupa angka.',
            'photo.image'         => 'Berkas foto harus berupa gambar.',
        ]);

        $data['name']          = strip_tags(trim($data['name']));
        $data['nip']           = $data['nip'] ? strip_tags(trim($data['nip'])) : null;
        $data['role']          = strip_tags(trim($data['role']));
        $data['expertise']     = $data['expertise'] ? strip_tags(trim($data['expertise'])) : null;
        $data['email']         = $data['email'] ? trim($data['email']) : null;
        $data['scholar_link']  = $data['scholar_link'] ? trim($data['scholar_link']) : null;
        $data['linkedin_link'] = $data['linkedin_link'] ? trim($data['linkedin_link']) : null;
        $data['sort_order']    = (int) ($data['sort_order'] ?? (Lecturer::max('sort_order') + 1));

        return $data;
    }

    private function validateRepeaters(Request $request): void
    {
        $request->validate([
            'educations'                 => ['nullable', 'array', 'max:30'],
            'educations.*.degree'        => ['nullable', 'string', 'max:30'],
            'educations.*.institution'   => ['nullable', 'string', 'max:150'],
            'educations.*.year_range'    => ['nullable', 'string', 'max:50'],

            'researches'                 => ['nullable', 'array', 'max:50'],
            'researches.*.title'         => ['nullable', 'string', 'max:300'],
            'researches.*.year'          => ['nullable', 'string', 'max:20'],

            'services'                   => ['nullable', 'array', 'max:50'],
            'services.*.title'           => ['nullable', 'string', 'max:300'],
            'services.*.year'            => ['nullable', 'string', 'max:20'],
        ]);
    }

    private function syncRepeaters(Lecturer $lecturer, Request $request): void
    {
        $lecturer->educations()->delete();
        foreach ($request->input('educations', []) as $row) {
            $institution = isset($row['institution']) ? strip_tags(trim($row['institution'])) : '';
            $yearRange = isset($row['year_range']) ? strip_tags(trim($row['year_range'])) : '';

            if (filled($institution) || filled($yearRange)) {
                $lecturer->educations()->create([
                    'degree'      => strip_tags(trim($row['degree'] ?? 'S1')),
                    'institution' => $institution,
                    'year_range'  => $yearRange,
                ]);
            }
        }

        $lecturer->researches()->delete();
        foreach ($request->input('researches', []) as $row) {
            $title = isset($row['title']) ? strip_tags(trim($row['title'])) : '';

            if (filled($title)) {
                $lecturer->researches()->create([
                    'title' => $title,
                    'year'  => isset($row['year']) ? strip_tags(trim($row['year'])) : null,
                ]);
            }
        }

        $lecturer->communityServices()->delete();
        foreach ($request->input('services', []) as $row) {
            $title = isset($row['title']) ? strip_tags(trim($row['title'])) : '';

            if (filled($title)) {
                $lecturer->communityServices()->create([
                    'title' => $title,
                    'year'  => isset($row['year']) ? strip_tags(trim($row['year'])) : null,
                ]);
            }
        }
    }

    private function safeDeleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'lecturers/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
