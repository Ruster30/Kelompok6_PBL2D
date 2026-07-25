<?php

declare(strict_types=1);

namespace App\Http\Controllers\DDMS;

use App\Exceptions\DDMS\QrVerificationExpiredException;
use App\Exceptions\DDMS\QrVerificationNotFoundException;
use App\Http\Requests\DDMS\ApproveDocumentRequest;
use App\Http\Requests\DDMS\RejectDocumentRequest;
use App\Http\Requests\DDMS\SubmitDocumentRequest;
use App\Http\Requests\DDMS\VerifyDocumentRequest;
use App\Http\Resources\DDMS\DocumentApprovalResource;
use App\Http\Resources\DDMS\DocumentNumberingResource;
use App\Http\Resources\DDMS\DocumentQrVerificationResource;
use App\Http\Resources\DDMS\DocumentResource;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    // ── CRUD ────────────────────────────────────────────────

    public function index(Request $request): JsonResource
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::with([
            'template', 'numbering', 'latestApproval', 'qrVerification',
        ])->latest()->paginate(15);

        return DocumentResource::collection($documents);
    }

    public function show(Document $document): JsonResource
    {
        $this->authorize('view', $document);

        $document->load(['template', 'numbering', 'approvals', 'qrVerification']);

        return new DocumentResource($document);
    }

    public function destroy(Document $document): JsonResponse
    {
        $this->authorize('delete', $document);

        $document->delete();

        return response()->json(['message' => 'Dokumen berhasil dihapus.']);
    }

    // ── Workflow ────────────────────────────────────────────

    public function submit(SubmitDocumentRequest $request, Document $document): JsonResource
    {
        $this->authorize('submitForApproval', $document);

        return new DocumentApprovalResource(
            $this->documentService->submitForApproval($request->toDTO()),
        );
    }

    public function approve(ApproveDocumentRequest $request, Document $document): JsonResource
    {
        $this->authorize('approve', $document);

        [$approval, $numbering, $qr] = $this->documentService->approveDocument($request->toDTO());

        return new DocumentApprovalResource($approval);
    }

    public function reject(RejectDocumentRequest $request, DocumentApproval $approval): JsonResource
    {
        $this->authorize('reject', $approval->document);

        return new DocumentApprovalResource(
            $this->documentService->rejectDocument($request->toDTO()),
        );
    }

    public function archive(Document $document): JsonResource
    {
        $this->authorize('archive', $document);

        $document->update(['is_archived' => true, 'status' => Document::STATUS_ARCHIVED]);

        return new DocumentResource($document->fresh());
    }
}
