<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\EventVendor;
use App\Models\Task;
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
            'nama_tugas' => 'nullable|string|max:255',
            'prioritas' => 'nullable|in:rendah,sedang,tinggi',
            'deskripsi' => 'nullable|string',
        ]);

        $assignment = EventVendor::create($data);
        $this->syncTask($assignment, $request);

        return redirect()
            ->route('admin.event-vendors.index')
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
            'nama_tugas' => 'nullable|string|max:255',
            'prioritas' => 'nullable|in:rendah,sedang,tinggi',
            'deskripsi' => 'nullable|string',
        ]);

        $task->update($data);
        $this->syncTask($task, $request);

        return redirect()
            ->route('admin.event-vendors.index')
            ->with('success', 'Penugasan vendor berhasil diperbarui.');
    }

    public function destroy(EventVendor $task)
    {
        Task::where('event_id', $task->event_id)
            ->where('vendor_id', $task->vendor_id)
            ->where('nama_tugas', 'like', 'Penugasan:%')
            ->delete();

        $task->delete();
        return redirect()
            ->route('admin.event-vendors.index')
            ->with('success', 'Penugasan vendor berhasil dihapus.');
    }

    private function syncTask(EventVendor $assignment, Request $request): void
    {
        $eventName = $assignment->event?->nama_event ?? 'Event';
        $taskName = $request->filled('nama_tugas')
            ? $request->nama_tugas
            : 'Penugasan: ' . $eventName;

        Task::updateOrCreate(
            [
                'event_id' => $assignment->event_id,
                'vendor_id' => $assignment->vendor_id,
                'nama_tugas' => $taskName,
            ],
            [
                'prioritas' => $request->prioritas ?? 'sedang',
                'deadline' => $assignment->jadwal_vendor,
                'status' => $assignment->status_vendor,
                'deskripsi' => $request->deskripsi ?: 'Tugas otomatis dari penugasan vendor.',
            ]
        );
    }
}
