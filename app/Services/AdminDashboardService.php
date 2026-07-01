<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Timeline;
use App\Models\User;
use App\Models\Vendor;

class AdminDashboardService
{
    public function getDashboardData(): array
    {
        return [
            'totalEvents'  => Event::count(),
            'totalClients' => User::where('role', 'client')->count(),
            'totalVendors' => Vendor::count(),
            'revenue'      => Payment::where('status_pembayaran', 'diverifikasi')->sum('nominal'),
            'pendingTasks' => Timeline::where('status_kegiatan', '!=', 'selesai')->count(),
            'recentEvents' => Event::with('client')->latest()->take(5)->get(),
        ];
    }
}
