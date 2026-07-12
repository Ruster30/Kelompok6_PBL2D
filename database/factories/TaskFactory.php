<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Event;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'nama_tugas' => fake()->sentence(3),
            'event_id'   => Event::factory(),
            'vendor_id'  => Vendor::factory(),
            'prioritas'  => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'deadline'   => fake()->dateTimeBetween('+1 week', '+1 month'),
            'status'     => 'ditugaskan',
            'deskripsi'  => fake()->paragraph(),
        ];
    }
}
