<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsEventsSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['topEvents'] as $event) {
            $rows[] = [
                $event->nama_event,
                $event->jenis_event,
                $event->client->name ?? '-',
                $event->status_event,
                'Rp ' . number_format($event->total_invoice_value, 0, ',', '.'),
                $event->tanggal_event->format('d/m/Y'),
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Nama Event', 'Jenis', 'Klien', 'Status', 'Nilai', 'Tanggal'];
    }

    public function title(): string
    {
        return 'Event';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
