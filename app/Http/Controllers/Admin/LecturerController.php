<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        $lecturers = Lecturer::with(['educations', 'researches', 'communityServices'])
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nip', 'like', '%' . $request->search . '%');
            }))
            ->when($request->role === 'chief', fn ($q) => $q->where(function ($q) {
                $q->where('role', 'like', '%chief%')
                    ->orWhere('role', 'like', '%head%')
                    ->orWhere('role', 'like', '%kepala%');
            }))
            ->when($request->role === 'member', fn ($q) => $q->where(function ($q) {
                $q->where('role', 'not like', '%chief%')
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
        $lecturer->load(['educations', 'researches', 'communityServices']);

        return view('admin.lecturers.show', compact('lecturer'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('lecturers', 'public');
        }

        $lecturer = Lecturer::create($data);
        $this->syncRepeaters($lecturer, $request);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Dosen "' . $lecturer->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Lecturer $lecturer)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) {
                Storage::disk('public')->delete($lecturer->photo);
            }
            $data['photo'] = $request->file('photo')->store('lecturers', 'public');
        }

        $lecturer->update($data);
        $this->syncRepeaters($lecturer, $request);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Data dosen "' . $lecturer->name . '" berhasil diperbarui.');
    }

    public function destroy(Lecturer $lecturer)
    {
        if ($lecturer->photo) {
            Storage::disk('public')->delete($lecturer->photo);
        }

        $name = $lecturer->name;
        $lecturer->delete();

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Data dosen "' . $name . '" berhasil dihapus.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'scholar_link' => ['nullable', 'url', 'max:255'],
            'linkedin_link' => ['nullable', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        unset($data['photo']);

        return $data;
    }

    private function syncRepeaters(Lecturer $lecturer, Request $request): void
    {
        $lecturer->educations()->delete();
        foreach ($request->input('educations', []) as $row) {
            if (filled($row['institution'] ?? null) || filled($row['year_range'] ?? null)) {
                $lecturer->educations()->create([
                    'degree' => $row['degree'] ?? 'S1',
                    'institution' => $row['institution'] ?? '',
                    'year_range' => $row['year_range'] ?? '',
                ]);
            }
        }

        $lecturer->researches()->delete();
        foreach ($request->input('researches', []) as $row) {
            if (filled($row['title'] ?? null)) {
                $lecturer->researches()->create([
                    'title' => $row['title'],
                    'year' => $row['year'] ?? null,
                ]);
            }
        }

        $lecturer->communityServices()->delete();
        foreach ($request->input('services', []) as $row) {
            if (filled($row['title'] ?? null)) {
                $lecturer->communityServices()->create([
                    'title' => $row['title'],
                    'year' => $row['year'] ?? null,
                ]);
            }
        }
    }
}
