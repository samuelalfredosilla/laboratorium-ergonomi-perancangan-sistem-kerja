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
            ->when($search, fn ($q) => $q->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('nip', 'like', '%' . $search . '%');
            }))
            ->when($request->role === 'chief', fn ($q) => $q->where(function ($query) {
                $query->where('role', 'like', '%chief%')
                      ->orWhere('role', 'like', '%head%')
                      ->orWhere('role', 'like', '%kepala%');
            }))
            ->when($request->role === 'member', fn ($q) => $q->where(function ($query) {
                $query->where('role', 'not like', '%chief%')
                      ->where('role', 'not like', '%head%')
                      ->where('role', 'not like', '%kepala%');
            }))
            ->orderByDesc('sort_order')
            ->orderBy('name')
            ->get();

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
        $this->validateRepeaters($request);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File foto rusak atau tidak valid.'])->withInput();
            }

            $this->safeDeleteFile($lecturer->photo);

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('lecturers', $safeFileName, 'public');
        }

        $oldValues = $lecturer->getOriginal();

        DB::transaction(function () use ($lecturer, $data, $request) {
            $lecturer->update($data);
            $this->syncRepeaters($lecturer, $request);
        });

        $changes = $lecturer->getChanges();
        $oldChanges = array_intersect_key($oldValues, $changes);

        Notification::log('Data dosen "' . $lecturer->name . '" diperbarui.', 'fa-user-pen', 'maroon', route('admin.lecturers.index'));

        if (! empty($changes)) {
            ActivityLog::record($lecturer, 'updated', 'Memperbarui data dosen: ' . $lecturer->name, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Data dosen "' . $lecturer->name . '" berhasil diperbarui.');
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
        $lecturerId = $lecturer ? $lecturer->id : null;

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'nip'           => ['nullable', 'string', 'max:30', 'regex:/^[0-9\s\.\-]+$/', Rule::unique('lecturers', 'nip')->ignore($lecturerId)],
            'role'          => ['required', 'string', 'max:100'],
            'expertise'     => ['nullable', 'string', 'max:255'],
            'email'         => ['nullable', 'email:rfc,dns', 'max:150', Rule::unique('lecturers', 'email')->ignore($lecturerId)],
            'scholar_link'  => ['nullable', 'url', 'max:255'],
            'linkedin_link' => ['nullable', 'url', 'max:255'],
            'photo'         => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192', // Maksimal 8MB
                'dimensions:max_width=4000,max_height=4000',
            ],
        ], [
            'name.required'       => 'Nama lengkap dan gelar dosen wajib diisi.',
            'nip.regex'           => 'Format NIP hanya boleh memuat angka dan tanda hubung.',
            'nip.unique'          => 'NIP tersebut sudah terdaftar di sistem.',
            'role.required'       => 'Peran dosen wajib dipilih.',
            'email.email'         => 'Format alamat email tidak valid.',
            'email.unique'        => 'Email tersebut sudah terdaftar.',
            'scholar_link.url'    => 'Format URL Google Scholar tidak valid.',
            'linkedin_link.url'   => 'Format URL LinkedIn tidak valid.',
            'photo.image'         => 'File foto harus berupa gambar valid.',
            'photo.mimes'         => 'Format foto yang diperbolehkan: JPG, PNG, atau WebP.',
            'photo.max'           => 'Ukuran foto profil maksimal 8 MB.',
        ]);

        // Sanitasi teks dari tag HTML
        $data['name'] = strip_tags(trim($data['name']));
        $data['role'] = strip_tags(trim($data['role']));
        $data['expertise'] = isset($data['expertise']) ? strip_tags(trim($data['expertise'])) : null;
        $data['nip'] = isset($data['nip']) ? preg_replace('/[^0-9]/', '', $data['nip']) : null;

        unset($data['photo']);

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