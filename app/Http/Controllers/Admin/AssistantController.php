<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\AssistantPeriod;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AssistantController extends Controller
{
    public function index(Request $request)
    {
        // 1. Sanitasi input pencarian dari wildcard injection
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        $query = Assistant::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('division')) {
            $query->where('division', strip_tags(trim($request->division)));
        }

        if ($request->filled('period')) {
            $query->where('period', strip_tags(trim($request->period)));
        }

        $assistants = $query->orderBy('period', 'desc')
                            ->orderBy('sort_order', 'asc')
                            ->paginate(12)
                            ->withQueryString();

        // 2. Sinkronisasi & Ambil data master periode
        $periodStats = AssistantPeriod::withCount('assistants')
            ->orderBy('name', 'desc')
            ->get();

        if ($periodStats->isEmpty()) {
            $existingPeriods = Assistant::whereNotNull('period')
                ->where('period', '!=', '')
                ->distinct()
                ->pluck('period');

            if ($existingPeriods->isEmpty()) {
                $existingPeriods = ['2025/2026'];
            }

            foreach ($existingPeriods as $p) {
                AssistantPeriod::firstOrCreate(['name' => $p]);
            }

            $periodStats = AssistantPeriod::withCount('assistants')->orderBy('name', 'desc')->get();
        }

        // Pastikan minimal ada 1 periode yang aktif
        if (! AssistantPeriod::where('is_active', true)->exists() && $periodStats->isNotEmpty()) {
            $periodStats->first()->update(['is_active' => true]);
            $periodStats = AssistantPeriod::withCount('assistants')->orderBy('name', 'desc')->get();
        }

        $periods = $periodStats->pluck('name')->toArray();
        $divisions = Assistant::select('division')->whereNotNull('division')->where('division', '!=', '')->distinct()->pluck('division');

        return view('admin.assistants.index', compact('assistants', 'periods', 'periodStats', 'divisions'));
    }

    public function show(Assistant $assistant)
    {
        $assistant->load(['activityLogs.user']);

        $periods = AssistantPeriod::orderBy('name', 'desc')->pluck('name')->toArray();
        if (empty($periods)) {
            $periods = ['2025/2026'];
        }

        return view('admin.assistants.show', compact('assistant', 'periods'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File foto rusak atau tidak valid.'])->withInput();
            }

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('assistants', $safeFileName, 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 1;

        $assistant = DB::transaction(function () use ($data) {
            $created = Assistant::create($data);
            if (! empty($created->period)) {
                AssistantPeriod::firstOrCreate(['name' => $created->period]);
            }
            return $created;
        });

        Notification::log('Asisten baru "' . $assistant->name . '" ditambahkan.', 'fa-user-plus', 'success', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Asisten "' . $assistant->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Assistant $assistant)
    {
        $data = $this->validatedFields($request, $assistant);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if (! $file->isValid()) {
                return back()->withErrors(['photo' => 'File foto rusak atau tidak valid.'])->withInput();
            }

            $this->safeDeleteFile($assistant->photo);

            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('assistants', $safeFileName, 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 1;

        DB::transaction(function () use ($assistant, $data) {
            $assistant->update($data);
            if (! empty($assistant->period)) {
                AssistantPeriod::firstOrCreate(['name' => $assistant->period]);
            }
        });

        Notification::log('Data asisten "' . $assistant->name . '" diperbarui.', 'fa-user-pen', 'maroon', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Data asisten "' . $assistant->name . '" berhasil diperbarui.');
    }

    public function destroy(Assistant $assistant)
    {
        $this->safeDeleteFile($assistant->photo);

        $name = $assistant->name;
        $assistant->delete();

        Notification::log('Data asisten "' . $name . '" dihapus.', 'fa-trash-can', 'danger', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Data asisten "' . $name . '" berhasil dihapus.');
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'period' => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}\/[0-9]{4}$/', 'unique:assistant_periods,name'],
        ], [
            'period.required' => 'Kolom periode wajib diisi.',
            'period.regex'    => 'Format periode tidak valid. Gunakan format tahun seperti: 2026/2027.',
            'period.unique'   => 'Periode tersebut sudah terdaftar.',
        ]);

        $period = trim($request->period);
        AssistantPeriod::create(['name' => $period]);

        Notification::log("Periode baru \"{$period}\" berhasil ditambahkan.", 'fa-calendar-plus', 'success', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$period}\" berhasil ditambahkan.");
    }

    public function renamePeriod(Request $request)
    {
        $request->validate([
            'old_period' => ['required', 'string', 'exists:assistant_periods,name'],
            'new_period' => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}\/[0-9]{4}$/', 'different:old_period', 'unique:assistant_periods,name'],
        ], [
            'new_period.required'  => 'Nama periode baru wajib diisi.',
            'new_period.regex'     => 'Format periode tidak valid. Gunakan format tahun seperti: 2026/2027.',
            'new_period.different' => 'Nama periode baru harus berbeda dari periode sebelumnya.',
            'new_period.unique'    => 'Periode tersebut sudah terdaftar.',
        ]);

        $oldPeriod = trim($request->old_period);
        $newPeriod = trim($request->new_period);

        $count = DB::transaction(function () use ($oldPeriod, $newPeriod) {
            AssistantPeriod::where('name', $oldPeriod)->update(['name' => $newPeriod]);
            return Assistant::where('period', $oldPeriod)->update(['period' => $newPeriod]);
        });

        Notification::log("Periode \"{$oldPeriod}\" diubah menjadi \"{$newPeriod}\" ({$count} asisten disinkronkan).", 'fa-calendar-days', 'maroon', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$oldPeriod}\" berhasil diubah menjadi \"{$newPeriod}\".");
    }

    public function destroyPeriod(Request $request)
    {
        $request->validate([
            'period' => ['required', 'string', 'exists:assistant_periods,name'],
        ]);

        $period = trim($request->period);

        $count = DB::transaction(function () use ($period) {
            AssistantPeriod::where('name', $period)->delete();
            return Assistant::where('period', $period)->update(['period' => null]);
        });

        Notification::log("Periode \"{$period}\" dihapus ({$count} asisten dikosongkan status periodenya).", 'fa-calendar-xmark', 'warning', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$period}\" berhasil dihapus. {$count} asisten terkait kini berstatus tanpa periode.");
    }

    public function togglePeriod(AssistantPeriod $period)
    {
        DB::transaction(function () use ($period) {
            if (! $period->is_active) {
                // Nonaktifkan semua periode lain agar hanya 1 periode aktif
                AssistantPeriod::where('id', '!=', $period->id)->update(['is_active' => false]);
                $period->update(['is_active' => true]);
            } else {
                $period->update(['is_active' => false]);
            }
        });

        $status = $period->is_active ? 'diaktifkan sebagai periode utama di website' : 'dinonaktifkan';

        Notification::log("Periode \"{$period->name}\" {$status}.", 'fa-toggle-on', 'info', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$period->name}\" berhasil {$status}.");
    }

    private function validatedFields(Request $request, ?Assistant $assistant = null): array
    {
        $assistantId = $assistant ? $assistant->id : null;

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'nim'        => ['nullable', 'string', 'max:30', 'regex:/^[0-9]+$/', Rule::unique('assistants', 'nim')->ignore($assistantId)],
            'division'   => ['nullable', 'string', 'max:100'],
            'period'     => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{4}\/[0-9]{4}$/'],
            'email'      => ['nullable', 'email:rfc,dns', 'max:150', Rule::unique('assistants', 'email')->ignore($assistantId)],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:500'],
            'photo'      => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:8192', // Maksimal 8MB
                'dimensions:max_width=4000,max_height=4000',
            ],
        ], [
            'name.required'     => 'Nama lengkap asisten wajib diisi.',
            'nim.regex'         => 'Format NIM hanya boleh memuat angka.',
            'nim.unique'        => 'NIM tersebut sudah terdaftar.',
            'period.regex'      => 'Format periode tidak valid (contoh: 2025/2026).',
            'email.email'       => 'Format email mahasiswa tidak valid.',
            'email.unique'      => 'Email tersebut sudah terdaftar.',
            'photo.image'       => 'File foto harus berupa gambar valid.',
            'photo.mimes'       => 'Format foto yang diperbolehkan: JPG, PNG, atau WebP.',
            'photo.max'         => 'Ukuran foto profil asisten maksimal 8 MB.',
        ]);

        // Sanitasi input string
        $data['name'] = strip_tags(trim($data['name']));
        $data['division'] = isset($data['division']) ? strip_tags(trim($data['division'])) : null;
        $data['period'] = isset($data['period']) ? trim($data['period']) : null;
        $data['nim'] = isset($data['nim']) ? trim($data['nim']) : null;

        unset($data['photo']);

        return $data;
    }

    private function safeDeleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'assistants/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}