<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * DdmsSettingPolicy
 *
 * Authorization untuk konfigurasi global DDMS.
 */
class DdmsSettingPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke pengaturan DDMS.');
    }

    public function view(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke pengaturan ini.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat membuat pengaturan.');
    }

    public function update(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat mengubah pengaturan.');
    }

    public function delete(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat menghapus pengaturan.');
    }
}
