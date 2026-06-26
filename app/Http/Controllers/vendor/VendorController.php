<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Task;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class VendorController extends Controller
{
    /**
     * Ringkasan (Dashboard)
     */
    public function ringkasan()
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $totalEvent    = Event::whereHas('vendors', fn($q) => $q->where('vendor_id', $vendor->id))->count();
        $tugasAktif    = Task::where('vendor_id', $vendor->id)->whereNotIn('status', ['selesai'])->count();
        $tugasSelesai  = Task::where('vendor_id', $vendor->id)->where('status', 'selesai')->count();

        $eventTerdekat = Event::whereHas('vendors', fn($q) => $q->where('vendor_id', $vendor->id))
        ->where('tanggal_event', '>=', now())
        ->orderBy('tanggal_event')
        ->take(3)
        ->get();

        $tugasMendatang = Task::where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['selesai'])
            ->orderBy('deadline')
            ->take(5)
            ->with('event')
            ->get();

        return view('vendor.ringkasan', compact(
            'totalEvent', 'tugasAktif', 'tugasSelesai',
            'eventTerdekat', 'tugasMendatang'
        ));
    }

    /**
     * Event Saya
     */
    public function eventSaya(Request $request)
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $events = Event::whereHas('vendors', fn($q) => $q->where('vendor_id', $vendor->id))
            ->with('client')
            ->when($request->search, fn($q) =>
                $q->where('nama_event', 'like', '%' . $request->search . '%')
            )
            ->orderBy('tanggal_event')
            ->get();

        return view('vendor.event-saya', compact('events'));
    }

    /**
     * Jadwal Event
     */
    public function jadwal(Request $request)
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        // Ambil semua event vendor untuk dropdown
        $events = Event::whereHas('vendors', fn($q) => $q->where('vendor_id', $vendor->id))
            ->orderBy('tanggal_event')
            ->get();

        $selectedEvent = $request->filled('event') ? (int) $request->event : optional($events->first())->id;

        // Pastikan event yang dipilih memang milik vendor ini
        if ($selectedEvent && !$events->pluck('id')->contains($selectedEvent)) {
            $selectedEvent = optional($events->first())->id;
        }

        // Ambil jadwal dari event terpilih
        $jadwal = $selectedEvent
            ? \App\Models\Jadwal::where('event_id', $selectedEvent)
                ->orderBy('tanggal')
                ->get()
            : collect();

        return view('vendor.jadwal', compact('events', 'jadwal', 'selectedEvent'));
    }

    /**
     * Pengaturan (Profile Vendor - read only)
     */
    public function pengaturan()
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);
        return view('vendor.pengaturan', compact('vendor'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}