<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Negotiation;
use App\Models\Notification;
use Illuminate\Http\Request;

class ClientRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['client', 'latestProposal', 'negotiations'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('client', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhere('nama_event', 'like', '%' . $request->search . '%');
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
        return view('admin.requests.show', [
            'request' => $clientRequest->load(['client', 'latestProposal']),
        ]);
    }

    /**
     * Tampilkan riwayat negosiasi untuk event tertentu.
     * Route: GET /admin/requests/{event}/negosiasi
     */
    public function negosiasi(Event $event)
    {
        $event->load(['client', 'latestProposal']);
        $negotiations = Negotiation::with('user')
            ->where('event_id', $event->id)
            ->latest()
            ->get();

        return view('admin.requests.negosiasi', compact('event', 'negotiations'));
    }

    public function approve(Event $clientRequest)
    {
        $clientRequest->update(['status_event' => 'diproses']);

        Notification::create([
            'user_id' => $clientRequest->client_id,
            'judul'   => 'Request Event Disetujui',
            'pesan'   => 'Event "' . $clientRequest->nama_event . '" sudah disetujui dan masuk tahap diproses.',
            'tipe'    => 'sukses',
        ]);

        return back()->with('success', 'Request berhasil disetujui.');
    }

    public function reject(Event $clientRequest)
    {
        $clientRequest->update(['status_event' => 'dibatalkan']);

        Notification::create([
            'user_id' => $clientRequest->client_id,
            'judul'   => 'Request Event Ditolak',
            'pesan'   => 'Event "' . $clientRequest->nama_event . '" belum dapat kami proses.',
            'tipe'    => 'peringatan',
        ]);

        return back()->with('success', 'Request ditolak.');
    }
}
