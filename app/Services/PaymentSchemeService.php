<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentScheme;
use App\Services\RabService;
use Illuminate\Support\Facades\DB;

class PaymentSchemeService
{
    public function __construct(
        private RabService $rabService,
    ) {}

    public function getScheme(int $eventId): ?PaymentScheme
    {
        return PaymentScheme::where("event_id", $eventId)->first();
    }

    public function saveScheme(int $eventId, array $data): PaymentScheme
    {
        return DB::transaction(function () use ($eventId, $data) {
            $scheme = PaymentScheme::updateOrCreate(
                ["event_id" => $eventId],
                [
                    "jenis_pembayaran" => $data["jenis_pembayaran"] ?? "full_payment",
                    "mode_dp"         => $data["mode_dp"] ?? null,
                    "nilai_dp"        => $data["nilai_dp"] ?? null,
                    "persentase_dp"   => $data["persentase_dp"] ?? null,
                ]
            );

            $this->generateInvoices($eventId, $scheme);

            return $scheme;
        });
    }

    protected function generateInvoices(int $eventId, PaymentScheme $scheme): void
    {
        $totalDibayarKlien = $this->rabService->getTotalDibayarKlien($eventId);
        Invoice::where("event_id", $eventId)
            ->whereNotIn("status_invoice", ["lunas", "dp_lunas", "dibayar"])
            ->delete();

        if ($scheme->jenis_pembayaran === "full_payment") {
            Invoice::create([
                "event_id"        => $eventId,
                "nomor_invoice"   => $this->generateNomorInvoice(),
                "total_invoice"   => $totalDibayarKlien,
                "status_invoice"  => "belum_bayar",
                "tanggal_invoice" => now()->toDateString(),
            ]);
        } else {
            $dpNominal = $scheme->dp_nominal;

            Invoice::create([
                "event_id"        => $eventId,
                "nomor_invoice"   => $this->generateNomorInvoice(),
                "total_invoice"   => $dpNominal,
                "status_invoice"  => "belum_bayar",
                "tanggal_invoice" => now()->toDateString(),
            ]);
        }
    }

    protected function generateNomorInvoice(): string
    {
        return sprintf("INV-%s-%03d",
            now()->format("Ymd"),
            Invoice::whereDate("created_at", today())->count() + 1
        );
    }

    public function getSchemeData(int $eventId): array
    {
        $scheme = $this->getScheme($eventId);
        $totalDibayarKlien = $this->rabService->getTotalDibayarKlien($eventId);
        $dpNominal = 0;
        $sisaNominal = $totalDibayarKlien;

        if ($scheme) {
            $dpNominal = $scheme->dp_nominal;
            $sisaNominal = $scheme->sisa_pelunasan;
        }

        return compact("scheme", "totalDibayarKlien", "dpNominal", "sisaNominal");
    }

    public function createInvoicePelunasan(int $eventId): Invoice
    {
        $scheme = $this->getScheme($eventId);
        $sisaNominal = $scheme?->sisa_pelunasan ?? 0;

        return Invoice::create([
            "event_id"        => $eventId,
            "nomor_invoice"   => $this->generateNomorInvoice(),
            "total_invoice"   => $sisaNominal,
            "status_invoice"  => "belum_bayar",
            "tanggal_invoice" => now()->toDateString(),
        ]);
    }

    public function activatePelunasan(int $eventId): Invoice
    {
        return $this->createInvoicePelunasan($eventId);
    }
}