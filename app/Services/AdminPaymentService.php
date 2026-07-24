<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdminPaymentService
{
    public function __construct(
        private PaymentSchemeService $paymentSchemeService,
    ) {}

    public function getIndexData(): array
    {
        return [
            "payments" => Payment::with(["invoice.event"])
                ->latest()
                ->paginate(15),
        ];
    }

    public function getShowData(Payment $payment): array
    {
        $payment->load("invoice.event");
        return compact("payment");
    }

    public function verifyPayment(Payment $payment, string $status): void
    {
        DB::transaction(function () use ($payment, $status) {
            $payment->update(["status_pembayaran" => $status]);

            if ($status === "diverifikasi") {
                $invoice = $payment->invoice;
                $event = $invoice->event;
                $scheme = $this->paymentSchemeService->getScheme($event->id);

                if ($scheme && $scheme->jenis_pembayaran === "dp_dan_pelunasan") {
                    $isFirstInvoice = $event->invoices()
                        ->where("status_invoice", "!=", "menunggu_dp")
                        ->orderBy("id", "asc")
                        ->first()?->id === $invoice->id;

                    if ($isFirstInvoice) {
                        // DP diverifikasi: jangan auto-buat pelunasan & kwitansi
                        // Admin akan kirim manual lewat tombol Kirim Pelunasan & Kirim Kwitansi
                        $invoice->update(["status_invoice" => "dp_lunas"]);
                    } else {
                        // Pelunasan diverifikasi
                        $invoice->update(["status_invoice" => "lunas"]);
                        $event->update(["status_event" => "selesai", "status_pembayaran" => "lunas"]);
                    }
                } else {
                    // Full payment diverifikasi
                    $invoice->update(["status_invoice" => "lunas"]);
                    $event->update(["status_event" => "selesai", "status_pembayaran" => "lunas"]);
                }

                return;
            }

            $payment->invoice?->update(["status_invoice" => "ditolak"]);
        });
    }

    /**
     * Cek apakah pelunasan bisa dikirim.
     * Returns error message string jika tidak bisa, null jika bisa.
     */
    public function canSendPelunasan(Payment $payment): ?string
    {
        $payment->load("invoice.event");

        if (!$payment->invoice || !$payment->invoice->event) {
            return "Data pembayaran tidak valid.";
        }

        $event = $payment->invoice->event;
        $scheme = $this->paymentSchemeService->getScheme($event->id);

        if (!$scheme || $scheme->jenis_pembayaran !== "dp_dan_pelunasan") {
            return "Skema pembayaran bukan DP + Pelunasan.";
        }

        $invoicePelunasan = $event->invoices()
            ->whereIn("status_invoice", ["belum_bayar", "terkirim", "draft"])
            ->where("id", "!=", $payment->invoice_id)
            ->first();

        if ($invoicePelunasan) {
            return "Invoice pelunasan sudah ada dan belum dibayar.";
        }

        return null;
    }

    /**
     * Kirim invoice pelunasan ke client.
     */
    public function sendPelunasan(Payment $payment): void
    {
        $payment->load("invoice.event");
        $event = $payment->invoice->event;

        DB::transaction(function () use ($event) {
            // Buat invoice pelunasan
            $this->paymentSchemeService->createInvoicePelunasan($event->id);

            // Kirim notifikasi ke client
            if ($event->client) {
                \App\Models\Notification::create([
                    'user_id' => $event->client_id,
                    'judul'   => 'Invoice Pelunasan Tersedia',
                    'pesan'   => 'Invoice pelunasan untuk event "' . $event->nama_event . '" telah diterbitkan. Silakan cek tagihan & pembayaran Anda.',
                    'tipe'    => 'info',
                    'dibaca'  => false,
                ]);
            }
        });
    }
}
