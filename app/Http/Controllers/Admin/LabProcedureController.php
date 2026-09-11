<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabProcedure;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabProcedureController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $search = addcslashes(strip_tags(trim($search)), '%_');
        }

        $procedures = LabProcedure::query()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(8)
            ->withQueryString();

        return view('admin.procedures.index', compact('procedures'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedFields($request);

        $procedure = DB::transaction(function () use ($data) {
            return LabProcedure::create($data);
        });

        if (class_exists(Notification::class)) {
            Notification::log(
                'Prosedur layanan baru "' . $procedure->title . '" ditambahkan.',
                'fa-file-lines',
                'success',
                route('admin.procedures.index')
            );
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record($procedure, 'created', 'Menambahkan prosedur layanan baru: ' . $procedure->title, [
                'attributes' => $procedure->only(['title', 'file_url', 'sort_order']),
            ]);
        }

        return back()->with('success', 'Data prosedur "' . $procedure->title . '" berhasil disimpan.');
    }

    public function update(Request $request, LabProcedure $procedure)
    {
        $data = $this->validatedFields($request);

        // Snapshot data lama
        $oldContentText = trim(preg_replace('/\s+/', ' ', strip_tags($procedure->description)));
        $oldValues = [
            'Judul Prosedur' => $procedure->title,
            'Tautan Dokumen' => $procedure->file_url,
            'Urutan'         => $procedure->sort_order,
            'Deskripsi'      => Str::limit($oldContentText, 70),
        ];

        DB::transaction(function () use ($procedure, $data) {
            $procedure->update($data);
        });

        // Snapshot data baru
        $newContentText = trim(preg_replace('/\s+/', ' ', strip_tags($procedure->description)));
        $newValues = [
            'Judul Prosedur' => $procedure->title,
            'Tautan Dokumen' => $procedure->file_url,
            'Urutan'         => $procedure->sort_order,
            'Deskripsi'      => Str::limit($newContentText, 70),
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
                'Prosedur "' . $procedure->title . '" diperbarui.',
                'fa-file-lines',
                'maroon',
                route('admin.procedures.index')
            );
        }

        if (! empty($changes) && class_exists(ActivityLog::class)) {
            ActivityLog::record($procedure, 'updated', 'Memperbarui data prosedur: ' . $procedure->title, [
                'old' => $oldChanges,
                'new' => $changes,
            ]);
        }

        return back()->with('success', 'Data prosedur "' . $procedure->title . '" berhasil diperbarui.');
    }

    public function destroy(LabProcedure $procedure)
    {
        $title = $procedure->title;
        $backupData = $procedure->toArray();

        DB::transaction(function () use ($procedure) {
            $procedure->delete();
        });

        if (class_exists(Notification::class)) {
            Notification::log('Data prosedur "' . $title . '" dihapus.', 'fa-trash-can', 'danger', route('admin.procedures.index'));
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record($procedure, 'deleted', 'Menghapus data prosedur: ' . $title, [
                'attributes' => $backupData,
            ]);
        }

        return back()->with('success', 'Data prosedur "' . $title . '" berhasil dihapus.');
    }

    private function validatedFields(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_url'    => ['required', 'url', 'max:2048'],
            'sort_order'  => ['required', 'integer', 'min:1'],
        ], [
            'title.required'      => 'Judul prosedur wajib diisi.',
            'file_url.required'   => 'Tautan dokumen wajib diisi.',
            'file_url.url'        => 'Format URL tautan tidak valid.',
            'sort_order.required' => 'Urutan tampil wajib diisi.',
            'sort_order.integer'  => 'Urutan harus berupa angka bulat.',
        ]);

        $data['title'] = strip_tags(trim($data['title']));
        
        if (isset($data['description'])) {
            $data['description'] = $this->cleanHtmlContent($data['description']);
        }

        return $data;
    }

    private function cleanHtmlContent(string $html): string
    {
        $allowedTags = '<p><br><b><strong><i><em><u><strike><s><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><table><thead><tbody><tr><th><td><hr><code><pre>';
        $cleaned = strip_tags($html, $allowedTags);
        $cleaned = preg_replace('/(<[^>]+?)([\s\r\n\t]+on\w+=\s*(["\'][^"\']*["\']|[^\s>]+))/i', '$1', $cleaned);
        $cleaned = preg_replace('/href=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $cleaned);
        $cleaned = preg_replace('/src=\s*["\']\s*javascript:[^"\']*["\']/i', 'src=""', $cleaned);

        return $cleaned;
    }
}