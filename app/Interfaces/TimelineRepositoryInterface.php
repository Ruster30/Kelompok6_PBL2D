<?php

namespace App\Interfaces;

use App\Models\Timeline;
use Illuminate\Database\Eloquent\Collection;

interface TimelineRepositoryInterface
{
    public function getByEventId(int $eventId): Collection;

    public function create(array $data): Timeline;

    public function update(Timeline $timeline, array $data): Timeline;

    public function delete(Timeline $timeline): void;
}