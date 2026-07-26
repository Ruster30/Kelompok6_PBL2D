<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * DirectorPinService
 *
 * Mengelola PIN Director untuk verifikasi Approval.
 * PIN disimpan dalam bentuk Hash (bcrypt).
 */
class DirectorPinService
{
    /**
     * Buat PIN pertama untuk Director.
     *
     * @throws ValidationException jika sudah memiliki PIN.
     */
    public function setPin(User $director, string $pin): void
    {
        if ($director->hasApprovalPin()) {
            throw ValidationException::withMessages([
                "pin" => "Director sudah memiliki PIN. Gunakan fitur Ubah PIN.",
            ]);
        }

        $director->update([
            "approval_pin" => Hash::make($pin),
        ]);
    }

    /**
     * Ubah PIN Director.
     *
     * @throws ValidationException jika PIN lama salah.
     */
    public function changePin(User $director, string $currentPin, string $newPin): void
    {
        if (! $director->hasApprovalPin()) {
            throw ValidationException::withMessages([
                "current_pin" => "Director belum memiliki PIN. Silakan buat PIN terlebih dahulu.",
            ]);
        }

        if (!Hash::check($currentPin, $director->approval_pin)) {
            throw ValidationException::withMessages([
                "current_pin" => "PIN lama salah.",
            ]);
        }

        $director->update([
            "approval_pin" => Hash::make($newPin),
        ]);
    }

    /**
     * Verifikasi kebenaran PIN.
     * Untuk digunakan pada phase berikutnya (Approval dengan PIN).
     */
        /**
     * Verifikasi kebenaran PIN.
     *
     * @throws \Illuminate\Validation\ValidationException jika PIN kosong atau salah.
     */
    public function verifyPin(User $director, string $pin): void
    {
        if (! $director->hasApprovalPin()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "pin" => "Director belum memiliki PIN. Silakan buat PIN terlebih dahulu.",
            ]);
        }

        if (!Hash::check($pin, $director->approval_pin)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "pin" => "PIN yang dimasukkan salah.",
            ]);
        }
    }
}