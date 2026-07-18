<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Vendor;
use App\Services\AdminVendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct(
        private AdminVendorService $vendorService
    ) {}

    public function index(Request $request)
    {
        $data = $this->vendorService->getIndexData($request->search);

        return view("admin.vendor.index", $data);
    }

    public function store(StoreVendorRequest $request)
    {
        $this->vendorService->createVendor($request->validated());

        return redirect()
            ->route("admin.vendors.index")
            ->with("success", "Vendor berhasil ditambahkan.");
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $this->vendorService->updateVendor($vendor, $request->validated());

        return redirect()
            ->route("admin.vendors.index")
            ->with("success", "Vendor berhasil diperbarui.");
    }

    public function destroy(Vendor $vendor)
    {
        $this->vendorService->deleteVendor($vendor);

        return redirect()
            ->route("admin.vendors.index")
            ->with("success", "Vendor berhasil dihapus.");
    }
}