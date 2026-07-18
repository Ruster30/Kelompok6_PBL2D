<?php

namespace App\Services;

use App\Interfaces\TimelineRepositoryInterface;
use App\Models\Event;
use App\Models\Timeline;
use Illuminate\Database\Eloquent\Collection;

class TimelineService
{
    public function __construct(
        private TimelineRepositoryInterface $timelineRepository
    ) {}

    public function getTimelineData(?int $eventId): array
    {
        $events = Event::orderBy("nama_event")->get();

        $selectedEvent = null;
        $timelines     = collect();

        if ($eventId) {
            $selectedEvent = Event::findOrFail($eventId);
            $timelines = $this->timelineRepository->getByEventId($eventId);
        } elseif ($events->isNotEmpty()) {
            $selectedEvent = $events->first();
            $timelines = $this->timelineRepository->getByEventId($selectedEvent->id);
        }

        return compact("events", "selectedEvent", "timelines");
    }

    public function createTimeline(array $data): Timeline
    {
        return $this->timelineRepository->create($data);
    }

    public function updateTimeline(Timeline $timeline, array $data): Timeline
    {
        return $this->timelineRepository->update($timeline, $data);
    }

    public function deleteTimeline(Timeline $timeline): void
    {
        $this->timelineRepository->delete($timeline);
    }
}