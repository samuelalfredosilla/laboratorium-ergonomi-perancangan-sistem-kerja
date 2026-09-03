<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        $equipments = \App\Models\Equipment::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('specification', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->input('sort') === 'newest', function ($query) {
                $query->orderBy('created_at', 'desc');
            }, function ($query) {
                $query->orderBy('sort_order', 'asc');
            })
            ->paginate(10)
            ->withQueryString();

        $categoryStats = \App\Models\EquipmentCategory::orderBy('name', 'asc')->get()->map(function($cat) {
            $cat->equipments_count = \App\Models\Equipment::where('category', $cat->name)->count();
            return $cat;
        });
        
        $categories = $categoryStats->pluck('name')->toArray();

        return view('admin.equipment.index', compact('equipments', 'categories', 'categoryStats'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('equipment', $safeFileName, 'public');
        }

        $data['sort_order'] = $data['sort_order'] ?? 1;
        $equipment = Equipment::create($data);

        Notification::log('Peralatan baru "' . $equipment->name . '" ditambahkan.', 'fa-microscope', 'success', route('admin.equipment.index'));
        ActivityLog::record($equipment, 'created', 'Menambahkan peralatan baru: ' . $equipment->name, ['attributes' => $equipment->toArray()]);

        return redirect()->route('admin.equipment.index')->with('success', 'Data peralatan berhasil ditambahkan.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $this->validatedFields($request);

        $oldValues = [
            'Nama Alat' => $equipment->name,
            'Spesifikasi' => $equipment->specification ?? '—',
            'Deskripsi' => $equipment->description ?? '—',
            'Urutan' => (string) $equipment->sort_order,
            'Foto' => $equipment->photo ? 'Ada Foto' : 'Tanpa Foto',
        ];

        $photoChanged = false;
        if ($request->hasFile('photo')) {
            if ($equipment->photo && Storage::disk('public')->exists($equipment->photo)) {
                Storage::disk('public')->delete($equipment->photo);
            }
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::random(40) . '.' . strtolower($extension);
            $data['photo'] = $file->storeAs('equipment', $safeFileName, 'public');
            $photoChanged = true;
        }

        $data['sort_order'] = $data['sort_order'] ?? 1;
        $equipment->update($data);

        $newValues = [
            'Nama Alat' => $equipment->name,
            'Spesifikasi' => $equipment->specification ?? '—',
            'Deskripsi' => $equipment->description ?? '—',
            'Urutan' => (string) $equipment->sort_order,
            'Foto' => $photoChanged ? 'Foto Diperbarui' : ($equipment->photo ? 'Ada Foto' : 'Tanpa Foto'),
        ];

        $changes = array_diff_assoc($newValues, $oldValues);
        $oldChanges = array_intersect_key($oldValues, $changes);

        Notification::log('Data peralatan "' . $equipment->name . '" diperbarui.', 'fa-pen-to-square', 'maroon', route('admin.equipment.index'));

        if (!empty($changes)) {
            ActivityLog::record($equipment, 'updated', 'Memperbarui data peralatan: ' . $equipment->name, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return redirect()->route('admin.equipment.index')->with('success', 'Data peralatan berhasil diperbarui.');
    }

    public function destroy(Equipment $equipment)
    {
        $name = $equipment->name;
        $backupData = $equipment->toArray();

        if ($equipment->photo && Storage::disk('public')->exists($equipment->photo)) {
            Storage::disk('public')->delete($equipment->photo);
        }

        $equipment->delete();

        Notification::log('Peralatan "' . $name . '" dihapus.', 'fa-trash-can', 'danger', route('admin.equipment.index'));
        ActivityLog::record($equipment, 'deleted', 'Menghapus data peralatan: ' . $name, ['attributes' => $backupData]);

        return redirect()->route('admin.equipment.index')->with('success', 'Data peralatan berhasil dihapus.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'specification' => ['nullable', 'string', 'max:255'],
            'category'      => ['nullable', 'string', 'max:150'], // <-- BARIS INI YANG SEBELUMNYA HILANG
            'description'   => ['nullable', 'string'],
            'sort_order'    => ['nullable', 'integer', 'min:1'],
            'photo'         => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'name.required' => 'Nama peralatan wajib diisi.',
            'photo.image'   => 'Berkas harus berupa gambar.',
            'photo.max'     => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $data['name']          = strip_tags(trim($data['name']));
        $data['specification'] = $data['specification'] ? strip_tags(trim($data['specification'])) : null;
        $data['category']      = $data['category'] ? trim($data['category']) : null; // <-- DAN INI
        $data['description']   = $data['description'] ? trim($data['description']) : null;

        return $data;
    }

    /* ========================================================================= */
    /* LOGIKA MANAJEMEN KATEGORI ALAT                                            */
    /* ========================================================================= */

    public function storeCategory(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'max:150', 'unique:equipment_categories,name'],
        ], [
            'category.unique' => 'Nama kategori tersebut sudah ada.',
        ]);

        \App\Models\EquipmentCategory::create(['name' => trim($request->category)]);
        \App\Models\Notification::log("Kategori baru \"{$request->category}\" ditambahkan.", 'fa-tags', 'success', route('admin.equipment.index'));

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function renameCategory(Request $request)
    {
        $request->validate([
            'old_category' => ['required', 'string', 'exists:equipment_categories,name'],
            'new_category' => ['required', 'string', 'max:150', 'unique:equipment_categories,name'],
        ]);

        $old = trim($request->old_category);
        $new = trim($request->new_category);

        // Update di tabel kategori DAN tabel equipment menggunakan transaksi agar sinkron
        \Illuminate\Support\Facades\DB::transaction(function () use ($old, $new) {
            \App\Models\EquipmentCategory::where('name', $old)->update(['name' => $new]);
            \App\Models\Equipment::where('category', $old)->update(['category' => $new]);
        });

        \App\Models\Notification::log("Kategori \"{$old}\" diubah menjadi \"{$new}\".", 'fa-tags', 'info', route('admin.equipment.index'));

        return back()->with('success', "Kategori berhasil diubah menjadi {$new}.");
    }

    public function destroyCategory(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'exists:equipment_categories,name'],
        ]);

        $category = trim($request->category);

        // Hapus kategori dan kosongkan status kategori di alat yang sebelumnya memakai kategori ini
        \Illuminate\Support\Facades\DB::transaction(function () use ($category) {
            \App\Models\EquipmentCategory::where('name', $category)->delete();
            \App\Models\Equipment::where('category', $category)->update(['category' => null]);
        });

        \App\Models\Notification::log("Kategori \"{$category}\" dihapus.", 'fa-trash', 'warning', route('admin.equipment.index'));

        return back()->with('success', 'Kategori berhasil dihapus. Alat terkait kini menjadi Tanpa Kategori.');
    }
}
