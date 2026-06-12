<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Client;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('client')->latest();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return view('admin.events.index', [
            'events' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.events.form', ['clients' => Client::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'client_id'   => 'nullable|exists:clients,id',
            'type'        => 'nullable|string',
            'event_date'  => 'required|date',
            'location'    => 'nullable|string',
            'budget'      => 'nullable|numeric|min:0',
            'status'      => 'required|in:pending,aktif,selesai,batal',
            'description' => 'nullable|string',
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
        return view('admin.events.form', [
            'event'   => $event,
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'client_id'   => 'nullable|exists:clients,id',
            'type'        => 'nullable|string',
            'event_date'  => 'required|date',
            'location'    => 'nullable|string',
            'budget'      => 'nullable|numeric|min:0',
            'status'      => 'required|in:pending,aktif,selesai,batal',
            'description' => 'nullable|string',
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
