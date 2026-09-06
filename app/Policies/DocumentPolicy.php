<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * DocumentPolicy
 *
 * Authorization untuk seluruh operasi dokumen DDMS.
 * Business rule tetap di Domain Service — Policy hanya memeriksa role.
 */
class DocumentPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isManagement() || $user->isClient()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke daftar dokumen.');
    }

    public function view(User $user, Document $document): Response
    {
        if ($user->isManagement()) {
            return Response::allow();
        }

        if ($user->isClient() && $document->event?->client_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('Anda tidak memiliki akses ke dokumen ini.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat membuat dokumen.');
    }

    public function update(User $user, Document $document): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat mengubah dokumen.');
    }

    public function delete(User $user, Document $document): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat menghapus dokumen.');
    }

    public function restore(User $user, Document $document): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat memulihkan dokumen.');
    }

    public function forceDelete(User $user, Document $document): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat menghapus dokumen secara permanen.');
    }

    // ── Domain Methods ──────────────────────────────────────

    public function submitForApproval(User $user, Document $document): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat mengajukan approval.');
    }

    public function approve(User $user, Document $document): Response
    {
        return $user->isDirector()
            ? Response::allow()
            : Response::deny('Hanya Director yang dapat menyetujui dokumen.');
    }

    public function reject(User $user, Document $document): Response
    {
        return $user->isDirector()
            ? Response::allow()
            : Response::deny('Hanya Director yang dapat menolak dokumen.');
    }

    public function publish(User $user, Document $document): Response
    {
        return $user->isDirector()
            ? Response::allow()
            : Response::deny('Hanya Director yang dapat menerbitkan dokumen.');
    }

    public function archive(User $user, Document $document): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat mengarsipkan dokumen.');
    }
}
