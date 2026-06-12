<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
           // Total Event
            'totalEvents' => Event::count(),

            // Total Client
            'totalClients' => User::where('role', 'client')->count(),

            // Total Vendor
            'totalVendors' => User::where('role', 'vendor')->count(),

            // Total Pendapatan
            'revenue' => Payment::where('status_pembayaran', 'diverifikasi')
                                ->sum('nominal'),

            // Event terbaru
            'recentEvents' => Event::latest()
                                   ->take(5)
                                   ->get(),
        ]);
    }
}
