<?php

namespace App\Exports;

use App\Services\AdminAnalyticsService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnalyticsExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $analyticsService = new AdminAnalyticsService();
        $data = $analyticsService->getAnalyticsData($this->filters);

        return [
            new AnalyticsSummarySheet($data),
            new AnalyticsEventsSheet($data),
            new AnalyticsInvoicesSheet($data),
            new AnalyticsPaymentsSheet($data),
            new AnalyticsClientsSheet($data),
            new AnalyticsVendorsSheet($data),
        ];
    }
}
