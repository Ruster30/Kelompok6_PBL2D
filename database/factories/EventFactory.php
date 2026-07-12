<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'client_id'        => User::factory()->create(['role' => 'client'])->id,
            'pic_admin_id'     => User::factory()->create(['role' => 'admin'])->id,
            'nama_event'       => fake()->sentence(3),
            'jenis_event'      => fake()->randomElement(['Pameran', 'Seminar', 'Konser', 'Pernikahan', 'Workshop']),
            'tanggal_event'    => fake()->dateTimeBetween('+1 month', '+6 months'),
            'lokasi_event'     => fake()->city(),
            'jumlah_tamu'      => fake()->numberBetween(50, 5000),
            'rentang_anggaran' => fake()->randomElement(['< 50 Juta', '50-100 Juta', '100-500 Juta', '> 500 Juta']),
            'detail_kebutuhan' => fake()->paragraph(),
            'status_event'     => 'menunggu',
            'status_pembayaran'=> 'belum_lunas',
        ];
    }

    public function withClient(User $client): static
    {
        return $this->state(fn (array $attrs) => ['client_id' => $client->id]);
    }

    public function withPic(User $admin): static
    {
        return $this->state(fn (array $attrs) => ['pic_admin_id' => $admin->id]);
    }

    public function status(string $status): static
    {
        return $this->state(fn (array $attrs) => ['status_event' => $status]);
    }
}
