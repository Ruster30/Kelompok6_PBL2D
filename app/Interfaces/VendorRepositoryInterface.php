<?php

namespace App\Interfaces;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendorRepositoryInterface
{
    public function paginateWithFilters(?string $search): LengthAwarePaginator;

    public function countTotal(): int;

    public function countActive(): int;

    public function countBusy(): int;

    public function create(array $data): Vendor;

    public function update(Vendor $vendor, array $data): Vendor;

    public function delete(Vendor $vendor): void;
}