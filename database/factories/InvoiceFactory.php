<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'nomor_invoice' => 'INV-' . now()->format('Ymd') . '-' . fake()->unique()->randomNumber(3),
            'total_invoice' => fake()->numberBetween(1000000, 100000000),
            'status_invoice' => 'belum_bayar',
            'tanggal_invoice' => now(),
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn (array $attrs) => ['status_invoice' => 'lunas']);
    }

    public function menungguVerifikasi(): static
    {
        return $this->state(fn (array $attrs) => ['status_invoice' => 'menunggu_verifikasi']);
    }
}
