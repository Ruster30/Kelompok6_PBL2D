<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimelineRequest;
use App\Http\Requests\UpdateTimelineRequest;
use App\Models\Timeline;
use App\Services\TimelineService;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function __construct(
        private TimelineService $timelineService
    ) {}

    public function index(Request $request)
    {
        $data = $this->timelineService->getTimelineData($request->event_id);

        return view("admin.timeline.index", $data);
    }

    public function store(StoreTimelineRequest $request)
    {
        $this->timelineService->createTimeline($request->validated());

        return redirect()
            ->route("admin.timeline.index", ["event_id" => $request->event_id])
            ->with("success", "Timeline berhasil ditambahkan.");
    }

    public function update(UpdateTimelineRequest $request, Timeline $timeline)
    {
        $this->timelineService->updateTimeline($timeline, $request->validated());

        return redirect()
            ->route("admin.timeline.index", ["event_id" => $timeline->event_id])
            ->with("success", "Timeline berhasil diperbarui.");
    }

    public function destroy(Timeline $timeline)
    {
        $eventId = $timeline->event_id;

        $this->timelineService->deleteTimeline($timeline);

        return redirect()
            ->route("admin.timeline.index", ["event_id" => $eventId])
            ->with("success", "Timeline berhasil dihapus.");
    }
}