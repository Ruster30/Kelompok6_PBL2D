<?php

namespace App\Interfaces;

use App\Models\Rab;
use Illuminate\Database\Eloquent\Collection;

interface RabRepositoryInterface
{
    public function getByEventId(int $eventId): Collection;

    public function create(array $data): Rab;

    public function update(Rab $rab, array $data): Rab;

    public function delete(Rab $rab): void;
}