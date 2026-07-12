<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'nama_vendor'  => fake()->company(),
            'jenis_vendor' => fake()->randomElement(['Katering', 'Dekorasi', 'Dokumentasi', 'Venue', 'Transportasi', 'Hiburan']),
            'email'        => fake()->companyEmail(),
            'alamat'       => fake()->address(),
            'deskripsi'    => fake()->sentence(),
            'user_id'      => null,
        ];
    }

    public function withAccount(): static
    {
        return $this->state(function (array $attributes) {
            $user = \App\Models\User::factory()->create(['role' => 'vendor']);
            return ['user_id' => $user->id];
        });
    }
}
