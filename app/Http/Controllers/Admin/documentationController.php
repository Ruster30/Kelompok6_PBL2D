<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {
        $query = Documentation::with(['event', 'vendor'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('file_dokumentasi', 'like', '%' . $request->search . '%')
                  ->orWhereHas('event', fn($q2) => $q2->where('nama_event', 'like', '%' . $request->search . '%'))
                  ->orWhereHas('vendor', fn($q2) => $q2->where('nama_vendor', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $documentations = $query->paginate(10)->withQueryString();

        return view('admin.documentations.index', [
            'documentations' => $documentations,
            'totalDocs'      => Documentation::count(),
            'pendingDocs'    => Documentation::where('status', 'menunggu')->count(),
            'approvedDocs'   => Documentation::where('status', 'disetujui')->count(),
        ]);
    }

    public function approve(Documentation $documentation)
    {
        $documentation->update(['status' => 'disetujui']);
        return back()->with('success', 'Dokumentasi disetujui.');
    }

    public function reject(Documentation $documentation)
    {
        $documentation->update(['status' => 'ditolak']);
        return back()->with('success', 'Dokumentasi ditolak.');
    }
}