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
        ];

        $data = $this->analyticsService->getAnalyticsData($filters);
        
        return view('admin.analytics.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $filters = [
            'year' => $request->input('year', now()->year),
            'month' => $request->input('month'),
            'status_event' => $request->input('status_event'),
            'jenis_event' => $request->input('jenis_event'),
        ];

        $data = $this->analyticsService->getAnalyticsData($filters);
        
        // Add company information
        $data['companyName'] = 'Your Company Name'; // You can fetch from settings
        $data['companyLogo'] = public_path('images/logo.png'); // Adjust path as needed
        $data['filters'] = $filters; // Add filters to data
        
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
        ];

        $filename = 'Laporan-Analitik-' . $filters['year'] . 
                    ($filters['month'] ? '-' . str_pad($filters['month'], 2, '0', STR_PAD_LEFT) : '') . 
                    '.xlsx';
        
        return Excel::download(new AnalyticsExport($filters), $filename);
    }
}
