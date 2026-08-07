<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Exceptions\DDMS\ApprovalNotPendingException;
use App\Repositories\Contracts\DocumentApprovalRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\DocumentNumberingRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\DirectorPinService;
use App\Services\DocumentNumberService;
use App\Services\DocumentQrCodeService;
use App\Services\DocumentVerificationService;

/**
 * DocumentApprovalService
 *
 * Bertanggung jawab terhadap seluruh Approval Workflow DDMS.
 *
 * Flow:
 *   Draft -> submit() -> Pending -> approve() -> Approved
 *                               \-> reject()  -> Rejected
 *
 * Transaction boundary:
 *   Setiap method yang mengubah lebih dari satu aggregate root
 *   (Approval + Document) menggunakan DB::transaction().
 *
 * @todo Resubmission policy: saat ini dokumen Rejected belum bisa
 *       disubmit ulang. Menunggu keputusan bisnis.
 */
class DocumentApprovalService
{
    public function __construct(
        private readonly DocumentApprovalRepositoryInterface $approvalRepository,
        private readonly DocumentRepositoryInterface $documentRepository,
        private readonly DocumentNumberingRepositoryInterface $numberingRepository,
        private readonly DirectorPinService $pinService,
        private readonly DocumentNumberService $numberService,
        private readonly DocumentQrCodeService $qrCodeService,
        private readonly DocumentVerificationService $verificationService,
    ) {}

    // ── Submit ───────────────────────────────────────────────

