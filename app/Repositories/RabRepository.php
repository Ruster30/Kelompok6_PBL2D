<?php

namespace App\Repositories;

use App\Interfaces\RabRepositoryInterface;
use App\Models\Rab;
use Illuminate\Database\Eloquent\Collection;

class RabRepository implements RabRepositoryInterface
{
    public function getByEventId(int $eventId): Collection
    {
        return Rab::with("vendor")
            ->where("event_id", $eventId)
            ->get();
    }

    public function create(array $data): Rab
    {
        return Rab::create($data);
    }

    public function update(Rab $rab, array $data): Rab
    {
        $rab->update($data);
        return $rab->fresh();
    }

    public function delete(Rab $rab): void
    {
        $rab->delete();
    }
}