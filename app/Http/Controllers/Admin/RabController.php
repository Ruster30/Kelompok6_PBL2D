<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Rab;
use App\Models\Vendor;
use Illuminate\Http\Request;

class RabController extends Controller
{
    public function index(Request $request)
    {
        $events        = Event::orderBy('nama_event')->get();
        $vendors       = Vendor::orderBy('nama_vendor')->get();
        $selectedEvent = null;
        $rabItems      = collect();

        if ($request->event_id) {
            $selectedEvent = Event::findOrFail($request->event_id);
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
        }

        if ($selectedEvent) {
            $rabItems = Rab::with('vendor')
                ->where('event_id', $selectedEvent->id)
                ->get();
        }

        return view('admin.rab.index', compact('events', 'vendors', 'selectedEvent', 'rabItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id'       => 'required|exists:events,id',
            'vendor_id'      => 'nullable|exists:vendors,id',
            'nama_biaya'     => 'required|string|max:255',
            'kategori_biaya' => 'nullable|string|max:100',
            'jumlah_item'    => 'required|integer|min:1',
            'harga_satuan'   => 'required|numeric|min:0',
        ]);

        $data['subtotal_biaya'] = $data['jumlah_item'] * $data['harga_satuan'];

        Rab::create($data);

        return redirect()->route('admin.rab.index', ['event_id' => $data['event_id']])
                         ->with('success', 'Item RAB berhasil ditambahkan.');
    }

    public function update(Request $request, Rab $rab)
    {
        $data = $request->validate([
            'vendor_id'      => 'nullable|exists:vendors,id',
            'nama_biaya'     => 'required|string|max:255',
            'kategori_biaya' => 'nullable|string|max:100',
            'jumlah_item'    => 'required|integer|min:1',
            'harga_satuan'   => 'required|numeric|min:0',
        ]);

        $data['subtotal_biaya'] = $data['jumlah_item'] * $data['harga_satuan'];

        $rab->update($data);

        return redirect()->route('admin.rab.index', ['event_id' => $rab->event_id])
                         ->with('success', 'Item RAB berhasil diperbarui.');
    }

    public function destroy(Rab $rab)
    {
        $eventId = $rab->event_id;
        $rab->delete();

        return redirect()->route('admin.rab.index', ['event_id' => $eventId])
                         ->with('success', 'Item RAB berhasil dihapus.');
    }
}
