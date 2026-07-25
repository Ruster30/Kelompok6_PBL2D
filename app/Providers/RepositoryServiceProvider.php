<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\DdmsSettingRepositoryInterface;
use App\Repositories\Contracts\DocumentApprovalRepositoryInterface;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use App\Repositories\Contracts\DocumentQrVerificationRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use App\Repositories\Contracts\DocumentVerificationLogRepositoryInterface;
use App\Repositories\Eloquent\DdmsSettingRepository;
use App\Repositories\Eloquent\DocumentApprovalRepository;
use App\Repositories\Eloquent\DocumentNumberingRepository;
use App\Repositories\Eloquent\DocumentQrVerificationRepository;
use App\Repositories\Eloquent\DocumentRepository;
use App\Repositories\Eloquent\DocumentTemplateRepository;
use App\Repositories\Eloquent\DocumentVerificationLogRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings ke dalam Laravel Service Container.
     */
    public function register(): void
    {
        // Document
        $this->app->bind(
            DocumentRepositoryInterface::class,
            DocumentRepository::class,
        );

        // Document Template
        $this->app->bind(
            DocumentTemplateRepositoryInterface::class,
            DocumentTemplateRepository::class,
        );

        // Document Approval
        $this->app->bind(
            DocumentApprovalRepositoryInterface::class,
            DocumentApprovalRepository::class,
        );

        // Document Numbering
        $this->app->bind(
            DocumentNumberingRepositoryInterface::class,
            DocumentNumberingRepository::class,
        );

        // Document QR Verification
        $this->app->bind(
            DocumentQrVerificationRepositoryInterface::class,
            DocumentQrVerificationRepository::class,
        );

        // Document Verification Log
        $this->app->bind(
            DocumentVerificationLogRepositoryInterface::class,
            DocumentVerificationLogRepository::class,
        );

        // DDMS Settings
        $this->app->bind(
            DdmsSettingRepositoryInterface::class,
            DdmsSettingRepository::class,
        );
    }

    /**
     * Tidak ada boot logic yang diperlukan.
     */
    public function boot(): void
    {
        //
    }
}
