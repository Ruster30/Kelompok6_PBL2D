<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DirectorSeeder extends Seeder
{
    public function run(): void
    {
        $directors = [
            [
                "name"     => "Bambang Supriyadi",
                "email"    => "director@alphacorp.com",
                "password" => "Director123",
                "phone"    => "081234567899",
            ],
        ];

        foreach ($directors as $data) {
            User::firstOrCreate(
                ["email" => $data["email"]],
                [
                    "name"              => $data["name"],
                    "password"          => Hash::make($data["password"]),
                    "phone"             => $data["phone"],
                    "role"              => "director",
                    "email_verified_at" => now(),
                ]
            );
        }
    }
}