<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul'   => fake()->sentence(4),
            'pesan'   => fake()->paragraph(),
            'tipe'    => fake()->randomElement(['info', 'sukses', 'peringatan']),
            'dibaca'  => false,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attrs) => ['dibaca' => true]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attrs) => ['dibaca' => false]);
    }
}
