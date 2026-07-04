<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    public function updateProfile(Request $request): void
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty("email")) {
            $user->email_verified_at = null;
        }

        $user->save();
    }

    public function deleteAccount(Request $request): void
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}