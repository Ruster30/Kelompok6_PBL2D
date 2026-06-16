<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\EventVendor;
use Illuminate\Http\Request;

class EventVendorController extends Controller
{
    public function index(Request $request)
    {
        $query = EventVendor::with(['event', 'vendor'])->latest();

        if ($request->search) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('nama_event', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status_vendor', $request->status);
        }

        return view('admin.EventVendor.index', [
            'eventVendors' => $query->paginate(10)->withQueryString(),
            'events' => Event::orderBy('nama_event')->get(),
            'vendors' => Vendor::orderBy('nama_vendor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'vendor_id' => 'required|exists:vendors,id',
            'jadwal_vendor' => 'nullable|date',
            'status_vendor' => 'required|in:ditugaskan,dikerjakan,selesai',
            'harga_vendor' => 'nullable|numeric',
        ]);

        EventVendor::create($data);

        return redirect()
            ->route('admin.event-vendors    .index')
            ->with('success', 'Penugasan vendor berhasil dibuat.');
    }

    public function update(Request $request, EventVendor $task)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'vendor_id' => 'required|exists:vendors,id',
            'jadwal_vendor' => 'nullable|date',
            'status_vendor' => 'required|in:ditugaskan,dikerjakan,selesai',
            'harga_vendor' => 'nullable|numeric',
        ]);

        $task->update($data);

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Penugasan vendor berhasil diperbarui.');
    }

    public function destroy(EventVendor $task)
    {
        $task->delete();
        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Penugasan vendor berhasil dihapus.');
    }
    }