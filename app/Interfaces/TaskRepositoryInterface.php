<?php

namespace App\Interfaces;

use App\Models\Event;
use App\Models\Task;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    // ─── Vendor methods ───
    public function getByVendorId(int $vendorId, ?int $eventId = null): Collection;

    public function getByVendorIdAndId(int $vendorId, int $id): Task;

    public function updateStatus(int $id, string $status): void;

    // ─── Admin methods ───
    public function paginateWithFilters(?string $search, ?string $status): LengthAwarePaginator;

    public function getAllEvents(): Collection;

    public function getAllVendors(): Collection;

    public function create(array $data): Task;

    public function adminUpdate(Task $task, array $data): Task;

    public function adminDelete(Task $task): void;
}