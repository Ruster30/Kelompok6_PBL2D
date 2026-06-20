<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;

class ClientRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('client')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('client', function($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('nama_event', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->status) {
            $query->where('status_event', $request->status);
        }

        return view('admin.requests.index', [
            'requests' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function show(Event $clientRequest)
    {
        return view('admin.requests.show', ['request' => $clientRequest->load('client')]);
    }

    public function approve(Event $clientRequest)
    {
        $clientRequest->update(['status_event' => 'diproses']);
        Notification::create([
            'user_id' => $clientRequest->client_id,
            'judul' => 'Request Event Disetujui',
            'pesan' => 'Event "' . $clientRequest->nama_event . '" sudah disetujui dan masuk tahap diproses.',
            'tipe' => 'sukses',
        ]);

        return back()->with('success', 'Request berhasil disetujui.');
    }

    public function reject(Event $clientRequest)
    {
        $clientRequest->update(['status_event' => 'dibatalkan']);
        Notification::create([
            'user_id' => $clientRequest->client_id,
            'judul' => 'Request Event Ditolak',
            'pesan' => 'Event "' . $clientRequest->nama_event . '" belum dapat kami proses.',
            'tipe' => 'peringatan',
        ]);

        return back()->with('success', 'Request ditolak.');
    }
}
