<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use App\Models\Tugas;
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
            'tugas_id'  => 'required|exists:tugas,id',
            'nama_file' => 'required|string|max:255',
            'url_file'  => 'required|url|max:500',
            'catatan'   => 'nullable|string|max:1000',
        ]);

        $vendor = Auth::user()->vendor;

        // Pastikan tugas milik vendor ini
        $tugas = Tugas::where('id', $request->tugas_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        Dokumentasi::create([
            'tugas_id'  => $tugas->id,
            'vendor_id' => $vendor->id,
            'nama_file' => $request->nama_file,
            'url_file'  => $request->url_file,
            'catatan'   => $request->catatan,
        ]);

        return redirect()->route('vendor.daftar-tugas')
            ->with('success', 'Dokumentasi berhasil diunggah.');
    }
}
