<?php

namespace App\Repositories;

use App\Interfaces\RabAdditionalDetailRepositoryInterface;
use App\Models\RabAdditionalDetail;

class RabAdditionalDetailRepository implements RabAdditionalDetailRepositoryInterface
{
    public function getByEventId(int $eventId): ?RabAdditionalDetail
    {
        return RabAdditionalDetail::where('event_id', $eventId)->first();
    }

    public function createOrUpdate(int $eventId, array $data): RabAdditionalDetail
    {
        $data['event_id'] = $eventId;

        return RabAdditionalDetail::updateOrCreate(
            ['event_id' => $eventId],
            $data
        );
    }
}
