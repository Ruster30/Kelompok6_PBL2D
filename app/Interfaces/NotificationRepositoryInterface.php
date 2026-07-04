<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function getByUserId(int $userId): Collection;

    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function countUnreadByUserId(int $userId): int;

    public function markAllAsRead(int $userId): void;

    public function markAsRead(int $id, int $userId): void;
}