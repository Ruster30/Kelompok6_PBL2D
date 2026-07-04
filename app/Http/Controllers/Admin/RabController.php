<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRabItemRequest;
use App\Http\Requests\UpdateRabItemRequest;
use App\Models\Rab;
use App\Services\RabService;
use Illuminate\Http\Request;

class RabController extends Controller
{
    public function __construct(
        private RabService $rabService
    ) {}

    public function index(Request $request)
    {
        $data = $this->rabService->getRabData($request->event_id);

        return view("admin.rab.index", $data);
    }

    public function store(StoreRabItemRequest $request)
    {
        $this->rabService->createRabItem($request->validated());

        return redirect()
            ->route("admin.rab.index", ["event_id" => $request->event_id])
            ->with("success", "Item RAB berhasil ditambahkan.");
    }

    public function update(UpdateRabItemRequest $request, Rab $rab)
    {
        $this->rabService->updateRabItem($rab, $request->validated());

        return redirect()
            ->route("admin.rab.index", ["event_id" => $rab->event_id])
            ->with("success", "Item RAB berhasil diperbarui.");
    }

    public function destroy(Rab $rab)
    {
        $eventId = $rab->event_id;

        $this->rabService->deleteRabItem($rab);

        return redirect()
            ->route("admin.rab.index", ["event_id" => $eventId])
            ->with("success", "Item RAB berhasil dihapus.");
    }
}