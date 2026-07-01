<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Task;

class VendorDashboardService
{
    public function getRingkasanData(int $vendorId): array
    {
        $totalEvent   = Event::whereHas("vendors", fn($q) => $q->where("vendor_id", $vendorId))->count();
        $tugasAktif   = Task::where("vendor_id", $vendorId)->whereNotIn("status", ["selesai"])->count();
        $tugasSelesai = Task::where("vendor_id", $vendorId)->where("status", "selesai")->count();

        $eventTerdekat = Event::whereHas("vendors", fn($q) => $q->where("vendor_id", $vendorId))
            ->where("tanggal_event", ">=", now())
            ->orderBy("tanggal_event")
            ->take(3)
            ->get();

        $tugasMendatang = Task::where("vendor_id", $vendorId)
            ->whereNotIn("status", ["selesai"])
            ->orderBy("deadline")
            ->take(5)
            ->with("event")
            ->get();

        return compact(
            "totalEvent", "tugasAktif", "tugasSelesai",
            "eventTerdekat", "tugasMendatang"
        );
    }

    public function getEventSaya(int $vendorId, ?string $search): array
    {
        $events = Event::whereHas("vendors", fn($q) => $q->where("vendor_id", $vendorId))
            ->with("client")
            ->when($search, fn($q) =>
                $q->where("nama_event", "like", "%{$search}%")
            )
            ->orderBy("tanggal_event")
            ->get();

        return compact("events");
    }
}