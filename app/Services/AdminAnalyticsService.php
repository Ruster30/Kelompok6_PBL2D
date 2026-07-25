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
        $period = $filters['period'] ?? 'all';
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        [$dateFrom, $dateTo] = $this->resolvePeriod($period, $startDate, $endDate);

        $filters['date_from'] = $dateFrom;
        $filters['date_to'] = $dateTo;

        $year = isset($filters['year'])
            ? (int) $filters['year']
            : now()->year;

        $month = !empty($filters['month'])
            ? (int) $filters['month']
            : null;
        $statusEvent = $filters['status_event'] ?? null;
        $jenisEvent = $filters['jenis_event'] ?? null;

        // Base queries with filters
        $eventQuery = $this->buildEventQuery($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo);

        // Statistics Cards
        $totalEvents = (clone $eventQuery)->count();
        $eventsBerjalan = (clone $eventQuery)->where('status_event', 'berjalan')->count();
        $eventsSelesai = (clone $eventQuery)->where('status_event', 'selesai')->count();
        $totalClients = User::where('role', 'client')->count();
        $totalVendors = Vendor::count();
        
        $totalInvoices = Invoice::whereHas('event', function($q) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
            $q->whereYear('created_at', $year);
            if ($month) $q->whereMonth('created_at', $month);
            if ($statusEvent) $q->where('status_event', $statusEvent);
            if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
            $this->applyDateFilter($q, 'created_at', $dateFrom, $dateTo);
        })->count();
        
        // Total Revenue: gunakan Payment yang sudah diverifikasi, dengan filter event jika ada
        $revenueQuery = Payment::where('status_pembayaran', 'diverifikasi')
            ->whereYear('tanggal_pembayaran', $year);
        if ($month) {
            $revenueQuery->whereMonth('tanggal_pembayaran', $month);
        }
        $this->applyDateFilter($revenueQuery, 'tanggal_pembayaran', $dateFrom, $dateTo);
        if ($statusEvent || $jenisEvent) {
            $revenueQuery->whereHas('invoice.event', function ($q) use ($statusEvent, $jenisEvent) {
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
            });
        }
        $totalRevenue = $revenueQuery->sum('nominal');

        $paidInvoices = Invoice::where('status_invoice', 'lunas')
            ->whereHas('event', function($q) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
                $this->applyDateFilter($q, 'created_at', $dateFrom, $dateTo);
            })->count();

        // Chart Data - Monthly Revenue
        $monthlyRevenue = $this->getMonthlyRevenue($year, $statusEvent, $jenisEvent, $dateFrom, $dateTo);
        
        // Chart Data - Monthly Events
        $monthlyEvents = $this->getMonthlyEvents($year, $statusEvent, $jenisEvent, $dateFrom, $dateTo);
        
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
        $topClients = $this->getTopClients($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo);
        $topVendors = $this->getTopVendors($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo);
        $topEvents = $this->getTopEvents($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo);

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

    private function buildEventQuery($year, $month, $statusEvent, $jenisEvent, $dateFrom = null, $dateTo = null)
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
        $this->applyDateFilter($query, 'created_at', $dateFrom, $dateTo);
        
        return $query;
    }

    private function getMonthlyRevenue($year, $statusEvent, $jenisEvent, $dateFrom = null, $dateTo = null): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $query = Payment::where('status_pembayaran', 'diverifikasi')
                ->whereYear('tanggal_pembayaran', $year)
                ->whereMonth('tanggal_pembayaran', $m);
            
            $this->applyDateFilter($query, 'tanggal_pembayaran', $dateFrom, $dateTo);
            
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

    private function getMonthlyEvents($year, $statusEvent, $jenisEvent, $dateFrom = null, $dateTo = null): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $query = Event::whereYear('created_at', $year)
                ->whereMonth('created_at', $m);
            
            if ($statusEvent) $query->where('status_event', $statusEvent);
            if ($jenisEvent) $query->where('jenis_event', $jenisEvent);
            $this->applyDateFilter($query, 'created_at', $dateFrom, $dateTo);
            
            $data[] = $query->count();
        }
        return $data;
    }

    private function getTopClients($year, $month, $statusEvent, $jenisEvent, $dateFrom = null, $dateTo = null, $limit = 10)
    {
        return User::where('role', 'client')
            ->withCount(['events' => function($q) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
                $this->applyDateFilter($q, 'created_at', $dateFrom, $dateTo);
            }])
            ->with(['events' => function($q) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
                if ($statusEvent) $q->where('status_event', $statusEvent);
                if ($jenisEvent) $q->where('jenis_event', $jenisEvent);
                $this->applyDateFilter($q, 'created_at', $dateFrom, $dateTo);
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

    private function getTopVendors($year, $month, $statusEvent, $jenisEvent, $dateFrom = null, $dateTo = null, $limit = 10)
    {
        return Vendor::withCount(['rabs' => function($q) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                $q->whereHas('event', function($eq) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                    $eq->whereYear('created_at', $year);
                    if ($month) $eq->whereMonth('created_at', $month);
                    if ($statusEvent) $eq->where('status_event', $statusEvent);
                    if ($jenisEvent) $eq->where('jenis_event', $jenisEvent);
                    $this->applyDateFilter($eq, 'created_at', $dateFrom, $dateTo);
                });
            }])
            ->withSum(['rabs' => function($q) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                $q->whereHas('event', function($eq) use ($year, $month, $statusEvent, $jenisEvent, $dateFrom, $dateTo) {
                    $eq->whereYear('created_at', $year);
                    if ($month) $eq->whereMonth('created_at', $month);
                    if ($statusEvent) $eq->where('status_event', $statusEvent);
                    if ($jenisEvent) $eq->where('jenis_event', $jenisEvent);
                    $this->applyDateFilter($eq, 'created_at', $dateFrom, $dateTo);
                });
            }], 'subtotal_biaya')
            ->having('rabs_count', '>', 0)
            ->orderBy('rabs_sum_subtotal_biaya', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getTopEvents($year, $month, $statusEvent, $jenisEvent, $dateFrom = null, $dateTo = null, $limit = 10)
    {
        $query = Event::with(['client', 'invoices' => function ($q) {
                $q->orderBy('id', 'asc');
            }])
            ->whereYear('created_at', $year);
        
        if ($month) $query->whereMonth('created_at', $month);
        if ($statusEvent) $query->where('status_event', $statusEvent);
        if ($jenisEvent) $query->where('jenis_event', $jenisEvent);
        $this->applyDateFilter($query, 'created_at', $dateFrom, $dateTo);
        
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

    private function resolvePeriod(?string $period, ?string $startDate, ?string $endDate): array
    {
        if (!$period || $period === 'all') {
            return [null, null];
        }

        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'last_7_days' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'last_30_days' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : null,
                $endDate ? Carbon::parse($endDate)->endOfDay() : null,
            ],
            default => [null, null],
        };
    }

    private function applyDateFilter($query, string $column, $dateFrom, $dateTo): void
    {
        if ($dateFrom && $dateTo) {
            $query->whereBetween($column, [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where($column, '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where($column, '<=', $dateTo);
        }
    }
}
