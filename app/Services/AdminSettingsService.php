<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class AdminSettingsService
{
    public function updateProfile(array $data): void
    {
        request()->user()->update($data);
    }

    public function updatePassword(string $currentPassword, string $newPassword): array
    {
        $user = request()->user();

        if (!Hash::check($currentPassword, $user->password)) {
            return ["error" => "Password saat ini salah."];
        }

        $user->update(["password" => Hash::make($newPassword)]);

        return ["success" => "Password berhasil diubah."];
    }
}