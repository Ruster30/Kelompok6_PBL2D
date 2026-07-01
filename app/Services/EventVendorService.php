<?php

namespace App\Services;

use App\Interfaces\EventVendorRepositoryInterface;
use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Notification;
use App\Models\Task;
use App\Models\Vendor;

class EventVendorService
{
    public function __construct(
        private EventVendorRepositoryInterface $eventVendorRepository
    ) {}

    public function getIndexData(?string $search, ?string $status): array
    {
        $eventVendors = $this->eventVendorRepository->paginateWithFilters($search, $status);
        $events       = $this->eventVendorRepository->getAllEvents();
        $vendors      = $this->eventVendorRepository->getAllVendors();

        return compact("eventVendors", "events", "vendors");
    }

    public function createAssignment(array $data): EventVendor
    {
        $assignment = $this->eventVendorRepository->create($data);
        $this->syncTask($assignment, $data);
        $this->sendNotification($data, "Penugasan Baru", function ($vendor, $event, $data) {
            $jadwalInfo = $data["jadwal_vendor"]
                ? " pada " . \Carbon\Carbon::parse($data["jadwal_vendor"])->format("d M Y")
                : "";
            return "Anda telah ditugaskan pada event \"" . $event->nama_event . "\"" . $jadwalInfo . ".";
        });

        return $assignment;
    }

    public function updateAssignment(EventVendor $task, array $data): EventVendor
    {
        $assignment = $this->eventVendorRepository->update($task, $data);
        $this->syncTask($assignment, $data);
        $this->sendNotification($data, "Penugasan Diperbarui", function ($vendor, $event, $data) {
            $jadwalInfo = $data["jadwal_vendor"]
                ? " Jadwal diperbarui ke " . \Carbon\Carbon::parse($data["jadwal_vendor"])->format("d M Y") . "."
                : "";
            return "Detail penugasan Anda pada event \"" . $event->nama_event . "\" telah diperbarui." . $jadwalInfo;
        });

        return $assignment;
    }

    public function deleteAssignment(EventVendor $task): void
    {
        $this->eventVendorRepository->deleteRelatedTasks($task->event_id, $task->vendor_id);
        $this->eventVendorRepository->delete($task);
    }

    private function syncTask(EventVendor $assignment, array $data): void
    {
        $eventName = $assignment->event?->nama_event ?? "Event";
        $taskName = !empty($data["nama_tugas"])
            ? $data["nama_tugas"]
            : "Penugasan: " . $eventName;

        Task::updateOrCreate(
            [
                "event_id"  => $assignment->event_id,
                "vendor_id" => $assignment->vendor_id,
                "nama_tugas" => $taskName,
            ],
            [
                "prioritas" => $data["prioritas"] ?? "sedang",
                "deadline"  => $assignment->jadwal_vendor,
                "status"    => $assignment->status_vendor,
                "deskripsi" => $data["deskripsi"] ?: "Tugas otomatis dari penugasan vendor.",
            ]
        );
    }

    private function sendNotification(array $data, string $judul, callable $messageCallback): void
    {
        $vendor = Vendor::with("user")->find($data["vendor_id"]);
        $event  = Event::find($data["event_id"]);

        if ($vendor && $vendor->user_id && $event) {
            Notification::create([
                "user_id" => $vendor->user_id,
                "judul"   => $judul,
                "pesan"   => $messageCallback($vendor, $event, $data),
                "tipe"    => "event",
            ]);
        }
    }
}