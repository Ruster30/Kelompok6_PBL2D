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
                        $invoice->update(["status_invoice" => "dp_lunas"]);
                        $this->paymentSchemeService->activatePelunasan($event->id);
                    } else {
                        $invoice->update(["status_invoice" => "lunas"]);
                        $event->update(["status_event" => "selesai"]);
                    }
                } else {
                    $invoice->update(["status_invoice" => "lunas"]);
                    $event->update(["status_event" => "selesai"]);
                }
                return;
            }

            $payment->invoice?->update(["status_invoice" => "ditolak"]);
        });
    }
}
