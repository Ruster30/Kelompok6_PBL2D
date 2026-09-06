<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    public function __construct(
        private AdminAnalyticsService $analyticsService,
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'year' => $request->input('year', now()->year),
            'month' => $request->input('month'),
            'status_event' => $request->input('status_event'),
            'jenis_event' => $request->input('jenis_event'),
            'period' => $request->input('period', 'all'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $data = $this->analyticsService->getAnalyticsData($filters);
        $data['activePeriod'] = $filters['period'];
        $data['startDate'] = $filters['start_date'];
        $data['endDate'] = $filters['end_date'];
        
        return view('admin.analytics.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $filters = [
            'year' => $request->input('year', now()->year),
            'month' => $request->input('month'),
            'status_event' => $request->input('status_event'),
            'jenis_event' => $request->input('jenis_event'),
            'period' => $request->input('period', 'all'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $data = $this->analyticsService->getAnalyticsData($filters);
        $data['activePeriod'] = $filters['period'];
        $data['startDate'] = $filters['start_date'];
        $data['endDate'] = $filters['end_date'];
        
        // Add company information
        $data['companyName'] = 'Your Company Name';
        $data['companyLogo'] = public_path('images/logo.png');
        $data['filters'] = $filters;
        
        $pdf = Pdf::loadView('admin.analytics.pdf', $data);
        $pdf->setPaper('A4', 'landscape'); // Changed from 'portrait' to 'landscape' to match CSS
        
        $filename = 'Laporan-Analitik-' . $filters['year'] . 
                    ($filters['month'] ? '-' . str_pad($filters['month'], 2, '0', STR_PAD_LEFT) : '') . 
                    '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'year' => $request->input('year', now()->year),
            'month' => $request->input('month'),
            'status_event' => $request->input('status_event'),
            'jenis_event' => $request->input('jenis_event'),
            'period' => $request->input('period', 'all'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $filename = 'Laporan-Analitik-' . $filters['year'] . 
                    ($filters['month'] ? '-' . str_pad($filters['month'], 2, '0', STR_PAD_LEFT) : '') . 
                    '.xlsx';
        
        return Excel::download(new AnalyticsExport($filters), $filename);
    }
}
