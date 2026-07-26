<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreDirectorPinRequest;
use App\Http\Requests\Director\UpdateDirectorPinRequest;
use App\Services\DirectorPinService;
use Illuminate\Http\Request;

class DirectorPinController extends Controller
{
    public function __construct(
        private readonly DirectorPinService $pinService,
    ) {}

    /**
     * Tampilkan halaman Set/Ubah PIN.
     */
    public function create(Request $request)
    {
        $director = $request->user();

        return view("director.settings.pin", compact("director"));
    }

    /**
     * Simpan PIN pertama.
     */
    public function store(StoreDirectorPinRequest $request)
    {
        $this->pinService->setPin(
            director: $request->user(),
            pin:      $request->input("pin"),
        );

        return redirect()
            ->route("director.settings.pin")
            ->with("success", "PIN berhasil disimpan.");
    }

    /**
     * Ubah PIN yang sudah ada.
     */
    public function update(UpdateDirectorPinRequest $request)
    {
        $this->pinService->changePin(
            director:   $request->user(),
            currentPin: $request->input("current_pin"),
            newPin:     $request->input("pin"),
        );

        return redirect()
            ->route("director.settings.pin")
            ->with("success", "PIN berhasil diperbarui.");
    }
}