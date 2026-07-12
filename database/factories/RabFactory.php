<?php

namespace Database\Factories;

use App\Models\Rab;
use App\Models\Event;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rab>
 */
class RabFactory extends Factory
{
    protected $model = Rab::class;

    public function definition(): array
    {
        $qty  = fake()->numberBetween(1, 100);
        $price = fake()->numberBetween(10000, 5000000);

        return [
            'event_id'       => Event::factory(),
            'vendor_id'      => null,
            'nama_biaya'     => fake()->sentence(3),
            'kategori_biaya' => fake()->randomElement(['Dokumentasi', 'Konsumsi', 'Dekorasi', 'Transportasi', 'Publikasi', 'Perizinan', 'Lainnya']),
            'jumlah_item'    => $qty,
            'satuan'         => fake()->randomElement(['pcs', 'paket', 'unit', 'meter', 'liter', 'jam']),
            'harga_satuan'   => $price,
            'subtotal_biaya' => $qty * $price,
        ];
    }

    public function withVendor(Vendor $vendor): static
    {
        return $this->state(fn (array $attrs) => ['vendor_id' => $vendor->id]);
    }
}
