<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventVendorRequest;
use App\Http\Requests\UpdateEventVendorRequest;
use App\Models\EventVendor;
use App\Services\EventVendorService;
use Illuminate\Http\Request;

class EventVendorController extends Controller
{
    public function __construct(
        private EventVendorService $eventVendorService
    ) {}

    public function index(Request $request)
    {
        $data = $this->eventVendorService->getIndexData(
            $request->search,
            $request->status
        );

        return view("admin.EventVendor.index", $data);
    }

    public function store(StoreEventVendorRequest $request)
    {
        $this->eventVendorService->createAssignment($request->validated());

        return redirect()
            ->route("admin.event-vendors.index")
            ->with("success", "Penugasan vendor berhasil dibuat.");
    }

    public function update(UpdateEventVendorRequest $request, EventVendor $task)
    {
        $this->eventVendorService->updateAssignment($task, $request->validated());

        return redirect()
            ->route("admin.event-vendors.index")
            ->with("success", "Penugasan vendor berhasil diperbarui.");
    }

    public function destroy(EventVendor $task)
    {
        $this->eventVendorService->deleteAssignment($task);

        return redirect()
            ->route("admin.event-vendors.index")
            ->with("success", "Penugasan vendor berhasil dihapus.");
    }
}