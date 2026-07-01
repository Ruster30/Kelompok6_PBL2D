<?php

namespace App\Services;

use App\Models\Documentation;
use App\Models\DocumentationFile;

class AdminDocumentationService
{
    public function getDocumentations(?string $search, ?string $status): array
    {
        $query = Documentation::with(["event", "files"])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("judul", "like", "%{$search}%")
                  ->orWhereHas("event", fn($q2) => $q2->where("nama_event", "like", "%{$search}%"));
            });
        }

        if ($status) {
            $query->whereHas("files", fn($q) => $q->where("status", $status));
        }

        $documentations = $query->paginate(10)->withQueryString();

        $totalDocs    = Documentation::count();
        $pendingDocs  = DocumentationFile::where("status", "menunggu")->count();
        $approvedDocs = DocumentationFile::where("status", "disetujui")->count();

        return compact("documentations", "totalDocs", "pendingDocs", "approvedDocs");
    }

    public function approveFile(DocumentationFile $file): void
    {
        $file->update(["status" => "disetujui"]);
    }

    public function rejectFile(DocumentationFile $file): void
    {
        $file->update(["status" => "ditolak"]);
    }
}