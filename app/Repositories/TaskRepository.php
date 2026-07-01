<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getByVendorId(int $vendorId, ?int $eventId = null): Collection
    {
        return Task::where("vendor_id", $vendorId)
            ->with("event")
            ->when($eventId, fn($q) => $q->where("event_id", $eventId))
            ->orderBy("deadline")
            ->get();
    }

    public function getByVendorIdAndId(int $vendorId, int $id): Task
    {
        return Task::where("id", $id)
            ->where("vendor_id", $vendorId)
            ->firstOrFail();
    }

    public function updateStatus(int $id, string $status): void
    {
        Task::where("id", $id)->update(["status" => $status]);
    }
}