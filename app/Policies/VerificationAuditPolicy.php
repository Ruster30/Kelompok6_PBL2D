<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DocumentVerificationLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * VerificationAuditPolicy
 *
 * Authorization untuk akses audit trail verifikasi dokumen.
 * Read-only: Admin dan Director dapat melihat, tidak ada create/update/delete.
 */
class VerificationAuditPolicy
{
    /**
     * Apakah user dapat melihat daftar verification audit logs.
     */
    public function viewAny(User $user): Response
    {
        return $user->isManagement()
            ? Response::allow()
            : Response::deny('Hanya Admin dan Director yang dapat mengakses audit log verifikasi.');
    }

    /**
     * Apakah user dapat melihat detail satu verification log.
     */
    public function view(User $user, DocumentVerificationLog $log): Response
    {
        return $user->isManagement()
            ? Response::allow()
            : Response::deny('Hanya Admin dan Director yang dapat melihat detail verifikasi.');
    }
}
