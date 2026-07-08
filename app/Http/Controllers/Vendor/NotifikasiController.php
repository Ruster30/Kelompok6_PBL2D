<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function __construct(
        private NotifikasiService $notifikasiService
    ) {}

    public function index()
    {
        $data = $this->notifikasiService->getNotifications(auth()->id());

        return view("vendor.notifikasi", $data);
    }

    public function readAll()
    {
        $this->notifikasiService->markAllAsRead(Auth::user()->id);

        return redirect()->route("vendor.notifikasi")
            ->with("success", "Semua notifikasi telah ditandai sebagai dibaca.");
    }
}
