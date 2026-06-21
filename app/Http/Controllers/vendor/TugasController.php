<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Task;
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
        abort_if(!$vendor, 403);

        $tugas = Task::where('vendor_id', $vendor->id)
            ->with('event')
            ->when($request->event, fn($q) => $q->where('event_id', $request->event))
            ->orderBy('deadline')
            ->get();

        return view('vendor.daftar-tugas', compact('tugas'));
    }

    /**
     * Update Status Tugas
     */
    public function update(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tasks,id',
            'status'   => 'required|in:ditugaskan,dikerjakan,selesai',
        ]);

        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $tugas = Task::where('id', $request->tugas_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $tugas->update([
            'status'  => $request->status,
        ]);

        return redirect()->route('vendor.daftar-tugas')
            ->with('success', 'Status tugas berhasil diperbarui.');
    }
}
