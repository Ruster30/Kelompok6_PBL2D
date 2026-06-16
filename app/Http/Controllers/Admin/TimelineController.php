<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Timeline;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::orderBy('nama_event')->get();
        $selectedEvent = null;
        $timelines = collect();

        if ($request->event_id) {
            $selectedEvent = Event::findOrFail($request->event_id);
            $timelines = Timeline::where('event_id', $request->event_id)->orderBy('tanggal_kegiatan')->get();
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
            $timelines = Timeline::where('event_id', $selectedEvent->id)->orderBy('tanggal_kegiatan')->get();
        }

        return view('admin.timeline.index', compact('events', 'selectedEvent', 'timelines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id'         => 'required|exists:events,id',
            'nama_kegiatan'    => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'status_kegiatan'  => 'required|in:belum_mulai,berjalan,selesai',
        ]);

        Timeline::create($data);

        return redirect()->route('admin.timeline.index', ['event_id' => $data['event_id']])
                         ->with('success', 'Timeline berhasil ditambahkan.');
    }

    public function update(Request $request, Timeline $timeline)
    {
        $data = $request->validate([
            'nama_kegiatan'    => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'status_kegiatan'  => 'required|in:belum_mulai,berjalan,selesai',
        ]);

        $timeline->update($data);

        return redirect()->route('admin.timeline.index', ['event_id' => $timeline->event_id])
                         ->with('success', 'Timeline berhasil diperbarui.');
    }

    public function destroy(Timeline $timeline)
    {
        $eventId = $timeline->event_id;
        $timeline->delete();

        return redirect()->route('admin.timeline.index', ['event_id' => $eventId])
                         ->with('success', 'Timeline berhasil dihapus.');
    }
}
