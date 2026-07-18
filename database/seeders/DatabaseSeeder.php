<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ClientUserSeeder::class,
            VendorSeeder::class,
            LandingPageSeeder::class,
            EventSeeder::class,
            NotificationSeeder::class,
            DocumentSendSeeder::class,
        ]);
    }
}