<?php

namespace App\Interfaces;

use App\Models\Feedback;

interface FeedbackRepositoryInterface
{
    public function existsForEventAndClient(int $eventId, int $clientId): bool;
    public function create(array $data): Feedback;
}
