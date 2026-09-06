<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Http\Requests\Director\ApproveDocumentRequest;
use App\Http\Requests\Director\RejectDocumentRequest;
use App\Models\Document;
use App\Services\DocumentApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectorApprovalController extends Controller
{
    public function __construct(
        private readonly DocumentApprovalService $approvalService,
    ) {}

    /**
     * Tampilkan dashboard approval Director.
     */


    /**
     * Tampilkan review dokumen untuk Director.
     */
    public function show(Document $document)
    {
        $document->loadMissing(["event.client", "template", "user", "updatedBy", "numbering", "approvals", "qrVerification"]);

        return view("director.approval.show", compact("document"));
    }




    /**
     * Approve dokumen oleh Director.
     */
    public function approve(Document $document, ApproveDocumentRequest $request)
    {
        try {
            $this->approvalService->directorApprove(
                document: $document,
                director: $request->user(),
                pin:      $request->input("pin"),
            );
        } catch (\App\Exceptions\DDMS\DDMSException $e) {
            return redirect()
                ->route("director.approval.show", $document->id)
                ->with("error", $e->getMessage());
        }

        return redirect()
            ->route("director.approval.show", $document->id)
            ->with("success", "Dokumen berhasil disetujui.");
    }

    /**
     * Reject dokumen oleh Director.
     */
    public function reject(Document $document, RejectDocumentRequest $request)
    {
        try {
            $this->approvalService->directorReject(
                document: $document,
                director: $request->user(),
                reason:   $request->input("reason"),
                pin:      $request->input("pin"),
            );
        } catch (\App\Exceptions\DDMS\DDMSException $e) {
            return redirect()
                ->route("director.approval.show", $document->id)
                ->with("error", $e->getMessage());
        }

        return redirect()
            ->route("director.approval.show", $document->id)
            ->with("success", "Dokumen berhasil ditolak.");
    }

    /**
     * Tampilkan dashboard approval Director.
     */

    /**
     * Tampilkan dashboard Director.
     */
    public function dashboard()
    {
        $pendingCount = \App\Models\Document::query()
            ->where("status", \App\Enums\DocumentStatus::Pending)
            ->where("document_source", \App\Enums\DocumentSource::Generated)
            ->count();

        $approvedToday = \App\Models\Document::query()
            ->where("status", \App\Enums\DocumentStatus::Approved)
            ->whereDate("updated_at", now()->toDateString())
            ->count();

        $rejectedToday = \App\Models\Document::query()
            ->where("status", \App\Enums\DocumentStatus::Rejected)
            ->whereDate("updated_at", now()->toDateString())
            ->count();

        return view("director.dashboard", compact(
            "pendingCount",
            "approvedToday",
            "rejectedToday",
        ));
    }

    /**
     * Tampilkan dashboard approval Director.
     */

    /**
     * Tampilkan riwayat approval Director.
     */
    public function history(Request $request)
    {
        $documents = $this->approvalService->getHistory(
            search: $request->get("search"),
            status: $request->get("status"),
            perPage: 10,
        );

        return view("director.approval.history", compact("documents"));
    }

    /**
     * Tampilkan detail dokumen yang sudah diproses (read-only).
     */
    public function historyShow(\App\Models\Document $document)
    {
        $document->loadMissing([
            "event.client",
            "numbering",
            "qrVerification",
            "approvals.approvedBy",
            "approvals.submittedBy",
        ]);

        return view("director.approval.history-show", compact("document"));
    }

    /**
     * Download PDF dokumen yang sudah diproses (read-only untuk Director).
     *
     * Hanya dokumen berstatus Published/Rejected + Generated yang boleh diunduh
     * (scope riwayat Director). Dokumen lain dianggap tidak tersedia (404).
     */
    public function downloadDocument(\App\Models\Document $document)
    {
        $allowedStatuses = [
            \App\Enums\DocumentStatus::Published,
            \App\Enums\DocumentStatus::Rejected,
        ];

        if ($document->document_source !== \App\Enums\DocumentSource::Generated
            || ! in_array($document->status, $allowedStatuses, true)) {
            abort(404, "Dokumen tidak tersedia.");
        }

        if (! $document->file_path || ! Storage::disk("public")->exists($document->file_path)) {
            abort(404, "File PDF tidak ditemukan.");
        }

        return Storage::disk("public")->download($document->file_path, $document->nama_file . ".pdf");
    }

    /**
     * Tampilkan dashboard approval Director.
     */

    /**
     * Publish dokumen yang sudah disetujui.
     */
    public function publish(Document $document, Request $request)
    {
        try {
            $this->approvalService->publishDocument(
                document:  $document,
                publisher: $request->user(),
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route("director.approval.show", $document->id)
                ->with("error", $e->getMessage());
        }

        return redirect()
            ->route("director.approval.show", $document->id)
            ->with("success", "Dokumen berhasil dipublish.");
    }

    /**
     * Tampilkan dashboard approval Director.
     */
    public function index(Request $request)
    {
        $documents = $this->approvalService->getPendingDocuments(
            search:   $request->get("search"),
            category: $request->get("category"),
            perPage:  10,
        );

        $pendingCount = $documents->total();

        return view("director.approval.index", compact("documents", "pendingCount"));
    }
}
