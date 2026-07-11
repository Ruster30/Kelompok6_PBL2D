<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Arahkan user ke halaman login Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->with([
                'prompt' => 'select_account consent',
            ])
            ->redirect();
    }

    /**
     * Tangani callback dari Google setelah login berhasil.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')
                ->with('status', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        // 1. Cari user berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            // 2. Cari berdasarkan email (user sudah daftar biasa sebelumnya)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update google_id dan avatar jika belum ada
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $user->avatar ?? $googleUser->getAvatar(),
                ]);
            } else {
                // 3. Buat akun baru dari data Google
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'password'          => null,
                    'role'              => 'client',
                    'email_verified_at' => now(), // Email dari Google sudah terverifikasi
                ]);
            }
        }

        // Login user
        Auth::login($user, remember: true);

        // Redirect berdasarkan role
        return $this->redirectByRole($user);
    }

    /**
     * Redirect user ke halaman yang sesuai dengan role-nya.
     */
    private function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        return match ($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'vendor' => redirect()->route('vendor.ringkasan'),
            default  => redirect()->route('client.dashboard'),
        };
    }
}
