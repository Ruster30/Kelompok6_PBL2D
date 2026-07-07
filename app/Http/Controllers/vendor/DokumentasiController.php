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
            $request->file("file"), // Sekarang ini array of files
            $vendor->id
        );

        $fileCount = count($request->file("file"));
        $message = $fileCount === 1 
            ? "Dokumentasi berhasil diunggah." 
            : "$fileCount file dokumentasi berhasil diunggah.";

        return redirect()
            ->route("vendor.daftar-tugas")
            ->with("success", $message);
    }
}