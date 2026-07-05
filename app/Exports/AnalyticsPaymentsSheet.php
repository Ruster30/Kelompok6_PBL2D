<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsPaymentsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function query()
    {
        $filters = $this->data['filters'];
        
        return Payment::with('invoice.event.client')
            ->where('status_pembayaran', 'diverifikasi')
            ->whereYear('tanggal_pembayaran', $filters['year'])
            ->when($filters['month'], function($q) use ($filters) {
                $q->whereMonth('tanggal_pembayaran', $filters['month']);
            })
            ->orderBy('tanggal_pembayaran', 'desc');
    }

    public function map($payment): array
    {
        return [
            $payment->invoice->nomor_invoice ?? '-',
            $payment->invoice->event->nama_event ?? '-',
            $payment->invoice->event->client->name ?? '-',
            'Rp ' . number_format($payment->nominal, 0, ',', '.'),
            $payment->jenis_pembayaran,
            $payment->tanggal_pembayaran->format('d/m/Y'),
        ];
    }

    public function headings(): array
    {
        return ['No. Invoice', 'Event', 'Klien', 'Nominal', 'Jenis Pembayaran', 'Tanggal'];
    }

    public function title(): string
    {
        return 'Pembayaran';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
