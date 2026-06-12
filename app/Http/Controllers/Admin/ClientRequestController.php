<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use Illuminate\Http\Request;

class ClientRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientRequest::latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('client_name', 'like', '%' . $request->search . '%')
                  ->orWhere('event_name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return view('admin.requests.index', [
            'requests' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function show(ClientRequest $clientRequest)
    {
        return view('admin.requests.show', ['request' => $clientRequest]);
    }

    public function approve(ClientRequest $clientRequest)
    {
        $clientRequest->update(['status' => 'disetujui']);
        return back()->with('success', 'Request berhasil disetujui.');
    }

    public function reject(ClientRequest $clientRequest)
    {
        $clientRequest->update(['status' => 'ditolak']);
        return back()->with('success', 'Request ditolak.');
    }
}
