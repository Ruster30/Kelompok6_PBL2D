<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('client')->latest();

        if ($request->search) {
            $query->where('nama_event', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status_event', $request->status);
        }

        return view('admin.events.index', [
            'events' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        $clients = User::where('role', 'client')->orderBy('name')->get();
        return view('admin.events.form', ['clients' => $clients]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_event'       => 'required|string|max:255',
            'client_id'        => 'required|exists:users,id',
            'jenis_event'      => 'nullable|string|max:100',
            'tanggal_event'    => 'required|date',
            'lokasi_event'     => 'nullable|string|max:255',
            'jumlah_tamu'      => 'nullable|integer|min:0',
            'detail_kebutuhan' => 'nullable|string',
            'status_event'     => 'required|in:menunggu,diproses,berjalan,selesai,dibatalkan',
        ]);

        Event::create($data);
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event)
    {
        return view('admin.events.show', ['event' => $event->load('client')]);
    }

    public function edit(Event $event)
    {
        $clients = User::where('role', 'client')->orderBy('name')->get();
        return view('admin.events.form', [
            'event'   => $event,
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'nama_event'       => 'required|string|max:255',
            'client_id'        => 'required|exists:users,id',
            'jenis_event'      => 'nullable|string|max:100',
            'tanggal_event'    => 'required|date',
            'lokasi_event'     => 'nullable|string|max:255',
            'jumlah_tamu'      => 'nullable|integer|min:0',
            'detail_kebutuhan' => 'nullable|string',
            'status_event'     => 'required|in:menunggu,diproses,berjalan,selesai,dibatalkan',
        ]);

        $event->update($data);
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}
