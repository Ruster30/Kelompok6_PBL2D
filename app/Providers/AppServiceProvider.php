<?php

namespace App\Providers;

use App\Interfaces\FeedbackRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Repositories\FeedbackRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FeedbackRepositoryInterface::class, FeedbackRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
    }

    public function boot(): void
    {
        View::composer("*", function ($view) {
            $unreadNotifications = 0;

            if (auth()->check()) {
                $unreadNotifications = Notification::where("user_id", auth()->id())
                    ->where("dibaca", false)
                    ->count();
            }

            $view->with("unreadNotifications", $unreadNotifications);
        });
    }
}