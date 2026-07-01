<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminEventService
{
    public function getIndexData(?string $search, ?string $status): array
    {
        $query = Event::with('client')->latest();

        if ($search) {
            $query->where('nama_event', 'like', '%' . $search . '%');
        }
        if ($status) {
            $query->where('status_event', $status);
        }

        return [
            'events' => $query->paginate(10)->withQueryString(),
        ];
    }

    public function getFormData(?Event $event = null): array
    {
        $clients = User::where('role', 'client')->orderBy('name')->get();
        $data = ['clients' => $clients];

        if ($event) {
            $data['event'] = $event;
        }

        return $data;
    }

    public function createEvent(array $data): Event
    {
        return Event::create($data);
    }

    public function getShowData(Event $event): array
    {
        return [
            'event' => $event->load([
                'client',
                'feedbacks' => function ($query) {
                    $query->with('client')->latest();
                },
            ]),
        ];
    }

    public function updateEvent(Event $event, array $data): void
    {
        $event->update($data);
    }

    public function deleteEvent(Event $event): void
    {
        $event->delete();
    }
}
