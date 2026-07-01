<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    public function getAnalyticsData(): array
    {
        $totalEvents  = Event::count();
        $totalRevenue = Payment::where('status_pembayaran', 'diverifikasi')->sum('nominal');
        $activeClients = User::where('role', 'client')->count();

        $totalTasks    = DB::table('event_vendor')->count();
        $completedTasks = DB::table('event_vendor')->where('status_vendor', 'selesai')->count();
        $vendorPerformance = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $eventsByStatus = Event::selectRaw('status_event, count(*) as total')
            ->groupBy('status_event')
            ->pluck('total', 'status_event');

        $monthlyRevenue = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = \Carbon\Carbon::create()->month($m)->translatedFormat('F');
            $monthlyRevenue[$monthName] = Payment::where('status_pembayaran', 'diverifikasi')
                ->whereYear('tanggal_pembayaran', now()->year)
                ->whereMonth('tanggal_pembayaran', $m)
                ->sum('nominal');
        }

        return compact(
            'totalEvents', 'totalRevenue', 'activeClients', 'vendorPerformance',
            'eventsByStatus', 'monthlyRevenue'
        );
    }
}
