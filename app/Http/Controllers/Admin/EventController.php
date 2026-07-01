<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Models\Event;
use App\Services\AdminEventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        private AdminEventService $eventService,
    ) {}

    public function index(Request $request)
    {
        return view('admin.events.index', $this->eventService->getIndexData(
            $request->search,
            $request->status
        ));
    }

    public function create()
    {
        return view('admin.events.form', $this->eventService->getFormData());
    }

    public function store(StoreEventRequest $request)
    {
        $this->eventService->createEvent($request->validated());

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event)
    {
        return view('admin.events.show', $this->eventService->getShowData($event));
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', $this->eventService->getFormData($event));
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $this->eventService->updateEvent($event, $request->validated());

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $this->eventService->deleteEvent($event);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}
