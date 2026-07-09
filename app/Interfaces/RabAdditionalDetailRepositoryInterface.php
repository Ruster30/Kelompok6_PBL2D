<?php

namespace App\Interfaces;

use App\Models\RabAdditionalDetail;

interface RabAdditionalDetailRepositoryInterface
{
    public function getByEventId(int $eventId): ?RabAdditionalDetail;

    public function createOrUpdate(int $eventId, array $data): RabAdditionalDetail;
}