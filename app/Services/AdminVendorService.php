<?php

namespace App\Services;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminVendorService
{
    public function __construct(
        private VendorRepositoryInterface $vendorRepository
    ) {}

    public function getIndexData(?string $search): array
    {
        $vendors       = $this->vendorRepository->paginateWithFilters($search);
        $totalVendors  = $this->vendorRepository->countTotal();
        $activeVendors = $this->vendorRepository->countActive();
        $busyVendors   = $this->vendorRepository->countBusy();

        return compact("vendors", "totalVendors", "activeVendors", "busyVendors");
    }

    public function createVendor(array $data): void
    {
        if (!empty($data["password"]) && User::where("email", $data["email"])->exists()) {
            throw ValidationException::withMessages([
                "email" => "Email tersebut sudah digunakan oleh akun lain.",
            ]);
        }

        DB::transaction(function () use ($data) {
            $userId = null;

            if (!empty($data["password"])) {
                $user = User::create([
                    "name"     => $data["nama_vendor"],
                    "email"    => $data["email"],
                    "password" => Hash::make($data["password"]),
                    "role"     => "vendor",
                ]);
                $userId = $user->id;
            }

            $this->vendorRepository->create([
                "user_id"      => $userId,
                "nama_vendor"  => $data["nama_vendor"],
                "jenis_vendor" => $data["jenis_vendor"] ?? null,
                "email"        => $data["email"] ?? null,
                "alamat"       => $data["alamat"] ?? null,
                "deskripsi"    => $data["deskripsi"] ?? null,
            ]);
        });
    }

    public function updateVendor(Vendor $vendor, array $data): void
    {
        $this->vendorRepository->update($vendor, [
            "nama_vendor"  => $data["nama_vendor"],
            "jenis_vendor" => $data["jenis_vendor"] ?? null,
            "email"        => $data["email"] ?? null,
            "alamat"       => $data["alamat"] ?? null,
            "deskripsi"    => $data["deskripsi"] ?? null,
        ]);

        $this->syncUserAccount($vendor, $data);
    }

    public function deleteVendor(Vendor $vendor): void
    {
        $this->vendorRepository->delete($vendor);
    }

    private function syncUserAccount(Vendor $vendor, array $data): void
    {
        if (!$vendor->user_id && !empty($data["password"])) {
            if (User::where("email", $data["email"])->exists()) {
                throw ValidationException::withMessages([
                    "email" => "Email tersebut sudah digunakan oleh akun lain.",
                ]);
            }

            $user = User::create([
                "name"     => $data["nama_vendor"],
                "email"    => $data["email"],
                "password" => Hash::make($data["password"]),
                "role"     => "vendor",
            ]);
            $vendor->update(["user_id" => $user->id]);

        } elseif ($vendor->user_id) {
            $account = $vendor->user;

            if (!empty($data["email"]) && $data["email"] !== $account->email) {
                if (User::where("email", $data["email"])->where("id", "!=", $account->id)->exists()) {
                    throw ValidationException::withMessages([
                        "email" => "Email tersebut sudah digunakan oleh akun lain.",
                    ]);
                }
                $account->email = $data["email"];
            }

            $account->name = $data["nama_vendor"];
            if (!empty($data["password"])) {
                $account->password = Hash::make($data["password"]);
            }
            $account->save();
        }
    }
}