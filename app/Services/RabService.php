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
        // Normalisasi nilai boolean dari form (checkbox mengirim '1' atau '0')
        $data['fee_enabled'] = isset($data['fee_enabled']) && $data['fee_enabled'] == '1' ? true : false;
        $data['ppn_enabled'] = isset($data['ppn_enabled']) && $data['ppn_enabled'] == '1' ? true : false;
        $data['pph_enabled'] = isset($data['pph_enabled']) && $data['pph_enabled'] == '1' ? true : false;

        // Nominal: simpan null jika tidak diisi (agar sistem auto-hitung dari persentase)
        $data['fee_nominal'] = isset($data['fee_nominal']) && $data['fee_nominal'] !== '' ? (float) $data['fee_nominal'] : null;
        $data['ppn_nominal'] = isset($data['ppn_nominal']) && $data['ppn_nominal'] !== '' ? (float) $data['ppn_nominal'] : null;
        $data['pph_nominal'] = isset($data['pph_nominal']) && $data['pph_nominal'] !== '' ? (float) $data['pph_nominal'] : null;

        $this->additionalDetailRepository->createOrUpdate($eventId, $data);
    }

    /**
     * Hitung Total Dibayar Klien berdasarkan data RAB dan Rincian Tambahan.
     *
     * Rumus:
     *   Subtotal = Total RAB + Fee EO
     *   Grandtotal = Subtotal + PPN + PPh
     *
     * Catatan: PPN dan PPh keduanya DITAMBAHKAN ke subtotal (bukan PPh dikurangi).
     * Jika nominal disimpan secara manual, gunakan nilai tersebut.
     * Jika tidak (null), hitung otomatis dari persentase.
     */
    public function getTotalDibayarKlien(int $eventId): float
    {
        $subtotalVendor = (float) Rab::where('event_id', $eventId)->sum('subtotal_biaya');
        $additional     = $this->additionalDetailRepository->getByEventId($eventId);

        if (!$additional) {
            return $subtotalVendor;
        }

        // Fee EO
        if ($additional->fee_enabled) {
            $feeNominal = $additional->fee_nominal !== null
                ? (float) $additional->fee_nominal
                : $subtotalVendor * ((float) $additional->fee_percent / 100);
        } else {
            $feeNominal = 0;
        }

        $subtotal = $subtotalVendor + $feeNominal;

        // PPN
        if ($additional->ppn_enabled) {
            $ppnNominal = $additional->ppn_nominal !== null
                ? (float) $additional->ppn_nominal
                : $subtotal * ((float) $additional->ppn_percent / 100);
        } else {
            $ppnNominal = 0;
        }

        // PPh
        if ($additional->pph_enabled) {
            $pphNominal = $additional->pph_nominal !== null
                ? (float) $additional->pph_nominal
                : $subtotal * ((float) $additional->pph_percent / 100);
        } else {
            $pphNominal = 0;
        }

        // Grandtotal = Subtotal + PPN + PPh (PPh DITAMBAHKAN, bukan dikurangi)
        return $subtotal + $ppnNominal + $pphNominal;
    }
}
