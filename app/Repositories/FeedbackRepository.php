<?php

namespace App\Repositories;

use App\Interfaces\FeedbackRepositoryInterface;
use App\Models\Feedback;

class FeedbackRepository implements FeedbackRepositoryInterface
{
    public function existsForEventAndClient(int $eventId, int $clientId): bool
    {
        return Feedback::where("event_id", $eventId)
            ->where("client_id", $clientId)
            ->exists();
    }

    public function create(array $data): Feedback
    {
        return Feedback::create($data);
    }
}
