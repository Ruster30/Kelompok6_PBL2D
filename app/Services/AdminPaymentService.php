<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdminPaymentService
{
    public function getIndexData(): array
    {
        return [
            'payments' => Payment::with(['invoice.event'])
                ->latest()
                ->paginate(15),
        ];
    }

    public function getShowData(Payment $payment): array
    {
        $payment->load('invoice.event');
        return compact('payment');
    }

    public function verifyPayment(Payment $payment, string $status): void
    {
        DB::transaction(function () use ($payment, $status) {
            $payment->update(['status_pembayaran' => $status]);

            if ($status === 'diverifikasi') {
                if ($payment->jenis_pembayaran === 'dp') {
                    $payment->invoice?->update(['status_invoice' => 'dp_lunas']);
                } else {
                    $payment->invoice?->update(['status_invoice' => 'lunas']);
                    $payment->invoice?->event?->update(['status_event' => 'selesai']);
                }
                return;
            }

            $payment->invoice?->update(['status_invoice' => 'ditolak']);
        });
    }

    public function canSendPelunasan(Payment $payment): ?string
    {
        $payment->load('invoice.event');

        if ($payment->jenis_pembayaran !== 'dp' || $payment->status_pembayaran !== 'diverifikasi') {
            return 'Hanya pembayaran DP yang sudah diverifikasi yang dapat dibuatkan invoice pelunasan.';
        }

        if ($payment->invoice?->event?->invoices()->count() > 1) {
            return 'Invoice pelunasan sudah pernah dibuat untuk event ini.';
        }

        return null;
    }

    public function sendPelunasan(Payment $payment): void
    {
        $payment->load('invoice.event');
        $invoice = $payment->invoice;
        $event = $invoice->event;
        $sisaPembayaran = max(0, $invoice->total_invoice - $payment->nominal);

        DB::transaction(function () use ($event, $sisaPembayaran) {
            Invoice::create([
                'event_id' => $event->id,
                'nomor_invoice' => sprintf(
                    'INV-%s-%03d',
                    now()->format('Ymd'),
                    Invoice::whereDate('created_at', today())->count() + 1
                ),
                'total_invoice' => $sisaPembayaran,
                'status_invoice' => 'belum_bayar',
                'tanggal_invoice' => now()->toDateString(),
            ]);

            $documentBuilder = new DocumentBuilderService();
            $documentBuilder->sendToClient($event, 'invoice');
        });
    }
}
