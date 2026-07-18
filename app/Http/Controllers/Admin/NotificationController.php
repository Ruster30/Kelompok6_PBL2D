<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\AdminNotificationService;

class NotificationController extends Controller
{
    public function __construct(
        private AdminNotificationService $adminNotificationService
    ) {}

    public function index()
    {
        $data = $this->adminNotificationService->getNotifications(auth()->user()->id);

        return view("admin.notifications.index", $data);
    }

    public function markAllRead()
    {
        $this->adminNotificationService->markAllAsRead(auth()->user()->id);

        return back()->with(
            "success",
            "Semua notifikasi ditandai sudah dibaca."
        );
    }

    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $this->adminNotificationService->markAsRead(
            $notification->id,
            auth()->id()
        );

        return back();
    }
}