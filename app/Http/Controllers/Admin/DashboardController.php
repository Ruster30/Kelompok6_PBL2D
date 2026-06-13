<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            // Total Event
            'totalEvents' => Event::count(),

            // Klien Aktif (user dengan role client)
            'totalClients' => User::where('role', 'client')->count(),

            // Total Vendor
            'totalVendors' => Vendor::count(),

            // Total Pendapatan (pembayaran diverifikasi)
            'revenue' => Payment::where('status_pembayaran', 'diverifikasi')->sum('nominal'),

            // Tugas belum selesai
            'pendingTasks' => Task::where('status', '!=', 'selesai')->count(),

            // Event terbaru (5 terbaru, with client relation)
            'recentEvents' => Event::with('client')
                                   ->latest()
                                   ->take(5)
                                   ->get(),
        ]);
    }
}
