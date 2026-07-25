<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Exceptions\DDMS\ApprovalNotPendingException;
use App\Repositories\Contracts\DocumentApprovalRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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
}
