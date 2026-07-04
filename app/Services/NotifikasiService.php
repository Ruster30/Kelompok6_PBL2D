<?php

namespace App\Services;

use App\Interfaces\NotificationRepositoryInterface;

class NotifikasiService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function getNotifications(int $userId): array
    {
        $notifikasi = $this->notificationRepository->getByUserId($userId);
        $unreadCount = $this->notificationRepository->countUnreadByUserId($userId);

        return compact("notifikasi", "unreadCount");
    }

    public function markAllAsRead(int $userId): void
    {
        $this->notificationRepository->markAllAsRead($userId);
    }
}
