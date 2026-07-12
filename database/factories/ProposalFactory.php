<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        return [
            'event_id'         => Event::factory(),
            'nomor_proposal'   => 'PEN-' . now()->format('Ymd') . '-' . fake()->unique()->randomNumber(3),
            'file_proposal'    => 'proposals/sample.pdf',
            'versi'            => 1,
            'status'           => 'menunggu_konfirmasi',
            'is_active'        => true,
            'tanggal_proposal' => now(),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'diterima']);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'ditolak']);
    }

    public function negotiation(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'negosiasi']);
    }

    public function revision(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'direvisi']);
    }

    public function version(int $v): static
    {
        return $this->state(fn (array $attrs) => ['versi' => $v]);
    }
}
