<?php

namespace App\Repositories;

use App\Interfaces\ProposalRepositoryInterface;
use App\Models\Negotiation;
use App\Models\Proposal;

class ProposalRepository implements ProposalRepositoryInterface
{
    public function getActiveByEvent(int $eventId): ?Proposal
    {
        return Proposal::where('event_id', $eventId)
            ->where('is_active', true)
            ->first();
    }

    public function getLatestByEvent(int $eventId): ?Proposal
    {
        return Proposal::where('event_id', $eventId)
            ->latest()
            ->first();
    }

    public function getNextVersion(int $eventId): int
    {
        return ((int) Proposal::where('event_id', $eventId)->max('versi')) + 1;
    }

    public function getTodayCount(): int
    {
        return Proposal::whereDate('created_at', today())->count();
    }

    public function getEventCount(int $eventId): int
    {
        return Proposal::where('event_id', $eventId)->count();
    }

    public function deactivateActive(int $eventId): void
    {
        Proposal::where('event_id', $eventId)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    public function create(array $data): Proposal
    {
        return Proposal::create($data);
    }

    public function update(Proposal $proposal, array $data): Proposal
    {
        $proposal->update($data);
        return $proposal->fresh();
    }

    public function getLatestNegotiation(int $eventId): ?Negotiation
    {
        return Negotiation::where('event_id', $eventId)
            ->latest()
            ->first();
    }
}
