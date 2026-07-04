<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Negotiation;
use App\Models\Notification;

class ClientRequestService
{
    public function getIndexData(?string $search, ?string $status): array
    {
        $query = Event::with(["client", "latestProposal", "negotiations"])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas("client", fn($q2) => $q2->where("name", "like", "%{$search}%"))
                  ->orWhere("nama_event", "like", "%{$search}%");
            });
        }

        if ($status) {
            $query->where("status_event", $status);
        }

        return [
            "requests" => $query->paginate(10)->withQueryString(),
        ];
    }

    public function getShowData(Event $clientRequest): array
    {
        return [
            "request" => $clientRequest->load(["client", "latestProposal"]),
        ];
    }

    public function getNegosiasiData(Event $event): array
    {
        $event->load(["client", "latestProposal"]);

        $negotiations = Negotiation::with("user")
            ->where("event_id", $event->id)
            ->latest()
            ->get();

        return compact("event", "negotiations");
    }

    public function approveRequest(Event $clientRequest): void
    {
        $clientRequest->update(["status_event" => "diproses"]);

        Notification::create([
            "user_id" => $clientRequest->client_id,
            "judul"   => "Request Event Disetujui",
            "pesan"   => "Event \"" . $clientRequest->nama_event . "\" sudah disetujui dan masuk tahap diproses.",
            "tipe"    => "sukses",
        ]);
    }

    public function rejectRequest(Event $clientRequest): void
    {
        $clientRequest->update(["status_event" => "dibatalkan"]);

        Notification::create([
            "user_id" => $clientRequest->client_id,
            "judul"   => "Request Event Ditolak",
            "pesan"   => "Event \"" . $clientRequest->nama_event . "\" belum dapat kami proses.",
            "tipe"    => "peringatan",
        ]);
    }
}