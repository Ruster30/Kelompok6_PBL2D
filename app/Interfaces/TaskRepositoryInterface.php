<?php

namespace App\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getByVendorId(int $vendorId, ?int $eventId = null): Collection;

    public function getByVendorIdAndId(int $vendorId, int $id): Task;

    public function updateStatus(int $id, string $status): void;
}