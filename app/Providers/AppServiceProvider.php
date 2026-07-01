<?php

namespace App\Providers;

use App\Interfaces\EventVendorRepositoryInterface;
use App\Interfaces\FeedbackRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\RabRepositoryInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\TimelineRepositoryInterface;
use App\Repositories\EventVendorRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\RabRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TimelineRepository;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventVendorRepositoryInterface::class, EventVendorRepository::class);
        $this->app->bind(FeedbackRepositoryInterface::class, FeedbackRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(RabRepositoryInterface::class, RabRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TimelineRepositoryInterface::class, TimelineRepository::class);
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