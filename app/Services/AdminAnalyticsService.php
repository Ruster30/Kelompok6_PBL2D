<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAnalyticsService
{
    public function getAnalyticsData(array $filters = []): array
    {
        $year = isset($filters['year'])
            ? (int) $filters['year']
            : now()->year;

        $month = !empty($filters['month'])
            ? (int) $filters['month']
            : null;
        $statusEvent = $filters['status_event'] ?? null;
        $jenisEvent = $filters['jenis_event'] ?? null;

        // Base queries with filters
        $eventQuery = $this->buildEventQuery($year, $month, $statusEvent, $jenisEvent);

        // Statistics Cards
        $totalEvents = (clone $eventQuery)->count();
        $eventsBerjalan = (clone $eventQuery)->where('status_event', 'berjalan')->count();
        $eventsSelesai = (clone $eventQuery)->where('status_event', 'selesai')->count();
        $totalClients = User::where('role', 'client')->count();
        $totalVendors = Vendor::count();
        
        $totalInvoices = Invoice::whereHas('event', function($q) use ($year, $month, $statusEvent, $jenisEvent) {
            $q->whereYear('created_at', $year);
            if ($month) $q->whereMonth('created_at', $month);
            if ($statusEvent) $q->where('status_event', $statusEvent);
            if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
        })->count();
        
        // Total Revenue: gunakan Payment yang sudah diverifikasi, dengan filter event jika ada
        $revenueQuery = Payment::where('status_pembayaran', 'diverifikasi')
            ->whereYear('tanggal_pembayaran', $year);
        if ($month) {
            $revenueQuery->whereMonth('tanggal_pembayaran', $month);
        }
        if ($statusEvent || $jenisEvent) {
            $revenueQuery->whereHas('invoice.event', function ($q) use ($statusEvent, $jenisEvent) {
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
            });
        }
        $totalRevenue = $revenueQuery->sum('nominal');

        $paidInvoices = Invoice::where('status_invoice', 'lunas')
            ->whereHas('event', function($q) use ($year, $month, $statusEvent, $jenisEvent) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
            })->count();

        // Chart Data - Monthly Revenue
        $monthlyRevenue = $this->getMonthlyRevenue($year, $statusEvent, $jenisEvent);
        
        // Chart Data - Monthly Events
        $monthlyEvents = $this->getMonthlyEvents($year, $statusEvent, $jenisEvent);
        
        // Chart Data - Events by Status
        $eventsByStatus = (clone $eventQuery)
            ->selectRaw('status_event, count(*) as total')
            ->groupBy('status_event')
            ->pluck('total', 'status_event')
            ->toArray();
        
        // Chart Data - Events by Type
        $eventsByType = (clone $eventQuery)
            ->selectRaw('jenis_event, count(*) as total')
            ->groupBy('jenis_event')
            ->pluck('total', 'jenis_event')
            ->toArray();

        // Top Tables
        $topClients = $this->getTopClients($year, $month, $statusEvent, $jenisEvent);
        $topVendors = $this->getTopVendors($year, $month, $statusEvent, $jenisEvent);
        $topEvents = $this->getTopEvents($year, $month, $statusEvent, $jenisEvent);

        // Filter Options
        $availableYears = $this->getAvailableYears();
        $availableStatuses = ['menunggu', 'diproses', 'berjalan', 'selesai', 'dibatalkan'];
        $availableTypes = Event::distinct()->pluck('jenis_event')->filter()->toArray();

        return compact(
            'totalEvents', 'eventsBerjalan', 'eventsSelesai', 'totalClients', 'totalVendors',
            'totalInvoices', 'totalRevenue', 'paidInvoices',
            'monthlyRevenue', 'monthlyEvents', 'eventsByStatus', 'eventsByType',
            'topClients', 'topVendors', 'topEvents',
            'availableYears', 'availableStatuses', 'availableTypes',
            'filters'
        );
    }

    private function buildEventQuery($year, $month, $statusEvent, $jenisEvent)
    {
        $query = Event::query()->whereYear('created_at', $year);
        
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($statusEvent) {
            $query->where('status_event', $statusEvent);
        }
        if ($jenisEvent) {
            $query->where('jenis_event', $jenisEvent);
        }
        
        return $query;
    }

    private function getMonthlyRevenue($year, $statusEvent, $jenisEvent): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $query = Payment::where('status_pembayaran', 'diverifikasi')
                ->whereYear('tanggal_pembayaran', $year)
                ->whereMonth('tanggal_pembayaran', $m);
            
            if ($statusEvent || $jenisEvent) {
                $query->whereHas('invoice.event', function($q) use ($statusEvent, $jenisEvent) {
                    if ($statusEvent) $q->where('status_event', $statusEvent);
                    if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
                });
            }
            
            $data[] = $query->sum('nominal');
        }
        return $data;
    }

    private function getMonthlyEvents($year, $statusEvent, $jenisEvent): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $query = Event::whereYear('created_at', $year)
                ->whereMonth('created_at', $m);
            
            if ($statusEvent) $query->where('status_event', $statusEvent);
            if ($jenisEvent) $query->where('jenis_event', $jenisEvent);
            
            $data[] = $query->count();
        }
        return $data;
    }

    private function getTopClients($year, $month, $statusEvent, $jenisEvent, $limit = 10)
    {
        return User::where('role', 'client')
            ->withCount(['events' => function($q) use ($year, $month, $statusEvent, $jenisEvent) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
            }])
            ->with(['events' => function($q) use ($year, $month, $statusEvent, $jenisEvent) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
                $q->with(['payments' => function ($pq) {
                    $pq->where('status_pembayaran', 'diverifikasi');
                }]);
            }])
            ->get()
            ->map(function($client) {
                $client->total_invoice_value = $client->events->sum(function($event) {
                    return $event->payments->sum('nominal');
                });
                return $client;
            })
            ->filter(function($client) {
                return $client->events_count > 0;
            })
            ->sortByDesc('total_invoice_value')
            ->take($limit);
    }

    private function getTopVendors($year, $month, $statusEvent, $jenisEvent, $limit = 10)
    {
        return Vendor::withCount(['rabs' => function($q) use ($year, $month, $statusEvent, $jenisEvent) {
                $q->whereHas('event', function($eq) use ($year, $month, $statusEvent, $jenisEvent) {
                    $eq->whereYear('created_at', $year);
                    if ($month) $eq->whereMonth('created_at', $month);
                    if ($statusEvent) $eq->where('status_event', $statusEvent);
                    if ($jenisEvent) $eq->where('jenis_event', $jenisEvent);
                });
            }])
            ->withSum(['rabs' => function($q) use ($year, $month, $statusEvent, $jenisEvent) {
                $q->whereHas('event', function($eq) use ($year, $month, $statusEvent, $jenisEvent) {
                    $eq->whereYear('created_at', $year);
                    if ($month) $eq->whereMonth('created_at', $month);
                    if ($statusEvent) $eq->where('status_event', $statusEvent);
                    if ($jenisEvent) $eq->where('jenis_event', $jenisEvent);
                });
            }], 'subtotal_biaya')
            ->having('rabs_count', '>', 0)
            ->orderBy('rabs_sum_subtotal_biaya', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getTopEvents($year, $month, $statusEvent, $jenisEvent, $limit = 10)
    {
        $query = Event::with(['client', 'invoices' => function ($q) {
                $q->orderBy('id', 'asc');
            }])
            ->whereYear('created_at', $year);
        
        if ($month) $query->whereMonth('created_at', $month);
        if ($statusEvent) $query->where('status_event', $statusEvent);
        if ($jenisEvent) $query->where('jenis_event', $jenisEvent);
        
        return $query->get()
            ->map(function($event) {
                // Hanya gunakan invoice pertama (invoice utama) untuk menghindari double count
                $firstInvoice = $event->invoices->first();
                $event->total_invoice_value = $firstInvoice ? (float) $firstInvoice->total_invoice : 0.0;
                return $event;
            })
            ->sortByDesc('total_invoice_value')
            ->take($limit);
    }

    private function getAvailableYears(): array
    {
        $minYear = Event::min(DB::raw('YEAR(created_at)')) ?? now()->year;
        $maxYear = now()->year;
        return range($maxYear, $minYear);
    }
}