    /**
     * Submit dokumen untuk approval.
     *
     * Business Rule:
     * - Hanya dokumen Draft yang boleh disubmit.
     *
     * Flow:
     *   1. Validasi status document === draft
     *   2. Buat DocumentApproval baru (status = pending)
     *   3. Ubah document.status = pending via DocumentRepository
     */
    public function submit(Document $document, User $submittedBy): DocumentApproval
    {
        return DB::transaction(function () use ($document, $submittedBy): DocumentApproval {
            if (! $document->isDraft()) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    'Hanya dokumen dengan status Draft yang dapat diajukan approval. ' .
                    "Status saat ini: {$document->status}."
                );
            }

            // Business Rule: Nomor surat wajib diisi sebelum submit
            if (! $document->numbering || ! $document->numbering->document_number) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "nomor_surat" => "Nomor surat wajib diisi sebelum dokumen dikirim ke Director.",
                ]);
            }

            // Cegah duplicate submission: jika sudah ada approval pending
            $existing = $this->approvalRepository->findLatestByDocument($document->id);
            if ($existing && $existing->isPending()) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    'Dokumen sudah memiliki approval yang sedang menunggu.'
                );
            }

            $approval = $this->approvalRepository->create([
                'document_id'   => $document->id,
                'submitted_by'  => $submittedBy->id,
                'status'        => DocumentApproval::STATUS_PENDING,
                'submitted_at'  => now(),
            ]);

            // Update document via repository
            $this->documentRepository->update($document, [
                'status' => Document::STATUS_PENDING,
            ]);

            Log::info('Dokumen diajukan untuk approval', [
                'document_id' => $document->id,
                'submitted_by' => $submittedBy->id,
                'status' => Document::STATUS_PENDING,
            ]);

            return $approval;
        });
    }

    // ── Approve ──────────────────────────────────────────────

    /**
     * Menyetujui dokumen.
     *
     * Business Rule:
     * - Approval harus berstatus Pending.
     * - Tidak boleh approve dua kali.
     */
    public function approve(DocumentApproval $approval, User $approver): DocumentApproval
    {
        return DB::transaction(function () use ($approval, $approver): DocumentApproval {
            if (! $approval->isPending()) {
                throw new \App\Exceptions\DDMS\ApprovalNotPendingException(
                    'Approval sudah diproses. Status saat ini: ' . $approval->status . '.'
                );
            }

            $updated = $this->approvalRepository->update($approval, [
                'approver_id'  => $approver->id,
                'status'       => DocumentApproval::STATUS_APPROVED,
                'reviewed_at'  => now(),
            ]);

            // Update document via repository
            $this->documentRepository->update($updated->document, [
                'status' => Document::STATUS_APPROVED,
            ]);

            Log::info('Dokumen disetujui', [
                'approval_id' => $updated->id,
                'document_id' => $updated->document_id,
                'approver_id' => $approver->id,
            ]);

            return $updated;
        });
    }

    // ── Reject ───────────────────────────────────────────────

    /**
     * Menolak dokumen.
     *
     * Business Rule:
     * - Approval harus berstatus Pending.
     * - Alasan (reason) wajib diisi.
     */
    public function reject(DocumentApproval $approval, User $approver, string $reason): DocumentApproval
    {
        return DB::transaction(function () use ($approval, $approver, $reason): DocumentApproval {
            if (! $approval->isPending()) {
                throw new \App\Exceptions\DDMS\ApprovalNotPendingException(
                    'Approval sudah diproses. Status saat ini: ' . $approval->status . '.'
                );
            }

            if (empty(trim($reason))) {
                throw new \App\Exceptions\DDMS\DDMSException('Alasan reject wajib diisi.');
            }

            $updated = $this->approvalRepository->update($approval, [
                'approver_id'    => $approver->id,
                'status'         => DocumentApproval::STATUS_REJECTED,
                'reviewed_at'    => now(),
                'approval_note'  => $reason,
            ]);

            // Update document via repository
            $this->documentRepository->update($updated->document, [
                'status' => Document::STATUS_REJECTED,
            ]);

            return $updated;
        });
    }

    // ── Query Methods ────────────────────────────────────────

    public function findPending(): Collection
    {
        return $this->approvalRepository->findPending();
    }

    public function findByDocument(Document $document): Collection
    {
        return $this->approvalRepository->findByDocument($document->id);
    }

    public function getLatest(Document $document): ?DocumentApproval
    {
        return $this->approvalRepository->findLatestByDocument($document->id);
    }


    /**
     * Ambil dokumen yang menunggu approval Director.
     * Hanya menampilkan Generated documents dengan status Pending.
     */
    public function getPendingDocuments(
        ?string $search = null,
        ?string $category = null,
        int $perPage = 10,
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Document::query()
            ->where("status", \App\Enums\DocumentStatus::Pending)
            ->where("document_source", \App\Enums\DocumentSource::Generated)
            ->when($search, fn($q, $v) => $q->where("nama_file", "like", "%{$v}%"))
            ->when($category, fn($q, $v) => $q->where("document_category", $v))
            ->with(["event.client", "numbering"])
            ->orderBy("created_at", "desc")
            ->paginate($perPage);
    }



    /**
     * Ambil detail dokumen untuk review Director.
     * Hanya Generated + Pending. Jika tidak memenuhi, abort 404.
     */
    public function getDocumentForReview(int $id): Document
    {
        $document = Document::query()
            ->where("id", $id)
            ->where("status", \App\Enums\DocumentStatus::Pending)
            ->where("document_source", \App\Enums\DocumentSource::Generated)
            ->with([
                "event.client",
                "template",
                "user",
                "updatedBy",
                "numbering",
                "approvals",
            ])
            ->firstOrFail();

        return $document;
    }



    // -- Director Approve -------------------------------------

    /**
     * Approve dokumen oleh Director.
     * Validasi: Generated + Pending.
     */
    public function approveDocument(Document $document, User $director, string $pin): Document
    {
        // Verifikasi PIN sebelum proses approval
        $this->pinService->verifyPin($director, $pin);

        return DB::transaction(function () use ($document, $director): Document {
            if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen Generated yang dapat diapprove."
                );
            }

            if ($document->status !== \App\Enums\DocumentStatus::Pending) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen dengan status Pending yang dapat diapprove. Status saat ini: {$document->status->value}."
                );
            }

            // Buat atau update approval record
            $approval = $this->approvalRepository->findLatestByDocument($document->id);

            if (!$approval || !$approval->isPending()) {
                $approval = $this->approvalRepository->create([
                    "document_id"   => $document->id,
                    "submitted_by"  => $document->user_id ?? $director->id,
                    "status"        => \App\Models\DocumentApproval::STATUS_PENDING,
                    "submitted_at"  => now(),
                ]);
            }

            // Gunakan method approve() yang sudah ada
            $this->approve($approval, $director);

            // Nomor surat sudah diinput manual oleh Admin
            // (tidak ada auto-generation)

            Log::info("Dokumen diapprove oleh Director", [
                "document_id" => $document->id,
                "director_id" => $director->id,
            ]);

            return $document->fresh();
        });
    }

    // -- Director Reject --------------------------------------

    /**
     * Reject dokumen oleh Director.
     * Validasi: Generated + Pending.
     */
    public function rejectDocument(Document $document, User $director, string $reason, string $pin): Document
    {
        // Verifikasi PIN sebelum proses reject
        $this->pinService->verifyPin($director, $pin);

        return DB::transaction(function () use ($document, $director, $reason): Document {
            if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen Generated yang dapat direject."
                );
            }

            if ($document->status !== \App\Enums\DocumentStatus::Pending) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen dengan status Pending yang dapat direject. Status saat ini: {$document->status->value}."
                );
            }

            // Buat atau update approval record
            $approval = $this->approvalRepository->findLatestByDocument($document->id);

            if (!$approval || !$approval->isPending()) {
                $approval = $this->approvalRepository->create([
                    "document_id"   => $document->id,
                    "submitted_by"  => $document->user_id ?? $director->id,
                    "status"        => \App\Models\DocumentApproval::STATUS_PENDING,
                    "submitted_at"  => now(),
                ]);
            }

            // Gunakan method reject() yang sudah ada
            $this->reject($approval, $director, $reason);

            Log::info("Dokumen direject oleh Director", [
                "document_id" => $document->id,
                "director_id" => $director->id,
                "reason"      => $reason,
            ]);

            return $document->fresh();
        });
    }


    // -- Director Approve (tanpa PIN) --------------------------

    /**
     * Approve dokumen oleh Director.
     * Mencari approval pending, lalu memproses approve.
     */
    public function directorApprove(Document $document, User $director): Document
    {
        return DB::transaction(function () use ($document, $director): Document {
            if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen Generated yang dapat diapprove."
                );
            }

            if ($document->status !== \App\Enums\DocumentStatus::Pending) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen dengan status Pending yang dapat diapprove. Status saat ini: {$document->status->value}."
                );
            }

            $approval = $this->approvalRepository->findLatestByDocument($document->id);

            if (!$approval || !$approval->isPending()) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Tidak ada approval pending untuk dokumen ini."
                );
            }

            // Gunakan method approve() yang sudah ada (validasi + update)
            $this->approve($approval, $director);
            
            // Nomor surat sudah diinput manual oleh Admin sebelum submit
            // (tidak ada auto-generation)

            // Refresh object agar relasi numbering dan qrVerification termuat
            $document->refresh()->load([
                "numbering",
                "qrVerification",
            ]);

            // Regenerate PDF Final � replace file lama dengan PDF yang memuat nomor dan status
            // (QR dibuat terpisah saat Publish, tidak lagi saat Approve)
            $event = $document->event;
            $jenis = $document->tipe === 'kontrak' ? 'surat_kontrak' : $document->tipe;
            app(DocumentBuilderService::class)->regenerateFinalPdf($document, $event, $jenis);


            Log::info("Dokumen diapprove oleh Director", [
                "document_id" => $document->id,
                "director_id" => $director->id,
            ]);

            return $document->fresh();
        });
    }

    // -- Director Reject (tanpa PIN) ---------------------------

    /**
     * Reject dokumen oleh Director.
     * Mencari approval pending, lalu memproses reject.
     */
    public function directorReject(Document $document, User $director, string $reason): Document
    {
        return DB::transaction(function () use ($document, $director, $reason): Document {
            if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen Generated yang dapat direject."
                );
            }

            if ($document->status !== \App\Enums\DocumentStatus::Pending) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Hanya dokumen dengan status Pending yang dapat direject. Status saat ini: {$document->status->value}."
                );
            }

            $approval = $this->approvalRepository->findLatestByDocument($document->id);

            if (!$approval || !$approval->isPending()) {
                throw new \App\Exceptions\DDMS\DDMSException(
                    "Tidak ada approval pending untuk dokumen ini."
                );
            }

            // Gunakan method reject() yang sudah ada (validasi + update)
            $this->reject($approval, $director, $reason);

            Log::info("Dokumen direject oleh Director", [
                "document_id" => $document->id,
                "director_id" => $director->id,
                "reason"      => $reason,
            ]);

            return $document->fresh();
        });
    }

    /**
     * Ambil riwayat dokumen yang sudah diproses Director.
     * Status: Approved atau Rejected, Source: Generated.
     */
    public function getHistory(
        ?string $search = null,
        ?string $status = null,
        int $perPage = 10,
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return \App\Models\Document::query()
            ->whereIn("status", [\App\Enums\DocumentStatus::Approved, \App\Enums\DocumentStatus::Rejected])
            ->where("document_source", \App\Enums\DocumentSource::Generated)
            ->when($search, function ($q, $v) {
                $q->where(function ($q2) use ($v) {
                    $q2->where("nama_file", "like", "%{$v}%")
                       ->orWhereHas("event", fn($ev) => $ev->where("nama_event", "like", "%{$v}%"))
                       ->orWhereHas("event.client", fn($cl) => $cl->where("name", "like", "%{$v}%"))
                       ->orWhereHas("numbering", fn($nm) => $nm->where("document_number", "like", "%{$v}%"));
                });
            })
            ->when($status, fn($q, $v) => $q->where("status", $v))
            ->with(["event.client", "numbering", "approvals.approvedBy"])
            ->orderBy("updated_at", "desc")
            ->paginate($perPage);
    }

    // -- Publish ----------------------------------------------

    /**
     * Publish dokumen yang sudah disetujui.
     *
     * Business Rules:
     * - Status harus Approved.
     * - Document Source harus Generated.
     * - Nomor surat harus sudah ada.
     */
    public function publishDocument(Document $document, User $publisher): Document
    {
        return DB::transaction(function () use ($document, $publisher): Document {
            if ($document->status !== \App\Enums\DocumentStatus::Approved) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "publish" => "Hanya dokumen berstatus Approved yang dapat dipublish.",
                ]);
            }

            if ($document->document_source !== \App\Enums\DocumentSource::Generated) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "publish" => "Hanya dokumen Generated yang dapat dipublish.",
                ]);
            }

            $numbering = $this->numberingRepository->findByDocument($document->id);
            if (! $numbering || ! $numbering->document_number) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "publish" => "Nomor surat wajib diisi sebelum dokumen dipublish.",
                ]);
            }

            // Update status ke Published
            $this->documentRepository->update($document, [
                "status" => Document::STATUS_PUBLISHED,
            ]);

            Log::info("Dokumen dipublish", [
                "document_id" => $document->id,
                "document_number" => $numbering->document_number,
                "published_by" => $publisher->id,
            ]);

            // Pastikan Verification Token tersedia (tanpa QR)
            $this->verificationService->getOrCreateVerificationToken($document);

            // Phase 11G.1: QR otomatis dibuat setelah Publish (menggunakan Verification Token)
            $this->qrCodeService->getOrCreateQrCode($document);

            // Reload Document + relasi terbaru (numbering + qrVerification) sebelum render PDF.
            // Tidak mengandalkan lazy loading; memastikan PDF final tidak pernah stale.
            $document->refresh()->load([
                "numbering",
                "qrVerification",
            ]);

            // Phase 11G.2A: Regenerate PDF Final agar selalu berisi QR terbaru.
            // Hanya render ulang; tidak membuat QR/token/nomor surat baru.
            app(DocumentBuilderService::class)->regeneratePublishedPdf($document);

            return $document->fresh();
        });
    }
}