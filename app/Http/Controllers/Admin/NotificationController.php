<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where(
            'user_id',
            auth()->user()->id
        )
        ->latest()
        ->paginate(15);

        return view(
            'admin.notifications.index',
            compact('notifications')
        );
    }

    public function markAllRead()
    {
        Notification::where(
            'user_id',
            auth()->user()->id
        )->update([
            'dibaca' => true
        ]);

        return back()->with(
            'success',
            'Semua notifikasi ditandai sudah dibaca.'
        );
    }
}