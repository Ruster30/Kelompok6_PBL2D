<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\ClientRequestService;
use Illuminate\Http\Request;

class ClientRequestController extends Controller
{
    public function __construct(
        private ClientRequestService $clientRequestService
    ) {}

    public function index(Request $request)
    {
        $data = $this->clientRequestService->getIndexData(
            $request->search,
            $request->status
        );

        return view("admin.requests.index", $data);
    }

    public function show(Event $clientRequest)
    {
        $data = $this->clientRequestService->getShowData($clientRequest);

        return view("admin.requests.show", $data);
    }

    public function negosiasi(Event $event)
    {
        $data = $this->clientRequestService->getNegosiasiData($event);

        return view("admin.requests.negosiasi", $data);
    }

    public function approve(Event $clientRequest)
    {
        $this->clientRequestService->approveRequest($clientRequest);

        return back()->with("success", "Request berhasil disetujui.");
    }

    public function reject(Event $clientRequest)
    {
        $this->clientRequestService->rejectRequest($clientRequest);

        return back()->with("success", "Request ditolak.");
    }
}