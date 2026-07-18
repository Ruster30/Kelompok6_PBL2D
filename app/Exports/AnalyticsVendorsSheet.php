<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsVendorsSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['topVendors'] as $vendor) {
            $rows[] = [
                $vendor->nama_vendor,
                $vendor->jenis_vendor,
                $vendor->email,
                $vendor->rabs_count,
                'Rp ' . number_format($vendor->rabs_sum_subtotal_biaya ?? 0, 0, ',', '.'),
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Nama Vendor', 'Jenis Vendor', 'Email', 'Jumlah RAB', 'Total Nilai RAB'];
    }

    public function title(): string
    {
        return 'Vendor';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
