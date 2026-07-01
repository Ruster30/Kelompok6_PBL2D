<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getByUserId(int $userId): Collection
    {
        return Notification::where("user_id", $userId)
            ->latest()
            ->get();
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
}
