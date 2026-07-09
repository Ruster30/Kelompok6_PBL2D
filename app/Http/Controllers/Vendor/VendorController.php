<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function __construct(
        private VendorDashboardService $dashboardService
    ) {}

    public function ringkasan()
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $data = $this->dashboardService->getRingkasanData($vendor->id);

        return view("vendor.ringkasan", $data);
    }

    public function eventSaya(Request $request)
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        $data = $this->dashboardService->getEventSaya($vendor->id, $request->search);

        return view("vendor.event-saya", $data);
    }

    public function pengaturan()
    {
        $vendor = Auth::user()->vendor;
        abort_if(!$vendor, 403);

        return view("vendor.pengaturan", compact("vendor"));
    }

    public function logout()
    {
        Auth::logout();
        return redirect("/login");
    }
}