<?php

namespace App\Providers;

use App\Interfaces\FeedbackRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\RabRepositoryInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Repositories\FeedbackRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\RabRepository;
use App\Repositories\TaskRepository;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FeedbackRepositoryInterface::class, FeedbackRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(RabRepositoryInterface::class, RabRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
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