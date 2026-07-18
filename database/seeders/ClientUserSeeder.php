<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClientUserSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ["name" => "Budi Santoso",     "email" => "client@alphacorp.com",         "password" => "Client123!", "phone" => "081234567894"],
            ["name" => "Siti Rahmawati",   "email" => "siti.rahmawati@example.com",   "password" => "SitiClient456", "phone" => "081234567895"],
            ["name" => "Andi Pratama",     "email" => "andi.pratama@example.com",     "password" => "AndiClient789", "phone" => "081234567896"],
            ["name" => "Dewi Lestari",     "email" => "dewi.lestari@example.com",     "password" => "DewiClient000", "phone" => "081234567897"],
            ["name" => "Rizky Kurniawan",  "email" => "rizky.kurniawan@example.com",  "password" => "RizkyClient111", "phone" => "081234567898"],
        ];

        foreach ($clients as $data) {
            User::firstOrCreate(
                ["email" => $data["email"]],
                [
                    "name"              => $data["name"],
                    "password"          => Hash::make($data["password"]),
                    "phone"             => $data["phone"],
                    "role"              => "client",
                    "email_verified_at" => now(),
                ]
            );
        }
    }
}