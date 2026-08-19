<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assistant;
use App\Models\AssistantPeriod;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssistantController extends Controller
{
    public function index(Request $request)
    {
        // 1. Bersihkan karakter aneh jika ada
        AssistantPeriod::whereNull('name')
            ->orWhere('name', '')
            ->orWhere('name', "'")
            ->delete();

        $query = Assistant::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('nim', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $assistants = $query->orderBy('period', 'desc')
                            ->orderBy('sort_order', 'asc')
                            ->paginate(12)
                            ->withQueryString();

        // 2. Ambil data periode beserta jumlah asistennya
        $periodStats = AssistantPeriod::withCount('assistants')
            ->orderBy('name', 'desc')
            ->get();

        // Jika tabel master periode masih kosong, sinkronkan dari data asisten yang sudah ada
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

        // INI PENEMPATANNYA: Pastikan minimal ada 1 periode yang aktif
        if (!AssistantPeriod::where('is_active', true)->exists() && $periodStats->isNotEmpty()) {
            $periodStats->first()->update(['is_active' => true]);
            $periodStats = AssistantPeriod::withCount('assistants')->orderBy('name', 'desc')->get();
        }

        $periods = $periodStats->pluck('name')->toArray();
        $divisions = Assistant::select('division')->whereNotNull('division')->distinct()->pluck('division');

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
            $data['photo'] = $request->file('photo')->store('assistants', 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 1;

        $assistant = Assistant::create($data);

        if (!empty($assistant->period)) {
            AssistantPeriod::firstOrCreate(['name' => $assistant->period]);
        }

        Notification::log('Asisten baru "' . $assistant->name . '" ditambahkan.', 'fa-user-plus', 'success', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Asisten "' . $assistant->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Assistant $assistant)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            if ($assistant->photo && Storage::disk('public')->exists($assistant->photo)) {
                Storage::disk('public')->delete($assistant->photo);
            }
            $data['photo'] = $request->file('photo')->store('assistants', 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 1;

        $assistant->update($data);

        if (!empty($assistant->period)) {
            AssistantPeriod::firstOrCreate(['name' => $assistant->period]);
        }

        Notification::log('Data asisten "' . $assistant->name . '" diperbarui.', 'fa-user-pen', 'maroon', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Data asisten "' . $assistant->name . '" berhasil diperbarui.');
    }

    public function destroy(Assistant $assistant)
    {
        if ($assistant->photo && Storage::disk('public')->exists($assistant->photo)) {
            Storage::disk('public')->delete($assistant->photo);
        }

        $name = $assistant->name;
        $assistant->delete();

        Notification::log('Data asisten "' . $name . '" dihapus.', 'fa-user-xmark', 'danger', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Data asisten "' . $name . '" berhasil dihapus.');
    }

    /**
     * Validasi Ketat Pembuatan Periode (Hanya Format Tahun YYYY/YYYY)
     */
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

    /**
     * Validasi Ketat Edit Periode
     */
    public function renamePeriod(Request $request)
    {
        $request->validate([
            'old_period' => ['required', 'string'],
            'new_period' => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}\/[0-9]{4}$/', 'different:old_period', 'unique:assistant_periods,name'],
        ], [
            'new_period.required'  => 'Nama periode baru wajib diisi.',
            'new_period.regex'     => 'Format periode tidak valid. Gunakan format tahun seperti: 2026/2027.',
            'new_period.different' => 'Nama periode baru harus berbeda dari periode sebelumnya.',
            'new_period.unique'    => 'Periode tersebut sudah terdaftar.',
        ]);

        $oldPeriod = $request->old_period;
        $newPeriod = trim($request->new_period);

        AssistantPeriod::where('name', $oldPeriod)->update(['name' => $newPeriod]);
        $count = Assistant::where('period', $oldPeriod)->update(['period' => $newPeriod]);

        Notification::log("Periode \"{$oldPeriod}\" diubah menjadi \"{$newPeriod}\" ({$count} asisten disinkronkan).", 'fa-calendar-days', 'maroon', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$oldPeriod}\" berhasil diubah menjadi \"{$newPeriod}\".");
    }

    public function destroyPeriod(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
        ]);

        $period = $request->period;

        AssistantPeriod::where('name', $period)->delete();
        $count = Assistant::where('period', $period)->update(['period' => null]);

        Notification::log("Periode \"{$period}\" dihapus ({$count} asisten dikosongkan status periodenya).", 'fa-calendar-xmark', 'warning', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$period}\" berhasil dihapus. {$count} asisten terkait kini berstatus tanpa periode.");
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'nim'        => ['nullable', 'string', 'max:50'],
            'division'   => ['nullable', 'string', 'max:100'],
            'period'     => ['nullable', 'string', 'max:30'],
            'email'      => ['nullable', 'email', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:100'],
            'photo'      => ['nullable', 'image', 'max:5120'],
        ]);

        unset($data['photo']);

        return $data;
    }

    /**
     * Mengaktifkan/Menonaktifkan periode untuk halaman publik
     */
    public function togglePeriod(AssistantPeriod $period)
    {
        if (!$period->is_active) {
            // Nonaktifkan semua periode lain agar hanya 1 periode aktif di publik
            AssistantPeriod::where('id', '!=', $period->id)->update(['is_active' => false]);
            $period->update(['is_active' => true]);
            $status = 'diaktifkan sebagai periode utama di website';
        } else {
            $period->update(['is_active' => false]);
            $status = 'dinonaktifkan';
        }

        Notification::log("Periode \"{$period->name}\" {$status}.", 'fa-toggle-on', 'info', route('admin.assistants.index'));

        return redirect()->route('admin.assistants.index')
            ->with('success', "Periode \"{$period->name}\" berhasil {$status}.");
    }
}