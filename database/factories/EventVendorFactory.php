<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventVendorFactory extends Factory
{
    protected $model = EventVendor::class;

    public function definition(): array
    {
        return [
            "event_id"      => Event::factory(),
            "vendor_id"     => Vendor::factory(),
            "jadwal_vendor" => fake()->date(),
            "status_vendor" => fake()->randomElement(["ditugaskan", "dikerjakan", "selesai"]),
            "harga_vendor"  => fake()->randomFloat(2, 100000, 10000000),
            "deskripsi"     => fake()->sentence(),
        ];
    }
}
