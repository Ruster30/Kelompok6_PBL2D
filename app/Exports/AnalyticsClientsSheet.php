<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsClientsSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['topClients'] as $client) {
            $rows[] = [
                $client->name,
                $client->email,
                $client->events_count,
                'Rp ' . number_format($client->total_invoice_value ?? 0, 0, ',', '.'),
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Nama Klien', 'Email', 'Jumlah Event', 'Total Nilai Event'];
    }

    public function title(): string
    {
        return 'Klien';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
