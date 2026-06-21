<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\DocumentationFile;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokumentasiController extends Controller
{
    /**
     * Simpan Dokumentasi Tugas
     */
    public function store(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tasks,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
            'judul' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        // Pastikan tugas milik vendor ini
        $tugas = Task::where('id', $request->tugas_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $documentation = Documentation::firstOrCreate([
            'event_id' => $tugas->event_id,
            'judul' => $request->judul ?: 'Dokumentasi ' . $tugas->nama_tugas,
        ], [
            'deskripsi' => $request->catatan,
        ]);

        $file = $request->file('file');
        $path = $file->store('documentation-files', 'public');
        $extension = strtolower($file->getClientOriginalExtension());

        DocumentationFile::create([
            'documentation_id' => $documentation->id,
            'file_path' => $path,
            'tipe_file' => in_array($extension, ['mp4', 'mov']) ? 'video' : 'foto',
            'status' => 'menunggu',
        ]);

        return redirect()->route('vendor.daftar-tugas')
            ->with('success', 'Dokumentasi berhasil diunggah.');
    }
}
