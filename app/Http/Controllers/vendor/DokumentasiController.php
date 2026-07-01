<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDokumentasiRequest;
use App\Services\DokumentasiService;
use Illuminate\Support\Facades\Auth;

class DokumentasiController extends Controller
{
    public function __construct(
        private DokumentasiService $dokumentasiService
    ) {}

    public function store(StoreDokumentasiRequest $request)
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $this->dokumentasiService->storeDokumentasi(
            $request->validated(),
            $request->file("file"),
            $vendor->id
        );

        return redirect()
            ->route("vendor.daftar-tugas")
            ->with("success", "Dokumentasi berhasil diunggah.");
    }
}