<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['event', 'vendor'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_tugas', 'like', '%' . $request->search . '%')
                  ->orWhereHas('event', function ($q2) use ($request) {
                      $q2->where('nama_event', 'like', '%' . $request->search . '%');
                  });
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return view('admin.tasks.index', [
            'tasks'   => $query->paginate(10)->withQueryString(),
            'events'  => Event::orderBy('nama_event')->get(),
            'vendors' => Vendor::orderBy('nama_vendor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'event_id'   => 'required|exists:events,id',
            'vendor_id'  => 'nullable|exists:vendors,id',
            'prioritas'  => 'required|in:rendah,sedang,tinggi',
            'deadline'   => 'nullable|date',
            'status'     => 'required|in:ditugaskan,dikerjakan,selesai',
            'deskripsi'  => 'nullable|string',
        ]);

        Task::create($data);

        return redirect()->route('admin.tasks.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'event_id'   => 'required|exists:events,id',
            'vendor_id'  => 'nullable|exists:vendors,id',
            'prioritas'  => 'required|in:rendah,sedang,tinggi',
            'deadline'   => 'nullable|date',
            'status'     => 'required|in:ditugaskan,dikerjakan,selesai',
            'deskripsi'  => 'nullable|string',
        ]);

        $task->update($data);

        return redirect()->route('admin.tasks.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas berhasil dihapus.');
    }
}