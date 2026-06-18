<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Admin
        if (!User::where('email', 'admin@alphacorp.com')->exists()) {

            User::create([
                'name' => 'Administrator',
                'email' => 'admin@alphacorp.com',
                'password' => Hash::make('Adminalpha123'),
                'role' => 'admin',
            ]);
        }

        // Vendor
        if (!User::where('email', 'vendor@alphacorp.com')->exists()) {

            User::create([
                'name' => 'Vendor Demo',
                'email' => 'vendor@alphacorp.com',
                'password' => Hash::make('Vendor123'),
                'role' => 'vendor',
            ]);
        }

        // Client
        if (!User::where('email', 'client@alphacorp.com')->exists()) {

            User::create([
                'name' => 'Client Demo',
                'email' => 'client@alphacorp.com',
                'password' => Hash::make('Client123!'),
                'role' => 'client',
            ]);
        }

        $this->call([
            LandingPageSeeder::class,
        ]);
         $this->call([
        VendorSeeder::class,
    ]);
    }
}
