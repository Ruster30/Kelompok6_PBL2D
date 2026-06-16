<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\DocumentationFile;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {
        $query = Documentation::with(['event', 'files'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhereHas('event', fn($q2) => $q2->where('nama_event', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->status) {
            $query->whereHas('files', fn($q) => $q->where('status', $request->status));
        }

        $documentations = $query->paginate(10)->withQueryString();

        return view('admin.documentations.index', [
            'documentations' => $documentations,
            'totalDocs'      => Documentation::count(),
            'pendingDocs'    => DocumentationFile::where('status', 'menunggu')->count(),
            'approvedDocs'   => DocumentationFile::where('status', 'disetujui')->count(),
        ]);
    }

    // Approve/reject individual file
    public function approveFile(DocumentationFile $file)
    {
        $file->update(['status' => 'disetujui']);
        return back()->with('success', 'File dokumentasi disetujui.');
    }

    public function rejectFile(DocumentationFile $file)
    {
        $file->update(['status' => 'ditolak']);
        return back()->with('success', 'File dokumentasi ditolak.');
    }
}
