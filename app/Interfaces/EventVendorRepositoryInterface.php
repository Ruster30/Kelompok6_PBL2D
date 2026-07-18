<?php

namespace App\Interfaces;

use App\Models\EventVendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EventVendorRepositoryInterface
{
    public function paginateWithFilters(?string $search, ?string $status): LengthAwarePaginator;

    public function getAllEvents(): Collection;

    public function getAllVendors(): Collection;

    public function create(array $data): EventVendor;

    public function update(EventVendor $eventVendor, array $data): EventVendor;

    public function delete(EventVendor $eventVendor): void;

    public function deleteRelatedTasks(int $eventId, int $vendorId): void;
}