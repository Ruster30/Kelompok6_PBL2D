<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTugasStatusRequest;
use App\Services\TugasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function __construct(
        private TugasService $tugasService
    ) {}

    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $data = $this->tugasService->getTasks(
            $vendor->id,
            $request->event
        );

        return view("vendor.daftar-tugas", $data);
    }

    public function update(UpdateTugasStatusRequest $request)
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $this->tugasService->updateTaskStatus(
            $vendor->id,
            $request->tugas_id,
            $request->status
        );

        return redirect()->route("vendor.daftar-tugas")
            ->with("success", "Status tugas berhasil diperbarui.");
    }
}