<?php

namespace App\Repositories;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorRepository implements VendorRepositoryInterface
{
    public function paginateWithFilters(?string $search): LengthAwarePaginator
    {
        $query = Vendor::with("user")
            ->withCount([
                "eventVendors as active_jobs_count" => function ($q) {
                    $q->whereIn("status_vendor", ["ditugaskan", "dikerjakan"]);
                },
            ])
            ->latest();

        if ($search) {
            $query->where("nama_vendor", "like", "%{$search}%");
        }

        return $query->paginate(10)->withQueryString();
    }

    public function countTotal(): int
    {
        return Vendor::count();
    }

    public function countActive(): int
    {
        return Vendor::whereNotNull("user_id")->count();
    }

    public function countBusy(): int
    {
        return Vendor::whereHas("eventVendors", function ($q) {
            $q->whereIn("status_vendor", ["ditugaskan", "dikerjakan"]);
        })->count();
    }

    public function create(array $data): Vendor
    {
        return Vendor::create($data);
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);
        return $vendor->fresh();
    }

    public function delete(Vendor $vendor): void
    {
        $vendor->delete();
    }
}