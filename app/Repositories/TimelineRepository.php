<?php

namespace App\Repositories;

use App\Interfaces\TimelineRepositoryInterface;
use App\Models\Timeline;
use Illuminate\Database\Eloquent\Collection;

class TimelineRepository implements TimelineRepositoryInterface
{
    public function getByEventId(int $eventId): Collection
    {
        return Timeline::where("event_id", $eventId)
            ->orderBy("tanggal_kegiatan")
            ->orderBy("id")
            ->get();
    }

    public function create(array $data): Timeline
    {
        return Timeline::create($data);
    }

    public function update(Timeline $timeline, array $data): Timeline
    {
        $timeline->update($data);
        return $timeline->fresh();
    }

    public function delete(Timeline $timeline): void
    {
        $timeline->delete();
    }
}