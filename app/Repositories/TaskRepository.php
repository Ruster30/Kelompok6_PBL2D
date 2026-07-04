<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Event;
use App\Models\Task;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    // ─── Vendor methods ───
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

    // ─── Admin methods ───
    public function paginateWithFilters(?string $search, ?string $status): LengthAwarePaginator
    {
        $query = Task::with(["event", "vendor"])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("nama_tugas", "like", "%{$search}%")
                  ->orWhereHas("event", fn($q2) => $q2->where("nama_event", "like", "%{$search}%"));
            });
        }

        if ($status) {
            $query->where("status", $status);
        }

        return $query->paginate(10)->withQueryString();
    }

    public function getAllEvents(): Collection
    {
        return Event::orderBy("nama_event")->get();
    }

    public function getAllVendors(): Collection
    {
        return Vendor::orderBy("nama_vendor")->get();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function adminUpdate(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    public function adminDelete(Task $task): void
    {
        $task->delete();
    }
}