<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            ["name" => "Super Administrator", "email" => "admin@alphacorp.com",   "password" => "Adminalpha123", "phone" => "081234567890"],
            ["name" => "Rina Fitriani",       "email" => "keuangan@alphacorp.com", "password" => "Keuangan123",   "phone" => "081234567891"],
            ["name" => "Dimas Prayoga",       "email" => "operasional@alphacorp.com", "password" => "Operasional123", "phone" => "081234567892"],
            ["name" => "Ahmad Rizki",         "email" => "pic@alphacorp.com",      "password" => "PicAlpha123",  "phone" => "081234567893"],
        ];

        foreach ($admins as $data) {
            User::firstOrCreate(
                ["email" => $data["email"]],
                [
                    "name"              => $data["name"],
                    "password"          => Hash::make($data["password"]),
                    "phone"             => $data["phone"],
                    "role"              => "admin",
                    "email_verified_at" => now(),
                ]
            );
        }
    }
}