<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsInvoicesSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function query()
    {
        $filters = $this->data['filters'];
        
        return Invoice::with('event.client')
            ->whereHas('event', function($q) use ($filters) {
                $q->whereYear('created_at', $filters['year']);
                if ($filters['month']) $q->whereMonth('created_at', $filters['month']);
                if ($filters['status_event']) $q->where('status_event', $filters['status_event']);
                if ($filters['jenis_event']) $q->where('jenis_event', $filters['jenis_event']);
            })
            ->orderBy('created_at', 'desc');
    }

    public function map($invoice): array
    {
        return [
            $invoice->nomor_invoice,
            $invoice->event->nama_event ?? '-',
            $invoice->event->client->name ?? '-',
            'Rp ' . number_format($invoice->total_invoice, 0, ',', '.'),
            $invoice->status_label,
            $invoice->tanggal_invoice->format('d/m/Y'),
        ];
    }

    public function headings(): array
    {
        return ['No. Invoice', 'Event', 'Klien', 'Total', 'Status', 'Tanggal'];
    }

    public function title(): string
    {
        return 'Invoice';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
