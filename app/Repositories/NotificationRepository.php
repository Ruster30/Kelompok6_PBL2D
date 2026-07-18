<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getByUserId(int $userId): Collection
    {
        return Notification::where("user_id", $userId)
            ->latest()
            ->get();
    }

    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::where("user_id", $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function countUnreadByUserId(int $userId): int
    {
        return Notification::where("user_id", $userId)
            ->where("dibaca", false)
            ->count();
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where("user_id", $userId)
            ->where("dibaca", false)
            ->update(["dibaca" => true]);
    }

    public function markAsRead(int $id, int $userId): void
    {
        Notification::where("id", $id)
            ->where("user_id", $userId)
            ->update(["dibaca" => true]);
    }
}