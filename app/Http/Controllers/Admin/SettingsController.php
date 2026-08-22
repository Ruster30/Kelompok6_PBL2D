<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\AdminSettingsService;
use App\Services\DdmsSettingService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        private AdminSettingsService $settingsService,
        private DdmsSettingService $ddmsSettingService,
    ) {}

    public function index()
    {
        return view("admin.settings.index", [
            "ddmsEnabled"   => $this->ddmsSettingService->getSettingValue("ddms_enabled", "1") === "1",
            "ddmsDefaults"  => $this->ddmsSettingService->getDdmsDefaults(),
        ]);
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

    /**
     * Ubah status global DDMS (ON/OFF).
     * Hanya admin (route group 'admin' + AdminMiddleware).
     */
    public function toggleDdms(Request $request)
    {
        $validated = $request->validate([
            "enabled" => "required|in:0,1",
        ]);

        $enabled = $validated["enabled"] === "1" ? "1" : "0";

        $this->ddmsSettingService->updateSetting(
            "ddms_enabled",
            $enabled,
            "Toggle global DDMS (1 = aktif, 0 = nonaktif)",
        );

        $status = $enabled === "1" ? "diaktifkan" : "dinonaktifkan";

        return back()->with("success", "DDMS berhasil {$status}.");
    }

    /**
     * Simpan default DDMS per jenis surat (Surat Kontrak, Invoice, RAB).
     *
     * Proposal dikecualikan karena dibuat secara manual (upload), bukan
     * di-generate dan tidak mengikuti alur DDMS.
     *
     * Setting ini HANYA default untuk initial UI state di halaman Generate.
     * Tidak memengaruhi dokumen existing dan tidak membatasi keputusan admin
     * saat membuat dokumen baru (admin tetap dapat mengubah checkbox).
     *
     * Hanya admin (route group 'admin' + AdminMiddleware).
     */
    public function updateDdmsDefaults(Request $request)
    {
        $validated = $request->validate([
            "ddms_default_surat_kontrak" => "required|in:0,1",
            "ddms_default_invoice"        => "required|in:0,1",
            "ddms_default_rab"            => "required|in:0,1",
        ]);

        $keys = [
            "ddms_default_surat_kontrak",
            "ddms_default_invoice",
            "ddms_default_rab",
        ];

        foreach ($keys as $key) {
            $this->ddmsSettingService->updateSetting(
                $key,
                $validated[$key] === "1" ? "1" : "0",
                "Default DDMS per jenis surat (1 = DDMS, 0 = Non-DDMS)",
            );
        }

        return back()->with("success", "Default DDMS per jenis surat berhasil disimpan.");
    }
}