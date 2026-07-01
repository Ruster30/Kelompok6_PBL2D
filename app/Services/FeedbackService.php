<?php

namespace App\Services;

use App\Interfaces\FeedbackRepositoryInterface;
use App\Models\Event;
use App\Models\Feedback;

class FeedbackService
{
    public function __construct(
        private FeedbackRepositoryInterface $feedbackRepository
    ) {}

    public function hasGivenFeedback(Event $event, int $clientId): bool
    {
        return $this->feedbackRepository->existsForEventAndClient(
            $event->id,
            $clientId
        );
    }

    public function createFeedback(array $data): Feedback
    {
        return $this->feedbackRepository->create($data);
    }
}
