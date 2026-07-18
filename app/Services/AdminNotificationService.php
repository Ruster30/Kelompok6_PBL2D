<?php

namespace App\Services;

use App\Interfaces\NotificationRepositoryInterface;

class AdminNotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function getNotifications(int $userId): array
    {
        $notifications = $this->notificationRepository->paginateByUserId($userId);

        return compact("notifications");
    }

    public function markAllAsRead(int $userId): void
    {
        $this->notificationRepository->markAllAsRead($userId);
    }

    public function markAsRead(int $notificationId, int $userId): void
    {
        $this->notificationRepository->markAsRead($notificationId, $userId);
    }
}