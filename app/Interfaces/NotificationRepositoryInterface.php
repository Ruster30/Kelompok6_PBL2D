<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function getByUserId(int $userId): Collection;

    public function countUnreadByUserId(int $userId): int;

    public function markAllAsRead(int $userId): void;
}
