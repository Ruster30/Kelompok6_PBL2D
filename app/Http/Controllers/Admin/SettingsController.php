<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\AdminSettingsService;

class SettingsController extends Controller
{
    public function __construct(
        private AdminSettingsService $settingsService
    ) {}

    public function index()
    {
        return view("admin.settings.index");
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->settingsService->updateProfile($request->validated());

        return back()->with("success", "Profil berhasil diperbarui.");
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $result = $this->settingsService->updatePassword(
            $request->current_password,
            $request->password
        );

        if (isset($result["error"])) {
            return back()->withErrors(["current_password" => $result["error"]]);
        }

        return back()->with("success", $result["success"]);
    }
}