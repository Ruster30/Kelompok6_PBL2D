<?php

namespace App\Services;

use App\Interfaces\RabAdditionalDetailRepositoryInterface;
use App\Interfaces\RabRepositoryInterface;
use App\Models\Event;
use App\Models\Rab;
use App\Models\Vendor;

class RabService
{
    public function __construct(
        private RabRepositoryInterface $rabRepository,
        private RabAdditionalDetailRepositoryInterface $additionalDetailRepository,
    ) {}

    public function getRabData(?int $eventId): array
    {
        $events  = Event::orderBy("nama_event")->get();
        $vendors = Vendor::orderBy("nama_vendor")->get();

        $selectedEvent = null;
        $rabItems      = collect();
        $additionalDetail = null;

        if ($eventId) {
            $selectedEvent = Event::findOrFail($eventId);
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
        }

        if ($selectedEvent) {
            $rabItems = $this->rabRepository->getByEventId($selectedEvent->id);
            $additionalDetail = $this->additionalDetailRepository->getByEventId($selectedEvent->id);
        }

        return compact("events", "vendors", "selectedEvent", "rabItems", "additionalDetail");
    }

    public function createRabItem(array $data): Rab
    {
        $data["subtotal_biaya"] = $data["jumlah_item"] * $data["harga_satuan"];

        return $this->rabRepository->create($data);
    }

    public function updateRabItem(Rab $rab, array $data): Rab
    {
        $data["subtotal_biaya"] = $data["jumlah_item"] * $data["harga_satuan"];

        return $this->rabRepository->update($rab, $data);
    }

    public function deleteRabItem(Rab $rab): void
    {
        $this->rabRepository->delete($rab);
    }

    public function saveAdditionalDetails(int $eventId, array $data): void
    {
        $this->additionalDetailRepository->createOrUpdate($eventId, $data);
    }

    /**
     * Hitung Total Dibayar Klien berdasarkan data RAB dan Rincian Tambahan.
     * 
     * Rumus: DPP + PPN - PPh
     * DPP = Subtotal Vendor + Fee EO
     */
    public function getTotalDibayarKlien(int $eventId): float
    {
        $subtotalVendor = (float) Rab::where('event_id', $eventId)->sum('subtotal_biaya');
        $additional     = $this->additionalDetailRepository->getByEventId($eventId);

        if (!$additional) {
            return $subtotalVendor;
        }

        $feeNominal = $additional->fee_enabled
            ? $subtotalVendor * ($additional->fee_percent / 100)
            : 0;

        $dpp = $subtotalVendor + $feeNominal;

        $ppnNominal = $additional->ppn_enabled
            ? $dpp * ($additional->ppn_percent / 100)
            : 0;

        $pphNominal = $additional->pph_enabled
            ? $dpp * ($additional->pph_percent / 100)
            : 0;

        return $dpp + $ppnNominal - $pphNominal;
    }
}
