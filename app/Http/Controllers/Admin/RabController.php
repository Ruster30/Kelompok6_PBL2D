<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\RabItem;
use Illuminate\Http\Request;

class RabController extends Controller
{
    public function index(Request $request)
    {
        $events        = Event::orderBy('name')->get();
        $selectedEvent = null;
        $rabItems      = collect();

        if ($request->event_id) {
            $selectedEvent = Event::findOrFail($request->event_id);
            $rabItems      = RabItem::where('event_id', $request->event_id)->get();
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
            $rabItems      = RabItem::where('event_id', $selectedEvent->id)->get();
        }

        return view('admin.rab.index', compact('events', 'selectedEvent', 'rabItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id'   => 'required|exists:events,id',
            'item_name'  => 'required|string|max:255',
            'category'   => 'nullable|string',
            'unit'       => 'nullable|string',
            'qty'        => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $data['total_price'] = $data['qty'] * $data['unit_price'];
        RabItem::create($data);

        return redirect()->route('admin.rab.index', ['event_id' => $data['event_id']])
                         ->with('success', 'Item RAB berhasil ditambahkan.');
    }

    public function destroy(RabItem $rabItem)
    {
        $eventId = $rabItem->event_id;
        $rabItem->delete();
        return redirect()->route('admin.rab.index', ['event_id' => $eventId])
                         ->with('success', 'Item berhasil dihapus.');
    }
}
