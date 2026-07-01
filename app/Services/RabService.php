<?php

namespace App\Services;

use App\Interfaces\RabRepositoryInterface;
use App\Models\Event;
use App\Models\Rab;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;

class RabService
{
    public function __construct(
        private RabRepositoryInterface $rabRepository
    ) {}

    public function getRabData(?int $eventId): array
    {
        $events  = Event::orderBy("nama_event")->get();
        $vendors = Vendor::orderBy("nama_vendor")->get();

        $selectedEvent = null;
        $rabItems      = collect();

        if ($eventId) {
            $selectedEvent = Event::findOrFail($eventId);
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
        }

        if ($selectedEvent) {
            $rabItems = $this->rabRepository->getByEventId($selectedEvent->id);
        }

        return compact("events", "vendors", "selectedEvent", "rabItems");
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
}