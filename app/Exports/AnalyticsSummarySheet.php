<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsSummarySheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Total Event', $this->data['totalEvents']],
            ['Event Berjalan', $this->data['eventsBerjalan']],
            ['Event Selesai', $this->data['eventsSelesai']],
            ['Total Klien', $this->data['totalClients']],
            ['Total Vendor', $this->data['totalVendors']],
            ['Total Invoice', $this->data['totalInvoices']],
            ['Total Pendapatan', 'Rp ' . number_format($this->data['totalRevenue'], 0, ',', '.')],
            ['Pembayaran Lunas', $this->data['paidInvoices']],
        ];
    }

    public function headings(): array
    {
        return ['Metrik', 'Nilai'];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
