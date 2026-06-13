<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    /**
     * Daftar Tugas
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $tugas = Tugas::where('vendor_id', $vendor->id)
            ->with('event')
            ->when($request->event, fn($q) => $q->where('event_id', $request->event))
            ->orderBy('deadline')
            ->get();

        return view('vendor.pages.daftar-tugas', compact('tugas'));
    }

    /**
     * Update Status Tugas
     */
    public function update(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tugas,id',
            'status'   => 'required|in:pending,on_progress,selesai',
            'catatan'  => 'nullable|string|max:1000',
        ]);

        $vendor = Auth::user()->vendor;

        $tugas = Tugas::where('id', $request->tugas_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $tugas->update([
            'status'  => $request->status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('vendor.daftar-tugas')
            ->with('success', 'Status tugas berhasil diperbarui.');
    }
}
