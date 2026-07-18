<?php

namespace App\Interfaces;

use App\Models\Negotiation;
use App\Models\Proposal;

interface ProposalRepositoryInterface
{
    public function getActiveByEvent(int $eventId): ?Proposal;

    public function getLatestByEvent(int $eventId): ?Proposal;

    public function getNextVersion(int $eventId): int;

    public function getTodayCount(): int;

    public function getEventCount(int $eventId): int;

    public function deactivateActive(int $eventId): void;

    public function create(array $data): Proposal;

    public function update(Proposal $proposal, array $data): Proposal;

    public function getLatestNegotiation(int $eventId): ?Negotiation;
}
