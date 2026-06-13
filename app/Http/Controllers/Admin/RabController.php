<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Rab;
use Illuminate\Http\Request;

class RabController extends Controller
{
    public function index(Request $request)
    {
        $events        = Event::orderBy('nama_event')->get();
        $selectedEvent = null;
        $rabItems      = collect();

        if ($request->event_id) {
            $selectedEvent = Event::findOrFail($request->event_id);
            $rabItems      = Rab::where('event_id', $request->event_id)->get();
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
            $rabItems      = Rab::where('event_id', $selectedEvent->id)->get();
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

        Rab::create([
            'event_id' => $data['event_id'],
            'nama_biaya' => $data['item_name'],
            'kategori_biaya' => $data['category'] ?? 'Umum',
            'jumlah_item' => $data['qty'],
            'harga_satuan' => $data['unit_price'],
            'subtotal_biaya' => $data['qty'] * $data['unit_price'],
        ]);

        return redirect()->route('admin.rab.index', ['event_id' => $data['event_id']])
                         ->with('success', 'Item RAB berhasil ditambahkan.');
    }

    public function destroy(Rab $rabItem)
    {
        $eventId = $rabItem->event_id;
        $rabItem->delete();
        return redirect()->route('admin.rab.index', ['event_id' => $eventId])
                         ->with('success', 'Item berhasil dihapus.');
    }
}
