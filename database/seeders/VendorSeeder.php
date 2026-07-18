<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Vendor dengan akun login
        $vendorUsers = [
            ["name" => "Vendor Demo",        "email" => "vendor@alphacorp.com",    "password" => "Vendor123"],
            ["name" => "Catering Bahagia",   "email" => "catering@example.com",    "password" => "Catering123"],
            ["name" => "Dekorasi Indah",     "email" => "dekorasi@example.com",    "password" => "Dekorasi123"],
        ];

        foreach ($vendorUsers as $data) {
            $user = User::firstOrCreate(
                ["email" => $data["email"]],
                [
                    "name"              => $data["name"],
                    "password"          => Hash::make($data["password"]),
                    "role"              => "vendor",
                    "email_verified_at" => now(),
                ]
            );

            Vendor::updateOrCreate(
                ["user_id" => $user->id],
                [
                    "nama_vendor"  => $data["name"],
                    "email"        => $data["email"],
                    "jenis_vendor" => fake()->randomElement(["Katering", "Dekorasi", "Dokumentasi", "Venue", "Transportasi", "Hiburan"]),
                    "alamat"       => fake()->address(),
                    "deskripsi"    => fake()->sentence(),
                ]
            );
        }

        // Vendor tanpa akun login
        $vendors = [
            ["nama_vendor" => "PT Suara Nusantara",  "jenis_vendor" => "Hiburan",       "email" => "sound@example.com"],
            ["nama_vendor" => "CV Indah Fotografi",   "jenis_vendor" => "Dokumentasi",  "email" => "foto@example.com"],
            ["nama_vendor" => "Transportasi Cepat",    "jenis_vendor" => "Transportasi", "email" => "trans@example.com"],
        ];

        foreach ($vendors as $v) {
            Vendor::firstOrCreate(
                ["email" => $v["email"]],
                [
                    "nama_vendor"  => $v["nama_vendor"],
                    "jenis_vendor" => $v["jenis_vendor"],
                    "alamat"       => fake()->address(),
                    "deskripsi"    => fake()->sentence(),
                ]
            );
        }
    }
}