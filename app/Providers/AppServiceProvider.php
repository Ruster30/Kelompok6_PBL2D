<?php

namespace App\Providers;

use App\Interfaces\DocumentRepositoryInterface;
use App\Interfaces\EventVendorRepositoryInterface;
use App\Interfaces\FeedbackRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\ProposalRepositoryInterface;
use App\Interfaces\RabAdditionalDetailRepositoryInterface;
use App\Interfaces\RabRepositoryInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Interfaces\TimelineRepositoryInterface;
use App\Interfaces\VendorRepositoryInterface;
use App\Models\DocumentVerificationLog;
use App\Models\Notification;
use App\Policies\VerificationAuditPolicy;
use App\Repositories\DocumentRepository;
use App\Repositories\EventVendorRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ProposalRepository;
use App\Repositories\RabAdditionalDetailRepository;
use App\Repositories\RabRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TimelineRepository;
use App\Repositories\VendorRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(EventVendorRepositoryInterface::class, EventVendorRepository::class);
        $this->app->bind(FeedbackRepositoryInterface::class, FeedbackRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(ProposalRepositoryInterface::class, ProposalRepository::class);
        $this->app->bind(RabRepositoryInterface::class, RabRepository::class);
        $this->app->bind(RabAdditionalDetailRepositoryInterface::class, RabAdditionalDetailRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TimelineRepositoryInterface::class, TimelineRepository::class);
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(DocumentVerificationLog::class, VerificationAuditPolicy::class);
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $unreadNotifications = 0;

            if (auth()->check()) {
                $unreadNotifications = Notification::where('user_id', auth()->id())
                    ->where('dibaca', false)
                    ->count();
            }

            $view->with('unreadNotifications', $unreadNotifications);
        });
    }
}
