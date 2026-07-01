<?php

namespace App\Repositories;

use App\Interfaces\EventVendorRepositoryInterface;
use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Task;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EventVendorRepository implements EventVendorRepositoryInterface
{
    public function paginateWithFilters(?string $search, ?string $status): LengthAwarePaginator
    {
        $query = EventVendor::with(["event", "vendor"])->latest();

        if ($search) {
            $query->whereHas("event", fn($q) => $q->where("nama_event", "like", "%{$search}%"));
        }

        if ($status) {
            $query->where("status_vendor", $status);
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

    public function create(array $data): EventVendor
    {
        return EventVendor::create($data);
    }

    public function update(EventVendor $eventVendor, array $data): EventVendor
    {
        $eventVendor->update($data);
        return $eventVendor->fresh();
    }

    public function delete(EventVendor $eventVendor): void
    {
        $eventVendor->delete();
    }

    public function deleteRelatedTasks(int $eventId, int $vendorId): void
    {
        Task::where("event_id", $eventId)
            ->where("vendor_id", $vendorId)
            ->where("nama_tugas", "like", "Penugasan:%")
            ->delete();
    }
}