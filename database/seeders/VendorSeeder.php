<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'vendor')->first();

        if (!$user) {
            return;
        }

        Vendor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_vendor'  => $user->name,
                'jenis_vendor' => 'Katering',
                'alamat'       => 'Padang',
                'deskripsi'    => 'Vendor demo untuk testing sistem',
            ]
        );
    }
}