<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * DocumentTemplatePolicy
 *
 * Authorization untuk pengelolaan template dokumen.
 */
class DocumentTemplatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke daftar template.');
    }

    public function view(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke template ini.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat membuat template.');
    }

    public function update(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat mengubah template.');
    }

    public function delete(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat menghapus template.');
    }
}
